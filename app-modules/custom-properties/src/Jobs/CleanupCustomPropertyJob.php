<?php

declare(strict_types=1);

namespace Modules\CustomProperties\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class CleanupCustomPropertyJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public readonly Model $definable,
        public readonly string $propertyName,
    ) {}

    public function handle(): void
    {
        $relation = $this->definable->getCustomizableItemsRelation();

        // Führt ein hocheffizientes Mass-Update direkt auf der Datenbank aus.
        // Der '->' Operator ist für MySQL JSON-Operationen.
        // Für PostgreSQL wäre es '->>'. Dies kann man ggf. abstrahieren.
        $relation->getQuery()->update([
            'custom_properties' => DB::raw("JSON_REMOVE(custom_properties, '$.\"{$this->propertyName}\"')"),
        ]);
    }
}
