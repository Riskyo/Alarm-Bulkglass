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
            <select name="machine_type_id" class="border px-3 py-2 rounded w-full">

                @foreach($machineTypes as $type)
                    <option value="{{ $type->id }}"
                        {{ $pdf->machine_type_id == $type->id ? 'selected' : '' }}>
                        {{ $type->name }}
                    </option>
                @endforeach

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
