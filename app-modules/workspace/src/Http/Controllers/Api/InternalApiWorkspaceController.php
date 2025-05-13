<?php

declare(strict_types=1);

namespace Modules\Workspace\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Workspace\Models\Workspace;

class InternalApiWorkspaceController
{
    public function index(Request $request): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        return response()->json([
            'workspaces' => $user->allWorkspaces()
                ->map(function (Workspace $workspace) {
                    return [
                        'id' => $workspace->id,
                        'name' => $workspace->name,
                    ];
                })
                ->values(),
        ], JsonResponse::HTTP_OK, [
            'Cache-Control' => 'max-age=600, public, stale-while-revalidate=86400, stale-if-error=604800s',
        ]);
    }
}
