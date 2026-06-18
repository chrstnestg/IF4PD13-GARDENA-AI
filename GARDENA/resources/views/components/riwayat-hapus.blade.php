<div x-show="modalHapus"
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     @click.self="modalHapus = false"
     class="fixed inset-0 z-50 bg-black/40 backdrop-blur-sm flex items-center justify-center p-4"
     style="display:none;">
    <div x-show="modalHapus"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="bg-white rounded-3xl shadow-2xl w-full max-w-sm overflow-hidden">

        <div class="px-7 py-8 text-center">
            <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="bi bi-trash3-fill text-red-500 text-2xl"></i>
            </div>
            <h3 class="font-brand font-bold text-gray-800 text-xl mb-2">Hapus Data Panen?</h3>
            <p class="text-sm text-gray-400 mb-6">Data yang dihapus tidak dapat dikembalikan.</p>

            <form method="POST" :action="`/riwayat/${hapusId}`">
                @csrf
                @method('DELETE')
                <div class="flex gap-3">
                    <button type="button" @click="modalHapus = false"
                            class="flex-1 border border-gray-200 text-gray-600 font-semibold font-brand py-3 rounded-2xl hover:bg-gray-50 transition-colors text-sm">
                        Batal
                    </button>
                    <button type="submit"
                            class="flex-1 bg-red-500 hover:bg-red-600 text-white font-bold font-brand py-3 rounded-2xl transition-colors text-sm">
                        Ya, Hapus
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>