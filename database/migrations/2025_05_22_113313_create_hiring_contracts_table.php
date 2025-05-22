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
        Schema::create('hiring_contracts', function (Blueprint $table) {
             $table->id();
            $table->string('rid')->nullable();
            $table->string('title');
            $table->text('deliverables')->nullable();
            $table->string('indenting_section')->nullable();
            $table->string('indentor_do')->nullable();
            $table->string('value_inr')->nullable();
            $table->date('reqmt_recd_date')->nullable();
            $table->date('case_initiation_date')->nullable();
            $table->date('aa_date')->nullable();
            $table->date('sanction_date')->nullable();
            $table->date('indent_date')->nullable();
            $table->string('vendor_type')->nullable(); // OEM / Non-OEM
            $table->string('tender_do')->nullable();
            $table->string('tender_type')->nullable();
            $table->string('tendering_section')->nullable();
            $table->date('nit_date')->nullable();
            $table->date('tbo_date')->nullable();
            $table->date('pbo_date')->nullable();
            $table->date('noa_po_date')->nullable();
            $table->date('delivery_date')->nullable();
            $table->text('post_contract')->nullable();
            $table->string('pr_no')->nullable();
            $table->string('method')->nullable();
            $table->string('contract_no')->nullable();
            $table->string('sanction_value_cr')->nullable();
            $table->string('percentage_above_below')->nullable();
            $table->date('contract_start_date')->nullable();
            $table->date('contract_end_date')->nullable();
            $table->string('contractor_name')->nullable();
            $table->string('physical_progress')->nullable();
            $table->string('addl_dealing_officer')->nullable();
            $table->enum('status', ['active', 'closed', 'on_hold'])->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hiring_contracts');
    }
};
