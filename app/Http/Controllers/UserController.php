<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use App\Models\SubSection;


class UserController extends Controller
{
    public function index()
    {
        // $users = User::all();
        $users = User::where('email', '!=', 'admin@gmail.com')->get();
        return response()->json($users);
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:users,name',
                'email' => 'required|email|max:255|unique:users,email',
                'cpf_no' => 'nullable|string|max:50',
                'password' => 'required|string|min:6',
                'is_admin' => ['required', Rule::in([0, 1])],
                'user_status' => ['required', Rule::in(['Active', 'Inactive'])],
                'section' => 'nullable|string|max:255',
                'section_id' => 'nullable|integer',
            ]);



            $user = new User();

            $user->name = $validated['name'];
            $user->email = $validated['email'];
            $user->cpf_no = $validated['cpf_no'] ?? null;
            $user->password = Hash::make($validated['password']);
            $user->is_admin = $validated['is_admin'];
            $user->user_status = $validated['user_status'];
            $user->section = $validated['section'] ?? null;
            $user->section_id = $validated['section_id'] ?? null;
            $user->save();

            return response()->json($user, 201);
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function show(string $id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
        return response()->json($user);
    }

    public function update(Request $request, string $id)
    {
        $user = User::find($id);
        // echo $user;
        // echo "this is testing";
        // exit;
        // dd($request->all());


        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user->id)],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'cpf_no' => 'nullable|string|max:50',
            'password' => 'nullable|string|min:6',
            'is_admin' => ['required', Rule::in([0, 1])],
            'user_status' => ['required', Rule::in(['Active', 'Inactive'])],
            'section' => 'nullable|string',
            'section_id' => 'nullable|integer',
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->cpf_no = $validated['cpf_no'] ?? null;

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->is_admin = $validated['is_admin'];
        $user->user_status = $validated['user_status'];
        $user->section = $validated['section'] ?? null;
        $user->section_id = $validated['section_id'] ?? null;

        $user->save();

        return response()->json($user);
    }

    public function destroy(string $id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user->delete();

        return response()->json(['message' => 'User deleted successfully']);
    }


    public function getUserDetails(){

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
                'users.*',
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
                'userdata' => $user ?? null,
                'section' => [
                    'id' => $user->section_id ?? null,
                    'name' => $user->section_name ?? null
                ],
                'sub_sections' => $subSections
            ]
        ]);

    }
}
