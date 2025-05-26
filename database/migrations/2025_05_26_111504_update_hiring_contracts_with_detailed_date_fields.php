<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateHiringContractsWithDetailedDateFields extends Migration
{
    public function up()
    {
        Schema::table('hiring_contracts', function (Blueprint $table) {
            // Drop old single date fields
            $table->dropColumn([
                'reqmt_recd_date',
                'case_initiation_date',
                'aa_date',
                'sanction_date',
                'indent_date',
                'nit_date',
                'tbo_date',
                'pbo_date',
                'noa_po_date',
                'delivery_date',
                'contract_start_date',
                'contract_end_date',
            ]);

            // Add new grouped fields for each of the above
            $fields = [
                'reqmt_recd_date',
                'case_initiation_date',
                'aa_date',
                'sanction_date',
                'indent_date',
                'nit_date',
                'tbo_date',
                'pbo_date',
                'noa_po_date',
                'delivery_date',
                'contract_start_date',
                'contract_end_date',
            ];

            foreach ($fields as $field) {
                $table->date("{$field}_expected_date")->nullable();
                $table->date("{$field}_actual_date")->nullable();
                $table->text("{$field}_notes")->nullable();
            }
        });
    }

    public function down()
    {
        Schema::table('hiring_contracts', function (Blueprint $table) {
            // Drop the new expected/actual/notes fields
            $fields = [
                'reqmt_recd_date',
                'case_initiation_date',
                'aa_date',
                'sanction_date',
                'indent_date',
                'nit_date',
                'tbo_date',
                'pbo_date',
                'noa_po_date',
                'delivery_date',
                'contract_start_date',
                'contract_end_date',
            ];

            foreach ($fields as $field) {
                $table->dropColumn([
                    "{$field}_expected_date",
                    "{$field}_actual_date",
                    "{$field}_notes",
                ]);
            }

            // Re-add the original date fields
            $table->date('reqmt_recd_date')->nullable();
            $table->date('case_initiation_date')->nullable();
            $table->date('aa_date')->nullable();
            $table->date('sanction_date')->nullable();
            $table->date('indent_date')->nullable();
            $table->date('nit_date')->nullable();
            $table->date('tbo_date')->nullable();
            $table->date('pbo_date')->nullable();
            $table->date('noa_po_date')->nullable();
            $table->date('delivery_date')->nullable();
            $table->date('contract_start_date')->nullable();
            $table->date('contract_end_date')->nullable();
        });
    }
}
