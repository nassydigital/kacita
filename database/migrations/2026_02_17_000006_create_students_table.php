<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->unique()->constrained('users')->nullOnDelete();
            $table->string('student_number')->unique()->nullable();
            $table->string('name');
            $table->date('date_of_birth')->nullable();
            $table->string('level');
            $table->foreignId('school_id')->nullable()->constrained('schools')->nullOnDelete();
            $table->string('guardian_name')->nullable();
            $table->string('guardian_phone')->nullable();
            $table->string('status')->default('pending');
            $table->softDeletes();
            $table->timestamps();

            $table->index('status');
            $table->index('level');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
