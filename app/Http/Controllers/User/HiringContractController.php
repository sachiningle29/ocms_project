<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\HiringContract;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class HiringContractController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(HiringContract::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'rid' => 'nullable|string|max:255',
            'title' => 'required|string|max:255',
            'deliverables' => 'nullable|string',
            'indenting_section' => 'nullable|string|max:255',
            'indentor_do' => 'nullable|string|max:255',
            'value_inr' => 'nullable|numeric',
            'reqmt_recd_date' => 'nullable|date',
            'case_initiation_date' => 'nullable|date',
            'aa_date' => 'nullable|date',
            'sanction_date' => 'nullable|date',
            'indent_date' => 'nullable|date',
            'vendor_type' => ['nullable', Rule::in(['OEM', 'Non-OEM'])],
            'tender_do' => 'nullable|string|max:255',
            'tender_type' => 'nullable|string|max:255',
            'tendering_section' => 'nullable|string|max:255',
            'nit_date' => 'nullable|date',
            'tbo_date' => 'nullable|date',
            'pbo_date' => 'nullable|date',
            'noa_po_date' => 'nullable|date',
            'delivery_date' => 'nullable|date',
            'post_contract' => 'nullable|string',
            'pr_no' => 'nullable|string|max:255',
            'method' => 'nullable|string|max:255',
            'contract_no' => 'nullable|string|max:255',
            'sanction_value_cr' => 'nullable|numeric',
            'percentage_above_below' => 'nullable|string|max:50',
            'contract_start_date' => 'nullable|date',
            'contract_end_date' => 'nullable|date|after_or_equal:contract_start_date',
            'contractor_name' => 'nullable|string|max:255',
            'physical_progress' => 'nullable|string|max:255',
            'addl_dealing_officer' => 'nullable|string|max:255',
            'status' => ['required', Rule::in(['active', 'closed', 'on_hold'])],
        ]);

        $contract = HiringContract::create($validated);

        return response()->json($contract, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $contract = HiringContract::findOrFail($id);
        return response()->json($contract);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'rid' => 'nullable|string|max:255',
            'title' => 'required|string|max:255',
            'deliverables' => 'nullable|string',
            'indenting_section' => 'nullable|string|max:255',
            'indentor_do' => 'nullable|string|max:255',
            'value_inr' => 'nullable|numeric',
            'reqmt_recd_date' => 'nullable|date',
            'case_initiation_date' => 'nullable|date',
            'aa_date' => 'nullable|date',
            'sanction_date' => 'nullable|date',
            'indent_date' => 'nullable|date',
            'vendor_type' => ['nullable', Rule::in(['OEM', 'Non-OEM'])],
            'tender_do' => 'nullable|string|max:255',
            'tender_type' => 'nullable|string|max:255',
            'tendering_section' => 'nullable|string|max:255',
            'nit_date' => 'nullable|date',
            'tbo_date' => 'nullable|date',
            'pbo_date' => 'nullable|date',
            'noa_po_date' => 'nullable|date',
            'delivery_date' => 'nullable|date',
            'post_contract' => 'nullable|string',
            'pr_no' => 'nullable|string|max:255',
            'method' => 'nullable|string|max:255',
            'contract_no' => 'nullable|string|max:255',
            'sanction_value_cr' => 'nullable|numeric',
            'percentage_above_below' => 'nullable|string|max:50',
            'contract_start_date' => 'nullable|date',
            'contract_end_date' => 'nullable|date|after_or_equal:contract_start_date',
            'contractor_name' => 'nullable|string|max:255',
            'physical_progress' => 'nullable|string|max:255',
            'addl_dealing_officer' => 'nullable|string|max:255',
            'status' => ['required', Rule::in(['active', 'closed', 'on_hold'])],
        ]);

        $contract = HiringContract::findOrFail($id);
        $contract->update($validated);

        return response()->json($contract);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $contract = HiringContract::findOrFail($id);
        $contract->delete();

        return response()->json(['message' => 'Deleted successfully']);
    }
}
