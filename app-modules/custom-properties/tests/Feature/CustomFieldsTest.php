<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Modules\CustomProperties\Jobs\CleanupCustomPropertyJob;
use Modules\Todo\Models\Todo;
use Modules\Workspace\Models\Workspace;
use function Pest\Laravel\assertDatabaseHas;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->user = User::factory()->create();
    $this->workspace = Workspace::factory()->create(['user_id' => $this->user->id]);
});

test('a definable model can have a custom field definition', function (): void {
    // Arrange: Workspace already exists

    // Act: Create definition for the workspace todos
    $definition = $this->workspace->customPropertyDefinitions()->create([
        'name' => 'due_date',
        'label' => 'Due Date',
        'type' => 'date',
    ]);

    // Assert: Check the database
    assertDatabaseHas('custom_property_definitions', [
        'id' => $definition->id,
        'definable_id' => $this->workspace->id,
        'definable_type' => Workspace::class,
        'name' => 'due_date',
    ]);
});

test('a customizable model can set and get a custom property', function (): void {
    // Arrange: Create todo within the workspace
    $todo = Todo::factory()->create(['workspace_id' => $this->workspace->id]);

    // Act: set custom property.
    $todo->setCustomProperty('priority', 'high');
    $todo->save();

    // Assert: check the fetching of the value
    expect($todo->getCustomProperty('priority'))->toBe('high');

    // Assert: check the database entry
    $this->assertJsonStringEqualsJsonString(
        '{"priority": "high"}',
        $todo->fresh()->getRawOriginal('custom_properties')
    );
});

test('it validates custom properties based on defined rules', function (): void {
    // Arrange: define a field with email rule
    $this->workspace->customPropertyDefinitions()->create([
        'name' => 'reviewer_email',
        'label' => 'Reviewer Email',
        'type' => 'text',
        'rules' => ['required', 'email'],
    ]);
    $todo = Todo::factory()->create(['workspace_id' => $this->workspace->id]);

    // Act & Assert: expect validation exception
    expect(fn () => $todo->validateCustomProperties(['reviewer_email' => 'invalid-email']))
        ->toThrow(ValidationException::class);

    // Act & Assert: do not expect validation exception
    $validated = $todo->validateCustomProperties(['reviewer_email' => 'test@example.com']);
    expect($validated)->toBe(['reviewer_email' => 'test@example.com']);
});

test('it does not throw an error if no definable parent exists', function (): void {
    // Arrange: create todo without workspace
    $todo = new Todo(); // getDefinableParent() returns null.

    // Act: try to validate
    $validated = $todo->validateCustomProperties(['some_field' => 'some_value']);

    // Assert: no exception should be thrown
    expect($validated)->toBeEmpty();
});

test('it can search for a customizable model by a custom property', function (): void {
    // Arrange: create 2 todos with different customer property values
    $todoToFind = Todo::factory()->create(['workspace_id' => $this->workspace->id]);
    $todoToFind->setCustomProperty('search_key', 'unique_value_123');
    $todoToFind->save();

    $otherTodo = Todo::factory()->create(['workspace_id' => $this->workspace->id]);
    $otherTodo->setCustomProperty('search_key', 'another_value');
    $otherTodo->save();

    // Act: use the whereCustom Scope
    $results = Todo::whereCustom('search_key', 'unique_value_123')->get();

    // Assert: result should get just the first todo
    expect($results)->toHaveCount(1)
        ->and($results->first()->id)->toBe($todoToFind->id);
});

test('it dispatches a job to cleanup properties upon definition deletion', function (): void {
    // Arrange: create a definition
    $definition = $this->workspace->customPropertyDefinitions()->create(['name' => 'to_delete', 'label' => 'Old', 'type' => 'text']);

    Queue::fake();

    // Act: delete definition
    $this->actingAs($this->user)
        ->delete(route('custom-properties.destroy', $definition))
        ->assertRedirectBack();

    // Assert: check queue job
    Queue::assertPushed(CleanupCustomPropertyJob::class, fn ($job): bool => $job->definable->id === $this->workspace->id &&
        $job->propertyName === $definition->name);
});

test('getter returns definition default value if property is not set on model', function (): void {
    // Arrange: create definition with default valut
    $this->workspace->customPropertyDefinitions()->create([
        'name' => 'team',
        'label' => 'Team',
        'type' => 'text',
        'default_value' => 'Phoenix',
    ]);
    // create a todo without setting property value
    $todo = Todo::factory()->create(['workspace_id' => $this->workspace->id, 'custom_properties' => null]);

    // Act & Assert: getter should return default value
    expect($todo->getCustomProperty('team'))->toBe('Phoenix');
});
