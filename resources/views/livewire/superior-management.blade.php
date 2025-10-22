<div>
    @if (session()->has('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 fade-in">
            {{ session('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 fade-in">
            {{ session('error') }}
        </div>
    @endif

    <!-- Header -->
    <div style="background: white; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1); border-bottom: 1px solid #e5e7eb;">
        <div style="max-width: 1200px; margin: 0 auto; padding: 0 24px;">
            <div style="display: flex; justify-content: space-between; align-items: center; padding: 20px 0;">
                <div>
                    <h1 style="font-size: 28px; font-weight: 700; color: #111827; margin: 0;">Pengaturan Atasan Pegawai</h1>
                    <p style="color: #6b7280; margin: 4px 0 0 0; font-size: 14px;">{{ $dept->DeptName }}</p>
                </div>
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div style="text-align: right;">
                        <p style="font-size: 14px; font-weight: 500; color: #111827; margin: 0;">{{ $user->name }}</p>
                        <p style="font-size: 12px; color: #6b7280; margin: 2px 0 0 0;">Administrator</p>
                    </div>
                    <div style="width: 40px; height: 40px; background: #2563eb; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-user"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div style="max-width: 1200px; margin: 0 auto; padding: 32px 24px;">
        <!-- Stats Cards -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 24px; margin-bottom: 32px;">
            <div class="card-hover transition-all" style="background: white; border-radius: 12px; padding: 24px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1); border: 1px solid #e5e7eb;">
                <div style="display: flex; align-items: center;">
                    <div style="padding: 12px; border-radius: 12px; background: #dbeafe; color: #2563eb;">
                        <i class="fas fa-users" style="font-size: 20px;"></i>
                    </div>
                    <div style="margin-left: 16px;">
                        <p style="font-size: 14px; font-weight: 500; color: #6b7280; margin: 0;">Total Pegawai</p>
                        <p style="font-size: 32px; font-weight: 700; color: #111827; margin: 4px 0 0 0;">{{ collect($employees)->count() }}</p>
                    </div>
                </div>
            </div>

            <div class="card-hover transition-all" style="background: white; border-radius: 12px; padding: 24px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1); border: 1px solid #e5e7eb;">
                <div style="display: flex; align-items: center;">
                    <div style="padding: 12px; border-radius: 12px; background: #dcfce7; color: #059669;">
                        <i class="fas fa-user-check" style="font-size: 20px;"></i>
                    </div>
                    <div style="margin-left: 16px;">
                        <p style="font-size: 14px; font-weight: 500; color: #6b7280; margin: 0;">Sudah Ada Atasan</p>
                        <p style="font-size: 32px; font-weight: 700; color: #111827; margin: 4px 0 0 0;">{{ count($existingSuperiors) }}</p>
                    </div>
                </div>
            </div>

            <div class="card-hover transition-all" style="background: white; border-radius: 12px; padding: 24px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1); border: 1px solid #e5e7eb;">
                <div style="display: flex; align-items: center;">
                    <div style="padding: 12px; border-radius: 12px; background: #fed7aa; color: #d97706;">
                        <i class="fas fa-user-clock" style="font-size: 20px;"></i>
                    </div>
                    <div style="margin-left: 16px;">
                        <p style="font-size: 14px; font-weight: 500; color: #6b7280; margin: 0;">Belum Ada Atasan</p>
                        <p style="font-size: 32px; font-weight: 700; color: #111827; margin: 4px 0 0 0;">{{ collect($employees)->count() - count($existingSuperiors) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search -->
        <div style="background: white; border-radius: 12px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1); border: 1px solid #e5e7eb; margin-bottom: 24px;">
            <div style="padding: 24px; border-bottom: 1px solid #e5e7eb;">
                <div style="display: flex; align-items: center; justify-content: space-between;">
                    <h2 style="font-size: 20px; font-weight: 600; color: #111827; margin: 0;">Hierarki Pegawai</h2>
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <button wire:click="refreshData"
                                style="background: #059669; color: white; padding: 8px 12px; border-radius: 6px; border: none; cursor: pointer; font-size: 14px; display: flex; align-items: center; gap: 6px; transition: all 0.2s;"
                                onmouseover="this.style.background='#047857'"
                                onmouseout="this.style.background='#059669'">
                            <i class="fas fa-sync-alt"></i> Refresh
                        </button>
                        <button onclick="debugDragDrop()"
                                style="background: #dc2626; color: white; padding: 8px 12px; border-radius: 6px; border: none; cursor: pointer; font-size: 14px; display: flex; align-items: center; gap: 6px; transition: all 0.2s;"
                                onmouseover="this.style.background='#b91c1c'"
                                onmouseout="this.style.background='#dc2626'">
                            <i class="fas fa-bug"></i> Debug
                        </button>
                        <div style="position: relative;">
                            <input type="text" wire:model.live="search" placeholder="Cari pegawai..."
                                style="padding: 8px 12px 8px 40px; border: 1px solid #d1d5db; border-radius: 8px; width: 250px; outline: none; transition: all 0.2s;">
                            <i class="fas fa-search" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #9ca3af;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tree Hierarchy Display -->
        <div class="hierarchy-container" style="background: white; border-radius: 12px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1); border: 1px solid #e5e7eb;">
            <div style="padding: 24px; border-bottom: 1px solid #e5e7eb;">
                <h2 style="font-size: 20px; font-weight: 600; color: #111827; margin: 0; display: flex; align-items: center; gap: 8px;">
                    <i class="fas fa-sitemap" style="color: #2563eb;"></i>
                    Struktur Organisasi
                    <span style="font-size: 14px; font-weight: 400; color: #6b7280; background: #f3f4f6; padding: 2px 8px; border-radius: 12px;">({{ collect($employees)->count() }} pegawai)</span>
                </h2>
                <p style="margin: 8px 0 0 0; color: #6b7280; font-size: 14px;">
                    <i class="fas fa-info-circle"></i>
                    Drag dan drop pegawai untuk mengubah struktur hierarki. Drop ke area kosong untuk menjadikan level teratas.
                </p>
            </div>

            <div style="padding: 24px; font-family: 'Courier New', monospace; min-height: 200px;">
                @if(!empty($hierarchyTree))
                    @foreach($hierarchyTree as $item)
                        @include('livewire.partials.tree-item', ['item' => $item, 'isLast' => $loop->last, 'prefix' => ''])
                    @endforeach

                    <!-- Drop zone for top level -->
                    <div class="top-level-drop-zone" style="margin-top: 16px; padding: 16px; border: 2px dashed #d1d5db; border-radius: 8px; text-align: center; color: #6b7280; background: #f9fafb; opacity: 0; transition: all 0.3s;">
                        <i class="fas fa-level-up-alt"></i>
                        <span style="margin-left: 8px;">Drop di sini untuk menjadikan level teratas (tanpa atasan)</span>
                    </div>
                @else
                    <div style="text-align: center; padding: 48px; color: #6b7280;">
                        <i class="fas fa-users" style="font-size: 48px; margin-bottom: 16px; display: block;"></i>
                        <p style="font-size: 16px; margin: 0;">Belum ada struktur hierarki</p>
                        <p style="font-size: 14px; margin: 8px 0 0 0;">Mulai mengatur atasan untuk membuat struktur organisasi</p>
                    </div>
                @endif
            </div>
        </div>

        @if(count($hierarchyLevels) == 0)
            <div style="background: white; border-radius: 12px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1); border: 1px solid #e5e7eb; padding: 48px; text-align: center;">
                <i class="fas fa-users" style="font-size: 48px; color: #9ca3af; margin-bottom: 16px;"></i>
                <h3 style="font-size: 18px; font-weight: 500; color: #374151; margin: 0 0 8px 0;">Belum Ada Data Pegawai</h3>
                <p style="color: #6b7280; margin: 0;">Tidak ada pegawai yang ditemukan di departemen ini.</p>
            </div>
        @endif
    </div>

    <!-- Modal for Setting Superior -->
    @if($showModal)
        <div style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); z-index: 1000; display: flex; align-items: center; justify-content: center;"
             wire:click="closeModal">
            <div style="background: white; border-radius: 12px; padding: 24px; width: 400px; max-width: 90%; box-shadow: 0 25px 50px rgba(0, 0, 0, 0.25);"
                 wire:click.stop class="fade-in">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px;">
                    <h3 style="font-size: 20px; font-weight: 600; color: #111827; margin: 0;">Pilih Atasan</h3>
                    <button wire:click="closeModal" style="color: #9ca3af; background: none; border: none; font-size: 18px; cursor: pointer; padding: 4px;">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <div style="margin-bottom: 20px;">
                    <p style="font-size: 14px; color: #6b7280; margin: 0 0 4px 0;">Pegawai:</p>
                    <p style="font-weight: 500; color: #111827; margin: 0;">{{ $selectedEmployeeName }}</p>
                </div>

                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-size: 14px; font-weight: 500; color: #374151; margin-bottom: 8px;">Pilih Atasan</label>
                    <select wire:model="selectedSuperiorId" style="width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 8px; outline: none;">
                        <option value="">-- Pilih Atasan --</option>
                        @foreach($potentialSuperiors as $superior)
                            @if($superior->user && $superior->userid != $selectedEmployeeId)
                                <option value="{{ $superior->user->userinfo_id }}">{{ $superior->name }} (ID: {{ $superior->userid }})</option>
                            @endif
                        @endforeach
                    </select>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 12px;">
                    <button wire:click="closeModal" class="btn-secondary">
                        Batal
                    </button>
                    <button wire:click="saveSuperior" class="btn-primary">
                        <i class="fas fa-save"></i>
                        Simpan
                    </button>
                </div>
            </div>
        </div>
    @endif

    <style>
        .fade-in {
            animation: fadeIn 0.3s ease-in;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .card-hover:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        .transition-all {
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: #2563eb;
            color: white;
            padding: 8px 16px;
            border-radius: 8px;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s;
            border: none;
            cursor: pointer;
        }

        .btn-primary:hover {
            background: #1d4ed8;
            transform: translateY(-1px);
        }

        .btn-secondary {
            background: white;
            color: #374151;
            padding: 8px 16px;
            border-radius: 8px;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s;
            border: 1px solid #d1d5db;
            cursor: pointer;
        }

        .btn-secondary:hover {
            background: #f9fafb;
            transform: translateY(-1px);
        }

        /* Drag and Drop Styles */
        .tree-item.dragging {
            opacity: 0.5;
            transform: rotate(5deg);
            z-index: 1000;
        }

        .tree-item.drag-over {
            background: #dbeafe !important;
            border-color: #2563eb !important;
            transform: scale(1.02);
        }

        .drop-zone {
            background: linear-gradient(90deg, transparent, #bfdbfe, transparent);
            height: 4px;
            margin: 2px 0;
            border-radius: 2px;
            opacity: 0;
            transition: opacity 0.2s;
        }

        .drop-zone.active {
            opacity: 1;
        }

        .drag-handle:hover {
            color: #2563eb !important;
        }

        .drag-handle:active {
            cursor: grabbing !important;
        }

        /* Loading overlay styles */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }

        .loading-overlay.show {
            opacity: 1;
            visibility: visible;
        }

        .loading-content {
            background: white;
            border-radius: 12px;
            padding: 32px;
            text-align: center;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.25);
            max-width: 300px;
            width: 90%;
        }

        .loading-spinner {
            width: 48px;
            height: 48px;
            border: 4px solid #f3f4f6;
            border-top: 4px solid #2563eb;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 16px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .pulse {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
    </style>

    <script>
        console.log('Superior Management JavaScript loaded');

        // Debug function yang bisa dipanggil dari tombol
        function debugDragDrop() {
            console.log('=== DRAG DROP DEBUG ===');
            const treeItems = document.querySelectorAll('.tree-item');
            console.log('Tree items found:', treeItems.length);

            // Group by level
            const levels = {};
            treeItems.forEach((item, index) => {
                const level = item.dataset.level || 'unknown';
                if (!levels[level]) levels[level] = [];
                levels[level].push(item);

                console.log(`Item ${index} (Level ${level}):`, {
                    element: item,
                    id: item.id,
                    employeeName: item.dataset.employeeName,
                    employeeId: item.dataset.employeeId,
                    userId: item.dataset.userId,
                    level: item.dataset.level,
                    draggable: item.draggable,
                    classList: item.classList.toString(),
                    zIndex: window.getComputedStyle(item).zIndex,
                    position: item.getBoundingClientRect()
                });
            });

            console.log('Items by level:', levels);

            const dropZone = document.querySelector('.top-level-drop-zone');
            console.log('Top level drop zone:', dropZone);

            // Test event listeners on different levels
            Object.keys(levels).forEach(level => {
                const testItem = levels[level][0];
                if (testItem) {
                    console.log(`Testing drag on level ${level} item:`, testItem.dataset.employeeName);
                    console.log(`  - Has listeners attached:`, testItem.dataset.listenersAttached);
                    console.log(`  - Draggable:`, testItem.draggable);
                }
            });

            // Force re-initialization option
            console.log('💡 To force re-initialization, run: initializeDragAndDrop()');
        }

        // Make functions globally available
        window.debugDragDrop = debugDragDrop;
        window.forceReinitDragDrop = function() {
            console.log('🔧 Force re-initialization triggered...');
            // Clear all flags first
            document.querySelectorAll('.tree-item').forEach(item => {
                item.dataset.listenersAttached = 'false';
            });
            initializeDragAndDrop();
        };

        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM Content Loaded - Superior Management');

            let draggedElement = null;
            let draggedEmployeeId = null;
            let draggedEmployeeName = null;
            let dropHandled = false;

            // Loading functions
            function showLoading(message = 'Sedang memproses perubahan hierarki...') {
                const overlay = document.getElementById('loadingOverlay');
                const messageEl = document.getElementById('loadingMessage');
                if (overlay && messageEl) {
                    messageEl.textContent = message;
                    overlay.classList.add('show');
                }
            }

            function hideLoading() {
                const overlay = document.getElementById('loadingOverlay');
                if (overlay) {
                    overlay.classList.remove('show');
                }
            }

            // Listen for Livewire events
            document.addEventListener('livewire:init', function() {
                Livewire.on('loading-start', function(data) {
                    showLoading(data[0] || 'Sedang memproses...');
                });

                Livewire.on('loading-end', function() {
                    hideLoading();

                    // Re-initialize drag and drop after loading ends
                    console.log('🎯 Loading ended, re-initializing drag and drop...');
                    setTimeout(function() {
                        initializeDragAndDrop();
                    }, 10);
                });
            });

            // Add drag and drop event listeners
            function initializeDragAndDrop() {
                console.log('🚀 initializeDragAndDrop called');

                // First, clean up any existing listeners on old elements
                const oldItems = document.querySelectorAll('.tree-item[data-listeners-attached="true"]');
                console.log('Cleaning up old listeners from', oldItems.length, 'items');

                const treeItems = document.querySelectorAll('.tree-item');
                console.log('Found tree items:', treeItems.length);

                if (treeItems.length === 0) {
                    console.warn('⚠️ No tree items found! Check if .tree-item elements exist in DOM');
                    return;
                }

                // Mark that we're about to process these items
                console.log('🔧 Processing', treeItems.length, 'tree items...');

                treeItems.forEach((item, index) => {
                    const level = item.dataset.level || 'unknown';

                    // Skip if already processed
                    if (item.dataset.listenersAttached === 'true') {
                        console.log(`⏭️ Skipping item ${index} (Level ${level}):`, item.dataset.employeeName, '- already has listeners');
                        return;
                    }

                    console.log(`🔧 Setting up item ${index} (Level ${level}):`, item.dataset.employeeName, item);

                    // Test if draggable attribute is set
                    console.log(`Item ${index} draggable:`, item.draggable, 'Level:', level);

                    // Remove any existing listeners first (to avoid duplicates)
                    if (item._dragStartHandler) {
                        item.removeEventListener('dragstart', item._dragStartHandler);
                        item.removeEventListener('dragend', item._dragEndHandler);
                        item.removeEventListener('dragover', item._dragOverHandler);
                        item.removeEventListener('dragleave', item._dragLeaveHandler);
                        item.removeEventListener('drop', item._dropHandler);
                    }

                    // Drag start
                    const dragStartHandler = function(e) {
                        console.log('🔥 DRAG START EVENT FIRED:', this.dataset.employeeName, 'Level:', this.dataset.level);
                        draggedElement = this;
                        draggedEmployeeId = this.dataset.employeeId;
                        draggedEmployeeName = this.dataset.employeeName;
                        dropHandled = false;

                        this.classList.add('dragging');
                        e.dataTransfer.effectAllowed = 'move';
                        e.dataTransfer.setData('text/html', this.outerHTML);

                        // Create drag image
                        const dragImage = this.cloneNode(true);
                        dragImage.style.transform = 'rotate(5deg)';
                        dragImage.style.opacity = '0.8';
                        document.body.appendChild(dragImage);
                        e.dataTransfer.setDragImage(dragImage, 0, 0);
                        setTimeout(() => document.body.removeChild(dragImage), 0);
                    };

                    // Drag end handler
                    const dragEndHandler = function(e) {
                        console.log('🏁 Drag end:', this.dataset.employeeName, 'Level:', this.dataset.level);
                        this.classList.remove('dragging');

                        // Remove all drag over classes
                        document.querySelectorAll('.tree-item').forEach(el => {
                            el.classList.remove('drag-over');
                        });

                        // Reset variables with delay to ensure drop has time to execute
                        setTimeout(() => {
                            console.log('🔄 Resetting drag variables...');
                            draggedElement = null;
                            draggedEmployeeId = null;
                            draggedEmployeeName = null;
                            dropHandled = false;
                        }, 100);
                    };

                    // Drag over handler
                    const dragOverHandler = function(e) {
                        e.preventDefault();
                        e.stopPropagation(); // Prevent bubbling
                        e.dataTransfer.dropEffect = 'move';

                        if (this !== draggedElement) {
                            console.log('Drag over:', this.dataset.employeeName, 'Level:', this.dataset.level);
                            this.classList.add('drag-over');
                        }
                    };

                    // Drag leave handler
                    const dragLeaveHandler = function(e) {
                        this.classList.remove('drag-over');
                    };

                    // Drop handler
                    const dropHandler = function(e) {
                        console.log('🎯 DROP EVENT TRIGGERED on:', this.dataset.employeeName, 'Level:', this.dataset.level);
                        e.preventDefault();
                        e.stopPropagation(); // Prevent bubbling to container
                        this.classList.remove('drag-over');

                        if (this !== draggedElement && draggedEmployeeId && !dropHandled) {
                            dropHandled = true; // Mark as handled
                            const targetUserId = this.dataset.userId;
                            const targetEmployeeName = this.dataset.employeeName;
                            const targetLevel = this.dataset.level;

                            console.log('Drop details:', {
                                draggedEmployeeId: draggedEmployeeId,
                                draggedEmployeeName: draggedEmployeeName,
                                targetUserId: targetUserId,
                                targetEmployeeName: targetEmployeeName,
                                targetLevel: targetLevel,
                                element: this
                            });

                            if (targetUserId && confirm(`Apakah Anda yakin ingin memindahkan ${draggedEmployeeName} ke bawah ${targetEmployeeName} (Level ${targetLevel})?`)) {
                                console.log('User confirmed, calling moveTo...');
                                // Show loading
                                showLoading(`Memindahkan ${draggedEmployeeName} ke bawah ${targetEmployeeName}...`);

                                // Call Livewire method to move employee
                                Livewire.find('{{ $this->getId() }}').call('moveTo', draggedEmployeeId, targetUserId);
                            } else if (!targetUserId) {
                                console.error('Target user ID not found');
                                alert('Error: Target user ID tidak ditemukan');
                            } else {
                                console.log('User cancelled');
                                // User cancelled
                                dropHandled = false;
                            }
                        }
                    };

                    // Store handlers for cleanup
                    item._dragStartHandler = dragStartHandler;
                    item._dragEndHandler = dragEndHandler;
                    item._dragOverHandler = dragOverHandler;
                    item._dragLeaveHandler = dragLeaveHandler;
                    item._dropHandler = dropHandler;

                    // Attach all event listeners
                    item.addEventListener('dragstart', dragStartHandler);
                    item.addEventListener('dragend', dragEndHandler);
                    item.addEventListener('dragover', dragOverHandler);
                    item.addEventListener('dragleave', dragLeaveHandler);
                    item.addEventListener('drop', dropHandler);

                    // Mark this item as having listeners attached
                    item.dataset.listenersAttached = 'true';

                    console.log(`✅ All event listeners attached to ${item.dataset.employeeName} (Level ${item.dataset.level})`);
                });

                // Add drop zone for top level (no superior) - specific drop zone
                const topLevelDropZone = document.querySelector('.top-level-drop-zone');
                console.log('Top level drop zone found:', topLevelDropZone);

                if (topLevelDropZone) {
                    topLevelDropZone.addEventListener('dragover', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        e.dataTransfer.dropEffect = 'move';
                        this.style.opacity = '1';
                        this.style.borderColor = '#2563eb';
                        this.style.backgroundColor = '#dbeafe';
                    });

                    topLevelDropZone.addEventListener('dragleave', function(e) {
                        this.style.opacity = '0';
                        this.style.borderColor = '#d1d5db';
                        this.style.backgroundColor = '#f9fafb';
                    });

                    topLevelDropZone.addEventListener('drop', function(e) {
                        console.log('Drop on top level zone');
                        e.preventDefault();
                        e.stopPropagation();

                        if (!dropHandled && draggedEmployeeId) {
                            dropHandled = true;

                            this.style.opacity = '0';
                            this.style.borderColor = '#d1d5db';
                            this.style.backgroundColor = '#f9fafb';

                            if (confirm(`Apakah Anda yakin ingin memindahkan ${draggedEmployeeName} ke level teratas (tanpa atasan)?`)) {
                                console.log('User confirmed top level move, calling moveTo...');
                                // Show loading
                                showLoading(`Memindahkan ${draggedEmployeeName} ke level teratas...`);

                                Livewire.find('{{ $this->getId() }}').call('moveTo', draggedEmployeeId, null);
                            } else {
                                console.log('User cancelled top level move');
                                // User cancelled
                                dropHandled = false;
                            }
                        }
                    });
                }

                // Remove the container-level drop handling to prevent conflicts
                const hierarchyContainer = document.querySelector('.hierarchy-container');
                if (hierarchyContainer) {
                    // Add visual feedback when dragging over the container
                    hierarchyContainer.addEventListener('dragover', function(e) {
                        // Show drop zone if dragging over empty areas
                        const dropZone = this.querySelector('.top-level-drop-zone');
                        if (dropZone && !e.target.closest('.tree-item')) {
                            dropZone.style.opacity = '0.5';
                        }
                    });

                    hierarchyContainer.addEventListener('dragleave', function(e) {
                        const dropZone = this.querySelector('.top-level-drop-zone');
                        if (dropZone && !this.contains(e.relatedTarget)) {
                            dropZone.style.opacity = '0';
                        }
                    });
                }
            }

            // Initialize on page load and after Livewire updates
            console.log('Calling initializeDragAndDrop immediately...');
            initializeDragAndDrop();

            // Also try with a delay to ensure DOM is ready
            setTimeout(function() {
                console.log('Calling initializeDragAndDrop with delay...');
                initializeDragAndDrop();
            }, 500);

            // Re-initialize after Livewire updates with multiple attempts
            document.addEventListener('livewire:updated', function() {
                console.log('🔄 Livewire updated, re-initializing drag and drop...');

                // Multiple attempts to ensure DOM is ready
                setTimeout(function() {
                    console.log('🔄 First re-initialization attempt...');
                    initializeDragAndDrop();
                }, 50);

                setTimeout(function() {
                    console.log('🔄 Second re-initialization attempt...');
                    initializeDragAndDrop();
                }, 200);

                setTimeout(function() {
                    console.log('🔄 Final re-initialization attempt...');
                    initializeDragAndDrop();
                }, 500);
            });

            // Handle initial component load
            document.addEventListener('livewire:init', function() {
                console.log('Livewire init event fired');
                // Component is being initialized, show loading
                showLoading('Memuat data hierarki...');
            });

            // Handle when component is fully loaded
            document.addEventListener('livewire:navigated', function() {
                console.log('Livewire navigated event fired');
                // Navigation complete, but let the component control loading state
            });

            // Add click handlers for buttons that trigger loading
            document.addEventListener('click', function(e) {
                // Handle refresh button
                if (e.target.closest('[wire\\:click="refreshData"]')) {
                    showLoading('Memuat ulang data...');
                }

                // Handle save superior button in modal
                if (e.target.closest('[wire\\:click="saveSuperior"]')) {
                    showLoading('Menyimpan pengaturan atasan...');
                }

                // Handle start editing buttons
                if (e.target.closest('[wire\\:click*="startEditing"]')) {
                    showLoading('Memuat form edit...');
                }

                // Handle save inline edit button
                if (e.target.closest('[wire\\:click="saveInlineEdit"]')) {
                    showLoading('Menyimpan perubahan atasan...');
                }

                // Handle remove superior buttons
                if (e.target.closest('[wire\\:click*="removeSuperior"]')) {
                    showLoading('Menghapus hubungan atasan...');
                }

                // Handle open modal buttons
                if (e.target.closest('[wire\\:click*="openModal"]')) {
                    showLoading('Memuat modal pengaturan...');
                }
            });

        });
    </script>

    <!-- Loading Overlay -->
    <div id="loadingOverlay" class="loading-overlay">
        <div class="loading-content">
            <div class="loading-spinner"></div>
            <h3 style="font-size: 18px; font-weight: 600; color: #111827; margin: 0 0 8px 0;">Menyimpan Perubahan</h3>
            <p style="color: #6b7280; margin: 0; font-size: 14px;" id="loadingMessage">Sedang memproses perubahan hierarki...</p>
        </div>
    </div>

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
</div>
