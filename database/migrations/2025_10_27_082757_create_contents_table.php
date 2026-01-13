<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('contents', function (Blueprint $table) {
            $table->id();
            $table->string('name')->collation('utf8mb4_unicode_ci');
            $table->string('description')->nullable();
            $table->foreignId('content_type_id')->constrained();
            $table->boolean('is_common')->default(0);
            $table->foreignId('user_id')->nullable()->comment('Created by')->constrained();
            $table->timestamps();

            $table->unique(['name', 'content_type_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contents');
    }
};
