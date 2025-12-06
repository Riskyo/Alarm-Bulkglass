<?php

namespace App\Http\Controllers;

use App\Models\MachineType;
use Illuminate\Http\Request;

class MachineTypeController extends Controller
{
    public function index()
    {
        $types = MachineType::orderBy('name')->get();
        return view('machine-types.index', compact('types'));
    }

    public function create()
    {
        return view('machine-types.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:machine_types,name',
        ]);

        MachineType::create([
            'name' => $request->name,
        ]);

        return redirect()->route('machine-types.index')->with('success', 'Kategori mesin ditambahkan');
    }

    public function edit(MachineType $machineType)
    {
        return view('machine-types.edit', compact('machineType'));
    }

    public function update(Request $request, MachineType $machineType)
    {
        $request->validate([
            'name' => 'required|string|unique:machine_types,name,' . $machineType->id,
        ]);

        $machineType->update([
            'name' => $request->name,
        ]);

        return redirect()->route('machine-types.index')->with('success', 'Kategori mesin diperbarui');
    }

    public function destroy(MachineType $machineType)
    {
        $machineType->delete();
        return redirect()->route('machine-types.index')->with('success', 'Kategori dihapus');
    }
}
