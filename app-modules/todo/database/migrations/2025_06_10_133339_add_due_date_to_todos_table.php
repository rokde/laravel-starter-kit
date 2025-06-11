<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('todos', function (Blueprint $table): void {
            $table->dateTime('due_date')->after('completed')->nullable()->default(null);
        });
    }

    public function down(): void
    {
        Schema::table('todos', function (Blueprint $table): void {
            $table->removeColumn('due_date');
        });
    }
};
