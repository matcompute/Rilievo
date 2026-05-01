<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('issues', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id');
            $table->foreignId('raised_by_id')->constrained('users')->cascadeOnDelete();
            $table->string('category');
            $table->string('priority')->default('MEDIUM');
            $table->string('status')->default('OPEN');
            $table->string('title');
            $table->text('description');
            $table->string('assignee_name')->nullable();
            $table->date('due_date')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('issues');
    }
};
