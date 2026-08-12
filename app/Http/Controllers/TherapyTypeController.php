<?php

namespace App\Http\Controllers;

use App\Models\TherapyType;
use Illuminate\Http\Request;

class TherapyTypeController extends Controller
{
    public function index()
    {
        $therapyTypes = TherapyType::latest()->get();
        return view('therapy-types.index', compact('therapyTypes'));
    }

    public function create()
    {
        return view('therapy-types.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price_per_session' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:500',
        ]);

        TherapyType::create($validated);
        return redirect()->route('therapy-types.index')->with('success', 'Jenis terapi berhasil ditambahkan.');
    }

    public function edit(TherapyType $therapyType)
    {
        return view('therapy-types.edit', compact('therapyType'));
    }

    public function update(Request $request, TherapyType $therapyType)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price_per_session' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:500',
        ]);

        $therapyType->update($validated);
        return redirect()->route('therapy-types.index')->with('success', 'Jenis terapi berhasil diperbarui.');
    }

    public function destroy(TherapyType $therapyType)
    {
        $therapyType->delete();
        return redirect()->route('therapy-types.index')->with('success', 'Jenis terapi berhasil dihapus.');
    }
}
