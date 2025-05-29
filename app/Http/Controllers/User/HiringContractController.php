<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\HiringContract;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

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
            // 'rid' => 'nullable|string|max:255',
            'title' => 'required|string|max:255',
            'deliverables' => 'nullable|string',
            'indenting_section' => 'nullable|string|max:255',
            'indentor_do' => 'nullable|string|max:255',
            'value_inr' => 'nullable|numeric',

            // Each date field now has 3 parts
            'reqmt_recd_expected_date' => 'nullable|date',
            'reqmt_recd_actual_date' => 'nullable|date',
            'reqmt_recd_notes' => 'nullable|string',

            'case_initiation_expected_date' => 'nullable|date',
            'case_initiation_actual_date' => 'nullable|date',
            'case_initiation_notes' => 'nullable|string',

            'aa_expected_date' => 'nullable|date',
            'aa_actual_date' => 'nullable|date',
            'aa_notes' => 'nullable|string',

            'sanction_expected_date' => 'nullable|date',
            'sanction_actual_date' => 'nullable|date',
            'sanction_notes' => 'nullable|string',

            'indent_expected_date' => 'nullable|date',
            'indent_actual_date' => 'nullable|date',
            'indent_notes' => 'nullable|string',

            'nit_expected_date' => 'nullable|date',
            'nit_actual_date' => 'nullable|date',
            'nit_notes' => 'nullable|string',

            'tbo_expected_date' => 'nullable|date',
            'tbo_actual_date' => 'nullable|date',
            'tbo_notes' => 'nullable|string',

            'pbo_expected_date' => 'nullable|date',
            'pbo_actual_date' => 'nullable|date',
            'pbo_notes' => 'nullable|string',

            'noa_po_expected_date' => 'nullable|date',
            'noa_po_actual_date' => 'nullable|date',
            'noa_po_notes' => 'nullable|string',

            'delivery_expected_date' => 'nullable|date',
            'delivery_actual_date' => 'nullable|date',
            'delivery_notes' => 'nullable|string',

            'contract_start_expected_date' => 'nullable|date',
            'contract_start_actual_date' => 'nullable|date',
            'contract_start_notes' => 'nullable|string',

            'contract_end_expected_date' => 'nullable|date',
            'contract_end_actual_date' => 'nullable|date|after_or_equal:contract_start_actual_date',
            'contract_end_notes' => 'nullable|string',

            'vendor_type' => ['nullable', Rule::in(['OEM', 'Non-OEM'])],
            'tender_do' => 'nullable|string|max:255',
            'tender_type' => 'nullable|string|max:255',
            'tendering_section' => 'nullable|string|max:255',
            'post_contract' => 'nullable|string',
            'pr_no' => 'nullable|string|max:255',
            'method' => 'nullable|string|max:255',
            'contract_no' => 'nullable|string|max:255',
            'sanction_value_cr' => 'nullable|numeric',
            'percentage_above_below' => 'nullable|string|max:50',
            'contractor_name' => 'nullable|string|max:255',
            'physical_progress' => 'nullable|string|max:255',
            'addl_dealing_officer' => 'nullable|string|max:255',
            'status' => ['nullable', Rule::in(['active', 'closed', 'on_hold'])],
        ]);
        if (!isset($validated['status'])) {
            $validated['status'] = 'active';
        }
        $validated['rid'] = 'RID' . time() . rand(100, 999);

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

            'reqmt_recd_expected_date' => 'nullable|date',
            'reqmt_recd_actual_date' => 'nullable|date',
            'reqmt_recd_notes' => 'nullable|string',

            'case_initiation_expected_date' => 'nullable|date',
            'case_initiation_actual_date' => 'nullable|date',
            'case_initiation_notes' => 'nullable|string',

            'aa_expected_date' => 'nullable|date',
            'aa_actual_date' => 'nullable|date',
            'aa_notes' => 'nullable|string',

            'sanction_expected_date' => 'nullable|date',
            'sanction_actual_date' => 'nullable|date',
            'sanction_notes' => 'nullable|string',

            'indent_expected_date' => 'nullable|date',
            'indent_actual_date' => 'nullable|date',
            'indent_notes' => 'nullable|string',

            'nit_expected_date' => 'nullable|date',
            'nit_actual_date' => 'nullable|date',
            'nit_notes' => 'nullable|string',

            'tbo_expected_date' => 'nullable|date',
            'tbo_actual_date' => 'nullable|date',
            'tbo_notes' => 'nullable|string',

            'pbo_expected_date' => 'nullable|date',
            'pbo_actual_date' => 'nullable|date',
            'pbo_notes' => 'nullable|string',

            'noa_po_expected_date' => 'nullable|date',
            'noa_po_actual_date' => 'nullable|date',
            'noa_po_notes' => 'nullable|string',

            'delivery_expected_date' => 'nullable|date',
            'delivery_actual_date' => 'nullable|date',
            'delivery_notes' => 'nullable|string',

            'contract_start_expected_date' => 'nullable|date',
            'contract_start_actual_date' => 'nullable|date',
            'contract_start_notes' => 'nullable|string',

            'contract_end_expected_date' => 'nullable|date',
            'contract_end_actual_date' => 'nullable|date|after_or_equal:contract_start_actual_date',
            'contract_end_notes' => 'nullable|string',

            'vendor_type' => ['nullable', Rule::in(['OEM', 'Non-OEM'])],
            'tender_do' => 'nullable|string|max:255',
            'tender_type' => 'nullable|string|max:255',
            'tendering_section' => 'nullable|string|max:255',
            'post_contract' => 'nullable|string',
            'pr_no' => 'nullable|string|max:255',
            'method' => 'nullable|string|max:255',
            'contract_no' => 'nullable|string|max:255',
            'sanction_value_cr' => 'nullable|numeric',
            'percentage_above_below' => 'nullable|string|max:50',
            'contractor_name' => 'nullable|string|max:255',
            'physical_progress' => 'nullable|string|max:255',
            'addl_dealing_officer' => 'nullable|string|max:255',
            'status' => ['nullable', Rule::in(['active', 'closed', 'on_hold'])],
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


    public function getCurrentUserSection()
    {
        $userId = Auth::id();

        if (!$userId) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        // Using LEFT JOIN to get section data
        $section = User::where('users.id', $userId)
            ->leftJoin('sections', 'users.section_id', '=', 'sections.id')
            ->select('sections.id as section_id', 'sections.section_name')
            ->first();

        return response()->json([
            'success' => true,
            'section' => [
                'id' => $section->section_id ?? null,
                'name' => $section->section_name ?? null
            ]
        ]);
    }
}
