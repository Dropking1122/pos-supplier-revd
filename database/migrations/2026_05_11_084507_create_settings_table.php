<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('company_name')->default('Toko Saya');
            $table->string('company_logo')->nullable();
            $table->text('company_address')->nullable();
            $table->string('company_phone')->nullable();
            $table->text('invoice_footer')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('settings'); }
};
