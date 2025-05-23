<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\RunningContract;
use Illuminate\Http\Request;

class RunningContractController extends Controller
{
    public function index()
    {
        return response()->json(RunningContract::all());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'section' => 'required|string|max:255',
            'case_type' => 'required|string|max:255',
            'contract_type' => 'required|string|max:255',
            'pr_no' => 'required|string|max:255',
            'dealing_officer' => 'required|string|max:255',
            'value_usd' => 'nullable|numeric',
            'value_inr' => 'nullable|numeric',
            'contract_start_date' => 'nullable|date',
            'contract_end_date' => 'nullable|date',
            'funds_utilised' => 'nullable|numeric',
            'remarks' => 'nullable|string',
            'trigger' => 'nullable|string|max:255',
            'original_date_of_delivery' => 'nullable|date',
            'no_of_extensions' => 'nullable|integer',
            'extended_po_lc_last_date_of_shipment' => 'nullable|date',
            'ec_and_sims_status' => 'nullable|string|max:255',
            'post_contract_issues_in_brief' => 'nullable|string',
            'current_status' => 'nullable|string|max:255',
        ]);

        $contract = RunningContract::create($validated);
      
        return response()->json($contract, 201);
    }

    public function show($id)
    {
        $contract = RunningContract::findOrFail($id);
        return response()->json($contract);
    }

    public function update(Request $request, $id)
    {
        $contract = RunningContract::findOrFail($id);
        $contract->update($request->all());
        return response()->json($contract);
    }

    public function destroy($id)
    {
        $contract = RunningContract::findOrFail($id);
        $contract->delete();
        return response()->json(['message' => 'Deleted successfully']);
    }
}
