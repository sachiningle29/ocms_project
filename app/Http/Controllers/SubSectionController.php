<?php

namespace App\Http\Controllers;

use App\Models\SubSection;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SubSectionController extends Controller
{
    public function index()
    {
        $subSections = SubSection::all();
        return response()->json($subSections);
    }

    public function store(Request $request)
    {
         try {
            $validated = $request->validate([
                'name' => 'required',
            ]);

            $subSection = new SubSection();
            $subSection->sub_section_name = $validated['name'];
            $subSection->save();

            return response()->json($subSection, 201);
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function show(string $id)
    {
        $subSection = subSection::find($id);
        if (!$subSection) {
            return response()->json(['message' => 'subSection not found'], 404);
        }
        return response()->json($subSection);
    }

    public function update(Request $request, string $id)
    {
         $subSection = subSection::find($id);
        if (!$subSection) {
            return response()->json(['message' => 'subSection not found'], 404);
        }

        $validated = $request->validate([
            'name' => ['required'],
        ]);

        $subSection->sub_section_name = $validated['name'];
        $subSection->save();

        return response()->json($subSection);
    }

    public function destroy(string $id)
    {
        $subSection = subSection::find($id);
        if (!$subSection) {
            return response()->json(['message' => 'subSection not found'], 404);
        }

        $subSection->delete();

        return response()->json(['message' => 'subSection deleted successfully']);
    }
}
