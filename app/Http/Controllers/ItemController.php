<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    public function index()
    {
        return Item::all();
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required']);
        return Item::create($request->only('name'));
    }

    public function update(Request $request, Item $item)
    {
        $request->validate(['name' => 'required']);
        $item->update($request->only('name'));
        return $item;
    }

    public function destroy(Item $item)
    {
        $item->delete();
        return response()->noContent();
    }
}
