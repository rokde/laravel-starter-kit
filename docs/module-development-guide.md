# Module Development Guide for Laravel Starter Kit

This guide provides step-by-step instructions for creating new modules in the Laravel Starter Kit. It covers backend implementation, frontend with Vue.js, and testing with Pest.

## Table of Contents

1. [Module Structure](#module-structure)
2. [Backend Implementation](#backend-implementation)
   - [Creating the Module](#creating-the-module)
   - [Models](#models)
   - [Controllers](#controllers)
   - [Form Requests](#form-requests)
   - [Routes](#routes)
   - [Migrations](#migrations)
   - [Service Provider](#service-provider)
3. [Frontend Implementation](#frontend-implementation)
   - [Vue.js Components](#vuejs-components)
   - [TypeScript Types](#typescript-types)
   - [Translations](#translations)
   - [Plugin Registration](#plugin-registration)
4. [Testing with Pest](#testing-with-pest)
   - [Unit Tests](#unit-tests)
   - [Feature Tests](#feature-tests)
5. [Integration with Workspace](#integration-with-workspace)
6. [Documentation](#documentation)

## Module Structure

Each module should follow this directory structure:

```
app-modules/
└── your-module/
    ├── README.md
    ├── composer.json
    ├── database/
    │   ├── factories/
    │   ├── migrations/
    │   └── seeders/
    ├── lang/
    ├── resources/
    │   ├── js/
    │   │   ├── Pages/
    │   │   ├── plugin.ts
    │   │   └── types.ts
    │   └── views/
    ├── routes/
    │   └── web.php
    ├── src/
    │   ├── Http/
    │   │   ├── Controllers/
    │   │   └── Requests/
    │   ├── Models/
    │   └── Providers/
    └── tests/
        ├── Feature/
        └── Unit/
```

## Backend Implementation

### Creating the Module

1. Create the module directory structure:

```bash
mkdir -p app-modules/your-module/{database/{factories,migrations,seeders},lang,resources/{js/Pages,views},routes,src/{Http/{Controllers,Requests},Models,Providers},tests/{Feature,Unit}}
```

2. Create a `composer.json` file:

```json
{
    "name": "modules/your-module",
    "description": "Description of your module",
    "type": "library",
    "version": "1.0",
    "license": "proprietary",
    "require": {},
    "autoload": {
        "psr-4": {
            "Modules\\YourModule\\": "src/",
            "Modules\\YourModule\\Tests\\": "tests/",
            "Modules\\YourModule\\Database\\Factories\\": "database/factories/",
            "Modules\\YourModule\\Database\\Seeders\\": "database/seeders/"
        }
    },
    "minimum-stability": "stable",
    "extra": {
        "laravel": {
            "providers": [
                "Modules\\YourModule\\Providers\\YourModuleServiceProvider"
            ]
        }
    }
}
```

### Models

1. Create a model in `src/Models/YourModel.php`:

```php
<?php

declare(strict_types=1);

namespace Modules\YourModule\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\YourModule\Database\Factories\YourModelFactory;
use Modules\Workspace\Models\Workspace;

#[UseFactory(YourModelFactory::class)]
class YourModel extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'description',
        // Add other fillable attributes
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        // Define your casts here
    ];

    /**
     * Get the workspace that the model belongs to.
     */
    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    /**
     * Get the user that the model belongs to.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
```

2. Create a factory in `database/factories/YourModelFactory.php`:

```php
<?php

declare(strict_types=1);

namespace Modules\YourModule\Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Workspace\Models\Workspace;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Modules\YourModule\Models\YourModel>
 */
class YourModelFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(),
            'description' => $this->faker->paragraph(),
            'workspace_id' => Workspace::factory(),
            'user_id' => User::factory(),
            // Add other attributes
        ];
    }

    /**
     * Define additional states as needed.
     */
    public function someState(): self
    {
        return $this->state(fn (array $attributes) => [
            'some_attribute' => 'some_value',
        ]);
    }
}
```

### Controllers

Create controllers in `src/Http/Controllers/`:

```php
<?php

declare(strict_types=1);

namespace Modules\YourModule\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Modules\YourModule\Http\Requests\StoreYourModelRequest;
use Modules\YourModule\Http\Requests\UpdateYourModelRequest;
use Modules\YourModule\Models\YourModel;

class YourModelController
{
    /**
     * Display a listing of the resources for the current workspace.
     */
    public function index(Request $request): Response
    {
        $workspace = $request->user()->currentWorkspace;
        abort_if($workspace === null, 404);

        $items = YourModel::where('workspace_id', $workspace->id)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('your-module::Index', [
            'items' => $items,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): Response
    {
        $workspace = $request->user()->currentWorkspace;
        abort_if($workspace === null, 404);

        return Inertia::render('your-module::Create', [
            'workspace' => $workspace->only('id', 'name'),
            'workspaceUsers' => $workspace->allUsers()->map->only('id', 'name', 'email'),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreYourModelRequest $request): RedirectResponse
    {
        $workspace = $request->user()->currentWorkspace;
        abort_if($workspace === null, 404);

        $item = new YourModel($request->validated());
        $item->workspace_id = $workspace->id;
        $item->user_id = $request->validated('user_id');
        $item->save();

        return redirect()
            ->route('your-module.index')
            ->with('message', __('your-module::Item created.'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateYourModelRequest $request, YourModel $item): RedirectResponse
    {
        $workspace = $request->user()->currentWorkspace;
        abort_if($workspace === null, 404);
        abort_if($item->workspace_id !== $workspace->id, 403);

        $item->update($request->validated());

        return redirect()
            ->route('your-module.index')
            ->with('message', __('your-module::Item updated.'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, YourModel $item): RedirectResponse
    {
        $workspace = $request->user()->currentWorkspace;
        abort_if($workspace === null, 404);
        abort_if($item->workspace_id !== $workspace->id, 403);

        $item->delete();

        return redirect()
            ->route('your-module.index')
            ->with('message', __('your-module::Item deleted.'));
    }
}
```

### Form Requests

Create form requests in `src/Http/Requests/`:

```php
<?php

declare(strict_types=1);

namespace Modules\YourModule\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreYourModelRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->currentWorkspace !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'user_id' => ['required', 'exists:users,id'],
            // Add other validation rules
        ];
    }

    /**
     * Get the user ID from the request.
     */
    public function userId(): int
    {
        return $this->user()->id;
    }
}
```

```php
<?php

declare(strict_types=1);

namespace Modules\YourModule\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\YourModule\Models\YourModel;

class UpdateYourModelRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $item = $this->route('item');
        
        if (!$item instanceof YourModel) {
            return false;
        }
        
        $workspace = $this->user()->currentWorkspace;
        
        if ($workspace === null) {
            return false;
        }
        
        return $item->workspace_id === $workspace->id;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'string', 'max:255'],
            'description' => ['sometimes', 'string'],
            'user_id' => ['sometimes', 'exists:users,id'],
            // Add other validation rules
        ];
    }
}
```

### Routes

Define routes in `routes/web.php`:

```php
<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\YourModule\Http\Controllers\YourModelController;

Route::middleware(['web', 'auth', 'verified'])
    ->prefix('your-module')
    ->name('your-module.')
    ->group(function (): void {
        Route::get('/', [YourModelController::class, 'index'])
            ->name('index');

        Route::get('/create', [YourModelController::class, 'create'])
            ->name('create');

        Route::post('/', [YourModelController::class, 'store'])
            ->name('store');

        Route::patch('/{item}', [YourModelController::class, 'update'])
            ->whereNumber('item')
            ->name('update');

        Route::delete('/{item}', [YourModelController::class, 'destroy'])
            ->whereNumber('item')
            ->name('destroy');
    });
```

### Migrations

Create migrations in `database/migrations/`:

```php
<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('your_models', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('workspace_id')->index()->comment('The workspace that the item belongs to.');
            $table->foreignId('user_id')->index()->comment('The user that the item belongs to.');
            $table->string('title');
            $table->text('description')->nullable();
            // Add other columns
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('your_models');
    }
};
```

### Service Provider

Create a service provider in `src/Providers/YourModuleServiceProvider.php`:

```php
<?php

declare(strict_types=1);

namespace Modules\YourModule\Providers;

use Illuminate\Support\ServiceProvider;

class YourModuleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Register bindings
    }

    public function boot(): void
    {
        // Bootstrap the module
        $this->loadRoutesFrom(__DIR__.'/../../routes/web.php');
        $this->loadMigrationsFrom(__DIR__.'/../../database/migrations');
        $this->loadTranslationsFrom(__DIR__.'/../../lang', 'your-module');
        $this->loadViewsFrom(__DIR__.'/../../resources/views', 'your-module');

        // Register Inertia components
        $this->registerInertiaComponents();
    }

    /**
     * Register Inertia components.
     */
    private function registerInertiaComponents(): void
    {
        $this->app->booted(function (): void {
            $manager = $this->app->make(\Inertia\ResponseFactory::class);

            $manager->share('yourModule', fn () => [
                'routes' => [
                    'index' => route('your-module.index'),
                    'create' => route('your-module.create'),
                    'store' => route('your-module.store'),
                ],
            ]);

            $manager->rootView('app');

            $manager->version(fn () => md5_file(public_path('mix-manifest.json') ?? ''));
        });
    }
}
```

## Frontend Implementation

### Vue.js Components

Create Vue.js components in `resources/js/Pages/`:

1. `Index.vue` - List view:

```vue
<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { getI18n } from '@/i18n';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { YourModel } from '../types';

const { t } = getI18n();

interface Props {
    items: YourModel[];
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: t('Your Module'),
        href: '/your-module',
    },
];

const deleteItem = (item: YourModel) => {
    if (confirm(t('Are you sure you want to delete this item?'))) {
        router.delete(route('your-module.destroy', item.id), {
            preserveScroll: true,
        });
    }
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head :title="$t('Your Module')" />

        <div class="mx-auto my-8 flex w-full max-w-5xl flex-col">
            <div class="mb-6 flex justify-between">
                <h1 class="text-2xl font-semibold">{{ $t('Your Module') }}</h1>
                <Link :href="route('your-module.create')">
                    <Button>{{ $t('Create Item') }}</Button>
                </Link>
            </div>

            <div v-if="items.length === 0" class="rounded-md bg-neutral-50 p-8 text-center">
                <p class="text-neutral-600">{{ $t('No items found. Create your first item!') }}</p>
            </div>

            <div v-else class="space-y-4">
                <div v-for="item in items" :key="item.id" class="flex items-center justify-between rounded-md border border-neutral-200 p-4">
                    <div>
                        <h2 class="font-medium">{{ item.title }}</h2>
                        <p class="text-sm text-neutral-500">{{ item.description }}</p>
                        <p class="text-sm text-neutral-500" v-if="item.user">
                            {{ $t('Created by') }}: {{ item.user.name }}
                        </p>
                    </div>
                    <div class="flex space-x-2">
                        <Button variant="destructive" size="sm" @click="deleteItem(item)">
                            {{ $t('Delete') }}
                        </Button>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
```

2. `Create.vue` - Create form:

```vue
<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';
import { getI18n } from '@/i18n';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';

const { t } = getI18n();

interface User {
    id: number;
    name: string;
    email: string;
}

interface Props {
    workspace: {
        id: number;
        name: string;
    };
    workspaceUsers: User[];
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: t('Your Module'),
        href: '/your-module',
    },
    {
        title: t('Create Item'),
        href: '/your-module/create',
    },
];

const form = useForm({
    title: '',
    description: '',
    user_id: props.workspaceUsers.length > 0 ? props.workspaceUsers[0].id : '',
});

const submit = () => {
    form.post(route('your-module.store'), {
        preserveScroll: true,
    });
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head :title="$t('Create Item')" />

        <div class="mx-auto my-8 flex w-2xl max-w-2xl flex-col">
            <h1 class="mb-6 text-2xl font-semibold">{{ $t('Create Item') }}</h1>

            <form @submit.prevent="submit" class="w-full space-y-6">
                <div class="grid gap-2">
                    <Label for="title">{{ $t('Title') }}</Label>
                    <Input 
                        id="title" 
                        class="mt-1 block w-full" 
                        v-model="form.title" 
                        required 
                        autocomplete="off" 
                        :placeholder="$t('Item title')" 
                        autofocus 
                    />
                    <InputError class="mt-2" :message="form.errors.title" />
                </div>

                <div class="grid gap-2">
                    <Label for="description">{{ $t('Description') }}</Label>
                    <Textarea 
                        id="description" 
                        class="mt-1 block w-full" 
                        v-model="form.description" 
                        required 
                        :placeholder="$t('Item description')" 
                    />
                    <InputError class="mt-2" :message="form.errors.description" />
                </div>

                <div class="grid gap-2">
                    <Label for="user_id">{{ $t('Assign to') }}</Label>
                    <Select v-model="form.user_id">
                        <SelectTrigger class="w-full">
                            <SelectValue :placeholder="$t('Select a user')" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem 
                                v-for="user in props.workspaceUsers" 
                                :key="user.id" 
                                :value="user.id"
                            >
                                {{ user.name }} ({{ user.email }})
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <InputError class="mt-2" :message="form.errors.user_id" />
                </div>

                <div class="flex items-center gap-4">
                    <Button :disabled="form.processing">{{ $t('Save') }}</Button>

                    <Transition
                        enter-active-class="transition ease-in-out"
                        enter-from-class="opacity-0"
                        leave-active-class="transition ease-in-out"
                        leave-to-class="opacity-0"
                    >
                        <p v-show="form.recentlySuccessful" class="text-sm text-neutral-600">{{ $t('Saved.') }}</p>
                    </Transition>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
```

### TypeScript Types

Create TypeScript types in `resources/js/types.ts`:

```typescript
export interface YourModel {
    id: number;
    title: string;
    description: string;
    workspace_id: number;
    user_id: number;
    user?: {
        id: number;
        name: string;
        email: string;
    };
    created_at: string;
    updated_at: string;
}
```

### Translations

Create translations in `lang/en.json`:

```json
{
    "Your Module": "Your Module",
    "Create Item": "Create Item",
    "No items found. Create your first item!": "No items found. Create your first item!",
    "Created by": "Created by",
    "Delete": "Delete",
    "Are you sure you want to delete this item?": "Are you sure you want to delete this item?",
    "Title": "Title",
    "Description": "Description",
    "Item title": "Item title",
    "Item description": "Item description",
    "Assign to": "Assign to",
    "Select a user": "Select a user",
    "Save": "Save",
    "Saved.": "Saved.",
    "Item created.": "Item created.",
    "Item updated.": "Item updated.",
    "Item deleted.": "Item deleted."
}
```

### Plugin Registration

Create a plugin registration file in `resources/js/plugin.ts`:

```typescript
import { definePlugin } from '@/plugin';
import { RouteLocationRaw } from 'vue-router';
import { YourModel } from './types';

declare module '@inertiajs/vue3' {
    interface PageProps {
        yourModule?: {
            routes: {
                index: string;
                create: string;
                store: string;
            };
        };
    }
}

export default definePlugin(({ app, route }) => {
    // Register components
    app.component('your-module::Index', () => import('./Pages/Index.vue'));
    app.component('your-module::Create', () => import('./Pages/Create.vue'));

    // Register routes
    route('your-module.index', (): RouteLocationRaw => {
        return { name: 'your-module.index' };
    });

    route('your-module.create', (): RouteLocationRaw => {
        return { name: 'your-module.create' };
    });

    route('your-module.store', (): RouteLocationRaw => {
        return { name: 'your-module.store' };
    });

    route('your-module.update', (item: YourModel): RouteLocationRaw => {
        return { name: 'your-module.update', params: { item: item.id } };
    });

    route('your-module.destroy', (item: YourModel): RouteLocationRaw => {
        return { name: 'your-module.destroy', params: { item: item.id } };
    });
});
```

## Testing with Pest

### Unit Tests

Create unit tests in `tests/Unit/Models/YourModelTest.php`:

```php
<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\YourModule\Models\YourModel;
use Modules\Workspace\Models\Workspace;

uses(RefreshDatabase::class);

test('it can be instantiated using factory', function (): void {
    $item = YourModel::factory()->create();

    expect($item)->toBeInstanceOf(YourModel::class)
        ->and($item->id)->toBeInt()
        ->and($item->title)->toBeString()
        ->and($item->description)->toBeString();
});

test('it belongs to a workspace', function (): void {
    $workspace = Workspace::factory()->create();
    $item = YourModel::factory()->create(['workspace_id' => $workspace->id]);

    expect($item->workspace)->toBeInstanceOf(Workspace::class)
        ->and($item->workspace->id)->toBe($workspace->id);
});

test('it belongs to a user', function (): void {
    $user = User::factory()->create();
    $item = YourModel::factory()->create(['user_id' => $user->id]);

    expect($item->user)->toBeInstanceOf(User::class)
        ->and($item->user->id)->toBe($user->id);
});
```

### Feature Tests

Create feature tests in `tests/Feature/YourModelControllerTest.php`:

```php
<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\YourModule\Models\YourModel;
use Modules\Workspace\Models\Workspace;

uses(RefreshDatabase::class);

test('user can view items for current workspace', function (): void {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->create(['user_id' => $user->id]);
    $user->switchWorkspace($workspace);

    $item = YourModel::factory()->create([
        'workspace_id' => $workspace->id,
        'user_id' => $user->id,
        'title' => 'Test Item',
    ]);

    $response = $this->actingAs($user)->get(route('your-module.index'));

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page
        ->component('your-module::Index')
        ->has('items', 1)
        ->where('items.0.id', $item->id)
        ->where('items.0.title', 'Test Item')
    );
});

test('user can create item', function (): void {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->create(['user_id' => $user->id]);
    $user->switchWorkspace($workspace);

    $response = $this->actingAs($user)->get(route('your-module.create'));

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page
        ->component('your-module::Create')
        ->has('workspace')
        ->has('workspaceUsers')
    );
});

test('user can store item', function (): void {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->create(['user_id' => $user->id]);
    $user->switchWorkspace($workspace);

    $response = $this->actingAs($user)->post(route('your-module.store'), [
        'title' => 'New Item',
        'description' => 'Item description',
        'user_id' => $user->id,
    ]);

    $response->assertRedirect(route('your-module.index'));
    expect(YourModel::where([
        'title' => 'New Item',
        'description' => 'Item description',
        'user_id' => $user->id,
        'workspace_id' => $workspace->id,
    ])->exists())->toBeTrue();
});

test('user can update item', function (): void {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->create(['user_id' => $user->id]);
    $user->switchWorkspace($workspace);

    $item = YourModel::factory()->create([
        'workspace_id' => $workspace->id,
        'user_id' => $user->id,
        'title' => 'Original Title',
    ]);

    $response = $this->actingAs($user)->patch(route('your-module.update', $item), [
        'title' => 'Updated Title',
    ]);

    $response->assertRedirect(route('your-module.index'));
    expect(YourModel::where([
        'id' => $item->id,
        'title' => 'Updated Title',
    ])->exists())->toBeTrue();
});

test('user can delete item', function (): void {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->create(['user_id' => $user->id]);
    $user->switchWorkspace($workspace);

    $item = YourModel::factory()->create([
        'workspace_id' => $workspace->id,
        'user_id' => $user->id,
    ]);

    $response = $this->actingAs($user)->delete(route('your-module.destroy', $item));

    $response->assertRedirect(route('your-module.index'));
    expect(YourModel::where('id', $item->id)->exists())->toBeFalse();
});
```

## Integration with Workspace

To integrate your module with the workspace concept:

1. Add a `workspace_id` foreign key to your model's table
2. Add a relationship to the Workspace model in your model
3. In your controllers, always check that the user has access to the current workspace
4. Filter queries by the current workspace's ID
5. Ensure that users can only access resources that belong to their current workspace

Example of workspace integration in a controller method:

```php
public function index(Request $request): Response
{
    $workspace = $request->user()->currentWorkspace;
    abort_if($workspace === null, 404);

    $items = YourModel::where('workspace_id', $workspace->id)
        ->with('user')
        ->orderBy('created_at', 'desc')
        ->get();

    return Inertia::render('your-module::Index', [
        'items' => $items,
    ]);
}
```

## Documentation

Create a README.md file for your module:

```markdown
# Your Module

This module provides functionality for workspaces. It allows users to create, view, update, and delete items within their workspaces.

## Features

- Create items with a title and description
- Assign items to users in the workspace
- View a list of items for the current workspace
- Delete items

## Installation

This module is part of the Laravel Starter Kit and is automatically installed when you run:

```bash
php artisan modules:sync
```

## Usage

Once installed, the module provides the following routes:

- `/your-module` - View a list of items for the current workspace
- `/your-module/create` - Create a new item
- `/your-module/{item}` - Update an item
- `/your-module/{item}` (DELETE) - Delete an item

## Components

The module includes the following Vue.js components:

- `Index.vue` - Displays a list of items for the current workspace
- `Create.vue` - Provides a form for creating a new item

## Models

The module includes the following model:

- `YourModel` - Represents an item with a title, description, and relationships to a workspace and a user

## Controllers

The module includes the following controller:

- `YourModelController` - Handles CRUD operations for items

## Requests

The module includes the following form request classes:

- `StoreYourModelRequest` - Validates requests to create a new item
- `UpdateYourModelRequest` - Validates requests to update an item
```
