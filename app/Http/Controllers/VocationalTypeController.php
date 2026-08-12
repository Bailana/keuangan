<?php

namespace App\Http\Controllers;

use App\Models\VocationalType;
use Illuminate\Http\Request;

class VocationalTypeController extends Controller
{
    public function index()
    {
        $vocationalTypes = VocationalType::latest()->get();
        return view('vocational-types.index', compact('vocationalTypes'));
    }

    public function create()
    {
        return view('vocational-types.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price_per_session' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:500',
        ]);

        VocationalType::create($validated);
        return redirect()->route('vocational-types.index')->with('success', 'Jenis vokasi berhasil ditambahkan.');
    }

    public function edit(VocationalType $vocationalType)
    {
        return view('vocational-types.edit', compact('vocationalType'));
    }

    public function update(Request $request, VocationalType $vocationalType)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price_per_session' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:500',
        ]);

        $vocationalType->update($validated);
        return redirect()->route('vocational-types.index')->with('success', 'Jenis vokasi berhasil diperbarui.');
    }

    public function destroy(VocationalType $vocationalType)
    {
        $vocationalType->delete();
        return redirect()->route('vocational-types.index')->with('success', 'Jenis vokasi berhasil dihapus.');
    }
}
