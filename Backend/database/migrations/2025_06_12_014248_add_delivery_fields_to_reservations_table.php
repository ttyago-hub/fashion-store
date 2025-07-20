<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
{
    Schema::table('reservations', function (Blueprint $table) {
        $table->enum('delivery_type', ['Retiro en tienda', 'Entrega a domicilio'])->after('quantity');
        $table->string('delivery_address')->nullable()->after('delivery_type');
        $table->string('delivery_code')->nullable()->after('delivery_address');
    });
}


    /**
     * Reverse the migrations.
     */
   public function down()
{
    Schema::table('reservations', function (Blueprint $table) {
        $table->dropColumn(['delivery_type', 'delivery_address', 'delivery_code']);
    });
}

};
