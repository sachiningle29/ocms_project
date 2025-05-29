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
            $table->tinyInteger('current_status')->after('status')->default(1);
            $table->date('reqmt_recd_norm_date')->after('reqmt_recd_date_actual_date')->nullable();
            $table->date('case_initiation_norm_date')->after('case_initiation_date_actual_date')->nullable();
            $table->date('aa_norm_date')->after('aa_date_actual_date')->nullable();
            $table->date('sanction_norm_date')->after('sanction_date_actual_date')->nullable();
            $table->date('indent_norm_date')->after('indent_date_actual_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hiring_contracts', function (Blueprint $table) {
            $table->dropColumn(['current_status','reqmt_recd_norm_date', 'case_initiation_norm_date','aa_norm_date', 'sanction_norm_date','indent_norm_date']);
        });
    }
};
