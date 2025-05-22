<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('todos', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('workspace_id')->index()->comment('The workspace that the todo belongs to.');
            $table->foreignId('user_id')->index()->comment('The user that is assigned to the todo.');
            $table->string('title');
            $table->boolean('completed')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('todos');
    }
};
