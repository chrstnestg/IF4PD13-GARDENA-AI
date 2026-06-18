{{-- Dipanggil dengan: <x-modal-tambah-panen :siklus="$siklusBerikutnya" /> --}}

@props(['siklus' => 1])

<div x-show="modalTambah"
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     @click.self="modalTambah = false"
     class="fixed inset-0 z-50 bg-black/40 backdrop-blur-sm flex items-center justify-center p-4"
     style="display:none;">

    <div x-show="modalTambah"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="bg-white rounded-3xl shadow-2xl w-full max-w-lg overflow-hidden">

        {{-- Header --}}
        <div class="bg-gradient-to-r from-teal-600 to-green-500 px-7 py-6 relative">
            <p class="text-teal-100 text-xs font-bold uppercase tracking-widest mb-1">
                Catat Panen Baru
            </p>
            <h3 class="font-brand font-extrabold text-white text-2xl">
                Siklus {{ $siklus }}
            </h3>
            <button @click="modalTambah = false"
                    class="absolute top-5 right-5 text-white/70 hover:text-white transition-colors">
                <i class="bi bi-x-lg text-lg"></i>
            </button>
        </div>

        {{-- Body --}}
        <div class="px-7 py-6">

            @if($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-600 text-sm rounded-xl px-4 py-3 mb-5">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('riwayat.store') }}" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-2">
                        Tanggal Panen
                    </label>
                    <input type="date" name="tanggal_panen"
                           value="{{ old('tanggal_panen', date('Y-m-d')) }}"
                           class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-300">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-2">
                            Berat Panen (kg)
                        </label>
                        <input type="number" name="berat_panen" step="0.1" min="0.1"
                               value="{{ old('berat_panen') }}" placeholder="contoh: 12.5"
                               class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-300">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-2">
                            Jumlah Ikat
                        </label>
                        <input type="number" name="jumlah_ikat" min="1"
                               value="{{ old('jumlah_ikat') }}" placeholder="contoh: 80"
                               class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-300">
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-2">
                        Catatan (opsional)
                    </label>
                    <textarea name="catatan" rows="3"
                              placeholder="kondisi panen, kendala, dll..."
                              class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-300 resize-none">{{ old('catatan') }}</textarea>
                </div>

                <p class="text-xs text-gray-400 bg-gray-50 rounded-xl px-4 py-3">
                    💡 Health score & kualitas dihitung otomatis dari rata-rata sensor 30 hari terakhir.
                </p>

                {{-- Footer --}}
                <div class="flex gap-3 pt-1">
                    <button type="button"
                            @click="modalTambah = false"
                            class="flex-1 border border-gray-200 text-gray-600 font-semibold font-brand py-3 rounded-2xl hover:bg-gray-50 transition-colors text-sm">
                        Batal
                    </button>
                    <button type="submit"
                            class="flex-1 bg-gradient-to-r from-teal-600 to-green-500 hover:from-teal-700 hover:to-green-600 text-white font-bold font-brand py-3 rounded-2xl transition-all hover:shadow-lg hover:shadow-green-200 text-sm">
                        Simpan Panen
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>