<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('site_diary_entries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id');
            $table->foreignId('author_id')->constrained('users')->cascadeOnDelete();
            $table->date('entry_date');
            $table->string('weather');
            $table->unsignedInteger('workforce_count')->default(0);
            $table->text('completed_work');
            $table->text('blockers')->nullable();
            $table->text('safety_note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('site_diary_entries');
    }
};
