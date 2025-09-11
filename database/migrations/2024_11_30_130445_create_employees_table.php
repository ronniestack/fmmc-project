<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\User;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class);
            $table->longText('bio')->nullable();
            $table->json('days')->nullable();
            $table->unsignedInteger('slot_duration')->nullable();
            $table->unsignedInteger('break_duration')->nullable();
            $table->json('social')->nullable();
            $table->json('other')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
