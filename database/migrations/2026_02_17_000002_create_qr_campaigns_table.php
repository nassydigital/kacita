<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('qr_campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('market_location');
            $table->string('code')->unique();
            $table->unsignedInteger('registrations_count')->default(0);
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('qr_campaigns');
    }
};
