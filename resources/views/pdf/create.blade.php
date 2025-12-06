@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6 max-w-xl">

    <h1 class="text-2xl font-bold mb-4">Upload PDF</h1>
    @error('title')
    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
    @enderror
    <form action="{{ route('pdf.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
        @csrf

        <div>
            <label class="font-medium">Machine Type</label>
            <select name="machine_type_id" class="border px-3 py-2 rounded w-full" required>

                @foreach($machineTypes as $type)
                    <option value="{{ $type->id }}">
                        {{ $type->name }}
                    </option>
                @endforeach

            </select>
        </div>

        <div>
            <label class="font-medium">Judul PDF</label>
            <input type="text" name="title" class="border px-3 py-2 rounded w-full" required>
        </div>

        <div>
            <label class="font-medium">File PDF</label>
            <input type="file" name="file" accept="application/pdf"
                   class="border px-3 py-2 rounded w-full" required>
        </div>

        <button class="px-4 py-2 bg-green-600 text-white rounded">Upload</button>
        <a href="{{ route('pdf.index') }}" class="px-4 py-2 border rounded">Batal</a>
    </form>
</div>
@endsection
