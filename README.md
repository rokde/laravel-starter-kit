# Laravel Starter Kit

## Usage

`laravel new --using=rokde/laravel-starter-kit`

## What is inside?

A customized version with the following things:

- [x] PHP 8.4
- [x] [laravel/vue-starter-kit](https://github.com/laravel/vue-starter-kit)
  - using [Inertia](https://inertiajs.com/) and [shadcn-vue](https://www.shadcn-vue.com/) components
- [x] [barryvdh/laravel-ide-helper](https://github.com/barryvdh/laravel-ide-helper)
- [x] pint.json with strict rules
- [x] internationalization (en, de)
- [x] static pages based on CommonMark Markdown files, with FrontMatter support and localized
- [ ] notification in app
  - [ ] preference in user profile to handle preferred by mail or in-app
- [x] [modules](https://github.com/InterNACHI/modular) supported for domain driven design
  - with the `foundation-layout` module to switch configured layouts
  - like [avosalmon/artisan-airlines](https://github.com/avosalmon/artisan-airlines) and [article](https://pacific-nymphea-e41.notion.site/Modularizing-Inertia-Laracon-India-2025-1a6320a6974e8014b91ec08cc6b79c4e)
  - example modules

## Features

### Laravel Features and Configuration

- Register a user
- Login a user
- [User must verify email](https://laravel.com/docs/verification#model-preparation)
- various settings configured in the [AppServiceProvider::boot()](./blob/main/app/Providers/AppServiceProvider.php#L20) method
- Localized views in english and german
- [database notifications](https://laravel.com/docs/notifications#database-prerequisites) already set up
- Profile settings including locale settings
- Imprint, Terms and Policy templates supported

### Configure the used layouts with a console command (provided by the foundation-layout module)

`php artisan app:configure-layouts`

Then you can switch between all the provided layouts within the [starter kits](https://laravel.com/docs/starter-kits#vue-available-layouts).

### Transfer localization

We support localization. If your user implements the `HasLocalePreference` interface we would support that by the `SetLocale` middleware.

With `php artisan translations:generate` the php stored translations get transferred to the typescript translations used by `vue-i18n`.

### Creating model documentation

`composer run ide-helper`

### Format your code

- during github workflows a new commit will handle this
- or: `composer run format` and `npm run format`
