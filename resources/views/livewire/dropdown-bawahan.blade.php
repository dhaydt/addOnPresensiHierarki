<div>

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

        /* Modal animations */
        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        @keyframes scaleIn {
            from {
                opacity: 0;
                transform: scale(0.9);
            }

            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        .show {
            display: flex !important;
            justify-content: center;
            align-items: center;
        }

        .hidden {
            display: none !important;
        }

    </style>
    <!-- Header Section -->
    <div class="bg-gradient-to-r from-blue-600 to-blue-800 text-white rounded-t-xl p-6" wire:ignore.self>
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold flex items-center gap-3">
                    <i class="fas fa-sitemap"></i>
                    {{ $department->DeptName ?? 'Departemen' }}
                </h1>
                {{-- <p class="text-blue-100 mt-1">{{ $department->DeptName ?? 'Departemen' }}</p> --}}
            </div>
            <div class="text-right">
                <button wire:click="refreshData"
                    class="bg-white/20 hover:bg-white/30 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center gap-2">
                    <i class="fas fa-sync-alt" wire:loading.class="animate-spin" wire:target="refreshData"></i>
                    Refresh
                </button>
            </div>
        </div>
    </div>

    <!-- Loading Indicator -->
    <div wire:loading wire:loading.class="show" wire:loading.remove.class="hidden"
        class="fixed inset-0 bg-gray-900 bg-opacity-30 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-4 shadow-xl flex items-center space-x-3">
            <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-600"></div>
            <span class="text-gray-700 font-medium">Memproses...</span>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="bg-white p-6 border-b">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-gradient-to-r from-blue-50 to-blue-100 rounded-lg p-4 border border-blue-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-600 text-sm font-medium">Total Karyawan</p>
                        <p class="text-2xl font-bold text-blue-800">{{ $stats['totalEmployees'] }}</p>
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
                        <p class="text-2xl font-bold text-green-800">{{ $stats['withSuperior'] }}</p>
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
                        <p class="text-2xl font-bold text-amber-800">{{ $stats['withoutSuperior'] }}</p>
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
                <input type="text" wire:model.live="search" placeholder="Cari pegawai..."
                    class="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200">
                <div class="absolute left-4 top-1/2 transform -translate-y-1/2">
                    <i class="fas fa-search text-gray-400"></i>
                </div>
                @if($search)
                <button wire:click="$set('search', '')"
                    class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600">
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
                        Sudah Ada Atasan ({{ $stats['withSuperior'] }})
                    </h2>
                </div>

                <div class="p-4 space-y-3 max-h-[95vh] overflow-y-auto">
                    @forelse($employeesWithSuperior as $superior)
                    @php
                    $employee = $employees->firstWhere('userid', $superior->userinfo_id);
                    $superiorEmployee = $employees->firstWhere('user.userinfo_id', $superior->superior_id);
                    @endphp

                    @if($employee)
                    <div
                        class="bg-white border border-green-200 rounded-lg p-4 hover:shadow-md transition-shadow duration-200">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <!-- Employee Info -->
                                <div class="flex items-center gap-3 mb-2">
                                    <div
                                        class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center text-white font-semibold">
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
                                <button onclick="openModal('{{ $employee->userid }}')"
                                    class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-2 rounded-lg text-sm transition-colors duration-200 flex items-center gap-1"
                                    title="Ubah Atasan">
                                    <i class="fas fa-edit"></i>
                                    Ubah
                                </button>

                                <button
                                    onclick="confirmDelete('{{ $employee->userid }}', '{{ addslashes($employee->name) }}')"
                                    class="bg-red-500 hover:bg-red-600 text-white px-3 py-2 rounded-lg text-sm transition-colors duration-200 flex items-center gap-1"
                                    title="Hapus Atasan">
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
                        Belum Ada Atasan ({{ $stats['withoutSuperior'] }})
                    </h2>
                </div>

                <div class="p-4 space-y-3 max-h-[95vh] overflow-y-auto">
                    @forelse($employeesWithoutSuperior as $employee)
                    <div
                        class="bg-white border border-amber-200 rounded-lg p-4 hover:shadow-md transition-shadow duration-200">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-10 h-10 bg-amber-500 rounded-full flex items-center justify-center text-white font-semibold">
                                    {{ substr($employee->name, 0, 1) }}
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-800">{{ $employee->name }}</h3>
                                    <p class="text-sm text-gray-500">ID: {{ $employee->userid }}</p>
                                    <div class="flex items-center gap-1 text-sm text-amber-600 mt-1">
                                        <i class="fas fa-user-clock"></i>
                                        <span>Belum ada atasan</span>
                                    </div>
                                </div>
                            </div>

                            <button onclick="openModal('{{ $employee->userid }}')"
                                class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center gap-2">
                                <i class="fas fa-plus"></i>
                                <span wire:loading.remove>Atur Atasan</span>
                                <span wire:loading>Memuat...</span>
                            </button>
                        </div>
                    </div>
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
    @if($showAssignModal)
    <div id="assignModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-40"
        style="animation: fadeIn 0.3s ease-out;">
        <div class="bg-white rounded-xl shadow-2xl max-w-lg w-full mx-4" style="animation: scaleIn 0.3s ease-out;">

            <!-- Modal Header -->
            <div class="bg-gradient-to-r from-blue-600 to-blue-800 text-white p-6 rounded-t-xl">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-xl font-semibold flex items-center gap-2">
                            <i class="fas fa-user-cog"></i>
                            Atur Atasan
                        </h3>
                        @php
                        $selectedEmployeeData = $employees->firstWhere('userid', $selectedEmployee);
                        @endphp
                        <p class="text-blue-100 mt-1">{{ $selectedEmployeeData->name ?? 'Employee' }}</p>
                    </div>
                    <button onclick="closeModal()" type="button"
                        class="text-blue-200 hover:text-white transition-colors duration-200">
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
                        <select wire:model="selectedSuperior"
                            class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">-- Pilih Atasan --</option>
                            @if($potentialSuperiors)
                            @foreach($potentialSuperiors as $potential)
                            @if($potential->user && $potential->userid != $selectedEmployee)
                            <option value="{{ $potential->user->userinfo_id }}">
                                {{ $potential->name }} (ID: {{ $potential->userid }})
                            </option>
                            @endif
                            @endforeach
                            @endif
                        </select>
                        @error('selectedSuperior')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Warning for circular dependency -->
                    <div class="bg-amber-50 border border-amber-200 rounded-lg p-4">
                        <div class="flex items-start gap-2">
                            <i class="fas fa-exclamation-triangle text-amber-500 mt-0.5"></i>
                            <div class="text-sm">
                                <p class="font-medium text-amber-800">Perhatian:</p>
                                <p class="text-amber-700">Sistem akan mencegah pembuatan hierarki melingkar secara
                                    otomatis.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="bg-gray-50 px-6 py-4 rounded-b-xl flex justify-end gap-3">
                <button id="btnCloseModal" type="button"
                    class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors duration-200 flex items-center gap-2"
                    onclick="closeModal()">
                    <i class="fas fa-times"></i>
                    Batal
                </button>
                <button wire:click="assignSuperior" type="button"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition-colors duration-200 flex items-center gap-2"
                    wire:loading.attr="disabled" wire:target="assignSuperior">
                    <i class="fas fa-save"></i>
                    <span wire:loading.remove wire:target="assignSuperior">Simpan</span>
                    <span wire:loading wire:target="assignSuperior">Menyimpan...</span>
                </button>
            </div>
        </div>
    </div>
    @endif

    <!-- Flash Messages - Now handled by SweetAlert2 in parent page -->
</div>
@push('js')
<script>
    function closeModal(){
            @this.set('showAssignModal', false);
            @this.set('showRemoveModal', false);
            @this.set('selectedEmployee', null);
            @this.set('selectedSuperior', null);
        }

        function openModal(employeeId) {
            @this.set('selectedEmployee', employeeId);
            @this.set('selectedSuperior', null);
            @this.set('showAssignModal', true);
        }
        // Modal Close Handler
        document.addEventListener('DOMContentLoaded', function() {
            // Success message from PHP session
            Livewire.on('successMessage', function(msg) {
                Swal.fire({
                    title: 'Berhasil!',
                    text: msg,
                    icon: 'success',
                    confirmButtonColor: '#10b981',
                    confirmButtonText: 'OK',
                    timer: 3000,
                    timerProgressBar: true,
                    customClass: {
                        popup: 'rounded-xl',
                        confirmButton: 'rounded-lg px-4 py-2'
                    }
                });
            })

            Livewire.on('errorMessage', function(msg) {
                Swal.fire({
                    title: 'Gagal!',
                    text: msg,
                    icon: 'error',
                    confirmButtonColor: '#ef4444',
                    confirmButtonText: 'OK',
                    customClass: {
                        popup: 'rounded-xl',
                        confirmButton: 'rounded-lg px-4 py-2'
                    }
                });
            })

            // Close modal when clicking backdrop
            const modal = document.getElementById('assignModal');
            if (modal) {
                modal.addEventListener('click', function(e) {
                    if (e.target === modal) {
                        closeModal();
                    }
                });
            }

            // Close modal when clicking close button
            const btnClose = document.getElementById('btnCloseModal');
            if (btnClose) {
                btnClose.addEventListener('click', function() {
                    closeModal();
                });
            }

            // Close modal when pressing ESC key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && document.getElementById('assignModal')) {
                    closeModal();
                }
            });
        });

        // Re-attach event listeners after Livewire updates
        document.addEventListener('livewire:navigated', function() {
            const modal = document.getElementById('assignModal');
            if (modal) {
                modal.addEventListener('click', function(e) {
                    if (e.target === modal) {
                        closeModal();
                    }
                });
            }

            const btnClose = document.getElementById('btnCloseModal');
            if (btnClose) {
                btnClose.addEventListener('click', function() {
                    closeModal();
                });
            }
        });

         // SweetAlert2 Confirm Delete Function
        function confirmDelete(employeeId, employeeName) {
            Swal.fire({
                title: 'Konfirmasi Hapus',
                html: `Apakah Anda yakin ingin menghapus hubungan atasan untuk:<br><strong>${employeeName}</strong>?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: '<i class="fas fa-trash mr-2"></i>Ya, Hapus!',
                cancelButtonText: '<i class="fas fa-times mr-2"></i>Batal',
                reverseButtons: true,
                customClass: {
                    popup: 'rounded-xl',
                    confirmButton: 'rounded-lg px-4 py-2',
                    cancelButton: 'rounded-lg px-4 py-2'
                },
                buttonsStyling: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // Call Livewire method
                    @this.call('removeSuperior', employeeId);

                    // Show processing message
                    Swal.fire({
                        title: 'Menghapus...',
                        text: 'Mohon tunggu sebentar',
                        icon: 'info',
                        showConfirmButton: false,
                        allowOutsideClick: false,
                        timer: 1500,
                        customClass: {
                            popup: 'rounded-xl'
                        }
                    });
                }
            });
        }
</script>
@endpush
