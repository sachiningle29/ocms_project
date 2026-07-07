<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HiringContract extends Model
{
    protected $fillable = [
        // Case Information Fields
        'rid',
        'title',
        'deliverables',
        'indenting_section',
        'indentor_sub_section',
        'indentor_do',
        'value_inr',
        'tendering_section',

        // Indenting Timeline Fields
        'reqmt_recd_date_expected_date',
        'reqmt_recd_date_actual_date',
        'reqmt_recd_norm_date',
        'reqmt_recd_date_notes',
        'reqmt_recd_date_deviation',
        'reqmt_recd_date_deviation_days',

        'case_initiation_date_expected_date',
        'case_initiation_date_actual_date',
        'case_initiation_norm_date',
        'case_initiation_date_notes',
        'case_initiation_date_deviation',
        'case_initiation_date_deviation_days',

        'aa_date_expected_date',
        'aa_date_actual_date',
        'aa_norm_date',
        'aa_date_notes',
        'aa_date_deviation',
        'aa_date_deviation_days',

        'sanction_date_expected_date',
        'sanction_date_actual_date',
        'sanction_norm_date',
        'sanction_date_notes',
        'sanction_date_deviation',
        'sanction_date_deviation_days',

        'indent_date_expected_date',
        'indent_date_actual_date',
        'indent_norm_date',
        'indent_date_notes',
        'indent_date_deviation',
        'indent_date_deviation_days',

        // Tendering Timeline Fields
        'nit_date_expected_date',
        'nit_date_actual_date',
        'nit_date_norm_date',
        'nit_date_notes',
        'nit_date_deviation',
        'nit_date_deviation_days',

        'tbo_date_expected_date',
        'tbo_date_actual_date',
        'tbo_date_norm_date',
        'tbo_date_notes',
        'tbo_date_deviation',
        'tbo_date_deviation_days',

        'pbo_date_expected_date',
        'pbo_date_actual_date',
        'pbo_date_norm_date',
        'pbo_date_notes',
        'pbo_date_deviation',
        'pbo_date_deviation_days',

        'noa_po_date_expected_date',
        'noa_po_date_actual_date',
        'noa_po_date_norm_date',
        'noa_po_date_notes',
        'noa_po_date_deviation',
        'noa_po_date_deviation_days',

        'delivery_date_expected_date',
        'delivery_date_actual_date',
        'delivery_date_norm_date',
        'delivery_date_notes',
        'delivery_date_deviation',
        'delivery_date_deviation_days',

        'contract_start_date_expected_date',
        'contract_start_date_actual_date',
        'contract_start_date_norm_date',
        'contract_start_date_notes',
        'contract_start_date_deviation',
        'contract_start_date_deviation_days',

        'contract_end_date_expected_date',
        'contract_end_date_actual_date',
        'contract_end_date_norm_date',
        'contract_end_date_notes',
        'contract_end_date_deviation',
        'contract_end_date_deviation_days',

        // Tendering Form Fields
        'vendor_type',
        'tender_do',
        'tendering_platform',
        'tender_type',
        'vendor_code',

        // Post Contract Fields
        'post_contract',
        'pr_no',
        'method',
        'contract_no',
        'sanction_value_cr',
        'percentage_above_below',
        'contractor_name',
        'physical_progress',
        'addl_dealing_officer',
        'status',
        'current_status'
    ];
}