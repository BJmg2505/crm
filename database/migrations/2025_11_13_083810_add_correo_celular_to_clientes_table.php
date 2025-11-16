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
        Schema::table('clientes', function (Blueprint $table) {
            $table->dropForeign(['distrito_codigo']);
            $table->dropForeign(['provincia_codigo']);
            $table->dropForeign(['departamento_codigo']);
        });

        Schema::table('clientes', function (Blueprint $table) {
            $table->string('distrito_codigo')->nullable()->change();
            $table->string('provincia_codigo')->nullable()->change();
            $table->string('departamento_codigo')->nullable()->change();
        });

        Schema::table('clientes', function (Blueprint $table) {
            $table->string('correo_cliente')->nullable()->after('apellido_materno_cliente');
            $table->json('celular_cliente')->nullable()->after('correo_cliente');
        });

        Schema::table('clientes', function (Blueprint $table) {
            $table->foreign('departamento_codigo')
                ->references('codigo')->on('departamentos')
                ->onDelete('set null');

            $table->foreign('provincia_codigo')
                ->references('codigo')->on('provincias')
                ->onDelete('set null');

            $table->foreign('distrito_codigo')
                ->references('codigo')->on('distritos')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->dropForeign(['distrito_codigo']);
            $table->dropForeign(['provincia_codigo']);
            $table->dropForeign(['departamento_codigo']);
        });

        Schema::table('clientes', function (Blueprint $table) {
            $table->unsignedBigInteger('distrito_codigo')->nullable(false)->change();
            $table->unsignedBigInteger('provincia_codigo')->nullable(false)->change();
            $table->unsignedBigInteger('departamento_codigo')->nullable(false)->change();
        });

        Schema::table('clientes', function (Blueprint $table) {
            $table->dropColumn(['correo_cliente', 'celular_cliente']);
        });

        Schema::table('clientes', function (Blueprint $table) {
            $table->foreign('departamento_codigo')
                ->references('codigo')->on('departamentos');

            $table->foreign('provincia_codigo')
                ->references('codigo')->on('provincias');

            $table->foreign('distrito_codigo')
                ->references('codigo')->on('distritos');
        });
    }
};
