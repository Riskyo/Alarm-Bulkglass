@extends('layouts.app')

@section('content')
<div class="relative min-h-screen bg-white text-gray-900">

    {{-- 🔵 Gambar background di kiri bawah --}}
    <img src="{{ asset('images/senyuminajah.jpg') }}"
         alt="Background"
         class="absolute bottom-0 left-0 w-32 opacity-10 pointer-events-none select-none hidden sm:block">

    {{-- 🟢 Jika BELUM ada pencarian → tampilkan search bar di tengah --}}
    @if(empty($search))
    <div class="flex flex-col items-center justify-start min-h-screen px-4 pt-20">
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

                {{-- Tombol search --}}
                <button type="submit"
                        class="bg-blue-600 text-white px-6 py-3 rounded-full hover:bg-blue-700 transition shadow w-full sm:w-auto">
                    Search
                </button>

                {{-- Tombol add --}}
                @can('isAdmin')
                <a href="{{ route('alarms.create') }}"
                   class="bg-green-600 text-white px-6 py-3 rounded-full hover:bg-green-700 transition shadow w-full sm:w-auto text-center">
                    Add
                </a>
                @endcan
            </form>

            {{-- 🔹 Daftar Search Logs (pencarian paling sering) --}}
            @if(isset($mostSearched) && $mostSearched->count() > 0)
                @php
                    $sortedLogs = $mostSearched->sortByDesc(function($log) {
                        return [$log->total, $log->created_at];
                    });
                @endphp

                <div class="mt-8 w-full max-w-3xl">
                    <div class="flex flex-wrap justify-center gap-3">
                        @foreach($sortedLogs as $log)
                            <a href="{{ route('alarms.index', ['search' => $log->query]) }}"
                               class="flex items-center justify-center bg-blue-50 hover:bg-blue-100 border border-blue-200 text-blue-800 font-medium rounded-lg px-4 py-2 shadow-sm transition duration-200 text-center whitespace-nowrap">
                                {{ $log->query }}
                                <span class="ml-2 text-sm text-gray-600">({{ $log->total ?? 0 }})</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

    @else
        {{-- 🟡 Jika SUDAH ada pencarian → tampilkan search bar di atas tabel --}}
        <div class="container mx-auto p-6 relative z-10">
            <form action="{{ route('alarms.index') }}" method="GET"
                  class="flex flex-col sm:flex-row sm:items-center sm:justify-center gap-3 w-full max-w-3xl mx-auto mb-6">
                <div class="flex items-center w-full border rounded-full shadow px-4 py-3 bg-white">
                    <input type="text"
                           name="search"
                           value="{{ $search }}"
                           placeholder="Masukkan kode atau deskripsi alarm..."
                           class="flex-grow px-3 py-2 focus:outline-none text-lg rounded-full">
                </div>

                <button type="submit"
                        class="bg-blue-600 text-white px-6 py-3 rounded-full hover:bg-blue-700 transition shadow w-full sm:w-auto">
                    Search
                </button>

                @can('isAdmin')
                <a href="{{ route('alarms.create') }}"
                   class="bg-green-600 text-white px-6 py-3 rounded-full hover:bg-green-700 transition shadow w-full sm:w-auto text-center">
                    + Add
                </a>
                @endcan
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

                                            @if($sIndex===0)
                                                <td class="p-2 border" rowspan="{{ $sensorCount }}">
                                                    {{ $action->action_text }}
                                                </td>
                                            @endif

                                            <td class="p-2 border text-center">
                                                {{ $action->sensors[$sIndex]->sensor_name ?? '-' }}
                                            </td>

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

{{-- 🚀 Tambahkan tutorial interaktif (Shepherd.js) --}}
@section('scripts')
<script>
window.addEventListener('load', function() {
    if (typeof Shepherd === 'undefined') {
        console.error('Shepherd belum siap!');
        return;
    }

    const TOUR_KEY = 'alarm_index_tour_v2'; // versi baru agar muncul lagi setelah update

    // Buat Shepherd tour global
    window.tour = new Shepherd.Tour({
        defaultStepOptions: {
            cancelIcon: { enabled: true },
            classes: 'shadow-md bg-purple-600 text-white rounded-md',
            scrollTo: { behavior: 'smooth', block: 'center' }
        },
        useModalOverlay: true
    });

    // Fungsi bantu untuk menambahkan step hanya jika elemen ada
    const addStepIf = (selector, opts) => {
        const el = document.querySelector(selector);
        if (el) {
            opts.attachTo.element = el;
            window.tour.addStep(opts);
        }
    };

    // 🔹 Step 1: Judul halaman
    addStepIf('h1', {
        title: 'Selamat Datang 👋',
        text: 'Ini adalah halaman utama pencarian kode alarm. Di sini kamu dapat mencari alarm berdasarkan kode atau deskripsi.',
        attachTo: { on: 'bottom' },
        buttons: [{ text: 'Lanjut', action: window.tour.next }]
    });

    // 🔹 Step 2: Kolom pencarian
    addStepIf('input[name=search]', {
        title: 'Kolom Pencarian 🔍',
        text: 'Masukkan kode atau deskripsi alarm yang ingin kamu cari di sini.',
        attachTo: { on: 'bottom' },
        buttons: [{ text: 'Lanjut', action: window.tour.next }]
    });

    // 🔹 Step 3: Tombol search
    addStepIf('button.bg-blue-600', {
        title: 'Tombol Search',
        text: 'Klik tombol ini untuk memulai pencarian alarm berdasarkan kata kunci yang kamu masukkan.',
        attachTo: { on: 'bottom' },
        buttons: [{ text: 'Lanjut', action: window.tour.next }]
    });

    // 🔹 Step 4: Pencarian Terpopuler (user & admin)
    addStepIf('.flex-wrap.justify-center', {
        title: 'Pencarian Terpopuler 💡',
        text: 'Bagian ini menampilkan daftar kata kunci alarm yang paling sering dicari oleh pengguna lain. Klik salah satu untuk langsung melihat hasilnya.',
        attachTo: { on: 'top' },
        buttons: [{ text: 'Lanjut', action: window.tour.next }]
    });

    // 🔹 Step 5: Tambah alarm baru (hanya admin)
    @can('isAdmin')
    addStepIf('a.bg-green-600', {
        title: 'Tambah Alarm Baru ➕',
        text: 'Khusus untuk admin: tombol ini digunakan untuk menambahkan data alarm baru ke sistem.',
        attachTo: { on: 'left' },
        buttons: [{ text: 'Selesai', action: window.tour.complete }]
    });
    @else
    // 🔹 Step terakhir untuk user biasa
    addStepIf('h1', {
        title: 'Selesai 🎉',
        text: 'Kamu sudah mengenal fitur-fitur utama pada halaman ini. Selamat mencoba!',
        attachTo: { on: 'bottom' },
        buttons: [{ text: 'Tutup', action: window.tour.complete }]
    });
    @endcan

    // Jalankan otomatis pertama kali
    if (!localStorage.getItem(TOUR_KEY)) {
        setTimeout(() => {
            window.tour.start();
            localStorage.setItem(TOUR_KEY, '1');
        }, 500);
    }
});

// 🔹 Fungsi global agar tombol "Show Tutorial" bisa memanggil ulang tour kapan saja
function startTutorial() {
    if (window.tour) {
        window.tour.start();
    } else {
        console.warn('Tour belum siap!');
    }
}
</script>
@endsection
