<?php

namespace App\Http\Controllers;

use App\Models\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PdfController extends Controller
{
    // =======================
    // 📄 LIST PDF (ALL USER)
    // =======================
    public function index(Request $request)
    {
        $machine_type = $request->machine_type;

        $pdfs = Pdf::when($machine_type, fn($q) =>
            $q->where('machine_type', $machine_type)
        )
        ->orderBy('created_at', 'DESC')
        ->paginate(10)
        ->withQueryString();

        return view('pdf.index', compact('pdfs', 'machine_type'));
    }

    // =======================
    // ➕ FORM UPLOAD PDF (ADMIN)
    // =======================
    public function create()
    {
        return view('pdf.create');
    }

    // =======================
    // 💾 SIMPAN PDF (ADMIN)
    // =======================
    public function store(Request $request)
    {
        $validated = $request->validate([
            'machine_type' => 'required|string',
            'title' => 'required|string|unique:pdfs,title',
            'file' => 'required|mimes:pdf|max:20480',
        ]);

        $folder = $request->machine_type . '/pdf';

        $ext = $request->file('file')->getClientOriginalExtension();
        $filename = Str::slug($request->title) . '.' . $ext;
        
        $path = $request->file('file')->storeAs($folder, $filename, 'public');

        Pdf::create([
            'machine_type' => $request->machine_type,
            'title' => $request->title,
            'filename' => $path,
        ]);

        return redirect()->route('pdf.index');
    }

    // =======================
    // ✏ EDIT PDF (ADMIN)
    // =======================
    public function edit(Pdf $pdf)
    {
        return view('pdf.edit', compact('pdf'));
    }

    // =======================
    // ♻ UPDATE PDF (ADMIN)
    // =======================
    public function update(Request $request, Pdf $pdf)
    {
        $validated = $request->validate([
            'machine_type' => 'required|string',
            'title' => 'required|string|unique:pdfs,title,' . $pdf->id,
            'file' => 'nullable|mimes:pdf|max:20480',
        ]);        

        $data = [
            'machine_type' => $request->machine_type,
            'title' => $request->title,
        ];

        // Jika upload file baru
        if ($request->hasFile('file')) {
            Storage::disk('public')->delete($pdf->filename);

            $folder = $request->machine_type . '/pdf';
            $path = $request->file('file')->store($folder, 'public');

            $data['filename'] = $path;
        }

        $pdf->update($data);

        return redirect()->route('pdf.index');
    }

    // =======================
    // 🗑 DELETE PDF (ADMIN)
    // =======================
    public function destroy(Pdf $pdf)
    {
        Storage::disk('public')->delete($pdf->filename);
        $pdf->delete();

        return redirect()->route('pdf.index');
    }
}
