<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan Atasan - Modern</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

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
                        <h1 class="text-xl font-semibold text-gray-900">Sistem Presensi</h1>
                    </div>
                </div>

                <div class="flex items-center space-x-4">
                    <a href="/dropdown_bawahan" class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                        <i class="fas fa-sitemap mr-1"></i>
                        Hierarki Lama
                    </a>
                    <a href="/hierarchy" class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                        <i class="fas fa-project-diagram mr-1"></i>
                        Hierarki Inertia
                    </a>
                    <a href="/superior-livewire" class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                        <i class="fas fa-users-cog mr-1"></i>
                        Atur Atasan
                    </a>
                    <div class="bg-blue-600 text-white px-3 py-2 rounded-md text-sm font-medium">
                        <i class="fas fa-users-rectangle mr-1"></i>
                        Modern
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="bg-white shadow-xl rounded-xl overflow-hidden">
            @livewire('superior-management')
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-white border-t mt-12">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="text-center text-gray-500 text-sm">
                © 2025 Sistem Presensi - Pengaturan Atasan Modern
            </div>
        </div>
    </footer>

    <!-- Livewire Scripts -->
    @livewireScripts

    <!-- Custom Scripts -->
    <script>
        // Handle loading states
        document.addEventListener('livewire:initialized', () => {
            console.log('Superior Management Modern - Livewire initialized');
        });

        // Auto-hide flash messages after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            const flashMessages = document.querySelectorAll('[x-data*="show: true"]');
            flashMessages.forEach(message => {
                setTimeout(() => {
                    if (message.getAttribute('x-show') === 'show') {
                        message.style.display = 'none';
                    }
                }, 5000);
            });
        });

        // Smooth scroll for better UX
        document.addEventListener('click', function(e) {
            if (e.target.closest('[wire\\:click]')) {
                // Add smooth loading effect
                document.body.style.cursor = 'wait';
                setTimeout(() => {
                    document.body.style.cursor = 'default';
                }, 1000);
            }
        });
    </script>
</body>
</html>
