<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use App\Models\HiringContract;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;

class UserDashboardController extends Controller
{
    public function GetSection($status = null)
    {
        $userId = Auth::id();

        $query = HiringContract::where('indentor_do', $userId);

        if ($status === 'ongoing') {
            $query->whereNotNull('reqmt_recd_date_actual_date')
                ->whereNull('contract_end_date_actual_date');
        } elseif ($status === 'completed') {
            $query->whereNotNull('contract_end_date_actual_date');
        }
        // For 'all' or no status, we don't apply any additional filters

        $sections = $query->select('indenting_section as name', DB::raw('COUNT(*) as value'))
            ->groupBy('indenting_section')
            ->get();

        return response()->json($sections);
    }

    public function GetDeliverables($status = null)
    {
        $userId = Auth::id();

        $query = HiringContract::where('indentor_do', $userId);

        if ($status === 'ongoing') {
            $query->whereNotNull('reqmt_recd_date_actual_date')
                ->whereNull('contract_end_date_actual_date');
        } elseif ($status === 'completed') {
            $query->whereNotNull('contract_end_date_actual_date');
        }
        // For 'all' or no status, we don't apply any additional filters

        $deliverables = $query->select('deliverables as name', DB::raw('COUNT(*) as value'))
            ->groupBy('deliverables')
            ->get();

        return response()->json($deliverables);
    }
}
