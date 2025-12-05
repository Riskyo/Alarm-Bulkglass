@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6 max-w-xl">

    <h1 class="text-2xl font-bold mb-4">Edit PDF</h1>
    @error('title')
    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
    @enderror
    <form action="{{ route('pdf.update',$pdf) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
        @csrf @method('PUT')

        <div>
            <label class="font-medium">Machine Type</label>
            <select name="machine_type" class="border px-3 py-2 rounded w-full">
            <option value="bulkglass" {{ $pdf->machine_type=='bulkglass'?'selected':'' }}>Bulkglass</option>
            <option value="depalletiser" {{ $pdf->machine_type=='depalletiser'?'selected':'' }}>Depalletiser</option>
            <option value="robocolumn" {{ $pdf->machine_type=='robocolumn'?'selected':'' }}>Robocolumn</option>
            <option value="incarobot" {{ $pdf->machine_type=='incarobot'?'selected':'' }}>Incarobot</option>
            <option value="paletizer" {{ $pdf->machine_type=='paletizer'?'selected':'' }}>Paletizer</option>
            <option value="conveyor_b23" {{ $pdf->machine_type=='conveyor_b23'?'selected':'' }}>Conveyor B23</option>
            <option value="conveyor_b17" {{ $pdf->machine_type=='conveyor_b17'?'selected':'' }}>Conveyor B17</option>
            <option value="packer" {{ $pdf->machine_type=='packer'?'selected':'' }}>Packer</option>
            <option value="unpacker" {{ $pdf->machine_type=='unpacker'?'selected':'' }}>Unpacker</option>
            <option value="crate_magazine" {{ $pdf->machine_type=='crate_magazine'?'selected':'' }}>Crate Magazine</option>

            </select>
        </div>

        <div>
            <label class="font-medium">Judul PDF</label>
            <input type="text" name="title" class="border px-3 py-2 rounded w-full"
                   value="{{ $pdf->title }}">
        </div>

        <div>
            <label class="font-medium">File PDF Baru (optional)</label>
            <input type="file" name="file" accept="application/pdf"
                   class="border px-3 py-2 rounded w-full">

            <p class="text-sm text-gray-500 mt-1">
                File saat ini:
                <a href="{{ asset('storage/'.$pdf->filename) }}" target="_blank" class="text-blue-600 underline">
                    Lihat PDF
                </a>
            </p>
        </div>

        <button class="px-4 py-2 bg-blue-600 text-white rounded">Update</button>
        <a href="{{ route('pdf.index') }}" class="px-4 py-2 border rounded">Batal</a>
    </form>
</div>
@endsection
