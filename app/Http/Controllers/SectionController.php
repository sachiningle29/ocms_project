<?php

namespace App\Http\Controllers;

use App\Models\Section;
use Illuminate\Http\Request;

class SectionController extends Controller
{
    // Get list of all sections
    public function index()
    {
        return response()->json(Section::all());
    }

    // No need for create() method for API, can leave empty or remove

    // Store new section
    public function store(Request $request)
    {
        $validated = $request->validate([
            'section_name' => 'required|string|max:255',
            'sub_sectionId' => 'nullable|integer|exists:sections,id', // assuming sub_sectionId is an FK to sections.id
        ]);

        $section = Section::create($validated);

        return response()->json($section, 201);
    }

    // Show single section details
    public function show(Section $section)
    {
        return response()->json($section);
    }

    // No need for edit() method for API, can leave empty or remove

    // Update existing section
    public function update(Request $request, Section $section)
    {
        $validated = $request->validate([
            'section_name' => 'required|string|max:255',
            'sub_sectionId' => 'nullable|integer|exists:sections,id',
        ]);

        $section->update($validated);

        return response()->json($section);
    }

    // Delete a section
    public function destroy(Section $section)
    {
        $section->delete();

        return response()->json(null, 204);
    }
}
