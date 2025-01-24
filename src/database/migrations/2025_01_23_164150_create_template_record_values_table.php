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
        Schema::create('template_record_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('template_id');
            $table->foreignId('template_field_id');
            $table->foreignId('template_record_id');

            $table->integer('integer_value')->nullable();
            $table->string('string_value')->nullable();
            $table->text('text_value')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('template_record_values');
    }
};
