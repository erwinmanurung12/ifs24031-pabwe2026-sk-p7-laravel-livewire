<div>
    <!-- Top Navigation Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div class="flex items-center space-x-3">
            <a href="{{ route('stocks.index') }}" aria-label="Kembali ke Daftar Stok" class="p-2 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-200 transition" title="Kembali ke Daftar Stok">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <div>
                <div class="flex items-center space-x-2.5">
                    <h1 class="text-2xl font-bold text-slate-900 truncate max-w-md sm:max-w-xl">{{ $stock->name }}</h1>
                    <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full border {{ \App\Features\Stock\Enums\StockStatus::tryFrom($stock->status)?->badgeClass() ?? 'bg-slate-100 text-slate-700' }}">
                        {{ \App\Features\Stock\Enums\StockStatus::tryFrom($stock->status)?->label() ?? ucfirst($stock->status) }}
                    </span>
                </div>
                <p class="text-xs text-slate-600 mt-0.5">SKU: <span class="font-mono">{{ $stock->sku ?? '-' }}</span> &bull; Dibuat pada {{ $stock->created_at ? $stock->created_at->format('d M Y H:i') : '-' }}</p>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <!-- Adjust Quantity Controls -->
            <div class="inline-flex items-center rounded-lg border border-slate-200 bg-white p-1 shadow-sm">
                <button type="button" wire:click="adjustQuantity(-1)" aria-label="Kurangi stok" title="Kurangi stok" class="px-2.5 py-1 text-xs font-bold text-slate-700 hover:bg-rose-50 hover:text-rose-700 rounded transition">−</button>
                <span class="px-3 text-xs font-bold text-slate-900">{{ $stock->quantity }} {{ $stock->unit }}</span>
                <button type="button" wire:click="adjustQuantity(1)" aria-label="Tambah stok" title="Tambah stok" class="px-2.5 py-1 text-xs font-bold text-slate-700 hover:bg-emerald-50 hover:text-emerald-700 rounded transition">+</button>
            </div>

            <!-- Edit Stock Button -->
            <button wire:click="openEditModal" wire:loading.attr="disabled" class="inline-flex items-center space-x-1.5 px-4 py-2 bg-blue-600 hover:bg-blue-700 disabled:opacity-75 text-white text-xs font-semibold rounded-lg shadow-sm transition">
                <span>Edit Stok</span>
            </button>

            <!-- Delete Button -->
            <button wire:click="confirmDeleteStock" wire:loading.attr="disabled" class="inline-flex items-center space-x-1 px-3 py-2 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 text-xs font-semibold rounded-lg transition disabled:opacity-75">
                <span>Hapus</span>
            </button>
        </div>
    </div>

    <!-- Cover Stock Section -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden mb-8">
        <label for="coverPhotoInput" class="sr-only">Upload Foto Cover Barang</label>
        <input type="file" wire:model="coverPhoto" id="coverPhotoInput" aria-label="Upload Foto Cover Barang" accept="image/*" class="hidden">

        @if ($stock->cover_image_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($stock->cover_image_path))
            <div class="relative group h-64 sm:h-80 w-full bg-slate-900 overflow-hidden">
                <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($stock->cover_image_path) }}?v={{ time() }}" alt="Cover Stok" class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-slate-900/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center space-x-3">
                    <button type="button" onclick="document.getElementById('coverPhotoInput').click()" class="px-4 py-2 bg-white text-slate-800 text-xs font-semibold rounded-xl shadow-lg hover:bg-slate-50 transition">
                        <span>Ganti Cover</span>
                    </button>
                    <button type="button" wire:click="confirmDeleteCover" class="px-4 py-2 bg-rose-600 text-white text-xs font-semibold rounded-xl shadow-lg hover:bg-rose-700 transition">
                        <span>Hapus Cover</span>
                    </button>
                </div>
            </div>
        @else
            <div class="p-8 text-center bg-gradient-to-b from-slate-50 to-white flex flex-col items-center justify-center border-b border-slate-100">
                <h2 class="text-sm font-bold text-slate-800">Tambahkan Foto Cover Barang</h2>
                <p class="text-xs text-slate-600 mt-1 max-w-sm">Mendukung format gambar PNG, JPG, GIF hingga ukuran 5MB.</p>
                <button type="button" onclick="document.getElementById('coverPhotoInput').click()" class="mt-4 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-xl shadow-sm transition">
                    <span>Upload Cover Gambar</span>
                </button>
            </div>
        @endif

        @error('coverPhoto')
            <div class="p-3 bg-rose-50 border-t border-rose-100 text-center text-xs text-rose-700 font-medium">
                {{ $message }}
            </div>
        @enderror
    </div>

    <!-- Stock View Mode Details -->
    <div class="space-y-6">
        <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
            <div class="bg-white p-4 rounded-xl shadow-sm border border-slate-200">
                <span class="text-xs font-semibold text-slate-600 uppercase tracking-wider block mb-1">Status Stok</span>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold border {{ \App\Features\Stock\Enums\StockStatus::tryFrom($stock->status)?->badgeClass() ?? 'bg-slate-100 text-slate-700' }}">
                    {{ \App\Features\Stock\Enums\StockStatus::tryFrom($stock->status)?->label() ?? ucfirst($stock->status) }}
                </span>
            </div>

            <div class="bg-white p-4 rounded-xl shadow-sm border border-slate-200">
                <span class="text-xs font-semibold text-slate-600 uppercase tracking-wider block mb-1">Jumlah / Min. Stok</span>
                <div class="text-sm font-semibold text-slate-800">
                    {{ $stock->quantity }} {{ $stock->unit }} (Min: {{ $stock->min_stock }})
                </div>
            </div>

            <div class="bg-white p-4 rounded-xl shadow-sm border border-slate-200">
                <span class="text-xs font-semibold text-slate-600 uppercase tracking-wider block mb-1">Harga Satuan</span>
                <div class="text-sm font-semibold text-slate-800">
                    {{ $stock->price !== null ? 'Rp ' . number_format((float) $stock->price, 0, ',', '.') : 'Tidak ditentukan' }}
                </div>
            </div>

            <div class="bg-white p-4 rounded-xl shadow-sm border border-slate-200">
                <span class="text-xs font-semibold text-slate-600 uppercase tracking-wider block mb-1">Terakhir Diperbarui</span>
                <div class="text-sm font-semibold text-slate-800">
                    {{ $stock->updated_at ? $stock->updated_at->format('d M Y H:i') : '-' }}
                </div>
            </div>
        </div>

        <!-- Description Viewer Section -->
        <div class="bg-white p-6 sm:p-8 rounded-2xl shadow-sm border border-slate-200">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-6">
                <div>
                    <h2 class="text-lg font-bold text-slate-900">Deskripsi Barang</h2>
                    <p class="text-xs text-slate-600 mt-0.5">Diformat dengan Toast UI Editor / Markdown</p>
                </div>
                <button wire:click="openEditModal" class="inline-flex items-center space-x-1 px-3 py-1.5 text-xs font-semibold text-blue-700 hover:text-blue-800 hover:bg-blue-50 border border-blue-200 rounded-lg transition">
                    <span>Ubah Deskripsi</span>
                </button>
            </div>

            @if (!empty($stock->description))
                <div class="prose prose-slate max-w-none text-slate-800 text-sm leading-relaxed space-y-2">
                    {!! \Illuminate\Support\Str::markdown($stock->description) !!}
                </div>
            @else
                <div class="py-12 text-center text-slate-600">
                    <p class="text-sm font-medium text-slate-600">Belum ada deskripsi untuk barang ini</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Edit Stock Modal -->
    @if ($showEditModal)
        <div x-data="{ show: true }" x-show="show" x-cloak
            class="modal-scroll-lock fixed inset-0 z-50 flex items-center justify-center overflow-y-auto overscroll-contain p-4 bg-slate-900/50 backdrop-blur-sm">
            <div class="bg-white rounded-2xl max-w-2xl w-full shadow-2xl border border-slate-200 max-h-[90vh] overflow-hidden flex flex-col">
                <div class="flex items-center justify-between px-6 py-4 bg-blue-600">
                    <h2 class="text-lg font-bold text-white">Ubah Data Stok Barang</h2>
                    <button type="button" aria-label="Tutup modal" @click="show = false; $wire.closeEditModal()" class="text-white/80 hover:text-white transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form wire:submit.prevent="saveStock" class="flex flex-col flex-1 min-h-0">
                    <div class="space-y-4 px-6 py-5 overflow-y-auto flex-1">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="detail-edit-name" class="block text-sm font-semibold text-slate-800 mb-1">Nama Barang</label>
                                <input id="detail-edit-name" wire:model="name" type="text" class="w-full px-4 py-2 text-sm font-medium rounded-xl border border-slate-300" required>
                                @error('name') <span class="text-xs text-rose-700 mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label for="detail-edit-sku" class="block text-sm font-semibold text-slate-800 mb-1">Kode SKU</label>
                                <input id="detail-edit-sku" wire:model="sku" type="text" class="w-full px-4 py-2 text-sm font-mono rounded-xl border border-slate-300">
                                @error('sku') <span class="text-xs text-rose-700 mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                            <div>
                                <label for="detail-edit-quantity" class="block text-sm font-semibold text-slate-800 mb-1">Quantity</label>
                                <input id="detail-edit-quantity" wire:model="quantity" type="number" min="0" class="w-full px-3.5 py-2 text-sm rounded-xl border border-slate-300" required>
                                @error('quantity') <span class="text-xs text-rose-700 mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label for="detail-edit-unit" class="block text-sm font-semibold text-slate-800 mb-1">Satuan</label>
                                <input id="detail-edit-unit" wire:model="unit" type="text" class="w-full px-3.5 py-2 text-sm rounded-xl border border-slate-300" required>
                                @error('unit') <span class="text-xs text-rose-700 mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label for="detail-edit-min-stock" class="block text-sm font-semibold text-slate-800 mb-1">Min. Stok</label>
                                <input id="detail-edit-min-stock" wire:model="minStock" type="number" min="0" class="w-full px-3.5 py-2 text-sm rounded-xl border border-slate-300" required>
                                @error('minStock') <span class="text-xs text-rose-700 mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label for="detail-edit-status" class="block text-sm font-semibold text-slate-800 mb-1">Status</label>
                                <select id="detail-edit-status" wire:model="status" class="w-full px-3.5 py-2 text-sm rounded-xl border border-slate-300 bg-white">
                                    @foreach ($statuses as $st)
                                        <option value="{{ $st->value }}">{{ $st->label() }}</option>
                                    @endforeach
                                </select>
                                @error('status') <span class="text-xs text-rose-700 mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div>
                            <label for="detail-edit-price" class="block text-sm font-semibold text-slate-800 mb-1">Harga Satuan (Rp)</label>
                            <input id="detail-edit-price" wire:model="price" type="number" min="0" step="0.01" class="w-full px-4 py-2 text-sm rounded-xl border border-slate-300">
                            @error('price') <span class="text-xs text-rose-700 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="detail-edit-description" class="block text-sm font-semibold text-slate-800 mb-1">Deskripsi Barang</label>
                            <textarea id="detail-edit-description" wire:model="description" rows="4" class="w-full px-4 py-2 text-sm rounded-xl border border-slate-300"></textarea>
                            @error('description') <span class="text-xs text-rose-700 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 px-6 py-4 bg-blue-50 border-t border-blue-100">
                        <button type="button" @click="show = false; $wire.closeEditModal()" class="px-4 py-2 text-sm font-semibold text-slate-600 bg-white border border-slate-200 rounded-xl">
                            Batal
                        </button>
                        <button type="submit" class="px-6 py-2 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-xl shadow-sm">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Modal Konfirmasi Hapus Stok -->
    @if ($confirmingStockDeletion)
        <div x-data="{ show: true }" x-show="show" x-cloak
            class="modal-scroll-lock fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm">
            <div class="bg-white rounded-2xl max-w-md w-full shadow-2xl border border-slate-200 overflow-hidden">
                <div class="px-6 py-4 bg-rose-600 text-white font-bold">Konfirmasi Hapus Stok</div>
                <div class="px-6 py-5 text-sm text-slate-600">Apakah Anda yakin ingin menghapus stok barang ini secara permanen?</div>
                <div class="flex justify-end gap-3 px-6 py-4 bg-rose-50 border-t border-rose-100">
                    <button type="button" @click="show = false; $wire.cancelDeleteStock()" class="px-4 py-2 text-xs font-semibold text-slate-600 bg-white border border-slate-200 rounded-lg">Batal</button>
                    <button type="button" wire:click="deleteStock" class="px-4 py-2 bg-rose-600 text-white text-xs font-semibold rounded-lg">Ya, Hapus Stok</button>
                </div>
            </div>
        </div>
    @endif

    <!-- Modal Konfirmasi Hapus Cover Photo -->
    @if ($confirmingCoverDeletion)
        <div x-data="{ show: true }" x-show="show" x-cloak
            class="modal-scroll-lock fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm">
            <div class="bg-white rounded-2xl max-w-md w-full shadow-2xl border border-slate-200 overflow-hidden">
                <div class="px-6 py-4 bg-rose-600 text-white font-bold">Konfirmasi Hapus Cover</div>
                <div class="px-6 py-5 text-sm text-slate-600">Apakah Anda yakin ingin menghapus gambar cover dari stok ini?</div>
                <div class="flex justify-end gap-3 px-6 py-4 bg-rose-50 border-t border-rose-100">
                    <button type="button" @click="show = false; $wire.cancelDeleteCover()" class="px-4 py-2 text-xs font-semibold text-slate-600 bg-white border border-slate-200 rounded-lg">Batal</button>
                    <button type="button" wire:click="deleteCoverPhoto" class="px-4 py-2 bg-rose-600 text-white text-xs font-semibold rounded-lg">Ya, Hapus Cover</button>
                </div>
            </div>
        </div>
    @endif
</div>

