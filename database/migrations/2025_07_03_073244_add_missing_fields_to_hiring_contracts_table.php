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
            $table->integer('reqmt_recd_date_deviation_days')->nullable()->before('reqmt_recd_date_notes');
            $table->integer('case_initiation_date_deviation_days')->nullable()->before('case_initiation_date_notes');
            $table->integer('aa_date_deviation_days')->nullable()->before('aa_date_notes');
            $table->integer('sanction_date_deviation_days')->nullable()->before('sanction_date_notes');
            $table->integer('indent_date_deviation_days')->nullable()->before('indent_date_notes');
            $table->integer('nit_date_deviation_days')->nullable()->before('nit_date_notes');
            $table->integer('tbo_date_deviation_days')->nullable()->before('tbo_date_notes');
            $table->integer('pbo_date_deviation_days')->nullable()->before('pbo_date_notes');
            $table->integer('noa_po_date_deviation_days')->nullable()->before('noa_po_date_notes');
            $table->integer('delivery_date_deviation_days')->nullable()->before('delivery_date_notes');
            $table->integer('contract_start_date_deviation_days')->nullable()->before('contract_start_date_notes');
            $table->integer('contract_end_date_deviation_days')->nullable()->before('contract_end_date_notes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hiring_contracts', function (Blueprint $table) {
            $table->dropColumn([
                'reqmt_recd_date_deviation_days',
                'case_initiation_date_deviation_days',
                'aa_date_deviation_days',
                'sanction_date_deviation_days',
                'indent_date_deviation_days',
                'nit_date_deviation_days',
                'tbo_date_deviation_days',
                'pbo_date_deviation_days',
                'noa_po_date_deviation_days',
                'delivery_date_deviation_days',
                'contract_start_date_deviation_days',
                'contract_end_date_deviation_days',
                'tendering_section'
            ]);
        });
    }
};
