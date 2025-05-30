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
            $table->date('tbo_date_norm_date')->nullable()->after('tbo_date_expected_date');
            $table->date('pbo_date_norm_date')->nullable()->after('pbo_date_expected_date');
            $table->date('noa_po_date_norm_date')->nullable()->after('noa_po_date_expected_date');
            $table->date('delivery_date_norm_date')->nullable()->after('delivery_date_expected_date');
            $table->date('contract_start_date_norm_date')->nullable()->after('contract_start_date_expected_date');
            $table->date('contract_end_date_norm_date')->nullable()->after('contract_end_date_expected_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hiring_contracts', function (Blueprint $table) {
             $table->dropColumn([
                'tbo_date_norm_date',
                'pbo_date_norm_date',
                'noa_po_date_norm_date',
                'delivery_date_norm_date',
                'contract_start_date_norm_date',
                'contract_end_date_norm_date',
            ]);
        });
    }
};
