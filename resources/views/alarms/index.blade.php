@extends('layouts.app')

@section('content')
<div class="relative min-h-screen bg-white text-gray-900">

    {{-- 🔵 Gambar background di kiri bawah --}}
    <img src="{{ asset('images/senyuminajah.jpg') }}"
         alt="Background"
         class="absolute bottom-0 left-0 w-32 opacity-10 pointer-events-none select-none hidden sm:block">

    {{-- 🟢 Jika BELUM ada pencarian → tampilkan search bar di tengah --}}
    @if(empty($search))
        <div class="flex flex-col items-center justify-center min-h-screen px-4">
            <h1 class="text-4xl font-bold mb-8 text-center">Cari Kode Alarm</h1>

            <form action="{{ route('alarms.index') }}" method="GET"
                  class="flex flex-col sm:flex-row sm:items-center sm:justify-center gap-3 w-full max-w-3xl">
                {{-- Input di dalam border --}}
                <div class="flex items-center w-full border rounded-full shadow px-4 py-3 bg-white">
                    <input type="text"
                           name="search"
                           placeholder="Masukkan kode atau deskripsi alarm..."
                           autofocus
                           class="flex-grow px-3 py-2 focus:outline-none text-lg rounded-full">
                </div>

                {{-- Tombol search di luar border --}}
                <button type="submit"
                        class="bg-blue-600 text-white px-6 py-3 rounded-full hover:bg-blue-700 transition shadow w-full sm:w-auto">
                    Search
                </button>
            </form>
        </div>
    @else
        {{-- 🟡 Jika SUDAH ada pencarian → tampilkan search bar di atas tabel --}}
        <div class="container mx-auto p-6 relative z-10">
            <form action="{{ route('alarms.index') }}" method="GET"
                  class="flex flex-col sm:flex-row sm:items-center sm:justify-center gap-3 w-full max-w-3xl mx-auto mb-6">
                {{-- Input di dalam border --}}
                <div class="flex items-center w-full border rounded-full shadow px-4 py-3 bg-white">
                    <input type="text"
                           name="search"
                           value="{{ $search }}"
                           placeholder="Masukkan kode atau deskripsi alarm..."
                           class="flex-grow px-3 py-2 focus:outline-none text-lg rounded-full">
                </div>

                {{-- Tombol search di luar border --}}
                <button type="submit"
                        class="bg-blue-600 text-white px-6 py-3 rounded-full hover:bg-blue-700 transition shadow w-full sm:w-auto">
                    Search
                </button>
            </form>

            <h1 class="text-2xl font-semibold mb-4 text-center sm:text-left">Hasil Pencarian: "{{ $search }}"</h1>

            <div class="overflow-x-auto">
                <table class="min-w-full border">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="p-2 border text-center w-12">Code Alarm</th>
                            <th class="p-2 border">Description Alarm</th>
                            <th class="p-2 border text-center">Step</th>
                            <th class="p-2 border">Action</th>
                            <th class="p-2 border">Sensor</th>
                            <th class="p-2 border">Komponen</th>
                            @can('isAdmin')<th class="p-2 border text-center">Aksi</th>@endcan
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($alarms as $index => $alarm)
                            @php
                                $rows = 0;
                                foreach($alarm->actions as $action){
                                    $rows += max($action->sensors->count(),1);
                                }
                                $rowspan = $rows ?: 1;
                                $firstRow = true;
                            @endphp

                            @if($alarm->actions->isEmpty())
                                <tr>
                                    <td class="p-2 border">{{ $alarm->code_alarm}}</td>
                                    <td class="p-2 border">{{ $alarm->description}}</td>
                                    <td class="p-2 border text-center">{{ $alarm->step }}</td>
                                    <td class="p-2 border text-gray-400" colspan="4">Belum ada action</td>
                                    @can('isAdmin')
                                        <td class="p-2 border text-center">
                                            <a href="{{ route('alarms.edit', ['alarm' => $alarm->id, 'search' => request('search')]) }}" class="text-blue-700 underline">Edit</a>
                                            <form action="{{ route('alarms.destroy', ['alarm' => $alarm->id, 'search' => request('search')]) }}" method="POST" class="inline" onsubmit="return confirm('Hapus data ini?')">
                                                @csrf @method('DELETE')
                                                <button class="text-red-700 underline ml-2">Hapus</button>
                                            </form>
                                        </td>
                                    @endcan
                                </tr>
                            @else
                                @foreach($alarm->actions as $aIndex => $action)
                                    @php $sensorCount = max($action->sensors->count(),1); @endphp

                                    @for($sIndex=0; $sIndex<$sensorCount; $sIndex++)
                                        <tr class="align-top">
                                            @if($firstRow)
                                                <td class="p-2 border text-center" rowspan="{{ $rowspan }}">
                                                    {{ $alarm->code_alarm}}
                                                </td>
                                                <td class="p-2 border" rowspan="{{ $rowspan }}">
                                                    {{ $alarm->description}}
                                                </td>
                                                <td class="p-2 border text-center" rowspan="{{ $rowspan }}">
                                                    {{ $alarm->step }}
                                                </td>
                                                @php $firstRow=false; @endphp
                                            @endif

                                            {{-- Action --}}
                                            @if($sIndex===0)
                                                <td class="p-2 border" rowspan="{{ $sensorCount }}">
                                                    {{ $action->action_text }}
                                                </td>
                                            @endif

                                            {{-- Sensor --}}
                                            <td class="p-2 border text-center">
                                                {{ $action->sensors[$sIndex]->sensor_name ?? '-' }}
                                            </td>

                                            {{-- Komponen --}}
                                            <td class="p-2 border text-center">
                                                @if(isset($action->sensors[$sIndex]) && $action->sensors[$sIndex]->komponen)
                                                    <a href="{{ asset('storage/'.$action->sensors[$sIndex]->komponen) }}" target="_blank">
                                                        <img src="{{ asset('storage/'.$action->sensors[$sIndex]->komponen) }}"
                                                             class="h-16 w-16 object-cover border rounded mx-auto">
                                                    </a>
                                                @else
                                                    -
                                                @endif
                                            </td>

                                            {{-- Aksi --}}
                                            @if($sIndex===0 && $aIndex===0)
                                                @can('isAdmin')
                                                    <td class="p-2 border text-center" rowspan="{{ $rowspan }}">
                                                        <a href="{{ route('alarms.edit', ['alarm' => $alarm->id, 'search' => request('search')]) }}" class="text-blue-700 underline">Edit</a>
                                                        <form action="{{ route('alarms.destroy', ['alarm' => $alarm->id, 'search' => request('search')]) }}" method="POST" class="inline" onsubmit="return confirm('Hapus data ini?')">
                                                            @csrf @method('DELETE')
                                                            <button class="text-red-700 underline ml-2">Hapus</button>
                                                        </form>
                                                    </td>
                                                @endcan
                                            @endif
                                        </tr>
                                    @endfor
                                @endforeach
                            @endif
                        @empty
                            <tr>
                                <td class="p-3 border text-center" colspan="8">
                                    Tidak ada data untuk pencarian "<b>{{ $search }}</b>".
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $alarms->appends(['search' => request('search')])->links() }}
            </div>
        </div>
    @endif
</div>
@endsection
