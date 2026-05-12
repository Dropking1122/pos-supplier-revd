<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('sale_details', function (Blueprint $table) {
            $table->unsignedInteger('stock_before')->nullable()->after('quantity');
        });
    }
    public function down(): void {
        Schema::table('sale_details', function (Blueprint $table) {
            $table->dropColumn('stock_before');
        });
    }
};
