@extends('layouts.app')

@section('content')
<div class="relative min-h-screen bg-white text-gray-900">

    {{-- 🔵 BACKGROUND --}}
    <img src="{{ asset('images/senyuminajah.jpg') }}"
         alt="Background"
         class="absolute bottom-0 left-0 w-32 opacity-10 pointer-events-none select-none hidden sm:block">

    <div class="container mx-auto p-6 relative z-10">

        {{-- ================= TITLE ================= --}}
        <div id="pdf-title-wrapper" class="w-fit mx-auto">
            <h1 class="text-4xl font-bold mb-8 text-center">List PDF</h1>
        </div>

        {{-- ================= FILTER + UPLOAD ================= --}}
        <form method="GET"
              class="flex flex-col sm:flex-row sm:items-center sm:justify-center gap-3 w-full max-w-3xl mx-auto mb-6">

              <select name="machine_type_id"
                class="border rounded-full shadow px-4 py-3 bg-white text-lg pr-10 w-full sm:w-auto">

                <option value="">All Machine</option>

                @foreach($machineTypes as $type)
                    <option value="{{ $type->id }}"
                        {{ request('machine_type_id') == $type->id ? 'selected' : '' }}>
                        {{ $type->name }}
                    </option>
                @endforeach

            </select>

            <button class="bg-blue-600 text-white px-6 py-3 rounded-full hover:bg-blue-700 transition shadow w-full sm:w-auto">
                Filter
            </button>

            @can('isAdmin')
            <a href="{{ route('pdf.create') }}"
                class="bg-green-600 text-white px-6 py-3 rounded-full hover:bg-green-700 transition shadow w-full sm:w-auto text-center">
                Upload PDF
            </a>
            @endcan
        </form>

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
                        <th class="p-2 border text-center w-28">Machine</th>
                        <th class="p-2 border text-center">Title</th>
                        <th class="p-2 border text-center w-28">Preview</th>
                        @can('isAdmin')
                        <th class="p-2 border text-center w-32">Aksi</th>
                        @endcan
                    </tr>
                </thead>

                <tbody>
                    @forelse($pdfs as $pdf)
                    <tr class="align-top text-center">

                        {{-- Machine --}}
                        <td class="p-2 border">
                        {{ $pdf->machineType?->name ?? '-' }}
                        </td>

                        {{-- Title --}}
                        <td class="p-2 border">
                            {{ $pdf->title }}
                        </td>

                        {{-- Preview --}}
                        <td class="p-2 border">
                            <a href="{{ asset('storage/'.$pdf->filename) }}"
                               target="_blank"
                               class="text-blue-700 underline">
                                Open
                            </a>
                        </td>

                        {{-- Aksi (Admin Only) --}}
                        @can('isAdmin')
                        <td class="p-2 border">

                            <div class="flex justify-center items-center space-x-3">

                                {{-- Edit --}}
                                <a href="{{ route('pdf.edit', $pdf->id) }}"
                                   class="text-blue-700 underline">
                                    Edit
                                </a>

                                {{-- Hapus --}}
                                <form action="{{ route('pdf.destroy', $pdf->id) }}"
                                      method="POST"
                                      onsubmit="return confirm('Hapus PDF ini?')">
                                    @csrf @method('DELETE')
                                    <button class="text-red-700 underline">Hapus</button>
                                </form>

                            </div>

                        </td>
                        @endcan
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="p-3 border text-center">
                            Belum ada file PDF.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINATION --}}
        <div class="mt-4 flex justify-center">
            {{ $pdfs->links() }}
        </div>

    </div>
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

    const TOUR_KEY = 'pdf_index_tour_v1'; // versi tutorial PDF

    window.pdfTour = new Shepherd.Tour({
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
            window.pdfTour.addStep(opts);
        }
    };

    // 🔹 STEP 1 — Judul Halaman
    addStepIf('#pdf-title-wrapper', {
        title: 'List PDF 📄',
        text: 'Di halaman ini kamu bisa melihat daftar file PDF berdasarkan jenis mesin.',
        attachTo: { on: 'bottom' },
        buttons: [{ text: 'Lanjut', action: window.pdfTour.next }]
    });

    // 🔹 STEP 2 — Dropdown Machine Type
    addStepIf('select[name=machine_type_id]', {
        title: 'Filter Mesin 🏭',
        text: 'Gunakan dropdown ini untuk memfilter PDF berdasarkan mesin Bulkglass atau Depalletiser.',
        attachTo: { on: 'bottom' },
        buttons: [{ text: 'Lanjut', action: window.pdfTour.next }]
    });

    // 🔹 STEP 3 — Tombol Filter
    addStepIf('button.bg-blue-600', {
        title: 'Terapkan Filter 🔎',
        text: 'Klik tombol ini untuk menampilkan PDF sesuai jenis mesin yang dipilih.',
        attachTo: { on: 'bottom' },
        buttons: [{ text: 'Lanjut', action: window.pdfTour.next }]
    });

    // 🔹 STEP 4 — Tombol Upload PDF (Admin Only)
    @can('isAdmin')
    addStepIf('a.bg-green-600', {
        title: 'Upload PDF Baru ➕',
        text: 'Admin dapat mengunggah file PDF baru melalui tombol ini.',
        attachTo: { on: 'bottom' },
        buttons: [{ text: 'Lanjut', action: window.pdfTour.next }]
    });
    @endcan

    // 🔹 STEP 5 — Tabel PDF
    addStepIf('table', {
        title: 'Tabel Daftar PDF 📑',
        text: 'Semua PDF yang telah di-upload akan ditampilkan di tabel ini.',
        attachTo: { on: 'top' },
        buttons: [{ text: 'Selesai', action: window.pdfTour.complete }]
    });

    // Mulai otomatis sekali saja
    if (!localStorage.getItem(TOUR_KEY)) {
        setTimeout(() => {
            window.pdfTour.start();
            localStorage.setItem(TOUR_KEY, '1');
        }, 500);
    }
});

// Manual trigger dari menu navbar
function startTutorial() {
    if (window.pdfTour) window.pdfTour.start();
}
</script>
@endsection

