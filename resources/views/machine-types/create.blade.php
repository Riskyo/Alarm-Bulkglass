@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto p-6">

    <h1 class="text-3xl font-bold mb-6">Tambah Kategori Machine</h1>

    <form method="POST" action="{{ route('machine-types.store') }}">
        @csrf

        <label class="block font-medium">Nama Kategori</label>
        <input type="text" name="name" class="border p-2 rounded w-full" required>

        <button class="bg-green-600 text-white px-4 py-2 mt-4 rounded shadow hover:bg-green-700">
            Simpan
        </button>
    </form>

</div>
@endsection
