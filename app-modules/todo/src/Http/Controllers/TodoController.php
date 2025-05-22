<?php

declare(strict_types=1);

namespace Modules\Todo\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Modules\Todo\Http\Requests\StoreTodoRequest;
use Modules\Todo\Http\Requests\UpdateTodoRequest;
use Modules\Todo\Models\Todo;

class TodoController
{
    /**
     * Display a listing of the todos for the current workspace.
     */
    public function index(Request $request): Response
    {
        $workspace = $request->user()->currentWorkspace;
        abort_if($workspace === null, 404);

        $todos = Todo::where('workspace_id', $workspace->id)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('todo::Index', [
            'todos' => $todos,
        ]);
    }

    /**
     * Show the form for creating a new todo.
     */
    public function create(Request $request): Response
    {
        $workspace = $request->user()->currentWorkspace;
        abort_if($workspace === null, 404);

        return Inertia::render('todo::Create', [
            'workspace' => $workspace->only('id', 'name'),
            'workspaceUsers' => $workspace->allUsers()->map->only('id', 'name', 'email'),
        ]);
    }

    /**
     * Store a newly created todo in storage.
     */
    public function store(StoreTodoRequest $request): RedirectResponse
    {
        $workspace = $request->user()->currentWorkspace;
        abort_if($workspace === null, 404);

        $todo = new Todo($request->validated());
        $todo->workspace_id = $workspace->id;
        $todo->user_id = $request->validated('user_id');
        $todo->save();

        return redirect()
            ->route('todos.index')
            ->with('message', __('todo::Todo created.'));
    }

    /**
     * Update the specified todo in storage.
     */
    public function update(UpdateTodoRequest $request, Todo $todo): RedirectResponse
    {
        $workspace = $request->user()->currentWorkspace;
        abort_if($workspace === null, 404);

        // Check if the user is authorized to update the todo
        $request->user()->can('update', $todo);

        $todo->update($request->validated());

        return redirect()
            ->route('todos.index')
            ->with('message', __('todo::Todo updated.'));
    }

    /**
     * Toggle the completed status of the specified todo.
     */
    public function toggleComplete(Request $request, Todo $todo): RedirectResponse
    {
        $workspace = $request->user()->currentWorkspace;
        abort_if($workspace === null, 404);

        // Check if the user is authorized to update the todo
        $request->user()->can('update', $todo);

        $todo->completed = ! $todo->completed;
        $todo->save();

        return redirect()
            ->route('todos.index')
            ->with('message', $todo->completed
                ? __('todo::Todo marked as completed.')
                : __('todo::Todo marked as incomplete.'));
    }

    /**
     * Remove the specified todo from storage.
     */
    public function destroy(Request $request, Todo $todo): RedirectResponse
    {
        $workspace = $request->user()->currentWorkspace;
        abort_if($workspace === null, 404);

        // Check if the user is authorized to delete the todo
        $request->user()->can('delete', $todo);

        $todo->delete();

        return redirect()
            ->route('todos.index')
            ->with('message', __('todo::Todo deleted.'));
    }
}
