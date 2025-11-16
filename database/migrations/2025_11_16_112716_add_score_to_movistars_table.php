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
            $table->decimal('score', 10, 2)->default(0)->after('cargo_fijo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('movistars', function (Blueprint $table) {
            $table->dropColumn(['score']);
        });
    }
};
