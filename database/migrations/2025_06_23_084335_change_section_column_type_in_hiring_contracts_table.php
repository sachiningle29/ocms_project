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
        Schema::table('hiring_contracts', function (Blueprint $table) {
            // First, drop the old string column
            $table->dropColumn('indenting_section');
        });

        Schema::table('hiring_contracts', function (Blueprint $table) {
            // Then re-add it as integer
            $table->integer('indenting_section')->before('indentor_do')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hiring_contracts', function (Blueprint $table) {
            // Drop the integer column
            $table->dropColumn('indenting_section');
        });

        Schema::table('hiring_contracts', function (Blueprint $table) {
            // Re-add it as string
            $table->string('indenting_section')->before('indentor_do')->nullable();
        });
    }
};
