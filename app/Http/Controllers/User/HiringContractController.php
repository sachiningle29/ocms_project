<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\HiringContract;
use App\Models\Section;
use App\Models\SubSection;
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
        $userId = Auth::id(); // Get the currently authenticated user's ID

        $contracts = HiringContract::where('indentor_do', $userId)
            ->leftJoin('sections', 'hiring_contracts.indenting_section', '=', 'sections.id')
            ->select([
                'hiring_contracts.*',
                'sections.section_name as indenting_section_name'
            ])
            ->latest()
            ->get();

        return response()->json($contracts);
    }

    public function SectionList()
    {
        $sections = Section::select('id', 'section_name as label', 'section_name as value')
            ->get();

        return response()->json($sections);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if ($request->has('value_inr')) {
            $request->merge([
                'value_inr' => str_replace(',', '', $request->input('value_inr'))
            ]);
        }

        $status = $request->input('current_status');

        // Base validation rules
        $rules = [
            'current_status' => 'nullable|integer',

            // Case Information Fields
            'title' => 'nullable|string|max:255',
            'deliverables' => 'nullable|string',
            'indenting_section' => 'nullable|integer',
            'indentor_sub_section' => 'nullable|integer',
            'indentor_do' => 'nullable|integer|max:255',
            'value_inr' => 'nullable|numeric',
            'tendering_section' => 'nullable|string|max:255',

            // Indenting Timeline Fields
            'reqmt_recd_date_expected_date' => 'nullable|date',
            'reqmt_recd_date_actual_date' => 'nullable|date',
            'reqmt_recd_norm_date' => 'nullable|date',
            'reqmt_recd_date_notes' => 'nullable|string',
            'reqmt_recd_date_deviation' => 'nullable|string',
            'reqmt_recd_date_deviation_days' => 'nullable|integer',

            'case_initiation_date_expected_date' => 'nullable|date',
            'case_initiation_date_actual_date' => 'nullable|date',
            'case_initiation_norm_date' => 'nullable|date',
            'case_initiation_date_notes' => 'nullable|string',
            'case_initiation_date_deviation' => 'nullable|string',
            'case_initiation_date_deviation_days' => 'nullable|integer',

            'aa_date_expected_date' => 'nullable|date',
            'aa_date_actual_date' => 'nullable|date',
            'aa_norm_date' => 'nullable|date',
            'aa_date_notes' => 'nullable|string',
            'aa_date_deviation' => 'nullable|string',
            'aa_date_deviation_days' => 'nullable|integer',

            'sanction_date_expected_date' => 'nullable|date',
            'sanction_date_actual_date' => 'nullable|date',
            'sanction_norm_date' => 'nullable|date',
            'sanction_date_notes' => 'nullable|string',
            'sanction_date_deviation' => 'nullable|string',
            'sanction_date_deviation_days' => 'nullable|integer',

            'indent_date_expected_date' => 'nullable|date',
            'indent_date_actual_date' => 'nullable|date',
            'indent_norm_date' => 'nullable|date',
            'indent_date_notes' => 'nullable|string',
            'indent_date_deviation' => 'nullable|string',
            'indent_date_deviation_days' => 'nullable|integer',

            // Tendering Timeline Fields
            'nit_date_expected_date' => 'nullable|date',
            'nit_date_actual_date' => 'nullable|date',
            'nit_date_norm_date' => 'nullable|date',
            'nit_date_notes' => 'nullable|string',
            'nit_date_deviation' => 'nullable|string',
            'nit_date_deviation_days' => 'nullable|integer',

            'tbo_date_expected_date' => 'nullable|date',
            'tbo_date_actual_date' => 'nullable|date',
            'tbo_date_norm_date' => 'nullable|date',
            'tbo_date_notes' => 'nullable|string',
            'tbo_date_deviation' => 'nullable|string',
            'tbo_date_deviation_days' => 'nullable|integer',

            'pbo_date_expected_date' => 'nullable|date',
            'pbo_date_actual_date' => 'nullable|date',
            'pbo_date_norm_date' => 'nullable|date',
            'pbo_date_notes' => 'nullable|string',
            'pbo_date_deviation' => 'nullable|string',
            'pbo_date_deviation_days' => 'nullable|integer',

            'noa_po_date_expected_date' => 'nullable|date',
            'noa_po_date_actual_date' => 'nullable|date',
            'noa_po_date_norm_date' => 'nullable|date',
            'noa_po_date_notes' => 'nullable|string',
            'noa_po_date_deviation' => 'nullable|string',
            'noa_po_date_deviation_days' => 'nullable|integer',

            'delivery_date_expected_date' => 'nullable|date',
            'delivery_date_actual_date' => 'nullable|date',
            'delivery_date_norm_date' => 'nullable|date',
            'delivery_date_notes' => 'nullable|string',
            'delivery_date_deviation' => 'nullable|string',
            'delivery_date_deviation_days' => 'nullable|integer',

            'contract_start_date_expected_date' => 'nullable|date',
            'contract_start_date_actual_date' => 'nullable|date',
            'contract_start_date_norm_date' => 'nullable|date',
            'contract_start_date_notes' => 'nullable|string',

            'contract_end_date_expected_date' => 'nullable|date',
            'contract_end_date_actual_date' => 'nullable|date|after_or_equal:contract_start_date_actual_date',
            'contract_end_date_norm_date' => 'nullable|date',
            'contract_end_date_notes' => 'nullable|string',

            // Tendering Form Fields
            'tender_do' => 'nullable|string|max:255',
            'tendering_platform' => 'nullable|integer',
            'tender_type' => 'nullable|string|max:255',
            'vendor_type' => ['nullable', Rule::in(['OEM', 'Non-OEM'])],
            'vendor_code' => 'nullable|digits:6|integer',

            // Post Contract Fields
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

        // Additional validation when status is 1 (possibly draft to active)
        if ($status == 1) {
            $rules['title'] = 'required|string|max:255';
            $rules['deliverables'] = 'required|string';
            $rules['indenting_section'] = 'required|string|max:255';
            $rules['indentor_do'] = 'required|string|max:255';
            $rules['value_inr'] = 'required|numeric';

            // Add required fields from tendering form when status changes
            $rules['tender_do'] = 'required|string|max:255';
            $rules['tendering_platform'] = 'required|integer';
        }

        $validated = $request->validate($rules);

        if (!isset($validated['status'])) {
            $validated['status'] = 'active';
        }

        $prefix = strtoupper(substr($validated['deliverables'] ?? 'GEN', 0, 3));

        $contract = HiringContract::create($validated);

        $sequentialNumber = str_pad($contract->id, 6, '0', STR_PAD_LEFT);
        $rid = $prefix . $sequentialNumber;

        $contract->update(['rid' => $rid]);

        return response()->json($contract, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $contract = HiringContract::leftJoin('sub_sections', 'hiring_contracts.indentor_sub_section', '=', 'sub_sections.id')
            ->leftJoin('sections', 'hiring_contracts.indenting_section', '=', 'sections.id')
            ->leftJoin('users', 'hiring_contracts.indentor_do', '=', 'users.id')
            ->select(
                'hiring_contracts.*',
                'sections.section_name as indenting_section_name',
                'sub_sections.sub_section_name',
                'users.name as indentor_do_name'  // Add this line
            )
            ->where('hiring_contracts.id', $id)
            ->first();

        if (!$contract) {
            abort(404, 'Contract not found');
        }

        return response()->json($contract);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        if ($request->has('value_inr')) {
            $request->merge([
                'value_inr' => str_replace(',', '', $request->input('value_inr'))
            ]);
        }

        // Base validation rules
        $rules = [
            'current_status' => 'nullable|integer',

            // Case Information Fields
            'title' => 'nullable|string|max:255',
            'deliverables' => 'nullable|string',
            'indenting_section' => 'nullable|integer',
            'indentor_sub_section' => 'nullable|integer',
            'indentor_do' => 'nullable|integer|max:255',
            'value_inr' => 'nullable|numeric',
            'tendering_section' => 'nullable|string|max:255',

            // Indenting Timeline Fields
            'reqmt_recd_date_expected_date' => 'nullable|date',
            'reqmt_recd_date_actual_date' => 'nullable|date',
            'reqmt_recd_norm_date' => 'nullable|date',
            'reqmt_recd_date_notes' => 'nullable|string',
            'reqmt_recd_date_deviation' => 'nullable|string',
            'reqmt_recd_date_deviation_days' => 'nullable|integer',

            'case_initiation_date_expected_date' => 'nullable|date',
            'case_initiation_date_actual_date' => 'nullable|date',
            'case_initiation_norm_date' => 'nullable|date',
            'case_initiation_date_notes' => 'nullable|string',
            'case_initiation_date_deviation' => 'nullable|string',
            'case_initiation_date_deviation_days' => 'nullable|integer',

            'aa_date_expected_date' => 'nullable|date',
            'aa_date_actual_date' => 'nullable|date',
            'aa_norm_date' => 'nullable|date',
            'aa_date_notes' => 'nullable|string',
            'aa_date_deviation' => 'nullable|string',
            'aa_date_deviation_days' => 'nullable|integer',

            'sanction_date_expected_date' => 'nullable|date',
            'sanction_date_actual_date' => 'nullable|date',
            'sanction_norm_date' => 'nullable|date',
            'sanction_date_notes' => 'nullable|string',
            'sanction_date_deviation' => 'nullable|string',
            'sanction_date_deviation_days' => 'nullable|integer',

            'indent_date_expected_date' => 'nullable|date',
            'indent_date_actual_date' => 'nullable|date',
            'indent_norm_date' => 'nullable|date',
            'indent_date_notes' => 'nullable|string',
            'indent_date_deviation' => 'nullable|string',
            'indent_date_deviation_days' => 'nullable|integer',

            // Tendering Timeline Fields
            'nit_date_expected_date' => 'nullable|date',
            'nit_date_actual_date' => 'nullable|date',
            'nit_date_norm_date' => 'nullable|date',
            'nit_date_notes' => 'nullable|string',
            'nit_date_deviation' => 'nullable|string',
            'nit_date_deviation_days' => 'nullable|integer',

            'tbo_date_expected_date' => 'nullable|date',
            'tbo_date_actual_date' => 'nullable|date',
            'tbo_date_norm_date' => 'nullable|date',
            'tbo_date_notes' => 'nullable|string',
            'tbo_date_deviation' => 'nullable|string',
            'tbo_date_deviation_days' => 'nullable|integer',

            'pbo_date_expected_date' => 'nullable|date',
            'pbo_date_actual_date' => 'nullable|date',
            'pbo_date_norm_date' => 'nullable|date',
            'pbo_date_notes' => 'nullable|string',
            'pbo_date_deviation' => 'nullable|string',
            'pbo_date_deviation_days' => 'nullable|integer',

            'noa_po_date_expected_date' => 'nullable|date',
            'noa_po_date_actual_date' => 'nullable|date',
            'noa_po_date_norm_date' => 'nullable|date',
            'noa_po_date_notes' => 'nullable|string',
            'noa_po_date_deviation' => 'nullable|string',
            'noa_po_date_deviation_days' => 'nullable|integer',

            'delivery_date_expected_date' => 'nullable|date',
            'delivery_date_actual_date' => 'nullable|date',
            'delivery_date_norm_date' => 'nullable|date',
            'delivery_date_notes' => 'nullable|string',
            'delivery_date_deviation' => 'nullable|string',
            'delivery_date_deviation_days' => 'nullable|integer',

            'contract_start_date_expected_date' => 'nullable|date',
            'contract_start_date_actual_date' => 'nullable|date',
            'contract_start_date_norm_date' => 'nullable|date',
            'contract_start_date_notes' => 'nullable|string',

            'contract_end_date_expected_date' => 'nullable|date',
            'contract_end_date_actual_date' => 'nullable|date|after_or_equal:contract_start_date_actual_date',
            'contract_end_date_norm_date' => 'nullable|date',
            'contract_end_date_notes' => 'nullable|string',

            // Tendering Form Fields
            'tender_do' => 'nullable|string|max:255',
            'tendering_platform' => 'nullable|integer',
            'tender_type' => 'nullable|string|max:255',
            'vendor_type' => ['nullable', Rule::in(['OEM', 'Non-OEM'])],
            'vendor_code' => 'nullable|digits:6|integer',

            // Post Contract Fields
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

        $validated = $request->validate($rules);

        $contract = HiringContract::findOrFail($id);

        // Auto-set dates if needed
        if (isset($validated['delivery_date_expected_date'])) {
            $validated['contract_start_date_expected_date'] = $validated['delivery_date_expected_date'];
        }

        if (isset($validated['contract_start_date_actual_date'])) {
            $validated['delivery_date_actual_date'] = $validated['contract_start_date_actual_date'];
        }

        // Update RID if deliverables changed
        if (isset($validated['deliverables']) && $validated['deliverables'] !== $contract->deliverables) {
            $prefix = strtoupper(substr($validated['deliverables'] ?? 'GEN', 0, 3));
            $sequentialNumber = str_pad($contract->id, 6, '0', STR_PAD_LEFT);
            $validated['rid'] = $prefix . $sequentialNumber;
        }

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

        // Fetch user with section info
        $user = User::where('users.id', $userId)
            ->leftJoin('sections', 'users.section_id', '=', 'sections.id')
            ->select(
                'users.id as user_id',
                'users.name as user_name',
                'sections.id as section_id',
                'sections.section_name as section_name'
            )
            ->first();

        // Get sub-sections for the user's section
        $subSections = [];
        if ($user && $user->section_id) {
            $subSections = SubSection::where('section_id', $user->section_id)
                ->select('id', 'sub_section_name')
                ->get()
                ->map(function ($subSection) {
                    return [
                        'label' => $subSection->sub_section_name,
                        'value' => $subSection->id
                    ];
                });
        }

        return response()->json([
            'success' => true,
            'user' => [
                'id' => $user->user_id ?? null,
                'name' => $user->user_name ?? null,
                'section' => [
                    'id' => $user->section_id ?? null,
                    'name' => $user->section_name ?? null
                ],
                'sub_sections' => $subSections
            ]
        ]);
    }
}
