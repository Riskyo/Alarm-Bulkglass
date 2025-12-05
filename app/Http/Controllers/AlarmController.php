<?php

namespace App\Http\Controllers;

use App\Models\Alarm;
use App\Models\Action;
use App\Models\Sensor;
use App\Models\Visitor;
use App\Models\SearchLog;
use Illuminate\Http\Request;

class AlarmController extends Controller
{
    // 🟢 Publik: lihat & cari
    public function index(Request $request)
    {
        $ip = $request->ip();

        // ✅ Catat IP unik ke tabel visitors
        if (!Visitor::where('ip_address', $ip)->exists()) {
            Visitor::create(['ip_address' => $ip]);
        }

        // ✅ Hitung total visitor unik
        $visitorCount = Visitor::count();

        // ✅ Ambil parameter pencarian dan urutan
        $search = trim((string)$request->input('search'));
        $sort   = $request->input('sort', 'asc');

        $machineType = $request->input('machine_type');

        $alarms = Alarm::with('actions.sensors')
            ->when($search, function ($q) use ($search) {
                $q->where('code_alarm', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%");
            })
            ->when($machineType, function($q) use ($machineType){
                $q->where('machine_type', $machineType);
            }) // ⬅ FILTER MESIN
            ->orderBy('created_at', $sort)
            ->paginate(10)
            ->withQueryString();

        // ✅ Simpan ke search log jika ada hasil
        if ($search !== '' && $alarms->total() > 0) {
            SearchLog::create([
                'query'      => $search,
                'ip_address' => $ip,
            ]);
        }

        // ✅ Ambil 5 pencarian paling sering
        $mostSearched = SearchLog::select('query')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('query')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        return view('alarms.index', compact('alarms', 'search', 'sort', 'visitorCount', 'mostSearched'));
    }

    // 🟢 Form tambah
    public function create()
    {
        return view('alarms.create');
    }

    // 🟢 Simpan alarm baru
    public function store(Request $request)
    {
        $validated = $request->validate([
            'machine_type' => 'required|string|max:50',
            'code_alarm'    => 'required|string|max:50',
            'description'   => 'required|string|max:255',
            'actions'       => 'required|array',
            'actions.*.action_text' => 'required|string',
            'actions.*.sensors'     => 'array',
            'actions.*.sensors.*.sensor_name' => 'required|string',
            'actions.*.sensors.*.komponen'    => 'required|image|mimes:jpg,jpeg,png,webp,gif|max:4096',
            'actions.*.sensors.*.plc_io'      => 'nullable|image|mimes:jpg,jpeg,png,webp,gif|max:4096',
        ]);

        $codeAlarm = str_pad($validated['code_alarm'], 3, '0', STR_PAD_LEFT);

        // SIMPAN ALARM
        $alarm = Alarm::create([
            'machine_type' => $validated['machine_type'],
            'code_alarm'   => $codeAlarm,
            'description'  => $validated['description'],
        ]);

        $machine = $alarm->machine_type;

        // SIMPAN ACTION & SENSOR
        foreach ($validated['actions'] as $actionData) {

            $action = $alarm->actions()->create([
                'action_text' => $actionData['action_text'],
                'machine_type' => $machine,
            ]);

            if (!empty($actionData['sensors'])) {
                foreach ($actionData['sensors'] as $sensorData) {

                    $komponenPath = $sensorData['komponen']->store("$machine/komponen", 'public');
                    $plcPath      = !empty($sensorData['plc_io'])
                                    ? $sensorData['plc_io']->store("$machine/plc_io", 'public')
                                    : null;

                    $action->sensors()->create([
                        'sensor_name' => $sensorData['sensor_name'],
                        'komponen'    => $komponenPath,
                        'plc_io'      => $plcPath,
                        'machine_type' => $machine,
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
        return view('alarms.edit', compact('alarm'));
    }

    public function update(Request $request, Alarm $alarm)
{
    $validated = $request->validate([
        'machine_type' => 'required|string|max:50',
        'code_alarm'    => 'required|string|max:50',
        'description'   => 'required|string|max:255',
        'actions'       => 'required|array',
        'actions.*.action_text' => 'required|string',
        'actions.*.sensors'     => 'array',
        'actions.*.sensors.*.sensor_name' => 'required|string',
        'actions.*.sensors.*.komponen'    => 'nullable|image|mimes:jpg,jpeg,png,webp,gif|max:4096',
        'actions.*.sensors.*.plc_io'      => 'nullable|image|mimes:jpg,jpeg,png,webp,gif|max:4096',
    ]);

    $codeAlarm = str_pad($validated['code_alarm'], 3, '0', STR_PAD_LEFT);

    // UPDATE ALARM (INCLUDING MACHINE TYPE)
    $alarm->update([
        'machine_type' => $validated['machine_type'],
        'code_alarm'   => $codeAlarm,
        'description'  => $validated['description'],
    ]);

    $machine = $validated['machine_type'];

    // HAPUS ACTION LAMA
    $alarm->actions()->delete();

    // SIMPAN ACTION + SENSOR BARU
    foreach ($validated['actions'] as $i => $actionData) {

        $action = $alarm->actions()->create([
            'action_text' => $actionData['action_text'],
            'machine_type' => $machine,
        ]);

        if (!empty($actionData['sensors'])) {
            foreach ($actionData['sensors'] as $j => $sensorData) {

                // KOMONEN
                if (!empty($sensorData['komponen'])) {
                    $komponenPath = $sensorData['komponen']->store("$machine/komponen", 'public');
                } else {
                    $komponenPath = $request->input("actions.$i.sensors.$j.komponen_old");
                }

                // PLC IO
                if (!empty($sensorData['plc_io'])) {
                    $plcPath = $sensorData['plc_io']->store("$machine/plc_io", 'public');
                } else {
                    $plcPath = $request->input("actions.$i.sensors.$j.plc_old");
                }

                $action->sensors()->create([
                    'sensor_name' => $sensorData['sensor_name'],
                    'komponen'    => $komponenPath,
                    'plc_io'      => $plcPath,
                    'machine_type'=> $machine,
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
