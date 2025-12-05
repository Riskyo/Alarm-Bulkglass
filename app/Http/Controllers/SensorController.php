<?php

namespace App\Http\Controllers;

use App\Models\Action;
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

        // Ambil machine_type dari alarm parent
        $machine = $action->alarm->machine_type;

        $komponenPath = $request->file('komponen')
                        ? $request->file('komponen')->store("$machine/komponen", 'public')
                        : null;

        $plcPath = $request->file('plc_io')
                  ? $request->file('plc_io')->store("$machine/plc_io", 'public')
                  : null;

        $action->sensors()->create([
            'sensor_name' => $request->sensor_name,
            'komponen'    => $komponenPath,
            'plc_io'      => $plcPath,
        ]);

        return back()->with('success','Sensor ditambahkan');
    }
}
