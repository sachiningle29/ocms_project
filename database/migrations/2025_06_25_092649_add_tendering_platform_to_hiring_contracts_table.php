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
            $table->unsignedTinyInteger('tendering_platform')
                ->nullable()
                ->comment('1: GeM, 2: GePNIC, 3: eTender')
                ->after('tender_do');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hiring_contracts', function (Blueprint $table) {
            $table->dropColumn('tendering_platform');
        });
    }
};
