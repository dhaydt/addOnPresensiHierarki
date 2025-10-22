<div>
    <!-- Loading Overlay -->
    <div
        x-data="{ loading: false, message: 'Memuat...' }"
        x-on:loading-start.window="loading = true; message = $event.detail || 'Memuat...'"
        x-on:loading-end.window="loading = false"
        x-show="loading"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50"
        style="display: none;"
    >
        <div class="bg-white rounded-lg p-6 shadow-xl flex items-center space-x-4">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
            <span x-text="message" class="text-gray-700 font-medium"></span>
        </div>
    </div>

    <!-- Header Section -->
    <div class="bg-gradient-to-r from-blue-600 to-blue-800 text-white rounded-t-xl p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold flex items-center gap-3">
                    <i class="fas fa-sitemap"></i>
                    Pengaturan Atasan
                </h1>
                <p class="text-blue-100 mt-1">{{ $dept->name ?? 'Departemen' }}</p>
            </div>
            <div class="text-right">
                <button
                    wire:click="refreshData"
                    class="bg-white/20 hover:bg-white/30 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center gap-2"
                >
                    <i class="fas fa-sync-alt"></i>
                    Refresh
                </button>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    @php
        $totalEmployees = $employees->count();
        $withSuperior = $existingSuperiors->count();
        $withoutSuperior = $totalEmployees - $withSuperior;
    @endphp

    <div class="bg-white p-6 border-b">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-gradient-to-r from-blue-50 to-blue-100 rounded-lg p-4 border border-blue-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-600 text-sm font-medium">Total Karyawan</p>
                        <p class="text-2xl font-bold text-blue-800">{{ $totalEmployees }}</p>
                    </div>
                    <div class="bg-blue-500 p-3 rounded-full">
                        <i class="fas fa-users text-white"></i>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-r from-green-50 to-green-100 rounded-lg p-4 border border-green-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-green-600 text-sm font-medium">Sudah Ada Atasan</p>
                        <p class="text-2xl font-bold text-green-800">{{ $withSuperior }}</p>
                    </div>
                    <div class="bg-green-500 p-3 rounded-full">
                        <i class="fas fa-user-check text-white"></i>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-r from-amber-50 to-amber-100 rounded-lg p-4 border border-amber-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-amber-600 text-sm font-medium">Belum Ada Atasan</p>
                        <p class="text-2xl font-bold text-amber-800">{{ $withoutSuperior }}</p>
                    </div>
                    <div class="bg-amber-500 p-3 rounded-full">
                        <i class="fas fa-user-clock text-white"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search Section -->
    <div class="bg-white p-6 border-b">
        <div class="max-w-md mx-auto">
            <div class="relative">
                <input
                    type="text"
                    wire:model.live="search"
                    placeholder="Cari karyawan..."
                    class="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                >
                <div class="absolute left-4 top-1/2 transform -translate-y-1/2">
                    <i class="fas fa-search text-gray-400"></i>
                </div>
                @if($search)
                    <button
                        wire:click="$set('search', '')"
                        class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600"
                    >
                        <i class="fas fa-times"></i>
                    </button>
                @endif
            </div>
        </div>
    </div>

    <!-- Main Content - Two Columns -->
    <div class="bg-white">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-0 min-h-[600px]">

            <!-- Left Column: Sudah Ada Atasan -->
            <div class="border-r border-gray-200">
                <div class="bg-gradient-to-r from-green-50 to-green-100 p-4 border-b border-green-200">
                    <h2 class="text-lg font-semibold text-green-800 flex items-center gap-2">
                        <i class="fas fa-user-check"></i>
                        Sudah Ada Atasan ({{ $withSuperior }})
                    </h2>
                </div>

                <div class="p-4 space-y-3 max-h-[500px] overflow-y-auto">
                    @forelse($existingSuperiors as $superior)
                        @php
                            $employee = $employees->firstWhere('userid', $superior->userinfo_id);
                            $superiorEmployee = $employees->firstWhere('user.userinfo_id', $superior->superior_id);
                        @endphp

                        @if($employee && (!$search || str_contains(strtolower($employee->name), strtolower($search))))
                            <div class="bg-white border border-green-200 rounded-lg p-4 hover:shadow-md transition-shadow duration-200">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <!-- Employee Info -->
                                        <div class="flex items-center gap-3 mb-2">
                                            <div class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center text-white font-semibold">
                                                {{ substr($employee->name, 0, 1) }}
                                            </div>
                                            <div>
                                                <h3 class="font-semibold text-gray-800">{{ $employee->name }}</h3>
                                                <p class="text-sm text-gray-500">ID: {{ $employee->userid }}</p>
                                            </div>
                                        </div>

                                        <!-- Superior Info -->
                                        <div class="bg-green-50 rounded-lg p-3 mt-2">
                                            <div class="flex items-center gap-2 text-sm">
                                                <i class="fas fa-arrow-up text-green-600"></i>
                                                <span class="text-gray-600">Atasan:</span>
                                                <span class="font-medium text-green-700">
                                                    {{ $superiorEmployee->name ?? 'Unknown' }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Actions -->
                                    <div class="flex flex-col gap-2 ml-4">
                                        <button
                                            wire:click="openModal({{ $employee->userid }}, '{{ $employee->name }}')"
                                            class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-2 rounded-lg text-sm transition-colors duration-200 flex items-center gap-1"
                                            title="Ubah Atasan"
                                        >
                                            <i class="fas fa-edit"></i>
                                            Ubah
                                        </button>

                                        <button
                                            wire:click="removeSuperior({{ $employee->userid }})"
                                            onclick="return confirm('Hapus hubungan atasan untuk {{ $employee->name }}?')"
                                            class="bg-red-500 hover:bg-red-600 text-white px-3 py-2 rounded-lg text-sm transition-colors duration-200 flex items-center gap-1"
                                            title="Hapus Atasan"
                                        >
                                            <i class="fas fa-trash"></i>
                                            Hapus
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @empty
                        <div class="text-center py-12">
                            <div class="text-gray-400 text-6xl mb-4">
                                <i class="fas fa-user-slash"></i>
                            </div>
                            <p class="text-gray-500 text-lg">Belum ada karyawan yang memiliki atasan</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Right Column: Belum Ada Atasan -->
            <div>
                <div class="bg-gradient-to-r from-amber-50 to-amber-100 p-4 border-b border-amber-200">
                    <h2 class="text-lg font-semibold text-amber-800 flex items-center gap-2">
                        <i class="fas fa-user-clock"></i>
                        Belum Ada Atasan ({{ $withoutSuperior }})
                    </h2>
                </div>

                <div class="p-4 space-y-3 max-h-[500px] overflow-y-auto">
                    @php
                        $employeesWithoutSuperior = $employees->reject(function($employee) {
                            return $existingSuperiors->has($employee->userid);
                        });
                    @endphp

                    @forelse($employeesWithoutSuperior as $employee)
                        @if(!$search || str_contains(strtolower($employee->name), strtolower($search)))
                            <div class="bg-white border border-amber-200 rounded-lg p-4 hover:shadow-md transition-shadow duration-200">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 bg-amber-500 rounded-full flex items-center justify-center text-white font-semibold">
                                            {{ substr($employee->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <h3 class="font-semibold text-gray-800">{{ $employee->name }}</h3>
                                            <p class="text-sm text-gray-500">ID: {{ $employee->userid }}</p>
                                            <div class="flex items-center gap-1 text-sm text-amber-600 mt-1">
                                                <i class="fas fa-crown"></i>
                                                <span>Level Teratas</span>
                                            </div>
                                        </div>
                                    </div>

                                    <button
                                        wire:click="openModal({{ $employee->userid }}, '{{ $employee->name }}')"
                                        class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center gap-2"
                                    >
                                        <i class="fas fa-plus"></i>
                                        Atur Atasan
                                    </button>
                                </div>
                            </div>
                        @endif
                    @empty
                        <div class="text-center py-12">
                            <div class="text-gray-400 text-6xl mb-4">
                                <i class="fas fa-users"></i>
                            </div>
                            <p class="text-gray-500 text-lg">Semua karyawan sudah memiliki atasan</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for Assign/Change Superior -->
    @if($showModal)
        <div class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-40" x-data x-transition>
            <div class="bg-white rounded-xl shadow-2xl max-w-lg w-full mx-4" x-transition:enter="transform transition ease-out duration-300" x-transition:enter-start="scale-90 opacity-0" x-transition:enter-end="scale-100 opacity-100">

                <!-- Modal Header -->
                <div class="bg-gradient-to-r from-blue-600 to-blue-800 text-white p-6 rounded-t-xl">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-xl font-semibold flex items-center gap-2">
                                <i class="fas fa-user-cog"></i>
                                Atur Atasan
                            </h3>
                            <p class="text-blue-100 mt-1">{{ $selectedEmployeeName }}</p>
                        </div>
                        <button
                            wire:click="closeModal"
                            class="text-blue-200 hover:text-white transition-colors duration-200"
                        >
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                </div>

                <!-- Modal Body -->
                <div class="p-6">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Pilih Atasan <span class="text-red-500">*</span>
                            </label>
                            <select
                                wire:model="selectedSuperiorId"
                                class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            >
                                <option value="">-- Pilih Atasan --</option>
                                @foreach($potentialSuperiors as $potential)
                                    @if($potential->user && $potential->userid != $selectedEmployeeId)
                                        <option value="{{ $potential->user->userinfo_id }}">
                                            {{ $potential->name }} (ID: {{ $potential->userid }})
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                            @error('selectedSuperiorId')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Warning for circular dependency -->
                        <div class="bg-amber-50 border border-amber-200 rounded-lg p-4">
                            <div class="flex items-start gap-2">
                                <i class="fas fa-exclamation-triangle text-amber-500 mt-0.5"></i>
                                <div class="text-sm">
                                    <p class="font-medium text-amber-800">Perhatian:</p>
                                    <p class="text-amber-700">Sistem akan mencegah pembuatan hierarki melingkar secara otomatis.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="bg-gray-50 px-6 py-4 rounded-b-xl flex justify-end gap-3">
                    <button
                        wire:click="closeModal"
                        class="px-4 py-2 text-gray-600 hover:text-gray-800 transition-colors duration-200"
                    >
                        Batal
                    </button>
                    <button
                        wire:click="saveSuperior"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition-colors duration-200 flex items-center gap-2"
                    >
                        <i class="fas fa-save"></i>
                        Simpan
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Flash Messages -->
    @if (session()->has('success'))
        <div x-data="{ show: true }" x-show="show" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0" x-init="setTimeout(() => show = false, 5000)" class="fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50">
            <div class="flex items-center gap-2">
                <i class="fas fa-check-circle"></i>
                {{ session('success') }}
            </div>
        </div>
    @endif

    @if (session()->has('error'))
        <div x-data="{ show: true }" x-show="show" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0" x-init="setTimeout(() => show = false, 7000)" class="fixed bottom-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg z-50">
            <div class="flex items-center gap-2">
                <i class="fas fa-exclamation-circle"></i>
                {{ session('error') }}
            </div>
        </div>
    @endif
</div>

<style>
    /* Custom scrollbar */
    .max-h-\[500px\]::-webkit-scrollbar {
        width: 6px;
    }

    .max-h-\[500px\]::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 3px;
    }

    .max-h-\[500px\]::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 3px;
    }

    .max-h-\[500px\]::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
</style>
