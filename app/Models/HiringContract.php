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
        'reqmt_recd_date',
        'case_initiation_date',
        'aa_date',
        'sanction_date',
        'indent_date',
        'vendor_type',
        'tender_do',
        'tender_type',
        'tendering_section',
        'nit_date',
        'tbo_date',
        'pbo_date',
        'noa_po_date',
        'delivery_date',
        'post_contract',
        'pr_no',
        'method',
        'contract_no',
        'sanction_value_cr',
        'percentage_above_below',
        'contract_start_date',
        'contract_end_date',
        'contractor_name',
        'physical_progress',
        'addl_dealing_officer',
        'status'
    ];
}
