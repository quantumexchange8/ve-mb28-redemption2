<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('redemption_code_request_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('redemption_code_request_id');
            $table->unsignedBigInteger('setting_license_id');
            $table->timestamps();
        
            $table->foreign('redemption_code_request_id')->references('id')->on('redemption_code_requests')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('setting_license_id')->references('id')->on('setting_licenses')->onUpdate('cascade')->onDelete('cascade');
        });
            }

    public function down(): void {
        Schema::dropIfExists('redemption_code_request_items');
    }
};
