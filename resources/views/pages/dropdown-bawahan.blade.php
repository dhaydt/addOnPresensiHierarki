<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan Atasan - Modern Design</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Livewire Styles -->
    @livewireStyles
</head>
<body class="bg-gray-100 min-h-screen">

    <!-- Navigation Header -->
    <nav class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <div class="flex-shrink-0 flex items-center">
                        <i class="fas fa-building text-blue-600 text-xl mr-3"></i>
                        <h1 class="text-xl font-semibold text-gray-900">Pengaturan Atasan Pegawai</h1>
                    </div>
                </div>

                <div class="flex items-center space-x-4">
                    <div class="bg-blue-600 text-white px-3 py-2 rounded-md text-sm font-medium">
                        <i class="fas fa-users-rectangle mr-1"></i>
                        Pengaturan Atasan
                    </div>
                    <a href="/hierarchy" class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                        <i class="fas fa-project-diagram mr-1"></i>
                        Hierarki Inertia
                    </a>
                    <a href="/superior-livewire" class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                        <i class="fas fa-users-cog mr-1"></i>
                        Hierarki Lama
                    </a>
                </div>
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div style="text-align: right;">
                        <p style="font-size: 14px; font-weight: 500; color: #111827; margin: 0;">{{ Auth::user()->name }}</p>
                        <p style="font-size: 12px; color: #6b7280; margin: 2px 0 0 0;">Administrator</p>
                    </div>
                    <div style="width: 40px; height: 40px; background: #2563eb; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-user"></i>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="bg-white shadow-xl rounded-xl overflow-hidden">
            @livewire('dropdown-bawahan')
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-white border-t mt-12">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="text-center text-gray-500 text-sm">
                © 2025 Sistem Presensi - Pengaturan Atasan Modern Design
            </div>
        </div>
    </footer>

    <!-- Livewire Scripts -->
    @stack('js')
    @livewireScripts

    <script>

    </script>
</body>
</html>
