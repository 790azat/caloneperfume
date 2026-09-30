import { upload } from '@vercel/blob/client';

// Direct browser → Vercel Blob uploads for the admin panel (bypasses Vercel's 4.5 MB request limit).
// Usage: <div x-data="blobUploader({ folder: 'works', onUploaded: (kind, url) => $wire.addBlob(kind, url) })">
document.addEventListener('alpine:init', () => {
    window.Alpine.data('blobUploader', ({ folder, onUploaded }) => ({
        queue: [],
        error: null,

        get busy() {
            return this.queue.some((item) => item.progress < 100 && !item.failed);
        },

        async pick(event) {
            const files = Array.from(event.target.files || []);
            event.target.value = '';
            await Promise.all(files.map((file) => this.send(file)));
        },

        async send(file) {
            const item = { name: file.name, progress: 0, failed: false };
            this.queue.push(item);
            const entry = this.queue[this.queue.length - 1];
            const safeName = file.name.toLowerCase().replace(/[^a-z0-9.\-_]+/g, '-').replace(/^-+/, '') || 'file';

            try {
                const blob = await upload(`${folder}/${safeName}`, file, {
                    access: 'public',
                    handleUploadUrl: '/admin/blob-upload',
                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content },
                    multipart: file.size > 20 * 1024 * 1024,
                    onUploadProgress: ({ percentage }) => { entry.progress = Math.min(99, Math.round(percentage)); },
                });
                await onUploaded(file.type.startsWith('video/') ? 'video' : 'image', blob.url);
                entry.progress = 100;
                setTimeout(() => { this.queue = this.queue.filter((i) => i !== entry); }, 1500);
            } catch (e) {
                entry.failed = true;
                this.error = `${file.name}: ${e.message}`;
            }
        },
    }));
});

// "Import from folder": groups the picked folder into posts (same rules as App\Support\MediaFolder),
// skips posts already on the site, uploads the rest straight to Blob and creates a work per post.
const IMAGE = ['jpg', 'jpeg', 'png', 'webp'];
const VIDEO = ['mp4', 'mov', 'm4v', 'webm'];
const extOf = (name) => name.split('.').pop().toLowerCase();
const keyOf = (name) => {
    const base = name.replace(/\.[^.]+$/, '').replace(/\.fdash.*$/, '');
    return (base.split('_')[0] || base).slice(0, 120);
};
const csrf = () => document.querySelector('meta[name=csrf-token]').content;

document.addEventListener('alpine:init', () => {
    window.Alpine.data('folderImporter', () => ({
        total: 0,
        done: 0,
        busy: false,
        status: '',
        error: null,

        async pick(event) {
            const files = Array.from(event.target.files || [])
                // only files directly inside the chosen folder
                .filter((f) => (f.webkitRelativePath || f.name).split('/').length <= 2)
                .filter((f) => IMAGE.includes(extOf(f.name)) || VIDEO.includes(extOf(f.name)));
            event.target.value = '';
            if (!files.length) {
                this.error = 'В папке нет фото или видео.';
                return;
            }

            const groups = new Map();
            files.forEach((file) => {
                const key = keyOf(file.name);
                if (!groups.has(key)) groups.set(key, []);
                groups.get(key).push(file);
            });

            this.busy = true;
            this.error = null;
            this.status = 'Проверяем, что уже есть на сайте…';
            const known = new Set(await this.$wire.knownKeys([...groups.keys()]));
            const todo = [...groups.entries()]
                .filter(([key]) => !known.has(key))
                .sort(([a], [b]) => (a < b ? 1 : -1));

            this.total = todo.length;
            this.done = 0;
            if (!todo.length) {
                this.status = 'Новых постов нет, всё уже на сайте.';
                this.total = 0;
                this.busy = false;
                this.$dispatch('toast', { message: 'Новых постов нет' });
                return;
            }

            const worker = async () => {
                while (todo.length) {
                    const [key, group] = todo.shift();
                    try {
                        group.sort((a, b) => a.name.localeCompare(b.name));
                        const items = [];
                        for (const file of group) {
                            const safe = file.name.toLowerCase().replace(/[^a-z0-9.\-_]+/g, '-');
                            const blob = await upload(`works/import/${key.replace(/[^\w.-]+/g, '-')}/${safe}`, file, {
                                access: 'public',
                                handleUploadUrl: '/admin/blob-upload',
                                headers: { 'X-CSRF-TOKEN': csrf() },
                                multipart: file.size > 20 * 1024 * 1024,
                            });
                            items.push({ type: VIDEO.includes(extOf(file.name)) ? 'video' : 'image', url: blob.url });
                        }
                        await this.$wire.importUploaded(key, items, Math.max(...group.map((f) => f.lastModified)));
                    } catch (e) {
                        this.error = `${key}: ${e.message}`;
                    }
                    this.done++;
                    this.status = `Загружаем посты…`;
                }
            };
            await Promise.all([worker(), worker(), worker()]);
            this.status = 'Готово';
            this.busy = false;
            this.$dispatch('toast', { message: `Импорт завершён: ${this.done}` });
        },
    }));
});
