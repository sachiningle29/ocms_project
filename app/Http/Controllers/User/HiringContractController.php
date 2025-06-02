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
        $contracts = HiringContract::orderBy('created_at', 'desc')->get();
        return response()->json($contracts);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $status = $request->input('current_status');

        // Base validation rules
        $rules = [
            'current_status' => 'nullable|integer',

            'title' => 'nullable|string|max:255',
            'deliverables' => 'nullable|string',
            'indenting_section' => 'nullable|string|max:255',
            'indentor_do' => 'nullable|string|max:255',
            'value_inr' => 'nullable|numeric',

            // Indenting section norm date fields
            'reqmt_recd_date_expected_date' => 'nullable|date',
            'reqmt_recd_date_actual_date' => 'nullable|date',
            'reqmt_recd_norm_date' => 'nullable|date',
            'reqmt_recd_date_notes' => 'nullable|string',

            'case_initiation_date_expected_date' => 'nullable|date',
            'case_initiation_date_actual_date' => 'nullable|date',
            'case_initiation_norm_date' => 'nullable|date',
            'case_initiation_date_notes' => 'nullable|string',

            'aa_date_expected_date' => 'nullable|date',
            'aa_date_actual_date' => 'nullable|date',
            'aa_norm_date' => 'nullable|date',
            'aa_date_notes' => 'nullable|string',

            'sanction_date_expected_date' => 'nullable|date',
            'sanction_date_actual_date' => 'nullable|date',
            'sanction_norm_date' => 'nullable|date',
            'sanction_date_notes' => 'nullable|string',

            'indent_date_expected_date' => 'nullable|date',
            'indent_date_actual_date' => 'nullable|date',
            'indent_norm_date' => 'nullable|date',
            'indent_date_notes' => 'nullable|string',

            'nit_date_expected_date' => 'nullable|date',
            'nit_date_actual_date' => 'nullable|date',
            'nit_date_norm_date' => 'nullable|date',
            'nit_date_notes' => 'nullable|string',

            'tbo_date_expected_date' => 'nullable|date',
            'tbo_date_actual_date' => 'nullable|date',
            'tbo_date_norm_date' => 'nullable|date',
            'tbo_date_notes' => 'nullable|string',

            'pbo_date_expected_date' => 'nullable|date',
            'pbo_date_actual_date' => 'nullable|date',
            'pbo_date_norm_date' => 'nullable|date',
            'pbo_date_notes' => 'nullable|string',

            'noa_po_date_expected_date' => 'nullable|date',
            'noa_po_date_actual_date' => 'nullable|date',
            'noa_po_date_norm_date' => 'nullable|date',
            'noa_po_date_notes' => 'nullable|string',

            'delivery_date_expected_date' => 'nullable|date',
            'delivery_date_actual_date' => 'nullable|date',
            'delivery_date_norm_date' => 'nullable|date',
            'delivery_date_notes' => 'nullable|string',

            'contract_start_date_expected_date' => 'nullable|date',
            'contract_start_date_actual_date' => 'nullable|date',
            'contract_start_date_norm_date' => 'nullable|date',
            'contract_start_date_notes' => 'nullable|string',

            'contract_end_date_expected_date' => 'nullable|date',
            'contract_end_date_actual_date' => 'nullable|date|after_or_equal:contract_start_date_actual_date',
            'contract_end_date_norm_date' => 'nullable|date',
            'contract_end_date_notes' => 'nullable|string',

            // Misc fields
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
        ];

        // Add conditional required fields when current_status is 1
        if ($status == 1) {
            $rules['title'] = 'required|string|max:255';
            $rules['deliverables'] = 'required|string';
            $rules['indenting_section'] = 'required|string|max:255';
            $rules['indentor_do'] = 'required|string|max:255';
            $rules['value_inr'] = 'required|numeric';
        }

        $validated = $request->validate($rules);

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
            'title' => 'nullable|string|max:255',
            'deliverables' => 'nullable|string',
            'indenting_section' => 'nullable|string|max:255',
            'indentor_do' => 'nullable|string|max:255',
            'value_inr' => 'nullable|numeric',

            // Indenter section norm dates
            'reqmt_recd_date_expected_date' => 'nullable|date',
            'reqmt_recd_date_actual_date' => 'nullable|date',
            'reqmt_recd_norm_date' => 'nullable|date',
            'reqmt_recd_date_notes' => 'nullable|string',

            'case_initiation_date_expected_date' => 'nullable|date',
            'case_initiation_date_actual_date' => 'nullable|date',
            'case_initiation_date_norm_date' => 'nullable|date',
            'case_initiation_date_notes' => 'nullable|string',

            'aa_date_expected_date' => 'nullable|date',
            'aa_date_actual_date' => 'nullable|date',
            'aa_norm_date' => 'nullable|date',
            'aa_date_notes' => 'nullable|string',

            'sanction_date_expected_date' => 'nullable|date',
            'sanction_date_actual_date' => 'nullable|date',
            'sanction_norm_date' => 'nullable|date',
            'sanction_date_notes' => 'nullable|string',

            'indent_date_expected_date' => 'nullable|date',
            'indent_date_actual_date' => 'nullable|date',
            'indent_norm_date' => 'nullable|date',
            'indent_date_notes' => 'nullable|string',

            // Tender & contract stages (with norm dates)
            'nit_date_expected_date' => 'nullable|date',
            'nit_date_actual_date' => 'nullable|date',
            'nit_date_norm_date' => 'nullable|date',
            'nit_date_notes' => 'nullable|string',

            'tbo_date_expected_date' => 'nullable|date',
            'tbo_date_actual_date' => 'nullable|date',
            'tbo_date_norm_date' => 'nullable|date',
            'tbo_date_notes' => 'nullable|string',

            'pbo_date_expected_date' => 'nullable|date',
            'pbo_date_actual_date' => 'nullable|date',
            'pbo_date_norm_date' => 'nullable|date',
            'pbo_date_notes' => 'nullable|string',

            'noa_po_date_expected_date' => 'nullable|date',
            'noa_po_date_actual_date' => 'nullable|date',
            'noa_po_date_norm_date' => 'nullable|date',
            'noa_po_date_notes' => 'nullable|string',

            'delivery_date_expected_date' => 'nullable|date',
            'delivery_date_actual_date' => 'nullable|date',
            'delivery_date_norm_date' => 'nullable|date',
            'delivery_date_notes' => 'nullable|string',

            'contract_start_date_expected_date' => 'nullable|date',
            'contract_start_date_actual_date' => 'nullable|date',
            'contract_start_date_norm_date' => 'nullable|date',
            'contract_start_date_notes' => 'nullable|string',

            'contract_end_date_expected_date' => 'nullable|date',
            'contract_end_date_actual_date' => 'nullable|date|after_or_equal:contract_start_date_actual_date',
            'contract_end_date_norm_date' => 'nullable|date',
            'contract_end_date_notes' => 'nullable|string',

            // Misc fields
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
            'current_status' => 'nullable|integer',
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
