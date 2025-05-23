<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('workspaces', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->index()->comment('The user that owns the team.');
            $table->string('name');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workspaces');
    }
};
