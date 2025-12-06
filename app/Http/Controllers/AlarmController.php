<?php

namespace App\Http\Controllers;

use App\Models\Alarm;
use App\Models\Action;
use App\Models\Sensor;
use App\Models\MachineType;
use App\Models\Visitor;
use App\Models\SearchLog;
use Illuminate\Http\Request;

class AlarmController extends Controller
{
    // 🟢 Publik: lihat & cari
    public function index(Request $request)
    {
        $ip = $request->ip();

        // Catat visitor unik
        if (!Visitor::where('ip_address', $ip)->exists()) {
            Visitor::create(['ip_address' => $ip]);
        }

        $visitorCount = Visitor::count();

        $search = trim((string)$request->input('search'));
        $sort   = $request->input('sort', 'asc');
        $machineTypeId = $request->input('machine_type_id');

        $alarms = Alarm::with('actions.sensors', 'machineType')
            ->when($search, function ($q) use ($search) {
                $q->where('code_alarm', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%");
            })
            ->when($machineTypeId, function ($q) use ($machineTypeId) {
                $q->where('machine_type_id', $machineTypeId);
            })
            ->orderBy('created_at', $sort)
            ->paginate(10)
            ->withQueryString();

        // log pencarian
        if ($search !== '' && $alarms->total() > 0) {
            SearchLog::create([
                'query'      => $search,
                'ip_address' => $ip,
            ]);
        }

        $mostSearched = SearchLog::select('query')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('query')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        $machineTypes = MachineType::all();

        return view('alarms.index', compact(
            'alarms', 'search', 'sort', 'visitorCount', 'mostSearched', 'machineTypes'
        ));
    }

    // 🟢 Form tambah
    public function create()
    {
        $machineTypes = MachineType::all();
        return view('alarms.create', compact('machineTypes'));
    }

    // 🟢 Simpan alarm baru
    public function store(Request $request)
    {
        $validated = $request->validate([
            'machine_type_id' => 'required|exists:machine_types,id',
            'code_alarm'      => 'required|string|max:50',
            'description'     => 'required|string|max:255',

            'actions'                       => 'required|array',
            'actions.*.action_text'         => 'required|string',

            'actions.*.sensors'             => 'array',
            'actions.*.sensors.*.sensor_name' => 'required|string',
            'actions.*.sensors.*.komponen'    => 'required|image|max:4096',
            'actions.*.sensors.*.plc_io'      => 'nullable|image|max:4096',
        ]);

        $codeAlarm = str_pad($validated['code_alarm'], 3, '0', STR_PAD_LEFT);

        $alarm = Alarm::create([
            'machine_type_id' => $validated['machine_type_id'],
            'code_alarm'      => $codeAlarm,
            'description'     => $validated['description'],
        ]);

        $machineTypeId = $validated['machine_type_id'];
        $machineName = MachineType::find($machineTypeId)->name;

        // SIMPAN ACTION & SENSOR
        foreach ($validated['actions'] as $a) {

            $action = $alarm->actions()->create([
                'action_text'     => $a['action_text'],
                'machine_type_id' => $machineTypeId,
            ]);

            if (!empty($a['sensors'])) {
                foreach ($a['sensors'] as $s) {

                    $komponenPath = $s['komponen']->store("$machineName/komponen", 'public');
                    $plcPath      = !empty($s['plc_io'])
                                     ? $s['plc_io']->store("$machineName/plc_io", 'public')
                                     : null;

                    $action->sensors()->create([
                        'sensor_name'     => $s['sensor_name'],
                        'komponen'        => $komponenPath,
                        'plc_io'          => $plcPath,
                        'machine_type_id' => $machineTypeId,
                    ]);
                }
            }
        }

        return redirect()->route('alarms.index')->with('success', 'Data alarm ditambahkan.');
    }

    // 🟢 Edit alarm
    public function edit(Alarm $alarm)
    {
        $alarm->load('actions.sensors');
        $machineTypes = MachineType::all();

        return view('alarms.edit', compact('alarm', 'machineTypes'));
    }

    // 🟢 Update alarm
    public function update(Request $request, Alarm $alarm)
    {
        $validated = $request->validate([
            'machine_type_id' => 'required|exists:machine_types,id',
            'code_alarm'      => 'required|string|max:50',
            'description'     => 'required|string|max:255',

            'actions'                       => 'required|array',
            'actions.*.action_text'         => 'required|string',

            'actions.*.sensors'             => 'array',
            'actions.*.sensors.*.sensor_name' => 'required|string',
            'actions.*.sensors.*.komponen'    => 'nullable|image|max:4096',
            'actions.*.sensors.*.plc_io'      => 'nullable|image|max:4096',
        ]);

        $codeAlarm = str_pad($validated['code_alarm'], 3, '0', STR_PAD_LEFT);

        $alarm->update([
            'machine_type_id' => $validated['machine_type_id'],
            'code_alarm'      => $codeAlarm,
            'description'     => $validated['description'],
        ]);

        $machineTypeId = $validated['machine_type_id'];
        $machineName = MachineType::find($machineTypeId)->name;

        // HAPUS action & sensor lama
        $alarm->actions()->delete();

        // SIMPAN ulang
        foreach ($validated['actions'] as $i => $a) {

            $action = $alarm->actions()->create([
                'action_text'     => $a['action_text'],
                'machine_type_id' => $machineTypeId,
            ]);

            if (!empty($a['sensors'])) {
                foreach ($a['sensors'] as $j => $s) {

                    $komponenPath = !empty($s['komponen'])
                        ? $s['komponen']->store("$machineName/komponen", 'public')
                        : $request->input("actions.$i.sensors.$j.komponen_old");

                    $plcPath = !empty($s['plc_io'])
                        ? $s['plc_io']->store("$machineName/plc_io", 'public')
                        : $request->input("actions.$i.sensors.$j.plc_old");

                    $action->sensors()->create([
                        'sensor_name'     => $s['sensor_name'],
                        'komponen'        => $komponenPath,
                        'plc_io'          => $plcPath,
                        'machine_type_id' => $machineTypeId,
                    ]);
                }
            }
        }

        return redirect()->route('alarms.index')->with('success', 'Data alarm diperbarui.');
    }

    // 🟢 Hapus alarm
    public function destroy(Alarm $alarm)
    {
        $alarm->delete();
        return redirect()->route('alarms.index')->with('success', 'Data alarm dihapus.');
    }
}
