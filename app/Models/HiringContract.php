<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HiringContract extends Model
{
    protected $fillable = [
        'rid',
        'title',
        'deliverables',
        'indenting_section',
        'indentor_do',
        'value_inr',

        'reqmt_recd_expected_date',
        'reqmt_recd_actual_date',
        'reqmt_recd_norm_date',
        'reqmt_recd_notes',

        'case_initiation_expected_date',
        'case_initiation_actual_date',
        'case_initiation_norm_date',
        'case_initiation_notes',

        'aa_expected_date',
        'aa_actual_date',
        'aa_norm_date',
        'aa_notes',

        'sanction_expected_date',
        'sanction_actual_date',
        'sanction_norm_date',
        'sanction_notes',

        'indent_expected_date',
        'indent_actual_date',
        'indent_norm_date',
        'indent_notes',

        'nit_expected_date',
        'nit_actual_date',
        'nit_notes',

        'tbo_expected_date',
        'tbo_actual_date',
        'tbo_notes',

        'pbo_expected_date',
        'pbo_actual_date',
        'pbo_notes',

        'noa_po_expected_date',
        'noa_po_actual_date',
        'noa_po_notes',

        'delivery_expected_date',
        'delivery_actual_date',
        'delivery_notes',

        'contract_start_expected_date',
        'contract_start_actual_date',
        'contract_start_notes',

        'contract_end_expected_date',
        'contract_end_actual_date',
        'contract_end_notes',

        'vendor_type',
        'tender_do',
        'tender_type',
        'tendering_section',
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
