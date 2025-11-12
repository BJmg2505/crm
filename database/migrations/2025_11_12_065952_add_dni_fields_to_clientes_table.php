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
            $table->string('dni_cliente', 8)->nullable()->after('razon_social');
            $table->string('nombre_cliente')->nullable()->after('dni_cliente');
            $table->string('apellido_paterno_cliente')->nullable()->after('nombre_cliente');
            $table->string('apellido_materno_cliente')->nullable()->after('apellido_paterno_cliente');
            $table->enum('tipo_documento', ['dni', 'ruc'])->nullable()->after('apellido_materno_cliente');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->dropColumn(['dni', 'nombre', 'apellido_paterno', 'apellido_materno', 'tipo_documento']);
        });
    }
};
