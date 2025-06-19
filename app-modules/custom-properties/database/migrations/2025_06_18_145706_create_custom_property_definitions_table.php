<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('custom_property_definitions', function (Blueprint $table): void {
            $table->id();

            $table->morphs('definable');

            $table->string('name')->comment('internal name of the property');
            $table->string('label')->comment('user-friendly name of the property');
            $table->string('type')->comment('e.g. text, number, date, boolean, select');

            $table->json('rules')->nullable()->comment('validation rules as array');
            $table->text('default_value')->nullable()->comment('default value of the property');
            $table->json('options')->nullable()->comment('options for necessary types like select');

            $table->unsignedInteger('sequence')->default(0)->comment('order of the property in the list');

            $table->timestamps();

            $table->unique(['definable_id', 'definable_type', 'name'], 'definable_name_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('custom_property_definitions');
    }
};
