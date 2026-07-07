<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HiringContract;
use App\Models\Section;
use Illuminate\Http\Request;

class AdminHiringController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $contracts = HiringContract::latest()
            ->leftJoin('sections', 'hiring_contracts.indenting_section', '=', 'sections.id')
            ->select([
                'hiring_contracts.*',
                'sections.id as indenting_section_id',
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
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $contract = HiringContract::findOrFail($id);
        $contract->delete();

        return response()->json(['message' => 'Deleted successfully']);
    }


    // public function getCurrentUserSection()
    // {
    //     $userId = Auth::id();

    //     if (!$userId) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Unauthorized'
    //         ], 401);
    //     }

    //     // Fetch user with section info
    //     $user = User::where('users.id', $userId)
    //         ->leftJoin('sections', 'users.section_id', '=', 'sections.id')
    //         ->select(
    //             'users.id as user_id',
    //             'users.name as user_name',
    //             'sections.id as section_id',
    //             'sections.section_name as section_name'
    //         )
    //         ->first();

    //     // Get sub-sections for the user's section
    //     $subSections = [];
    //     if ($user && $user->section_id) {
    //         $subSections = SubSection::where('section_id', $user->section_id)
    //             ->select('id', 'sub_section_name')
    //             ->get()
    //             ->map(function ($subSection) {
    //                 return [
    //                     'label' => $subSection->sub_section_name,
    //                     'value' => $subSection->id
    //                 ];
    //             });
    //     }

    //     return response()->json([
    //         'success' => true,
    //         'user' => [
    //             'id' => $user->user_id ?? null,
    //             'name' => $user->user_name ?? null,
    //             'section' => [
    //                 'id' => $user->section_id ?? null,
    //                 'name' => $user->section_name ?? null
    //             ],
    //             'sub_sections' => $subSections
    //         ]
    //     ]);
    // }
}
