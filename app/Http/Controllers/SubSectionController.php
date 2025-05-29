<?php

namespace App\Http\Controllers;

use App\Models\SubSection;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SubSectionController extends Controller
{
    public function index()
    {
        $subSections = SubSection::with('section')->get();
        return response()->json($subSections);
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'section_id' => 'required|exists:sections,id',
                'sub_sections' => 'required|array|min:1',
                'sub_sections.*.name' => [
                    'required',
                    'string',
                    'unique:sub_sections,sub_section_name' // Simple unique check now
                ]
            ]);

            // Check for duplicates in the current request
            $inputNames = array_map('strtolower', array_column($request->sub_sections, 'name'));
            if (count($inputNames) !== count(array_unique($inputNames))) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => ['sub_sections' => ['Duplicate sub-section names in your request']]
                ], 422);
            }

            $created = [];
            foreach ($request->sub_sections as $sub) {
                $created[] = SubSection::create([
                    'section_id' => $request->section_id,
                    'sub_section_name' => $sub['name']
                ]);
            }

            return response()->json($created, 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Server error',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show(string $id)
    {
        $subSection = SubSection::with('section')->find($id);
        if (!$subSection) {
            return response()->json(['message' => 'SubSection not found'], 404);
        }
        return response()->json($subSection);
    }

    public function update(Request $request, string $id)
    {
        $subSection = SubSection::find($id);
        if (!$subSection) {
            return response()->json(['message' => 'SubSection not found'], 404);
        }

        $validated = $request->validate([
            'section_id' => 'required|exists:sections,id',
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('sub_sections', 'sub_section_name')->ignore($subSection->id)
            ],
        ]);

        $subSection->section_id = $validated['section_id'];
        $subSection->sub_section_name = $validated['name'];
        $subSection->save();

        return response()->json($subSection);
    }

    public function destroy(string $id)
    {
        $subSection = SubSection::find($id);
        if (!$subSection) {
            return response()->json(['message' => 'SubSection not found'], 404);
        }

        $subSection->delete();

        return response()->json(['message' => 'SubSection deleted successfully']);
    }
}