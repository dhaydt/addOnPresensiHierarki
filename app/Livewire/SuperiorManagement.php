<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\EmployeeSuperior;
use App\Models\User;
use App\Models\Userinfo;

class SuperiorManagement extends Component
{
    public $search = '';
    public $selectedEmployeeId = null;
    public $selectedSuperiorId = null;
    public $showModal = false;
    public $selectedEmployeeName = '';
    public $editingEmployeeId = null;
    public $editingSuperiorId = null;
    public $employees = [];
    public $existingSuperiors = [];
    public $potentialSuperiors = [];
    public $hierarchyLevels = [];
    public $hierarchyTree = [];
    public $dept;
    public $user;

    public function mount()
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->to(route('access-denied'));
        }

        $this->user = Auth::user();
        $this->dept = $this->user->info->department;

        // Emit loading start for initial data load
        $this->dispatch('loading-start', 'Memuat data hierarki...');

        $this->loadData();

        // Emit loading end after data loaded
        $this->dispatch('loading-end');
    }

    public function loadData()
    {
        // Get employees and order by name
        $this->employees = $this->dept->employees()->with('user')->orderBy('name')->get();

        // Get existing superior relationships
        $this->existingSuperiors = EmployeeSuperior::with(['user.info', 'superior.info'])
            ->whereIn('userinfo_id', collect($this->employees)->pluck('userid')->toArray())
            ->get()
            ->keyBy('userinfo_id');

        // Get all potential superiors (users in the same department) - ordered by name
        $this->potentialSuperiors = collect($this->employees)
            ->filter(function($employee) {
                return $employee->user !== null;
            })
            ->sortBy('name');

        // Build hierarchy levels and tree structure
        $this->buildHierarchyLevels();
    }

    public function buildHierarchyLevels()
    {
        $this->hierarchyLevels = [];

        // Build tree structure - start with employees who have no superior (top level)
        $topLevelEmployees = [];
        foreach ($this->employees as $employee) {
            if ($employee->user && !isset($this->existingSuperiors[$employee->userid])) {
                $topLevelEmployees[] = $employee;
            }
        }

        // Sort top level employees by name
        usort($topLevelEmployees, function($a, $b) {
            return strcmp($a->name, $b->name);
        });

        // Build the complete tree structure
        $this->hierarchyTree = $this->buildTreeStructure($topLevelEmployees, 0);
    }

    public function buildTreeStructure($employees, $level)
    {
        $tree = [];

        foreach ($employees as $employee) {
            if (!$employee->user) continue;

            $item = [
                'employee' => $employee,
                'level' => $level,
                'subordinates' => []
            ];

            // Find direct subordinates
            $subordinates = [];
            foreach ($this->existingSuperiors as $relation) {
                if ($relation->superior_id == $employee->user->userinfo_id) {
                    // Find the employee object for this subordinate
                    foreach ($this->employees as $subEmployee) {
                        if ($subEmployee->user && $subEmployee->userid == $relation->userinfo_id) {
                            $subordinates[] = $subEmployee;
                            break;
                        }
                    }
                }
            }

            // Sort subordinates by name
            usort($subordinates, function($a, $b) {
                return strcmp($a->name, $b->name);
            });

            // Recursively build subordinates tree for unlimited levels
            if (!empty($subordinates)) {
                $item['subordinates'] = $this->buildTreeStructure($subordinates, $level + 1);
            }

            $tree[] = $item;
        }

        return $tree;
    }

    public function renderTreeItems($treeItems)
    {
        $html = '';
        foreach ($treeItems as $item) {
            $html .= $this->renderSingleItem($item);
            if (!empty($item['subordinates'])) {
                $html .= $this->renderTreeItems($item['subordinates']);
            }
        }
        return $html;
    }

    private function renderSingleItem($item)
    {
        // This will be handled in the view
        return '';
    }

    public function getFilteredEmployeesProperty()
    {
        if (empty($this->search)) {
            return collect($this->employees);
        }

        return collect($this->employees)->filter(function($employee) {
            return stripos($employee->name, $this->search) !== false;
        });
    }

    public function updatedSearch()
    {
        // Optional: Add loading state for search operations
        if (!empty($this->search)) {
            $this->dispatch('loading-start', 'Mencari data...');

            // Small delay for visual feedback

            $this->dispatch('loading-end');
        }
    }

    public function openModal($employeeId, $employeeName)
    {
        $this->dispatch('loading-start', 'Memuat modal pengaturan...');

        $this->selectedEmployeeId = $employeeId;
        $this->selectedEmployeeName = $employeeName;
        $this->selectedSuperiorId = null;
        $this->showModal = true;

        // Small delay for visual feedback

        $this->dispatch('loading-end');
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->selectedEmployeeId = null;
        $this->selectedSuperiorId = null;
        $this->selectedEmployeeName = '';
    }

    public function saveSuperior()
    {
        // Emit loading start event
        $this->dispatch('loading-start', 'Menyimpan pengaturan atasan...');

        $this->validate([
            'selectedSuperiorId' => 'required|exists:users,userinfo_id',
            'selectedEmployeeId' => 'required|exists:userinfo,userid',
        ]);

        if ($this->selectedSuperiorId == $this->selectedEmployeeId) {
            $this->dispatch('loading-end');
            session()->flash('error', 'Pegawai tidak dapat menjadi atasan dirinya sendiri');
            return;
        }

        // Check for circular dependency
        if ($this->wouldCreateCircularDependency($this->selectedEmployeeId, $this->selectedSuperiorId)) {
            $this->dispatch('loading-end');
            session()->flash('error', 'Pengaturan ini akan membuat hierarki melingkar. Silakan pilih atasan yang berbeda.');
            return;
        }

        // Small delay to show loading (optional)

        // Delete existing relationship
        EmployeeSuperior::where('userinfo_id', $this->selectedEmployeeId)->delete();

        // Create new relationship
        EmployeeSuperior::create([
            'userinfo_id' => $this->selectedEmployeeId,
            'superior_id' => $this->selectedSuperiorId,
            'setupby_id' => Auth::id(),
        ]);

        $this->closeModal();
        $this->loadData();
        session()->flash('success', 'Atasan berhasil diatur');

        // Emit loading end event
        $this->dispatch('loading-end');
    }

    public function removeSuperior($employeeId)
    {
        // Emit loading start event
        $this->dispatch('loading-start', 'Menghapus hubungan atasan...');

        try {
            $employ = EmployeeSuperior::where('userinfo_id', $employeeId)->first();

            if ($employ) {
                $employ->delete();
                $this->loadData();
                session()->flash('success', 'Hubungan atasan berhasil dihapus');
            } else {
                session()->flash('error', 'Tidak ditemukan hubungan atasan untuk dihapus');
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }

        // Emit loading end event
        $this->dispatch('loading-end');
    }

    public function moveTo($employeeId, $newSuperiorId)
    {
        // Emit loading start event
        $this->dispatch('loading-start', 'Menyimpan perubahan hierarki...');

        // Small delay to show loading (optional - remove in production if not needed)

        // Validation
        if ($employeeId == $newSuperiorId) {
            $this->dispatch('loading-end');
            session()->flash('error', 'Pegawai tidak dapat menjadi atasan dirinya sendiri');
            return;
        }

        // Check for circular dependency
        if ($this->wouldCreateCircularDependency($employeeId, $newSuperiorId)) {
            $this->dispatch('loading-end');
            session()->flash('error', 'Pengaturan ini akan membuat hierarki melingkar. Silakan pilih atasan yang berbeda.');
            return;
        }

        // Delete existing relationship
        EmployeeSuperior::where('userinfo_id', $employeeId)->delete();

        // Create new relationship if newSuperiorId is not null
        if ($newSuperiorId) {
            EmployeeSuperior::create([
                'userinfo_id' => $employeeId,
                'superior_id' => $newSuperiorId,
                'setupby_id' => Auth::id(),
            ]);
        }

        $this->loadData();

        // Get employee name for feedback
        $employee = collect($this->employees)->first(function($emp) use ($employeeId) {
            return $emp->userid == $employeeId;
        });

        if ($newSuperiorId) {
            $superior = collect($this->employees)->first(function($emp) use ($newSuperiorId) {
                return $emp->user && $emp->user->userinfo_id == $newSuperiorId;
            });
            session()->flash('success', $employee->name . ' berhasil dipindahkan ke bawah ' . $superior->name);
        } else {
            session()->flash('success', $employee->name . ' berhasil dipindahkan ke level teratas');
        }

        // Emit loading end event
        $this->dispatch('loading-end');
    }

    public function startEditing($employeeId)
    {
        $this->dispatch('loading-start', 'Memuat form edit...');

        $this->editingEmployeeId = $employeeId;

        // Set current superior if exists
        if (isset($this->existingSuperiors[$employeeId])) {
            $this->editingSuperiorId = $this->existingSuperiors[$employeeId]->superior_id;
        } else {
            $this->editingSuperiorId = null;
        }

        // Small delay for visual feedback

        $this->dispatch('loading-end');
    }

    public function cancelEditing()
    {
        $this->editingEmployeeId = null;
        $this->editingSuperiorId = null;
    }

    public function saveInlineEdit()
    {
        if (!$this->editingEmployeeId) {
            return;
        }

        // Emit loading start event
        $this->dispatch('loading-start', 'Menyimpan perubahan atasan...');

        // Small delay to show loading (optional)

        // Validation
        if (!$this->editingSuperiorId) {
            $this->dispatch('loading-end');
            session()->flash('error', 'Silakan pilih atasan terlebih dahulu');
            return;
        }

        if ($this->editingSuperiorId == $this->editingEmployeeId) {
            $this->dispatch('loading-end');
            session()->flash('error', 'Pegawai tidak dapat menjadi atasan dirinya sendiri');
            return;
        }

        // Check for circular dependency
        if ($this->wouldCreateCircularDependency($this->editingEmployeeId, $this->editingSuperiorId)) {
            $this->dispatch('loading-end');
            session()->flash('error', 'Pengaturan ini akan membuat hierarki melingkar. Silakan pilih atasan yang berbeda.');
            return;
        }

        // Delete existing relationship
        EmployeeSuperior::where('userinfo_id', $this->editingEmployeeId)->delete();

        // Create new relationship
        EmployeeSuperior::create([
            'userinfo_id' => $this->editingEmployeeId,
            'superior_id' => $this->editingSuperiorId,
            'setupby_id' => Auth::id(),
        ]);

        $this->cancelEditing();
        $this->loadData();
        session()->flash('success', 'Atasan berhasil diatur');

        // Emit loading end event
        $this->dispatch('loading-end');
    }

    private function wouldCreateCircularDependency($employeeId, $superiorId)
    {
        // Check if the selected superior has the employee as their superior (directly or indirectly)
        $currentSuperiorId = $superiorId;
        $checkedIds = [];

        while ($currentSuperiorId && !in_array($currentSuperiorId, $checkedIds)) {
            $checkedIds[] = $currentSuperiorId;

            if ($currentSuperiorId == $employeeId) {
                return true;
            }

            $superiorRelation = collect($this->existingSuperiors)->first(function($relation) use ($currentSuperiorId) {
                return $relation->userinfo_id == $currentSuperiorId;
            });

            $currentSuperiorId = $superiorRelation ? $superiorRelation->superior_id : null;
        }

        return false;
    }

    public function render()
    {
        return view('livewire.superior-management');
        // return view('livewire.superior-management-modern');
    }

    // Debug method untuk testing
    public function debugJavaScript()
    {
        $this->dispatch('debug-info', 'JavaScript debug called from Livewire');
    }

    // Method untuk refresh data secara manual
    public function refreshData()
    {
        $this->dispatch('loading-start', 'Memuat ulang data...');

        // Small delay for visual feedback

        $this->loadData();
        session()->flash('success', 'Data berhasil dimuat ulang');

        $this->dispatch('loading-end');
    }

    // Method untuk bulk operations (jika dibutuhkan di masa depan)
    public function bulkRemoveSuperiors($employeeIds)
    {
        $this->dispatch('loading-start', 'Menghapus beberapa hubungan atasan...');

        // Small delay for visual feedback

        EmployeeSuperior::whereIn('userinfo_id', $employeeIds)->delete();
        $this->loadData();

        $count = count($employeeIds);
        session()->flash('success', "Berhasil menghapus {$count} hubungan atasan");

        $this->dispatch('loading-end');
    }
}
