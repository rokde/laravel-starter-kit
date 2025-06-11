<?php

declare(strict_types=1);

namespace Modules\Todo\Http\Controllers;

use App\Data\Facets\FacetCollection;
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
use Modules\Todo\Data\Facets\TodoDueDateFacet;
use Modules\Todo\Http\Requests\StoreTodoRequest;
use Modules\Todo\Http\Requests\UpdateTodoRequest;
use Modules\Todo\Models\Todo;
use Modules\Todo\Notifications\TodoAssignedNotification;

class TodoController
{
    /**
     * Display a listing of the todos for the current workspace.
     */
    public function index(PaginationRequest $request): Response
    {
        $workspace = $request->user()->currentWorkspace;
        abort_if($workspace === null, 404);

        $facets = FacetCollection::make()
            ->add(new TodoCompletedFacet(__('Completed')))
            ->add(new AssignedUserFacet(__('Assigned to'), 'user_id')
                ->setPossibleUsers($workspace->allUsers()))
            ->add(new TodoDueDateFacet(__('Due date')));

        /** @var Builder $query */
        $query = Todo::query()
            ->currentWorkspace()
            ->select(['id', 'title', 'completed', 'due_date', 'user_id'])
            ->with('user:id,name,email');

        $request->defaultSort('due_date', SortDirection::DESC);
        foreach ($request->sort() as $sort) {
            $query->orderBy($sort['field'], $sort['direction']->value);
        }

        $term = $request->term();
        $query->when($term, function (Builder $query, string $term): void {
            $query->where('title', 'LIKE', "%{$term}%");
        });

        $facets->filterQuery($request->facets(), $query);

        $result = $query->paginate(
            perPage: $request->size(50),
            columns: $request->fields(),
            pageName: $request->pageName(),
            page: $request->page(),
        )->toArray();

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
            'facets' => $facets->get()->values(),
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
        $todo->due_date = $request->validated('due_date');
        $todo->save();

        if ($todo->user_id !== $request->user()->id) {
            $todo->user->notify(new TodoAssignedNotification(
                todo: $todo,
                user: $request->user()->toDto(),
            ));
        }

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
            ->back()
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
            ->back()
            ->with('message', __('Todo deleted.'));
    }
}
