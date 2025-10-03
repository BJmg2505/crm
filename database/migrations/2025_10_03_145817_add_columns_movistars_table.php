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
        Schema::table('movistars', function (Blueprint $table) {
            $table->decimal('cantidad_lineas', 10, 2)->default(0)->after('linea_movistar');
            $table->decimal('cargo_fijo', 10, 2)->default(0)->after('cantidad_lineas');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('movistars', function (Blueprint $table) {
            $table->dropColumn('cantidad_lineas');
            $table->dropColumn('cargo_fijo');
        });
    }
};
