<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    
    public function index()
    {
        $users = User::all();
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
            'user_status' => ['required', Rule::in(['active', 'inactive'])],
        ]);

        $user = new User();
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->cpf_no = $validated['cpf_no'] ?? null;
        $user->password = Hash::make($validated['password']);
        $user->is_admin = $validated['is_admin'];   
        $user->user_status = $validated['user_status'];
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

    /**
     * Update the specified user.
     */public function update(Request $request, string $id)
{
    $user = User::find($id);
    if (!$user) {
        return response()->json(['message' => 'User not found'], 404);
    }

    $validated = $request->validate([
        'name' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user->id)],
        'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
        'cpf_no' => 'nullable|string|max:50',
        'password' => 'nullable|string|min:6',
       'is_admin' => ['required', Rule::in([0, 1])],
        'user_status' => ['required', Rule::in(['active', 'inactive'])],
    ]);

    $user->name = $validated['name'];
    $user->email = $validated['email'];
    $user->cpf_no = $validated['cpf_no'] ?? null;

    if (!empty($validated['password'])) {
        $user->password = Hash::make($validated['password']);
    }


    $user->is_admin = ($validated['is_admin'] === 'admin') ? 1 : 0;

    $user->user_status = $validated['user_status'];
    $user->save();

    return response()->json($user);
}


    /**
     * Remove the specified user.
     */
    public function destroy(string $id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user->delete();

        return response()->json(['message' => 'User deleted successfully']);
    }
}
