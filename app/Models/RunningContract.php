<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RunningContract extends Model
{
    protected $fillable = [
        'title',
        'section',
        'case_type',
        'contract_type',
        'pr_no',
        'dealing_officer',
        'value_usd',
        'value_inr',
        'contract_start_date',
        'contract_end_date',
        'funds_utilised',
        'remarks',
        'trigger',
        'original_date_of_delivery',
        'no_of_extensions',
        'extended_po_lc_last_date_of_shipment',
        'ec_and_sims_status',
        'post_contract_issues_in_brief',
        'current_status',
    ];
}
