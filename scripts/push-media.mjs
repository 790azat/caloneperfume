// Uploads converted videos to the site's Vercel Blob store and records their URLs
// in database/data/videos.json (used by `php artisan calone:manifest`).
//
//   SITE_URL=https://… IMPORT_KEY=… node scripts/push-media.mjs <folder-with-mp4>
//
// The site signs each upload (POST /import/blob-token, checked against its IMPORT_KEY env var),
// so no Blob token is needed on this machine. Safe to re-run: uploaded files are skipped.
import { upload } from '@vercel/blob/client';
import { readFile, readdir, writeFile } from 'node:fs/promises';
import { existsSync } from 'node:fs';
import path from 'node:path';

const [folder] = process.argv.slice(2);
const { SITE_URL, IMPORT_KEY } = process.env;
if (!folder || !SITE_URL || !IMPORT_KEY) {
    console.error('Usage: SITE_URL=… IMPORT_KEY=… node scripts/push-media.mjs <folder>');
    process.exit(1);
}

const out = path.resolve('database/data/videos.json');
const done = existsSync(out) ? JSON.parse(await readFile(out, 'utf8')) : {};
const files = (await readdir(folder)).filter((f) => /\.(mp4|mov|webm)$/i.test(f));
const todo = files.filter((f) => !done[path.parse(f).name]);
console.log(`${files.length} videos, ${todo.length} to upload`);

let saved = Promise.resolve();
const save = () => (saved = saved.then(() => writeFile(out, JSON.stringify(done, null, 1) + '\n')));

const worker = async () => {
    while (todo.length) {
        const file = todo.shift();
        const name = path.parse(file).name;
        for (let attempt = 1; attempt <= 3; attempt++) {
            try {
                const body = await readFile(path.join(folder, file));
                const blob = await upload(`works/instagram/${file.toLowerCase()}`, body, {
                    access: 'public',
                    contentType: 'video/mp4',
                    handleUploadUrl: `${SITE_URL.replace(/\/$/, '')}/import/blob-token`,
                    headers: { 'X-Import-Key': IMPORT_KEY },
                    multipart: body.length > 20 * 1024 * 1024,
                });
                done[name] = blob.url;
                await save();
                console.log(`ok ${Object.keys(done).length}/${files.length} ${file}`);
                break;
            } catch (e) {
                console.error(`fail ${file} (attempt ${attempt}): ${e.message}`);
            }
        }
    }
};

await Promise.all([worker(), worker(), worker(), worker()]);
await saved;
console.log('done');
