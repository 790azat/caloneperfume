<form wire:submit="save" class="max-w-3xl space-y-6">
    <div class="a-card grid gap-4 sm:grid-cols-2">
        <h2 class="font-semibold text-stone-100 sm:col-span-2">Контакты</h2>
        <div><label class="a-label">Телефон</label><input wire:model="values.phone" class="a-field"></div>
        <div><label class="a-label">Email</label><input wire:model="values.email" class="a-field">@error('values.email')<p class="error">{{ $message }}</p>@enderror</div>
        <div><label class="a-label">Адрес бутика</label><input wire:model="values.address" class="a-field"></div>
        <div><label class="a-label">Часы работы</label><input wire:model="values.hours" class="a-field" placeholder="10:30–21:30"></div>
    </div>
    <div class="a-card grid gap-4 sm:grid-cols-2">
        <h2 class="font-semibold text-stone-100 sm:col-span-2">Соцсети и мессенджеры</h2>
        <div><label class="a-label">Instagram (ссылка)</label><input wire:model="values.instagram" class="a-field">@error('values.instagram')<p class="error">{{ $message }}</p>@enderror</div>
        <div><label class="a-label">Facebook (ссылка)</label><input wire:model="values.facebook" class="a-field">@error('values.facebook')<p class="error">{{ $message }}</p>@enderror</div>
        <div><label class="a-label">WhatsApp (номер, кнопка появится на сайте)</label><input wire:model="values.whatsapp" class="a-field" placeholder="+374…"></div>
        <div><label class="a-label">Telegram (@username)</label><input wire:model="values.telegram" class="a-field"></div>
    </div>
    <div class="a-card grid gap-4 sm:grid-cols-3">
        <h2 class="font-semibold text-stone-100 sm:col-span-3">Цифры на главной <span class="text-xs font-normal text-stone-500">(пустое поле скрывает счётчик)</span></h2>
        <div><label class="a-label">Брендов в бутике</label><input wire:model="values.stat_events" type="number" min="0" class="a-field"></div>
        <div><label class="a-label">Подписчиков в Instagram</label><input wire:model="values.stat_guests" type="number" min="0" class="a-field"></div>
        <div><label class="a-label">Лет с вами</label><input wire:model="values.stat_years" type="number" min="0" class="a-field"></div>
    </div>
    <div class="a-card space-y-4">
        <h2 class="font-semibold text-stone-100">Видео на главном экране</h2>
        <p class="text-xs text-stone-500">Если задано, вместо слайд-шоу из фото на первом экране будет играть это видео (без звука, по кругу). Лучше короткий ролик MP4 до 20–30 МБ.</p>
        <div><label class="a-label">Ссылка на MP4</label><input wire:model="values.hero_video" class="a-field" placeholder="https://…/hero.mp4">@error('values.hero_video')<p class="error">{{ $message }}</p>@enderror</div>
        @if (\App\Support\VercelBlob::enabled())
            <x-admin.blob-uploader folder="site" method="setHeroVideo" accept="video/mp4,video/webm" label="Загрузить видео" hint="MP4 или WEBM" />
        @else
        <div><label class="a-label">…или загрузить файл</label><input type="file" wire:model="heroVideo" accept="video/mp4,video/webm" class="text-sm text-stone-400">
            <div wire:loading wire:target="heroVideo" class="text-xs text-gold-300">Загрузка…</div>
            @error('heroVideo')<p class="error">{{ $message }}</p>@enderror</div>
        @endif
    </div>
    <button class="a-btn bg-gold-500 px-6 py-3 text-ink-950 hover:bg-gold-300" wire:loading.attr="disabled">Сохранить</button>
</form>
