<div x-show="modalEdit"
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     @click.self="modalEdit = false"
     class="fixed inset-0 z-50 bg-black/40 backdrop-blur-sm flex items-center justify-center p-4"
     style="display:none;">
    <div x-show="modalEdit"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="bg-white rounded-3xl shadow-2xl w-full max-w-lg overflow-hidden">

        <div class="bg-gradient-to-r from-blue-600 to-blue-400 px-7 py-6 relative">
            <p class="text-blue-100 text-xs font-bold uppercase tracking-widest mb-1">Edit Data Panen</p>
            <h3 class="font-brand font-extrabold text-white text-2xl"
                x-text="'Siklus ' + (editData?.siklus ?? '')"></h3>
            <button @click="modalEdit = false"
                    class="absolute top-5 right-5 text-white/70 hover:text-white transition-colors">
                <i class="bi bi-x-lg text-lg"></i>
            </button>
        </div>

        <div class="px-7 py-6">
            <form method="POST" :action="`/riwayat/${editData?.id}`" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-2">
                        Tanggal Panen
                    </label>
                    <input type="date" name="tanggal_panen"
                           :value="editData?.tanggal"
                           class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-2">
                            Berat Panen (kg)
                        </label>
                        <input type="number" name="berat_panen" step="0.1" min="0.1"
                               :value="editData?.beratRaw"
                               class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-2">
                            Jumlah Ikat
                        </label>
                        <input type="number" name="jumlah_ikat" min="1"
                               :value="editData?.jumlahIkat"
                               class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300">
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-2">
                        Catatan
                    </label>
                    <textarea name="catatan" rows="3"
                              x-text="editData?.catatanLengkap !== '-' ? editData?.catatanLengkap : ''"
                              class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300 resize-none"></textarea>
                </div>

                <div class="flex gap-3 pt-1">
                    <button type="button" @click="modalEdit = false"
                            class="flex-1 border border-gray-200 text-gray-600 font-semibold font-brand py-3 rounded-2xl hover:bg-gray-50 transition-colors text-sm">
                        Batal
                    </button>
                    <button type="submit"
                            class="flex-1 bg-gradient-to-r from-blue-600 to-blue-400 hover:from-blue-700 hover:to-blue-500 text-white font-bold font-brand py-3 rounded-2xl transition-all hover:shadow-lg text-sm">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>