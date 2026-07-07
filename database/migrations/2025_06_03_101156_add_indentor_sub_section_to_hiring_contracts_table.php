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
            $table->integer('indentor_sub_section')->nullable()->after('indenting_section');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hiring_contracts', function (Blueprint $table) {
            $table->dropColumn('indentor_sub_section');
        });
    }
};
