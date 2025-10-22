<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Pengaturan Atasan Pegawai - {{ $dept->DeptName }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
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

        .modal-backdrop {
            backdrop-filter: blur(4px);
        }
    </style>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#2563eb',
                        secondary: '#64748b',
                        success: '#059669',
                        warning: '#d97706',
                        danger: '#dc2626',
                    }
                }
            }
        }
    </script>
</head>
<body style="background: #f8fafc; min-height: 100vh; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
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
                        <p style="font-size: 32px; font-weight: 700; color: #111827; margin: 4px 0 0 0;">{{ $employees->count() }}</p>
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
                        <p style="font-size: 32px; font-weight: 700; color: #111827; margin: 4px 0 0 0;">{{ $existingSuperiors->count() }}</p>
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
                        <p style="font-size: 32px; font-weight: 700; color: #111827; margin: 4px 0 0 0;">{{ $employees->count() - $existingSuperiors->count() }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Employee Management Table -->
        <div style="background: white; border-radius: 12px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1); border: 1px solid #e5e7eb;">
            <div style="padding: 24px; border-bottom: 1px solid #e5e7eb;">
                <div style="display: flex; align-items: center; justify-content: space-between;">
                    <h2 style="font-size: 20px; font-weight: 600; color: #111827; margin: 0;">Daftar Pegawai</h2>
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <div style="position: relative;">
                            <input type="text" id="searchInput" placeholder="Cari pegawai..."
                                style="padding: 8px 12px 8px 40px; border: 1px solid #d1d5db; border-radius: 8px; width: 250px; outline: none; transition: all 0.2s;"
                                onfocus="this.style.borderColor='#2563eb'; this.style.boxShadow='0 0 0 3px rgba(37, 99, 235, 0.1)'"
                                onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'">
                            <i class="fas fa-search" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #9ca3af;"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead style="background: #f9fafb;">
                        <tr>
                            <th style="padding: 12px 24px; text-align: left; font-size: 12px; font-weight: 500; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">Pegawai</th>
                            <th style="padding: 12px 24px; text-align: left; font-size: 12px; font-weight: 500; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">Atasan Saat Ini</th>
                            <th style="padding: 12px 24px; text-align: left; font-size: 12px; font-weight: 500; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">Status</th>
                            <th style="padding: 12px 24px; text-align: right; font-size: 12px; font-weight: 500; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="employeeTableBody">
                        @foreach($employees as $employee)
                        @if($employee->user)
                        <tr class="employee-row" style="border-bottom: 1px solid #e5e7eb; transition: all 0.2s;"
                            onmouseover="this.style.backgroundColor='#f9fafb'"
                            onmouseout="this.style.backgroundColor='white'">
                            <td style="padding: 16px 24px;">
                                <div style="display: flex; align-items: center;">
                                    <div style="width: 40px; height: 40px; border-radius: 50%; background: #2563eb; color: white; display: flex; align-items: center; justify-content: center; font-weight: 500; font-size: 14px;">
                                        {{ strtoupper(substr($employee->name, 0, 2)) }}
                                    </div>
                                    <div style="margin-left: 16px;">
                                        <div class="employee-name" style="font-size: 14px; font-weight: 500; color: #111827;">{{ $employee->name }}</div>
                                        <div style="font-size: 14px; color: #6b7280;">ID: {{ $employee->userid }}</div>
                                    </div>
                                </div>
                            </td>
                            <td style="padding: 16px 24px;">
                                @if(isset($existingSuperiors[$employee->userid]))
                                    <div style="display: flex; align-items: center;">
                                        <div style="width: 32px; height: 32px; border-radius: 50%; background: #059669; color: white; display: flex; align-items: center; justify-content: center; font-weight: 500; font-size: 12px;">
                                            {{ strtoupper(substr($existingSuperiors[$employee->userid]->superior->name, 0, 2)) }}
                                        </div>
                                        <div style="margin-left: 12px;">
                                            <div style="font-size: 14px; font-weight: 500; color: #111827;">{{ $existingSuperiors[$employee->userid]->superior->name }}</div>
                                            <div style="font-size: 12px; color: #6b7280;">ID: {{ $existingSuperiors[$employee->userid]->superior_id }}</div>
                                        </div>
                                    </div>
                                @else
                                    <span style="font-size: 14px; color: #6b7280; font-style: italic;">Belum ada atasan</span>
                                @endif
                            </td>
                            <td style="padding: 16px 24px;">
                                @if(isset($existingSuperiors[$employee->userid]))
                                    <span style="display: inline-flex; align-items: center; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 500; background: #dcfce7; color: #166534; gap: 4px;">
                                        <i class="fas fa-check-circle"></i>
                                        Sudah Diatur
                                    </span>
                                @else
                                    <span style="display: inline-flex; align-items: center; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 500; background: #fef3c7; color: #a16207; gap: 4px;">
                                        <i class="fas fa-exclamation-circle"></i>
                                        Perlu Diatur
                                    </span>
                                @endif
                            </td>
                            <td style="padding: 16px 24px; text-align: right;">
                                <button onclick="openModal({{ $employee->userid }}, '{{ $employee->name }}')"
                                    class="btn-primary">
                                    <i class="fas fa-edit"></i>
                                    Atur Atasan
                                </button>
                                @if(isset($existingSuperiors[$employee->userid]))
                                <button onclick="removeSuperior({{ $employee->userid }}, '{{ $employee->name }}')"
                                    class="btn-secondary" style="margin-left: 8px;">
                                    <i class="fas fa-trash"></i>
                                    Hapus
                                </button>
                                @endif
                            </td>
                        </tr>
                        @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal for Setting Superior -->
    <div id="superiorModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); z-index: 1000;" class="modal-backdrop">
        <div style="position: relative; top: 80px; margin: 0 auto; padding: 20px; border: 1px solid #e5e7eb; width: 400px; box-shadow: 0 25px 50px rgba(0, 0, 0, 0.25); border-radius: 12px; background: white;" class="fade-in">
            <div>
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px;">
                    <h3 style="font-size: 20px; font-weight: 600; color: #111827; margin: 0;">Pilih Atasan</h3>
                    <button onclick="closeModal()" style="color: #9ca3af; background: none; border: none; font-size: 18px; cursor: pointer; padding: 4px;"
                            onmouseover="this.style.color='#6b7280'" onmouseout="this.style.color='#9ca3af'">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <div style="margin-bottom: 20px;">
                    <p style="font-size: 14px; color: #6b7280; margin: 0 0 4px 0;">Pegawai:</p>
                    <p style="font-weight: 500; color: #111827; margin: 0;" id="selectedEmployeeName"></p>
                </div>

                <div style="margin-bottom: 20px;">
                    <label for="superiorSelect" style="display: block; font-size: 14px; font-weight: 500; color: #374151; margin-bottom: 8px;">Pilih Atasan</label>
                    <select id="superiorSelect" style="width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 8px; outline: none; transition: all 0.2s;"
                            onfocus="this.style.borderColor='#2563eb'; this.style.boxShadow='0 0 0 3px rgba(37, 99, 235, 0.1)'"
                            onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'">
                        <option value="">-- Pilih Atasan --</option>
                        @foreach($potentialSuperiors as $superior)
                        <option value="{{ $superior->user->userinfo_id }}">{{ $superior->name }} (ID: {{ $superior->userid }})</option>
                        @endforeach
                    </select>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 12px;">
                    <button onclick="closeModal()" class="btn-secondary">
                        Batal
                    </button>
                    <button onclick="saveSuperior()" class="btn-primary">
                        <i class="fas fa-save"></i>
                        Simpan
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Notification -->
    <div id="notification" style="display: none; position: fixed; top: 20px; right: 20px; z-index: 1100;" class="fade-in">
        <div style="background: white; border-left: 4px solid #059669; padding: 16px; box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1); border-radius: 8px; min-width: 300px;">
            <div style="display: flex; align-items: center;">
                <div style="color: #059669; margin-right: 12px;">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div>
                    <p style="font-size: 14px; color: #374151; margin: 0;" id="notificationMessage"></p>
                </div>
            </div>
        </div>
    </div>

    <script>
        let currentEmployeeId = null;

        // Search functionality
        document.getElementById('searchInput').addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const rows = document.querySelectorAll('.employee-row');

            rows.forEach(row => {
                const employeeName = row.querySelector('.employee-name').textContent.toLowerCase();
                if (employeeName.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });

        function openModal(employeeId, employeeName) {
            currentEmployeeId = employeeId;
            document.getElementById('selectedEmployeeName').textContent = employeeName;
            document.getElementById('superiorModal').style.display = 'block';
        }

        function closeModal() {
            document.getElementById('superiorModal').style.display = 'none';
            document.getElementById('superiorSelect').value = '';
            currentEmployeeId = null;
        }

        function saveSuperior() {
            const superiorId = document.getElementById('superiorSelect').value;

            if (!superiorId) {
                alert('Silakan pilih atasan terlebih dahulu');
                return;
            }

            if (superiorId == currentEmployeeId) {
                alert('Pegawai tidak dapat menjadi atasan dirinya sendiri');
                return;
            }

            fetch('{{ route("superior.store") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    userinfo_id: currentEmployeeId,
                    superior_id: superiorId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification(data.message, 'success');
                    closeModal();
                    setTimeout(() => location.reload(), 1500);
                } else {
                    alert('Terjadi kesalahan: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat menyimpan data');
            });
        }

        function removeSuperior(employeeId, employeeName) {
            if (confirm(`Apakah Anda yakin ingin menghapus hubungan atasan untuk ${employeeName}?`)) {
                fetch('{{ route("superior.destroy") }}', {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        userinfo_id: employeeId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification(data.message, 'success');
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        alert('Terjadi kesalahan: ' + (data.message || 'Unknown error'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat menghapus data');
                });
            }
        }

        function showNotification(message, type = 'success') {
            const notification = document.getElementById('notification');
            const messageElement = document.getElementById('notificationMessage');

            messageElement.textContent = message;
            notification.style.display = 'block';

            setTimeout(() => {
                notification.style.display = 'none';
            }, 3000);
        }

        // Close modal when clicking outside
        document.getElementById('superiorModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });

        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeModal();
            }
        });
    </script>
</body>
</html>
