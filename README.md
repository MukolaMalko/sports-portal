# Спортивний портал

Веб-система управління спортивними подіями на базі CMS WordPress.
Бакалаврський проект, 2026.

## Про проект

Система дозволяє вести облік спортивних подій, новин, результатів змагань та розкладу.
Адміністратор керує контентом через WordPress Dashboard, дані доступні через REST API.

## Технології

- **CMS**: WordPress + Custom Post Types (CPT)
- **Плагіни**: Advanced Custom Fields (ACF), JWT Authentication for WP REST API
- **API**: WP REST API (`/wp-json/wp/v2/`)
- **Frontend**: PHP, HTML5, CSS3, JavaScript
- **Інтеграції**: Google Drive API (зберігання медіа), Google Sheets API (розклад/результати)

## Структура проекту

| Файл | Призначення |
|---|---|
| `functions.php` | Реєстрація CPT, ACF полів та REST API |
| `front-page.php` | Головна сторінка з подіями та новинами |
| `archive-event.php` | Список подій з фільтрацією за статусом |
| `archive-news.php` | Список новин |
| `archive-results.php` | Результати змагань |
| `archive-schedule.php` | Розклад подій |
| `single-event.php` | Детальна сторінка події |
| `single-news.php` | Детальна сторінка новини |
| `header.php` | Шапка сайту з навігацією |
| `footer.php` | Підвал сайту |
| `index.php` | Базовий шаблон теми |
| `style.css` | Стилі теми WordPress |
| `js/app.js` | JavaScript для інтерактивності |
| `.gitignore` | Виключення чутливих файлів |

## REST API ендпоінти
