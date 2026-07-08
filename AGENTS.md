# Repository Guidelines

## Project Structure & Module Organization
This is a Laravel marketplace app for upcycling appointments, listings, chat, reviews, and admin workflows. Backend code lives in `app/`: HTTP controllers in `app/Http/Controllers`, form validation in `app/Http/Requests`, Eloquent models in `app/Models`, business logic in `app/Services`, and query/data access code in `app/Repositories`. Keep controllers thin and put feature behavior in the matching service/repository pair when one exists.

Routes are in `routes/web.php`, `routes/auth.php`, and `routes/channels.php`. Blade views are under `resources/views`, with shared components in `resources/views/components` and layout shells in `resources/views/layouts`. Frontend entry points are `resources/js/app.js` and `resources/css/app.css`; public assets belong in `public/`. Database migrations, factories, and seeders live in `database/`. Tests are split into `tests/Feature` and `tests/Unit`.

## Build, Test, and Development Commands
- `composer install` installs PHP dependencies.
- `npm install` installs Vite, Tailwind, Alpine, and frontend packages.
- `cp .env.example .env` then `php artisan key:generate` prepares local configuration.
- `php artisan migrate` applies database schema changes.
- `composer run dev` starts the Laravel server, queue listener, and Vite dev server together.
- `npm run dev` starts only Vite for asset development.
- `npm run build` builds production frontend assets.
- `php artisan test` runs the Laravel test suite; `./vendor/bin/pest` is also available.
- `./vendor/bin/pint` formats PHP code using Laravel Pint.

## Coding Style & Naming Conventions
Follow `.editorconfig`: UTF-8, LF endings, final newline, spaces, and 4-space indentation except YAML at 2 spaces. PHP classes use PSR-4 namespaces under `App\` and PascalCase names such as `ProductController`, `StoreProductRequest`, `ProductService`, and `ProductRepository`. Blade files use kebab-case or feature-oriented names, for example `resources/views/products/show.blade.php`.

## Testing Guidelines
Use Pest/Laravel tests. Put request and workflow coverage in `tests/Feature`; keep isolated behavior in `tests/Unit`. Name tests by feature and intent, such as `ProductCheckoutTest.php`, and cover authentication, authorization, validation, and persistence for changed workflows.

## Commit & Pull Request Guidelines
Recent history uses short, imperative or feature-focused subjects, for example `fix modal for uploading featured buyer` and `enhanced the ui of features buyer`. Keep commits focused on one change. Pull requests should include a concise description, linked issue when applicable, migration or environment notes, test results, and screenshots for Blade/UI changes.

## Security & Configuration Tips
Do not commit `.env`, credentials, Stripe keys, Pusher keys, or generated storage files. Keep sensitive setup in environment variables and document any new required keys in `.env.example`.
