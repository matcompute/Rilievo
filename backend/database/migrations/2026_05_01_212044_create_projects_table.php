<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('client_name');
            $table->string('city');
            $table->string('status')->default('ACTIVE');
            $table->string('permit_status')->default('IN_REVIEW');
            $table->unsignedTinyInteger('progress_percent')->default(0);
            $table->decimal('budget_total', 12, 2)->default(0);
            $table->decimal('budget_spent', 12, 2)->default(0);
            $table->date('start_date');
            $table->date('target_date');
            $table->foreignId('manager_id')->constrained('users')->cascadeOnDelete();
            $table->text('summary')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
