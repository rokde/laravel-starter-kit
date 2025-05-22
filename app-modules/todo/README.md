# Todo Module

This module provides todo functionality for workspaces. It allows users to create, view, update, and delete todos within their workspaces.

(This module was totally created by Junie by JetBrains)

## Features

- Create todos with a title and assign them to users in the workspace
- View a list of todos for the current workspace
- Mark todos as completed or incomplete
- Delete todos

## Installation

This module is part of the Laravel Starter Kit and is automatically installed when you run:

```bash
php artisan modules:sync
```

## Usage

Once installed, the module provides the following routes:

- `/todos` - View a list of todos for the current workspace
- `/todos/create` - Create a new todo
- `/todos/{todo}` - Update a todo
- `/todos/{todo}/toggle-complete` - Toggle the completed status of a todo
- `/todos/{todo}` (DELETE) - Delete a todo

## Components

The module includes the following Vue.js components:

- `Index.vue` - Displays a list of todos for the current workspace
- `Create.vue` - Provides a form for creating a new todo

## Models

The module includes the following model:

- `Todo` - Represents a todo item with a title, completed status, and relationships to a workspace and a user

## Controllers

The module includes the following controller:

- `TodoController` - Handles CRUD operations for todos

## Requests

The module includes the following form request classes:

- `StoreTodoRequest` - Validates requests to create a new todo
- `UpdateTodoRequest` - Validates requests to update a todo
