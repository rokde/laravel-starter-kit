# Laravel Starter Kit

A customized version with the following things:

- [x] PHP 8.4
- [x] [laravel/vue-starter-kit](https://github.com/laravel/vue-starter-kit)
  - using [Inertia](https://inertiajs.com/) and [shadcn-vue](https://www.shadcn-vue.com/) components
- [x] [barryvdh/laravel-ide-helper](https://github.com/barryvdh/laravel-ide-helper)
- [x] pint.json with strict rules
- [ ] internationalization (en, de)
  - [ ] laravel
  - [ ] vue
- [ ] notification in app
  - [ ] preference in user profile to handle preferred by mail or in-app
- [ ] modules supported for domain driven design
  - like [avosalmon/artisan-airlines](https://github.com/avosalmon/artisan-airlines) and [article](https://pacific-nymphea-e41.notion.site/Modularizing-Inertia-Laracon-India-2025-1a6320a6974e8014b91ec08cc6b79c4e)
  - example modules

## Features

### Laravel Features and Configuration

- Register a user
- Login a user
- [User must verify email](https://laravel.com/docs/verification#model-preparation)
- various settings configured in the [AppServiceProvider::boot()](./blob/main/app/Providers/AppServiceProvider.php#L20) method

### Creating model documentation

`composer run ide-helper`

### Format your code

- during github workflows a new commit will handle this
- or: `composer run format` and `npm run format`
