<?php

declare(strict_types=1);

namespace Modules\Workspace\Actions;

use App\ValueObjects\Id;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\Workspace\Models\Workspace;

class CreateWorkspace
{
    public function handle(Id $ownerId, string $name): Id
    {
        $workspace = DB::transaction(function () use ($ownerId, $name): Workspace {
            $workspace = new Workspace([
                'user_id' => $ownerId->value(),
                'name' => Str::trim($name),
            ]);

            $workspace->save();

            return $workspace;
        });

        return new Id($workspace->id);
    }
}
