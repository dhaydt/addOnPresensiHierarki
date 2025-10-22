@php
    $employee = $item['employee'];
    $level = $item['level'];
    $subordinates = $item['subordinates'];
    $superior = isset($existingSuperiors[$employee->userid]) ? $existingSuperiors[$employee->userid]->superior : null;
@endphp

@if($employee->user && (empty($search) || stripos($employee->name, $search) !== false))
    <div style="margin-bottom: 4px; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
        <!-- Tree structure line -->
        <div draggable="true"
             id="tree-item-{{ $employee->userid }}-{{ $level }}"
             data-employee-id="{{ $employee->userid }}"
             data-employee-name="{{ $employee->name }}"
             data-user-id="{{ $employee->user ? $employee->user->userinfo_id : '' }}"
             data-level="{{ $level }}"
             class="tree-item tree-item-level-{{ $level }}"
             style="display: flex; align-items: center; padding: 12px; background: white; border-radius: 8px; margin-bottom: 2px; border: 1px solid #e5e7eb; transition: all 0.2s; position: relative; cursor: move; z-index: {{ 100 - $level }};"
             onmouseover="this.style.backgroundColor='#f8fafc'; this.style.borderColor='#d1d5db'"
             onmouseout="this.style.backgroundColor='white'; this.style.borderColor='#e5e7eb'">

            <!-- Drag handle -->
            <div style="margin-right: 8px; color: #9ca3af; cursor: grab;" class="drag-handle">
                <i class="fas fa-grip-vertical"></i>
            </div>

            <!-- Tree prefix and connectors -->
            <div style="display: flex; align-items: center; margin-right: 12px; font-family: 'Courier New', monospace; color: #6b7280;">
                <span style="white-space: pre;">{{ $prefix ?? '' }}</span>
                @if($level > 0)
                    <span style="color: #9ca3af;">{{ $isLast ? '└─' : '├─' }}</span>
                @endif
                <!-- Level indicator for multiple levels -->
                @if($level > 2)
                    <span style="background: #3b82f6; color: white; font-size: 10px; padding: 1px 4px; border-radius: 8px; margin-left: 4px; font-family: sans-serif;">L{{ $level }}</span>
                @endif
            </div>

            <!-- Employee info -->
            <div style="display: flex; align-items: center; flex: 1;">
                <!-- Avatar -->
                <div style="width: 36px; height: 36px; border-radius: 50%; background: linear-gradient(135deg,
                    @if($level == 0)
                        #f59e0b, #d97706
                    @elseif($level == 1)
                        #2563eb, #1d4ed8
                    @elseif($level == 2)
                        #059669, #047857
                    @else
                        #7c3aed, #5b21b6
                    @endif
                    ); color: white; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 14px; margin-right: 12px; position: relative;">
                    {{ strtoupper(substr($employee->name, 0, 2)) }}
                    @if($level == 0)
                        <div style="position: absolute; top: -2px; right: -2px; width: 12px; height: 12px; background: #f59e0b; border-radius: 50%; display: flex; align-items: center; justify-content: center; border: 1px solid white;">
                            <i class="fas fa-crown" style="font-size: 6px; color: white;"></i>
                        </div>
                    @elseif($level == 1)
                        <div style="position: absolute; top: -2px; right: -2px; width: 12px; height: 12px; background: #2563eb; border-radius: 50%; display: flex; align-items: center; justify-content: center; border: 1px solid white;">
                            <i class="fas fa-star" style="font-size: 6px; color: white;"></i>
                        </div>
                    @elseif($level >= 2)
                        <div style="position: absolute; top: -2px; right: -2px; width: 12px; height: 12px; background: #059669; border-radius: 50%; display: flex; align-items: center; justify-content: center; border: 1px solid white;">
                            <span style="font-size: 8px; color: white; font-weight: bold;">{{ $level }}</span>
                        </div>
                    @endif
                </div>

                <!-- Name and details -->
                <div style="flex: 1;">
                    <div style="font-weight: 600; color: #111827; font-size: 16px; display: flex; align-items: center; gap: 8px;">
                        {{ $employee->name }}
                        @if($level > 0)
                            <span style="font-size: 10px; background: #e5e7eb; color: #6b7280; padding: 2px 6px; border-radius: 8px; font-weight: 400;">
                                Level {{ $level + 1 }}
                            </span>
                        @endif
                    </div>
                    <div style="font-size: 14px; color: #6b7280;">ID: {{ $employee->userid }}</div>
                    @if($superior)
                        <div style="font-size: 12px; color: #059669; margin-top: 2px;">
                            <i class="fas fa-arrow-up"></i> Atasan: {{ $superior->name }}
                        </div>
                    @endif
                    <!-- Show subordinate count if any -->
                    @if(!empty($subordinates))
                        <div style="font-size: 12px; color: #3b82f6; margin-top: 2px;">
                            <i class="fas fa-users"></i> {{ count($subordinates) }} Bawahan
                        </div>
                    @endif
                </div>

                <!-- Drop zone indicator -->
                <div class="drop-indicator" style="display: none; position: absolute; left: 0; right: 0; height: 2px; background: #2563eb; z-index: 10;"></div>

                <!-- Status and actions -->
                <div style="display: flex; align-items: center; gap: 8px;">
                    @if(isset($existingSuperiors[$employee->userid]))
                        <span style="font-size: 11px; background: #dcfce7; color: #166534; padding: 2px 8px; border-radius: 12px; font-weight: 500;">
                            <i class="fas fa-check-circle"></i> Sudah Diatur
                        </span>
                    @else
                        <span style="font-size: 11px; background: #fef3c7; color: #a16207; padding: 2px 8px; border-radius: 12px; font-weight: 500;">
                            <i class="fas fa-exclamation-circle"></i> Perlu Diatur
                        </span>
                    @endif

                    @if($editingEmployeeId == $employee->userid)
                        <span style="color: #2563eb; font-size: 11px; font-weight: 500;">
                            <i class="fas fa-edit fa-pulse"></i> Editing
                        </span>
                    @else
                        <button wire:click="startEditing({{ $employee->userid }})"
                                style="background: #2563eb; color: white; padding: 4px 8px; border-radius: 4px; border: none; cursor: pointer; font-size: 11px; display: flex; align-items: center; gap: 4px;">
                            <i class="fas fa-edit"></i> Edit
                        </button>

                        @if(isset($existingSuperiors[$employee->userid]))
                            <button wire:click="removeSuperior({{ $employee->userid }})"
                                    wire:confirm="Hapus hubungan atasan untuk {{ $employee->name }}?"
                                    style="background: #6b7280; color: white; padding: 4px 8px; border-radius: 4px; border: none; cursor: pointer; font-size: 11px; display: flex; align-items: center; gap: 4px;">
                                <i class="fas fa-trash"></i> Hapus
                            </button>
                        @endif
                    @endif
                </div>
            </div>
        </div>

        <!-- Inline edit form -->
        @if($editingEmployeeId == $employee->userid)
            <div style="margin-left: {{ strlen($prefix) * 8 + ($level > 0 ? 24 : 0) }}px; margin-bottom: 8px; padding: 16px; background: #f0f9ff; border: 2px solid #0ea5e9; border-radius: 8px;">
                <div style="margin-bottom: 8px; font-weight: 600; color: #0369a1; font-size: 14px;">
                    <i class="fas fa-edit"></i> Edit Atasan untuk {{ $employee->name }}
                </div>
                <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
                    <select wire:model="editingSuperiorId" style="padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; outline: none; min-width: 200px; font-size: 14px;">
                        <option value="">-- Pilih Atasan --</option>
                        @foreach($potentialSuperiors as $superior)
                            @if($superior->user && $superior->userid != $employee->userid)
                                <option value="{{ $superior->user->userinfo_id }}">{{ $superior->name }} (ID: {{ $superior->userid }})</option>
                            @endif
                        @endforeach
                    </select>
                    <div style="display: flex; gap: 8px;">
                        <button wire:click="saveInlineEdit" style="background: #059669; color: white; padding: 8px 16px; border-radius: 6px; border: none; cursor: pointer; font-size: 14px; display: flex; align-items: center; gap: 6px; font-weight: 500;">
                            <i class="fas fa-check"></i> Simpan
                        </button>
                        <button wire:click="cancelEditing" style="background: #6b7280; color: white; padding: 8px 16px; border-radius: 6px; border: none; cursor: pointer; font-size: 14px; display: flex; align-items: center; gap: 6px; font-weight: 500;">
                            <i class="fas fa-times"></i> Batal
                        </button>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endif

<!-- Render subordinates recursively -->
@if(!empty($subordinates))
    @foreach($subordinates as $subItem)
        @php
            $newPrefix = $prefix . ($level > 0 ? ($isLast ? '   ' : '│  ') : '');
        @endphp
        @include('livewire.partials.tree-item', [
            'item' => $subItem,
            'isLast' => $loop->last,
            'prefix' => $newPrefix
        ])
    @endforeach
@endif
