<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateReservationsStatusColumn extends Migration
{
    public function up()
    {
        Schema::table('reservations', function (Blueprint $table) {
            // Cambiar la columna status para que acepte los valores correctos
            $table->enum('status', ['apartado', 'pending', 'confirmed', 'completed', 'cancelled'])
                  ->default('apartado')
                  ->change();
        });
    }

    public function down()
    {
        Schema::table('reservations', function (Blueprint $table) {
            // Revertir si es necesario
            $table->string('status')->change();
        });
    }
}