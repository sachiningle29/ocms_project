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
            $table->text('reqmt_recd_date_deviation')->nullable()->after('reqmt_recd_date_notes');

            $table->text('case_initiation_date_deviation')->nullable()->after('case_initiation_date_notes');

            $table->text('aa_date_deviation')->nullable()->after('aa_date_notes');

            $table->text('sanction_date_deviation')->nullable()->after('sanction_date_notes');

            $table->text('indent_date_deviation')->nullable()->after('indent_date_notes');

            $table->text('nit_date_deviation')->nullable()->after('nit_date_notes');

            // Tendering Section Deviation Fields
            $table->text('tbo_date_deviation')->nullable()->after('tbo_date_notes');

            $table->text('pbo_date_deviation')->nullable()->after('pbo_date_notes');

            $table->text('noa_po_date_deviation')->nullable()->after('noa_po_date_notes');

            $table->text('delivery_date_deviation')->nullable()->after('delivery_date_notes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hiring_contracts', function (Blueprint $table) {
            $table->dropColumn('reqmt_recd_date_deviation');
            $table->dropColumn('case_initiation_date_deviation');
            $table->dropColumn('aa_date_deviation');
            $table->dropColumn('internal_meeting_date_deviation');
            $table->dropColumn('approval_note_date_deviation');
            $table->dropColumn('indenting_completion_date_deviation');

            // Drop Tendering Deviation Fields
            $table->dropColumn('nit_date_deviation');
            $table->dropColumn('tbo_date_deviation');
            $table->dropColumn('pbo_date_deviation');
            $table->dropColumn('noa_po_date_deviation');
            $table->dropColumn('delivery_date_deviation');
        });
    }
};
