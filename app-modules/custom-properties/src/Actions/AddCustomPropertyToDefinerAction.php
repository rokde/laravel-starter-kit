<?php

declare(strict_types=1);

namespace Modules\CustomProperties\Actions;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Modules\CustomProperties\DataTransferObjects\CustomProperty;

class AddCustomPropertyToDefinerAction
{
    public function handle(Model $definer, CustomProperty $customProperty): void
    {
        DB::transaction(function () use ($customProperty, $definer): void {
            $definer->customPropertyDefinitions()->create($customProperty->toArray());
        });
    }
}
