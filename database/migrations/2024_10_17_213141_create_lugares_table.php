<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLugaresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lugares', function (Blueprint $table) {
            $table->id(); // Campo 'id' autoincremental
            $table->string('nombre'); // Campo 'nombre' como string
            $table->string('direccion'); // Campo 'direccion' como string
            $table->decimal('latitude', 10, 7); // Campo 'latitude' como decimal (10 dígitos, 7 decimales)
            $table->decimal('longitude', 10, 7); // Campo 'longitude' como decimal (10 dígitos, 7 decimales)
            $table->timestamps(); // Campos 'created_at' y 'updated_at'
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lugares');
    }
}