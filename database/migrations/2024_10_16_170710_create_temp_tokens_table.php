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
    Schema::table('temp_tokens', function (Blueprint $table) {
        $table->text('token')->change(); // Cambia el tipo de columna a TEXT
    });
}

public function down()
{
    Schema::table('temp_tokens', function (Blueprint $table) {
        $table->string('token', 255)->change(); // Revertir el cambio si es necesario
    });
}
};
