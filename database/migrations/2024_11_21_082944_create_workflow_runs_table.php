<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('workflow_runs', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('remote_id')->unique();
            $table->string('repository', 255)->index();
            $table->string('name', 255)->index();
            $table->string('status', 25)->index();
            $table->string('conclusion', 25)->nullable()->index();
            $table->json('data');
            $table->json('jobs')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workflow_runs');
    }
};
