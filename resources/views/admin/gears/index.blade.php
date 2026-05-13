<x-app-layout>
    <!-- Modal Tambah Gear -->
    <div x-data="{ open: false }">
        <!-- Tombol Trigger -->
        <button @click="open = true" class="bg-slate-800 text-white px-6 py-3 rounded-2xl font-bold text-sm hover:bg-slate-700 transition mb-6">
            <i class="fa-solid fa-plus mr-2"></i> Tambah Gear Baru
        </button>

        <!-- Modal Background -->
        <div x-show="open" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <!-- Modal Content -->
            <div @click.away="open = false" class="bg-white w-full max-w-lg rounded-3xl shadow-2xl overflow-hidden">
                <form action="{{ route('admin.gears.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="p-6 space-y-4">
                        <h3 class="text-xl font-black text-slate-800">Tambah Unit Baru</h3>
                        
                        <!-- Kategori & Code -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Kategori</label>
                                <select name="category_id" id="category_select" class="w-full bg-slate-50 border-none rounded-xl text-sm focus:ring-2 focus:ring-slate-800">
                                    <option value="">Pilih Kategori</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}" data-prefix="{{ $cat->prefix }}">{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Unit Code</label>
                                <input type="text" name="unit_code" id="unit_code_input" readonly class="w-full bg-slate-100 border-none rounded-xl text-sm font-mono text-slate-500" placeholder="Auto-generated">
                            </div>
                        </div>

                        <!-- Nama Gear -->
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Nama Alat</label>
                            <input type="text" name="name" required class="w-full bg-slate-50 border-none rounded-xl text-sm focus:ring-2 focus:ring-slate-800">
                        </div>

                        <!-- Harga & Denda -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Harga Sewa / Hari</label>
                                <input type="number" name="rent_price" required class="w-full bg-slate-50 border-none rounded-xl text-sm">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Denda / Jam</label>
                                <input type="number" name="penalty_fee" required class="w-full bg-slate-50 border-none rounded-xl text-sm">
                            </div>
                        </div>

                        <!-- Upload Gambar -->
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Foto Unit</label>
                            <input type="file" name="image" class="text-xs file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-slate-800 file:text-white">
                        </div>
                    </div>

                    <div class="p-6 bg-slate-50 flex gap-3">
                        <button type="button" @click="open = false" class="flex-1 py-3 text-sm font-bold text-slate-500 hover:text-slate-700">Batal</button>
                        <button type="submit" class="flex-2 bg-teal-500 text-white px-8 py-3 rounded-xl font-bold text-sm hover:bg-teal-600 transition">Simpan Unit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Penjelasan tiap warna -->
    <div class="mb-4 mx-6 p-4 shadow-sm">
        <p class="text-sm text-slate-800">
            <span class="inline-block w-3 h-3 rounded-full bg-teal-100 mr-2"></span>
            Alat tersedia
            <span class="inline-block w-3 h-3 rounded-full bg-amber-100 mr-2 ml-4"></span>
            Alat sedang disewa
        </p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6 p-6">
        @foreach($gears as $gear)
        <div class="bg-white rounded-3xl shadow-sm border-2 overflow-hidden transition hover:shadow-md 
            {{ $gear->status == 'available' ? 'border-teal-100' : 'border-slate-100' }}">
            
            <!-- Header: Unit Code & Category -->
            <div class="p-4 flex justify-between items-start bg-slate-50/50">
                <div>
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">{{ $gear->category->name }}</span>
                    <h3 class="text-sm font-black text-slate-800">{{ $gear->unit_code }}</h3>
                </div>
                <span class="px-2 py-1 rounded-lg text-[9px] font-bold uppercase
                    {{ $gear->status == 'available' ? 'bg-teal-100 text-teal-600' : 'bg-amber-100 text-amber-600' }}">
                    {{ $gear->status }}
                </span>
            </div>

            <!-- Body: Image & Name -->
            <div class="p-4">
                <div class="aspect-video bg-slate-100 rounded-xl mb-3 flex items-center justify-center overflow-hidden">
                    @if($gear->image_path)
                        <img src="{{ asset('storage/' . $gear->image_path) }}" class="object-cover w-full h-full">
                    @else
                        <i class="fa-solid fa-camera text-slate-300 text-2xl"></i>
                    @endif
                </div>
                <p class="text-sm font-bold text-slate-700 truncate">{{ $gear->name }}</p>
                
                <!-- Condition Badge -->
                <div class="mt-2 flex items-center gap-2">
                    <div class="w-2 h-2 rounded-full {{ $gear->condition_status == 'baik' ? 'bg-teal-500' : 'bg-red-500' }}"></div>
                    <span class="text-[10px] font-bold text-slate-500 uppercase">{{ $gear->condition_status }}</span>
                </div>
            </div>

            <!-- Footer: Action Placeholder -->
            <div class="px-4 py-3 bg-slate-50 border-t border-slate-100">
                <p class="text-[9px] font-bold text-slate-400 uppercase mb-2">Set Kondisi:</p>
                <div class="flex gap-2">
                    <!-- Tombol Baik -->
                    <form action="{{ route('admin.gears.update-condition', $gear->id) }}" method="POST" class="flex-1">
                        @csrf @method('PATCH')
                        <input type="hidden" name="condition" value="baik">
                        <button type="submit" title="Set Baik" class="w-full py-2 rounded-lg border {{ $gear->condition_status == 'baik' ? 'bg-teal-500 text-white border-teal-500' : 'bg-white text-slate-400 border-slate-200' }}">
                            <i class="fa-solid fa-check text-[10px]"></i>
                        </button>
                    </form>

                    <!-- Tombol Maintenance -->
                    <form action="{{ route('admin.gears.update-condition', $gear->id) }}" method="POST" class="flex-1">
                        @csrf @method('PATCH')
                        <input type="hidden" name="condition" value="maintenance">
                        <button type="submit" title="Set Maintenance" class="w-full py-2 rounded-lg border {{ $gear->condition_status == 'maintenance' ? 'bg-amber-500 text-white border-amber-500' : 'bg-white text-slate-400 border-slate-200' }}">
                            <i class="fa-solid fa-screwdriver-wrench text-[10px]"></i>
                        </button>
                    </form>

                    <!-- Tombol Rusak -->
                    <form action="{{ route('admin.gears.update-condition', $gear->id) }}" method="POST" class="flex-1">
                        @csrf @method('PATCH')
                        <input type="hidden" name="condition" value="rusak">
                        <button type="submit" title="Set Rusak" class="w-full py-2 rounded-lg border {{ $gear->condition_status == 'rusak' ? 'bg-red-500 text-white border-red-500' : 'bg-white text-slate-400 border-slate-200' }}">
                            <i class="fa-solid fa-biohazard text-[10px]"></i>
                        </button>
                    </form>

                    <!-- Tombol Hilang -->
                    <form action="{{ route('admin.gears.update-condition', $gear->id) }}" method="POST" class="flex-1">
                        @csrf @method('PATCH')
                        <input type="hidden" name="condition" value="hilang">
                        <button type="submit" title="Set Hilang" class="w-full py-2 rounded-lg border {{ $gear->condition_status == 'hilang' ? 'bg-gray-500 text-white border-gray-500' : 'bg-white text-slate-400 border-slate-200' }}">
                            <i class="fa-solid fa-question text-[10px]"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    <!-- Script -->
     <script>
        document.getElementById('category_select').addEventListener('change', function() {
            const categoryId = this.value;
            if (!categoryId) return;

            // Panggil route yang menjalankan function generate unik kamu
            // Kamu bisa menggunakan fetch ke API khusus atau sesuaikan dengan logicmu
            fetch(`/admin/generate-gear-code/${categoryId}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('unit_code_input').value = data.code;
                });
        });
    </script>
</x-app-layout>