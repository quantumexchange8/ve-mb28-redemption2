<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('setting_licenses', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('shortform')->nullable();
            $table->string('slug')->nullable();
            $table->string('category')->nullable();
            $table->string('type')->nullable();
            $table->string('url')->nullable();
            $table->integer('valid_year')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('setting_licenses');
    }
};
