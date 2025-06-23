# Custom Properties

## Overview

The Custom properties module provides a generic way to add custom properties to various models within a parent-child (definable - customizable) relationship. This allows for dynamic extension of models without requiring database schema changes.

## Features

- Define custom properties on parent models (definers)
- Use custom properties on child models (customizables)
- Support for various property types: text, number, date, boolean, select
- Validation rules for custom properties
- Default values for custom properties
- Options for select-type properties

### Process to create a new property

1. Select type (composite types with internal datatype and the form input and validation rule to store a value)
    - text:
      - type: string
      - options: none
      - form input: text input
      - display: text
    - number:
      - type: number
      - options: format with precision, suffix (currency, percentage)
      - form input: number input
      - display: formatted number (currency, precision, ...), progress w/o value, ring w/o value
    - select:
      - type: text
      - options: sort a-z, z-a, no; option items - each has background color and icon
      - form input: select single option with adding items
      - display: selected item with background and icon
    - multiselect:
      - type: json
      - options: sort a-z, z-a, no; option items - each has background color and icon
      - form input: select multiple option with adding items
      - display: selected items with background and icon in their selected order
    - date:
      - type: date
      - options: format, relative display
      - form input: date picker
      - display: formatted date
    - datetime:
      - type: datetime
      - options: format, 12 hour, relative display
      - form input: date picker with time picker
      - display: formatted date and time
    - time:
      - type: time
      - options: format, 12 hour, relative display
      - form input: time picker
      - display: formatted time
    - checkbox:
      - type: boolean
      - options: none
      - form input: checkbox
      - display: checkbox
    - id:
      - type: unsigned big increment number
      - options: prefix
      - form input: none
      - display: prefixed-ID
2. Property got automatically a name
3. Change settings of the property
   - name and options

## Usage

### Setup

1. Add the `DefinesCustomProperties` trait to the model that will define custom properties (the parent/definer model):

```php
use Modules\CustomProperties\Models\Concerns\DefinesCustomProperties;

class Workspace extends Model
{
    use DefinesCustomProperties;

    // ...
}
```

2. Add the `HasCustomProperties` trait to the model that will use custom properties (the child/customizable model):

```php
use Modules\CustomProperties\Models\Concerns\HasCustomProperties;

class Todo extends Model
{
    use HasCustomProperties;

    // ...

    // Implement the required method to get the parent model
    public function getDefinableParent(): ?Model
    {
        return $this->workspace;
    }
}
```

3. Add a `custom_properties` JSON column to the customizable model's table:

```php
Schema::table('todos', function (Blueprint $table) {
    $table->json('custom_properties')->nullable();
});
```

### Defining Custom Properties

To add a custom property to a definer model, use the `AddCustomPropertyToDefinerAction`:

```php
use Modules\CustomProperties\Actions\AddCustomPropertyToDefinerAction;
use Modules\CustomProperties\DataTransferObjects\CustomProperty;
use Modules\CustomProperties\Models\CustomPropertyType;

$action = new AddCustomPropertyToDefinerAction();
$action->handle(
    $workspace, 
    new CustomProperty(
        name: 'priority',
        label: 'Priority',
        type: CustomPropertyType::SELECT,
        rules: ['required'],
        defaultValue: 'medium',
        options: ['low', 'medium', 'high']
    )
);
```

### Using Custom Properties

To set a custom property on a customizable model:

```php
$todo->setCustomProperty('priority', 'high');
$todo->save();
```

To get a custom property value:

```php
$priority = $todo->getCustomProperty('priority', 'medium'); // Second parameter is the default value
```

### Storing Custom Properties from Requests

To store custom properties from a request, use the `StoreCustomPropertiesOnCustomizableAction`:

```php
use Modules\CustomProperties\Actions\StoreCustomPropertiesOnCustomizableAction;

$action = new StoreCustomPropertiesOnCustomizableAction();

// Make sure the customizable is associated with its definer
$todo->workspace()->associate($workspace);

// Store custom properties from the request
$action->handle($todo, $request);
```

### Querying by Custom Properties

To query models by custom property values:

```php
$highPriorityTodos = Todo::whereCustom('priority', 'high')->get();
```

## Integration Examples

### Workspace and Todo Example

In this example, the Workspace model defines custom properties, and the Todo model uses them:

1. The Workspace model uses the `DefinesCustomProperties` trait
2. The Todo model uses the `HasCustomProperties` trait
3. The Todo model implements `getDefinableParent()` to return its workspace
4. The Todo model has a `custom_properties` JSON column in its table

This allows workspace administrators to define custom properties for todos in their workspace, and users can set values for these properties when creating or updating todos.

## Data Structure

### Custom Property Definition

A custom property definition includes:

- `name`: Internal name of the property
- `label`: User-friendly name of the property
- `type`: Type of the property (text, number, date, boolean, select)
- `rules`: Validation rules for the property (optional)
- `default_value`: Default value for the property (optional)
- `options`: Options for select-type properties (optional)

### Custom Property Types

Available property types:

- `TEXT`: For text input
- `NUMBER`: For numeric input
- `DATE`: For date input
- `BOOLEAN`: For boolean/checkbox input
- `SELECT`: For dropdown/select input
