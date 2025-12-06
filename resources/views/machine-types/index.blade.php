@extends('layouts.app')

@section('content')
<div class="relative min-h-screen bg-white text-gray-900">

    <div class="container mx-auto p-6 relative z-10">

        {{-- ================= TITLE ================= --}}
        <div id="machine-title-wrapper" class="w-fit mx-auto">
            <h1 class="text-4xl font-bold mb-8 text-center">Kategori Machine Type</h1>
        </div>

        {{-- ================= ADD BUTTON ================= --}}
        <div class="flex justify-center mb-6">
            <a href="{{ route('machine-types.create') }}"
                class="bg-blue-600 text-white px-6 py-3 rounded-full hover:bg-blue-700 transition shadow text-center">
                + Tambah Kategori
            </a>
        </div>

        {{-- ================= SUCCESS MESSAGE ================= --}}
        @if(session('success'))
            <div class="bg-green-100 text-green-700 p-3 rounded mb-3 text-center">
                {{ session('success') }}
            </div>
        @endif

        {{-- ==================== TABLE ==================== --}}
        <div class="overflow-x-auto">
            <table class="min-w-full border">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="p-2 border text-center">Nama</th>
                        <th class="p-2 border text-center w-32">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($types as $type)
                    <tr class="align-top text-center">
                        <td class="p-2 border">{{ $type->name }}</td>
                        <td class="p-2 border">
                            <div class="flex justify-center items-center space-x-3">
                                <a href="{{ route('machine-types.edit', $type->id) }}"
                                    class="text-blue-700 underline">
                                    Edit
                                </a>

                                <form action="{{ route('machine-types.destroy', $type->id) }}"
                                      method="POST"
                                      onsubmit="return confirm('Hapus kategori ini?')">
                                    @csrf @method('DELETE')
                                    <button class="text-red-700 underline">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="2" class="p-3 border text-center">
                            Belum ada kategori.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
</div>
@endsection

{{-- 🚀 Tutorial interaktif (Shepherd.js) --}}
@section('scripts')
<script>
window.addEventListener('load', function() {
    if (typeof Shepherd === 'undefined') {
        console.error('Shepherd belum siap!');
        return;
    }

    const TOUR_KEY = 'machine_index_tour_v1';

    window.machineTour = new Shepherd.Tour({
        defaultStepOptions: {
            cancelIcon: { enabled: true },
            classes: 'shadow-md bg-purple-600 text-white rounded-md',
            scrollTo: { behavior: 'smooth', block: 'center' }
        },
        useModalOverlay: true
    });

    const addStepIf = (selector, opts) => {
        const el = document.querySelector(selector);
        if (el) {
            opts.attachTo.element = el;
            window.machineTour.addStep(opts);
        }
    };

    // 🔹 STEP 1 — Judul Halaman
    addStepIf('#machine-title-wrapper', {
        title: 'Kategori Machine Type 🏭',
        text: 'Di halaman ini kamu bisa melihat daftar kategori machine type.',
        attachTo: { on: 'bottom' },
        buttons: [{ text: 'Lanjut', action: window.machineTour.next }]
    });

    // 🔹 STEP 2 — Tombol Tambah
    addStepIf('a[href$="create"]', {
        title: 'Tambah Kategori ➕',
        text: 'Klik tombol ini untuk menambahkan kategori machine type baru.',
        attachTo: { on: 'bottom' },
        buttons: [{ text: 'Lanjut', action: window.machineTour.next }]
    });

    // 🔹 STEP 3 — Tabel
    addStepIf('table', {
        title: 'Tabel Kategori 📑',
        text: 'Semua kategori machine type akan ditampilkan di tabel ini.',
        attachTo: { on: 'top' },
        buttons: [{ text: 'Selesai', action: window.machineTour.complete }]
    });

    // Mulai otomatis sekali saja
    if (!localStorage.getItem(TOUR_KEY)) {
        setTimeout(() => {
            window.machineTour.start();
            localStorage.setItem(TOUR_KEY, '1');
        }, 500);
    }
});

// Manual trigger
function startTutorial() {
    if (window.machineTour) window.machineTour.start();
}
</script>
@endsection
