<?php

namespace App\Http\Controllers;

use App\Models\Pdf;
use App\Models\MachineType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PdfController extends Controller
{
    // =======================
    // 📄 LIST PDF
    // =======================
    public function index(Request $request)
    {
        $machine_type_id = $request->machine_type_id;

        $pdfs = Pdf::with('machineType')
            ->when($machine_type_id, fn($q) =>
                $q->where('machine_type_id', $machine_type_id)
            )
            ->orderBy('created_at', 'DESC')
            ->paginate(10)
            ->withQueryString();

        $machineTypes = MachineType::all();

        return view('pdf.index', compact('pdfs', 'machine_type_id', 'machineTypes'));
    }

    // =======================
    // ➕ FORM UPLOAD
    // =======================
    public function create()
    {
        $machineTypes = MachineType::all();
        return view('pdf.create', compact('machineTypes'));
    }

    // =======================
    // 💾 SIMPAN PDF
    // =======================
    public function store(Request $request)
    {
        $validated = $request->validate([
            'machine_type_id' => 'required|exists:machine_types,id',
            'title' => 'required|string|unique:pdfs,title',
            'file' => 'required|mimes:pdf|max:20480',
        ]);

        $machine = MachineType::find($request->machine_type_id)->name;

        $ext = $request->file('file')->getClientOriginalExtension();
        $filename = Str::slug($request->title) . '.' . $ext;

        $folder = $machine . '/pdf';

        $path = $request->file('file')->storeAs($folder, $filename, 'public');

        Pdf::create([
            'machine_type_id' => $request->machine_type_id,
            'title'           => $request->title,
            'filename'        => $path,
        ]);

        return redirect()->route('pdf.index');
    }

    // =======================
    // ✏ EDIT PDF
    // =======================
    public function edit(Pdf $pdf)
    {
        $machineTypes = MachineType::all();
        return view('pdf.edit', compact('pdf', 'machineTypes'));
    }

    // =======================
    // ♻ UPDATE PDF
    // =======================
    public function update(Request $request, Pdf $pdf)
    {
        $validated = $request->validate([
            'machine_type_id' => 'required|exists:machine_types,id',
            'title' => 'required|string|unique:pdfs,title,' . $pdf->id,
            'file'  => 'nullable|mimes:pdf|max:20480',
        ]);

        $machine = MachineType::find($request->machine_type_id)->name;
        $folder = $machine . '/pdf';

        $data = [
            'machine_type_id' => $request->machine_type_id,
            'title'           => $request->title,
        ];

        // Jika upload PDF baru
        if ($request->hasFile('file')) {
            Storage::disk('public')->delete($pdf->filename);

            $filename = Str::slug($request->title) . '.' .
                        $request->file('file')->getClientOriginalExtension();

            $path = $request->file('file')->storeAs($folder, $filename, 'public');

            $data['filename'] = $path;
        }

        $pdf->update($data);

        return redirect()->route('pdf.index');
    }

    // =======================
    // 🗑 DELETE
    // =======================
    public function destroy(Pdf $pdf)
    {
        Storage::disk('public')->delete($pdf->filename);
        $pdf->delete();

        return redirect()->route('pdf.index');
    }
}
