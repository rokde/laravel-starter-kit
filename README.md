# Laravel Starter Kit

## Usage

`laravel new --using=rokde/laravel-starter-kit`

## What is inside?

A customized version with the following things:

### Repository
- [x] PHP 8.4
- [x] [laravel/vue-starter-kit](https://github.com/laravel/vue-starter-kit)
  - using [Inertia](https://inertiajs.com/) and [shadcn-vue](https://www.shadcn-vue.com/) components
- [x] pint.json with strict rules
- [x] repository dependency health with dependabot
- [x] automatic linting and testing on push
- [x] automatic CHANGELOG.md update on releases

### IDE support

- [x] [barryvdh/laravel-ide-helper](https://github.com/barryvdh/laravel-ide-helper)
- [x] JetBrains Junie guidelines

### Starter Kit features

- [x] internationalization (en, de)
- [x] static pages based on CommonMark Markdown files, with FrontMatter support and localized
  - in `resources/markdown` are the md files located
  - localized version can have a `*.[locale].md` extension (e.g. `.de.md`)
- [x] using UseFactory class attribute for eloquent models
- [x] [modules](https://github.com/InterNACHI/modular) supported for domain driven design
  - [x] [foundation-layout](app-modules/foundation-layout/README.md) - Configure and switch between different layout styles
  - [x] [notification](app-modules/notification/README.md) - Manage user notification preferences and delivery methods
  - [x] [todo](app-modules/todo/README.md) - Create, manage, and track todos within workspaces
  - [x] [workspace](app-modules/workspace/README.md) - Create and manage workspaces for team collaboration

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
- displaying the password rules on registration and password change
- reveal passwords on password input elements

### Build with DDD

`php artisan make:module [MODULE_NAME]`

For detailed instructions on creating modules, including backend implementation, Vue.js frontend, and Pest testing, see the [Module Development Guide](docs/module-development-guide.md).

This generates the whole module stub. Use contracts and dtos to communicate between domain boundaries as described in [Modularizing Inertia](https://pacific-nymphea-e41.notion.site/Modularizing-Inertia-Laracon-India-2025-1a6320a6974e8014b91ec08cc6b79c4e). An [example repository](https://github.com/avosalmon/artisan-airlines) describes it better. It supports also the module-based loading and providing of typescript code for inertia.

#### Module Dependency Graph

You can generate a [visual representation](docs/module-dependency-graph.md) of module dependencies with:

`php artisan modules:graph`

This command analyzes the codebase and creates a dependency graph using Mermaid, which is supported by GitHub Markdown. The graph shows which modules depend on each other and provides detailed information about each module.

The generated graph is saved to `docs/module-dependency-graph.md` by default, but you can specify a custom output path:

`php artisan modules:graph --output=custom/path/graph.md`

#### Database Entity Relationship Diagram

A [visual representation](docs/database-entity-relationship-diagram.md) of the database schema is available, showing all tables, columns, relationships, and indexes used in the project.

### Configure the used layouts with a console command

(provided by the [foundation-layout](app-modules/foundation-layout/README.md) module)

`php artisan app:configure-layouts`

Then you can switch between all the provided layouts within the [starter kits](https://laravel.com/docs/starter-kits#vue-available-layouts).

### Reuse backend localization in frontend

We support localization. If your user implements the `HasLocalePreference` interface we would support that by the `SetLocale` middleware.

With `php artisan translations:generate` the php stored translations get transferred to the typescript translations used by `vue-i18n`.

### Creating model documentation

`composer run ide-helper`

### Format your code

- during github workflows a new commit will handle this
- or: `composer run format` and `npm run format`

### Run the test suite

`composer run test`

### Code Coverage

To generate a code coverage report, run:

```bash
composer test:coverage
```

After the tests complete, you can view the coverage report by opening `./tests/coverage/index.html` in your browser.

### Dependabot

Weekly updates for npm and php dependencies.
