<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('version_controls', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('setting_license_id')->nullable();
            $table->string('version')->nullable();
            $table->text('remarks')->nullable();
            $table->string('download_url')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('setting_license_id')->references('id')->on('setting_licenses')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('version_controls');
    }
};
