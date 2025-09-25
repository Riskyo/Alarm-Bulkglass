@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-white text-gray-900">

    {{-- 🟢 Jika BELUM ada pencarian → tampilkan search bar di tengah --}}
    @if(empty($search))
        <div class="flex flex-col items-center justify-center min-h-screen">
            <h1 class="text-4xl font-bold mb-8">Cari Kode Alarm</h1>
            <form action="{{ route('alarms.index') }}" method="GET"
                  class="w-full max-w-2xl mx-auto flex items-center border rounded-full shadow px-4 py-3">
                <input type="text"
                       name="search"
                       placeholder="Masukkan kode atau deskripsi alarm..."
                       autofocus
                       class="flex-grow px-3 py-2 focus:outline-none text-lg">
                <button type="submit"
                        class="bg-blue-600 text-white px-8 py-2 rounded-full hover:bg-blue-700">
                    Search
                </button>
                <a href="{{ route('alarms.create') }}"
                   class="px-6 py-2 bg-green-600 text-white hover:bg-green-700 rounded-full">
                    Tambah
                </a>
            </form>
        </div>
    @else
        {{-- 🟡 Jika SUDAH ada pencarian → tampilkan search bar di atas tabel --}}
        <div class="container mx-auto p-6">
            {{-- Search bar atas --}}
            <form action="{{ route('alarms.index') }}" method="GET"
                  class="w-full max-w-2xl mb-6 flex items-center border rounded-full shadow px-4 py-3 mx-auto">
                <input type="text"
                       name="search"
                       value="{{ $search }}"
                       placeholder="Masukkan kode atau deskripsi alarm..."
                       class="flex-grow px-3 py-2 focus:outline-none text-lg">
                <button type="submit"
                        class="bg-blue-600 text-white px-8 py-2 rounded-full hover:bg-blue-700">
                    Search
                </button>
            </form>

            <h1 class="text-2xl font-semibold mb-4">Hasil Pencarian: "{{ $search }}"</h1>

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
                                            {{-- tambahkan parameter search --}}
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
