<?php

namespace App\Http\Controllers;

use App\Models\Option;
use Illuminate\Http\Request;

class OptionController extends Controller
{
    public function index()
    {
        return Option::orderBy('id', 'desc')->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255'
        ]);

        $option = Option::create([
            'name' => $request->name,
            'is_active' => true
        ]);

        return response()->json([
            'success' => true,
            'option' => $option
        ]);
    }

    public function update(Request $request, Option $option)
    {
        $request->validate([
            'name' => 'required|max:255'
        ]);

        $option->update([
            'name' => $request->name
        ]);

        return response()->json([
            'success' => true
        ]);
    }

    public function destroy(Option $option)
    {
        $option->delete();

        return response()->json([
            'success' => true
        ]);
    }

    public function toggleStatus(Option $option)
    {
        $option->update([
            'is_active' => !$option->is_active
        ]);

        return response()->json([
            'success'   => true,
            'is_active' => $option->is_active
        ]);
    }
}