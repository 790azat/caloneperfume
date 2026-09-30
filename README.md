# Calone Perfume

Сайт бутика нишевой парфюмерии Calone Perfume (Ереван): Laravel 13 + Livewire 4 + Tailwind CSS 4.

- Языки: армянский (по умолчанию), русский, английский. Переключатель в шапке, выбор запоминается.
- Витрина: коллекция с фильтром по категориям и подгрузкой, страница поста с каруселью фото и видео, лента видео (рилсы), BY CALONE, услуги бутика, форма персонального подбора.
- Регистрация и вход. Клиент видит свои заявки и их статус в «Мои заявки».
- Админ-панель `/admin`: работы (фото, видео, ссылки YouTube/Vimeo, порядок, обложка, подписи на трёх языках), **импорт из рабочей папки**, категории, услуги, отзывы, заявки, пользователи, контакты и часы работы.

## Импорт из рабочей папки

Админка → «Импорт из папки». Каждый пост становится работой: файлы `123_456.jpg` и `123_789.jpg` (выгрузка Instagram) попадают в одну карусель, остальные файлы становятся отдельными работами. Дата берётся из id поста Instagram или из даты файла. Уже импортированное повторно не добавляется, даже после удаления.

- Локально сайт читает папку сам: укажите путь и нажмите «Импортировать новые».
- На Vercel сервер не видит ваш компьютер: выберите папку кнопкой «Выбрать папку», браузер загрузит новые файлы прямо в Vercel Blob.

## Стартовый контент

`database/data/instagram.json` — посты из Instagram, которые добавляются в базу при деплое (`InstagramSeeder`, только новые). Фото лежат в `public/media` (WebP), видео в Vercel Blob. Пересобрать:

```bash
# фото/постеры уже сконвертированы в public/media/p/<post>/…
SITE_URL=https://caloneperfume.vercel.app IMPORT_KEY=… node scripts/push-media.mjs <папка с mp4>
php artisan calone:manifest <папка с оригиналами> --picks=picks.json
```

## Локальный запуск

```bash
composer install && npm install
cp .env.example .env && php artisan key:generate
# в .env укажите ADMIN_EMAIL и ADMIN_PASSWORD
php artisan migrate --seed
php artisan storage:link
npm run build
php artisan serve
```

Админ входит на `/login` с ADMIN_EMAIL/ADMIN_PASSWORD. Если админа ещё нет, достаточно зарегистрироваться с адресом из ADMIN_EMAIL.

## Деплой на Vercel

| Что | Где |
| --- | --- |
| PHP | `vercel-php` (`api/index.php`, `vercel.json`), регион `fra1` |
| База | Postgres **Neon** (Vercel → Storage → Neon), переменная `DATABASE_URL` + `DB_CONNECTION=pgsql` |
| Файлы | **Vercel Blob** (`BLOB_READ_WRITE_TOKEN`), загрузка из браузера напрямую, до 500 МБ на файл |

Миграции и новые посты из `instagram.json` применяются сами на первом запросе после деплоя. Пока база не подключена, сайт работает в демо-режиме на временной SQLite.

Переменные: `APP_KEY`, `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL`, `ADMIN_EMAIL`, `ADMIN_PASSWORD`, `IMPORT_KEY` (секрет для `scripts/push-media.mjs`), `DB_CONNECTION=pgsql`.
