<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('kode_barang')->unique();
            $table->string('nama_barang');
            $table->string('jenis_barang')->nullable();
            $table->integer('kuantitas')->default(0);
            $table->decimal('modal_awal', 12, 2)->default(0);
            $table->decimal('harga_grosir', 12, 2)->default(0);
            $table->decimal('harga_ecer', 12, 2)->default(0);
            $table->string('harga_satuan')->nullable();
            $table->integer('stock_minimum')->default(5);
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('products'); }
};
