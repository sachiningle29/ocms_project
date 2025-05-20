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
        Schema::create('running_contracts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('section');
            $table->string('case_type');
            $table->string('contract_type');
            $table->string('pr_no');
            $table->string('dealing_officer');
            $table->decimal('value_usd', 15, 2)->nullable();
            $table->decimal('value_inr', 20, 2)->nullable();
            $table->date('contract_start_date')->nullable();
            $table->date('contract_end_date')->nullable();
            $table->decimal('funds_utilised', 20, 2)->nullable();
            $table->text('remarks')->nullable();
            $table->string('trigger')->nullable();
            $table->date('original_date_of_delivery')->nullable();
            $table->integer('no_of_extensions')->default(0);
            $table->date('extended_po_lc_last_date_of_shipment')->nullable();
            $table->string('ec_and_sims_status')->nullable();
            $table->text('post_contract_issues_in_brief')->nullable();
            $table->string('current_status')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('running_contracts');
    }
};
