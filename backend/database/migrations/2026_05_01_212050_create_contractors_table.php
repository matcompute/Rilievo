<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contractors', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id')->nullable();
            $table->string('name');
            $table->string('trade');
            $table->string('contact_name');
            $table->string('contact_email');
            $table->string('compliance_status')->default('PENDING');
            $table->date('insurance_expires_on')->nullable();
            $table->unsignedInteger('worker_count')->default(0);
            $table->date('last_audit_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contractors');
    }
};
