<?php

namespace App\Http\Controllers;

use App\Models\Action;
use App\Models\MachineType;
use Illuminate\Http\Request;

class SensorController extends Controller
{
    public function store(Request $request, Action $action)
    {
        $request->validate([
            'sensor_name' => 'required|string',
            'komponen'    => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'plc_io'      => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096'
        ]);

        $machineTypeId = $action->machine_type_id;

        // Ambil nama mesin untuk penempatan folder
        $machineName = MachineType::find($machineTypeId)->name;

        $komponenPath = $request->file('komponen')
            ? $request->file('komponen')->store("$machineName/komponen", 'public')
            : null;

        $plcPath = $request->file('plc_io')
            ? $request->file('plc_io')->store("$machineName/plc_io", 'public')
            : null;

        $action->sensors()->create([
            'sensor_name'     => $request->sensor_name,
            'komponen'        => $komponenPath,
            'plc_io'          => $plcPath,
            'machine_type_id' => $machineTypeId,
        ]);

        return back()->with('success','Sensor ditambahkan');
    }
}
