<?php

namespace App\Http\Controllers;

use App\Models\HiringContract;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
   public function getFilteredCasesWithDurations(Request $request, $status = null)
{
    $validated = $request->validate([
        'section' => 'nullable|integer',
        'subSection' => 'nullable|integer',
        'deliverables' => 'nullable|string',
        'case' => 'nullable|integer',
        'year' => 'nullable|integer|min:1900|max:' . date('Y'), // Validate year input
    ]);

    // Define stage pairs with norm difference keys
    $stagePairs = [
        ['reqmt_recd_date_actual_date', 'case_initiation_date_actual_date', 'case_initiation_norm_date', 'Reqmt → Case Initiation', 'norm-1'],
        ['case_initiation_date_actual_date', 'aa_date_actual_date', 'aa_norm_date', 'Case Initiation → AA', 'norm-2'],
        ['aa_date_actual_date', 'sanction_date_actual_date', 'sanction_norm_date', 'AA → Sanction', 'norm-3'],
        ['sanction_date_actual_date', 'indent_date_actual_date', 'indent_norm_date', 'Sanction → Indent', 'norm-4'],
        ['indent_date_actual_date', 'nit_date_actual_date', null, 'Indent → NIT', null],
        ['nit_date_actual_date', 'tbo_date_actual_date', 'tbo_date_norm_date', 'NIT → TBO', 'norm-5'],
        ['tbo_date_actual_date', 'pbo_date_actual_date', 'pbo_date_norm_date', 'TBO → PBO', 'norm-6'],
        ['pbo_date_actual_date', 'noa_po_date_actual_date', 'noa_po_date_norm_date', 'PBO → NOA/PO', 'norm-7'],
        ['noa_po_date_actual_date', 'delivery_date_actual_date', 'delivery_date_norm_date', 'NOA/PO → Delivery', 'norm-8'],
        ['delivery_date_actual_date', 'contract_start_date_actual_date', 'contract_start_date_norm_date', 'Delivery → Contract Start', 'norm-9'],
        ['contract_start_date_actual_date', 'contract_end_date_actual_date', 'contract_end_date_norm_date', 'Contract Start → Contract End', 'norm-10'],
    ];

    $query = DB::table('hiring_contracts')
        ->select(
            'hiring_contracts.id as case_id',
            'hiring_contracts.title',
            'hiring_contracts.deliverables',
            'sections.id as section_id',
            'sections.section_name',
            'sub_sections.id as sub_section_id',
            'sub_sections.sub_section_name',
            ...collect($stagePairs)
                ->flatMap(fn ($pair) => array_filter([$pair[0], $pair[1], $pair[2]]))
                ->unique()
                ->toArray()
        )
        ->leftJoin('sub_sections', 'hiring_contracts.indentor_sub_section', '=', 'sub_sections.id')
        ->leftJoin('sections', 'sub_sections.section_id', '=', 'sections.id');

    // Apply year filter for individual cases
    if (!empty($validated['year'])) {
        $query->whereYear('hiring_contracts.reqmt_recd_date_actual_date', $validated['year']);
    }

    // Apply other existing filters
    if (!empty($validated['subSection'])) {
        $query->where('sub_sections.id', $validated['subSection']);
    }

    if (!empty($validated['section'])) {
        $query->where('sections.id', $validated['section']);
    }

    if (!empty($validated['deliverables'])) {
        $query->where('hiring_contracts.deliverables', $validated['deliverables']);
    }

    if (!empty($validated['case'])) {
        $query->where('hiring_contracts.id', $validated['case']);
    }

    $contracts = $query->get();

    // Fetch available years for the dropdown
    $availableYears = DB::table('hiring_contracts')
        ->selectRaw('DISTINCT YEAR(reqmt_recd_date_actual_date) as year')
        ->whereNotNull('reqmt_recd_date_actual_date')
        ->orderBy('year', 'desc')
        ->pluck('year')
        ->toArray();

    // Rest of the function remains the same until the response
    $durationsByCase = [];
    $subSectionGroups = [];
    $sectionGroups = [];
    $deliverablesGroups = [];

    foreach ($contracts as $contract) {
        $caseId = $contract->case_id;
        $subSectionId = $contract->sub_section_id;
        $sectionId = $contract->section_id;
        $deliverablesKey = $contract->deliverables;

        $durationsByCase[$caseId] = [
            'case_id' => $caseId,
            'title' => $contract->title,
            'deliverables' => $deliverablesKey,
            'section_id' => $sectionId,
            'section_name' => $contract->section_name,
            'sub_section_id' => $subSectionId,
            'sub_section_name' => $contract->sub_section_name,
            'durations' => [],
            'norm_differences' => [],
        ];

        foreach ($stagePairs as [$fromField, $toField, $normField, $label, $normDiffKey]) {
            $fromDate = $contract->$fromField;
            $toDate = $contract->$toField;
            $normDate = $normField ? $contract->$normField : null;

            if ($fromDate && $toDate) {
                $from = \Carbon\Carbon::parse($fromDate);
                $to = \Carbon\Carbon::parse($toDate);
                $diff = $from->diffInDays($to, false);
                $durationsByCase[$caseId]['durations'][$label] = $diff;

                $subSectionGroups[$subSectionId]['name'] = $contract->sub_section_name;
                $subSectionGroups[$subSectionId]['durations'][$label][] = $diff;

                $sectionGroups[$sectionId]['name'] = $contract->section_name;
                $sectionGroups[$sectionId]['durations'][$label][] = $diff;

                $deliverablesGroups[$deliverablesKey]['name'] = $deliverablesKey;
                $deliverablesGroups[$deliverablesKey]['durations'][$label][] = $diff;
            } else {
                $durationsByCase[$caseId]['durations'][$label] = null;
            }

            if ($normDate && $toDate && $normDiffKey) {
                $to = \Carbon\Carbon::parse($toDate);
                $norm = \Carbon\Carbon::parse($normDate);
                $normDiff = $to->diffInDays($norm, false);
                $durationsByCase[$caseId]['norm_differences'][$normDiffKey] = $normDiff;

                $subSectionGroups[$subSectionId]['norm_differences'][$normDiffKey][] = $normDiff;
                $sectionGroups[$sectionId]['norm_differences'][$normDiffKey][] = $normDiff;
                $deliverablesGroups[$deliverablesKey]['norm_differences'][$normDiffKey][] = $normDiff;
            }
        }
    }

    $computeAverages = function ($groupedData, $keyId, $keyName) {
        $results = [];
        foreach ($groupedData as $id => $group) {
            $avgDurations = [];
            $avgNormDifferences = [];
            foreach ($group['durations'] as $label => $values) {
                $valid = array_filter($values, fn ($v) => $v !== null && $v > 0);
                $avgDurations[$label] = count($valid) > 0
                    ? round(array_sum($valid) / count($valid), 2)
                    : null;
            }
            if (isset($group['norm_differences'])) {
                foreach ($group['norm_differences'] as $normDiffKey => $values) {
                    $valid = array_filter($values, fn ($v) => $v !== null);
                    $avgNormDifferences[$normDiffKey] = count($valid) > 0
                        ? round(array_sum($valid) / count($valid), 2)
                        : null;
                }
            }
            $results[] = [
                $keyId => $id,
                $keyName => $group['name'],
                'avg_durations' => $avgDurations,
                'avg_norm_differences' => $avgNormDifferences,
            ];
        }
        return $results;
    };

    $computeOverallAverage = function ($averages, $keyLabel, $labelValue) {
        $allDurations = [];
        $allNormDifferences = [];
        foreach ($averages as $group) {
            foreach ($group['avg_durations'] as $label => $value) {
                if ($value !== null && $value > 0) {
                    $allDurations[$label][] = $value;
                }
            }
            foreach ($group['avg_norm_differences'] as $normDiffKey => $value) {
                if ($value !== null) {
                    $allNormDifferences[$normDiffKey][] = $value;
                }
            }
        }
        $average = [
            $keyLabel => $labelValue,
            'avg_durations' => [],
            'avg_norm_differences' => [],
        ];
        foreach ($allDurations as $label => $values) {
            $average['avg_durations'][$label] = count($values) > 0
                ? round(array_sum($values) / count($values), 2)
                : null;
        }
        foreach ($allNormDifferences as $normDiffKey => $values) {
            $average['avg_norm_differences'][$normDiffKey] = count($values) > 0
                ? round(array_sum($values) / count($values), 2)
                : null;
        }
        return $average;
    };

    $subSectionAvgDurations = $computeAverages($subSectionGroups, 'sub_section_id', 'sub_section_name');
    $sectionAvgDurations = $computeAverages($sectionGroups, 'section_id', 'section_name');
    $deliverablesAvgDurations = $computeAverages($deliverablesGroups, 'deliverables', 'deliverables');

    array_unshift($subSectionAvgDurations, $computeOverallAverage($subSectionAvgDurations, 'sub_section_name', 'Average of All Sub-sections'));
    array_unshift($sectionAvgDurations, $computeOverallAverage($sectionAvgDurations, 'section_name', 'Average of All Sections'));
    array_unshift($deliverablesAvgDurations, $computeOverallAverage($deliverablesAvgDurations, 'deliverables', 'Average of All Deliverables'));

    $allStageDurations = [];
    $allNormDifferences = [];
    foreach ($durationsByCase as $case) {
        foreach ($case['durations'] as $label => $value) {
            if ($value !== null && $value > 0) {
                $allStageDurations[$label][] = $value;
            }
        }
        foreach ($case['norm_differences'] as $normDiffKey => $value) {
            if ($value !== null) {
                $allNormDifferences[$normDiffKey][] = $value;
            }
        }
    }

    $avgAllCases = [
        'case_id' => null,
        'title' => 'Average of All Cases',
        'deliverables' => null,
        'section_id' => null,
        'section_name' => null,
        'sub_section_id' => null,
        'sub_section_name' => null,
        'durations' => [],
        'norm_differences' => [],
    ];

    foreach ($allStageDurations as $label => $values) {
        $avgAllCases['durations'][$label] = count($values) > 0
            ? round(array_sum($values) / count($values), 2)
            : null;
    }

    foreach ($allNormDifferences as $normDiffKey => $values) {
        $avgAllCases['norm_differences'][$normDiffKey] = count($values) > 0
            ? round(array_sum($values) / count($values), 2)
            : null;
    }

    $finalCases = array_merge([$avgAllCases], array_values($durationsByCase));

    $dashboardQuery = DB::table('hiring_contracts');
    if (!empty($validated['subSection'])) {
        $dashboardQuery->where('indentor_sub_section', $validated['subSection']);
    }
    if (!empty($validated['section'])) {
        $dashboardQuery->where('indenting_section', $validated['section']);
    }
    if (!empty($validated['deliverables'])) {
        $dashboardQuery->where('deliverables', $validated['deliverables']);
    }
    if (!empty($validated['case'])) {
        $dashboardQuery->where('id', $validated['case']);
    }
    if (!empty($validated['year'])) {
        $dashboardQuery->whereYear('reqmt_recd_date_actual_date', $validated['year']);
    }

    $ongoingQuery = (clone $dashboardQuery);
    $noaQuery = (clone $dashboardQuery);
    $reqmtQuery = (clone $dashboardQuery);

    $ongoingCount = $ongoingQuery->whereNull('noa_po_date_actual_date')->count();
    $noaCount = $noaQuery->whereNotNull('noa_po_date_actual_date')->count();

    $reqmtFields = [
        'case_initiation_date_actual_date',
        'aa_date_actual_date',
        'sanction_date_actual_date',
        'indent_date_actual_date',
        'nit_date_actual_date',
        'tbo_date_actual_date',
        'pbo_date_actual_date',
        'noa_po_date_actual_date',
        'delivery_date_actual_date',
        'contract_start_date_actual_date',
        'contract_end_date_actual_date',
    ];

    foreach ($reqmtFields as $field) {
        $reqmtQuery->whereNull($field);
    }
    $reqmtQuery->whereNotNull('reqmt_recd_date_actual_date');
    $reqmtRecdCount = $reqmtQuery->count();

    $query = HiringContract::query();
    if ($status === 'ongoing') {
        $query->whereNotNull('reqmt_recd_date_actual_date')
              ->whereNull('contract_end_date_actual_date');
    } elseif ($status === 'completed') {
        $query->whereNotNull('contract_end_date_actual_date');
    }
    if (!empty($validated['year'])) {
        $query->whereYear('reqmt_recd_date_actual_date', $validated['year']);
    }

    $deliverabless = $query->select('deliverables as name', DB::raw('COUNT(*) as value'))
        ->groupBy('deliverables')
        ->get();

    return response()->json([
        'cases_durations' => $finalCases,
        'sub_sections_avg_durations' => $subSectionAvgDurations,
        'sections_avg_durations' => $sectionAvgDurations,
        'deliverables_avg_durations' => $deliverablesAvgDurations,
        'ongoing_cases' => $ongoingCount,
        'noa_po_placed' => $noaCount,
        'reqmt_recd' => $reqmtRecdCount,
        'deliverables' => $deliverabless,
        'available_years' => $availableYears, // Include available years in response
    ]);
}
}
