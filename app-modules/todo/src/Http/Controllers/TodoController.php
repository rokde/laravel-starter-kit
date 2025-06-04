<?php

declare(strict_types=1);

namespace Modules\Todo\Http\Controllers;

use App\Enums\SortDirection;
use App\Http\Requests\PaginationRequest;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Inertia\Inertia;
use Inertia\Response;
use Modules\Todo\Data\Facets\AssignedUserFacet;
use Modules\Todo\Data\Facets\TodoCompletedFacet;
use Modules\Todo\Http\Requests\StoreTodoRequest;
use Modules\Todo\Http\Requests\UpdateTodoRequest;
use Modules\Todo\Models\Todo;

class TodoController
{
    /**
     * Display a listing of the todos for the current workspace.
     */
    public function index(PaginationRequest $request): Response
    {
        $workspace = $request->user()->currentWorkspace;
        abort_if($workspace === null, 404);

        /** @var Builder $query */
        $query = Todo::query()
            ->select(['id', 'title', 'completed', 'user_id'])
            ->with('user:id,name,email')
            ->where('workspace_id', $workspace->id);

        foreach ($request->sort('created_at', SortDirection::DESC) as $sort) {
            $query->orderBy($sort['field'], $sort['direction']->value);
        }

        $term = $request->term();
        $query->when($term, function (Builder $query, string $term): void {
            $query->where('title', 'LIKE', "%{$term}%");
        });

        $request->facets()
            ->each(
                function (array $values, string $facetKey) use ($query): void {
                    $relation = null;
                    $column = $facetKey;
                    if (mb_substr_count($facetKey, '.') > 0) {
                        [$relation, $column] = explode('.', $facetKey);
                    }

                    $query->when($relation, function (Builder $query) use ($relation, $column, $values): void {
                        $query->whereHas($relation, function (Builder $query) use ($column, $values): void {
                            $query->whereIn($column, $values);
                        });
                    });

                    $query->unless($relation, function (Builder $query) use ($column, $values): void {
                        $query->where(function (Builder $query) use ($column, $values): void {
                            $nullValues = array_filter($values, fn ($value): bool => $value === null);
                            if ($nullValues !== []) {
                                $query->orWhereNull($column);
                            }

                            $valueValues = array_filter($values, fn ($value): bool => $value !== null);
                            if ($valueValues !== []) {
                                // check bool values
                                $valueValues = array_map(function (string $value): mixed {
                                    return match ($value) {
                                        'true' => true,
                                        'false' => false,
                                        default => $value,
                                    };
                                }, $valueValues);

                                $query->orWhereIn($column, $valueValues);
                            }
                        });
                    });
                }
            );

        $result = $query->paginate(
            perPage: $request->size(50),
            columns: $request->fields(),
            pageName: $request->pageName(),
            page: $request->page(),
        )->toArray();

        $todoCompletedFacet = new TodoCompletedFacet(__('Completed'));
        $assignedUserFacet = new AssignedUserFacet(__('Assigned to'), 'user_id')
            ->setPossibleUsers($workspace->allUsers())
            ->includeNoUserFilter();

        return Inertia::render('todo::Index', [
            'data' => Arr::only($result, ['data'])['data'],
            'meta' => Arr::except($result, ['first_page_url', 'last_page_url', 'next_page_url', 'prev_page_url', 'data']),
            'query' => [
                'sort' => $request->sort(),
                'filter' => [
                    'term' => $term,
                    'facets' => $request->filter(),
                ],
            ],
            'facets' => [
                $assignedUserFacet,
                $todoCompletedFacet,
            ],
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
            ->with('message', __('Todo created.'));
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
            ->back()
            ->with('message', __('Todo updated.'));
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
                ? __('Todo marked as completed.')
                : __('Todo marked as incomplete.'));
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
            ->with('message', __('Todo deleted.'));
    }
}
