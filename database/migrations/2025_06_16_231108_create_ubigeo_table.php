<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('departamentos', function (Blueprint $table) {
            $table->string('codigo', 2)->primary();
            $table->string('nombre');
        });
        Schema::create('provincias', function (Blueprint $table) {
            $table->string('codigo', 4)->primary();
            $table->string('nombre');
            $table->string('departamento_codigo', 2);
            $table->foreign('departamento_codigo')->references('codigo')->on('departamentos');
        });
        Schema::create('distritos', function (Blueprint $table) {
            $table->string('codigo', 6)->primary();
            $table->string('nombre');
            $table->string('provincia_codigo', 4);
            $table->string('departamento_codigo', 2);
            $table->foreign('provincia_codigo')->references('codigo')->on('provincias');
            $table->foreign('departamento_codigo')->references('codigo')->on('departamentos');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('distritos');
        Schema::dropIfExists('provincias');
        Schema::dropIfExists('departamentos');
    }
};
