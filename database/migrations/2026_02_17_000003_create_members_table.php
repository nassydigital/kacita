<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->string('member_number')->unique();
            $table->string('region')->nullable();
            $table->string('market')->nullable();
            $table->string('id_type')->nullable();
            $table->string('id_number')->nullable();
            $table->string('photo')->nullable();
            $table->string('registration_source')->default('web');
            $table->foreignId('qr_campaign_id')->nullable()->constrained('qr_campaigns')->nullOnDelete();
            $table->string('status')->default('pending');
            $table->timestamp('joined_at')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index('status');
            $table->index(['region', 'market']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('members');
    }
};
