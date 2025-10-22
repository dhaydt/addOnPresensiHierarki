<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use App\Models\Userinfo;
use App\Models\EmployeeSuperior;
use App\Models\Departement;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;

class DropdownBawahan extends Component
{
    use WithPagination;
public $count = 0;
    public $search = '';
    public $selectedEmployee = null;
    public $selectedSuperior = null;
    public $showAssignModal = false;
    public $showRemoveModal = false;
    public $department = null;
    public $employees;
    public $existingSuperiors;
    public $potentialSuperiors = null; // Will be loaded lazily

    // Cache untuk menghindari query berulang
    protected $employeesCache = null;
    protected $superiorsCache = null;

    protected $queryString = ['search'];

    public function mount()
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->to(route('access-denied'));
        }

        $user = Auth::user();
        if ($user && $user->info && $user->info->department) {
            $this->department = $user->info->department;
        }

        // Initialize collections
        $this->employees = collect([]);
        $this->existingSuperiors = collect([]);

        $this->loadData();
    }

    public function loadData()
    {
        if (!$this->department) {
            return;
        }

        // Get employees and order by name - dengan cache
        if (!$this->employeesCache) {
            $this->employeesCache = $this->department->employees()
                ->with('user')
                ->orderBy('name')
                ->get();
        }
        $this->employees = collect($this->employeesCache);

        // Get existing superior relationships - dengan cache
        if (!$this->superiorsCache) {
            $this->superiorsCache = EmployeeSuperior::with(['user.info', 'superior.info'])
                ->whereIn('userinfo_id', $this->employees->pluck('userid')->toArray())
                ->get()
                ->keyBy('userinfo_id');
        }
        $this->existingSuperiors = collect($this->superiorsCache);
        $this->potentialSuperiors = $this->employees->sortBy('name');
    }

    // Lazy load potential superiors hanya saat dibutuhkan
    public function getPotentialSuperiors()
    {
        // if ($this->potentialSuperiors === null) {
        //     $this->potentialSuperiors = $this->employees->sortBy('name');
        // }
        // return $this->potentialSuperiors;
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function getEmployeesWithSuperiorProperty()
    {
        $ids = $this->employees->pluck('userid')->map(fn($id) => (string)$id)->toArray();

        $employees = EmployeeSuperior::with(['user.info', 'superior.info'])
                    ->whereIn('userinfo_id', $ids)
                    ->get();
        $employee = EmployeeSuperior::with(['user.info', 'superior.info'])
                    ->get();

        // dd($employee, $employees, $ids);

        return $employees;
    }

    public function getEmployeesWithoutSuperiorProperty()
    {
        return $this->employees->reject(function($employee) {
            return $this->existingSuperiors->has($employee->userid);
        })->filter(function($employee) {
            return !$this->search || str_contains(strtolower($employee->name), strtolower($this->search));
        });
    }

    // public function openAssignModal($employeeId)
    // {
    //     // Lazy load potential superiors hanya saat modal dibuka
    //     // $this->getPotentialSuperiors();

    //     $this->selectedEmployee = $employeeId;
    //     $this->selectedSuperior = null;
    //     $this->showAssignModal = true;
    // }

    public function openRemoveModal($employeeId)
    {
        $this->selectedEmployee = $employeeId;
        $this->showRemoveModal = true;
    }

    public function assignSuperior()
    {
        $this->validate([
            'selectedEmployee' => 'required',
            'selectedSuperior' => 'required|different:selectedEmployee',
        ]);

        // Check for circular dependency
        if ($this->wouldCreateCircularDependency($this->selectedEmployee, $this->selectedSuperior)) {
            $this->dispatch('errorMessage', 'Pengaturan ini akan membuat hierarki melingkar. Silakan pilih atasan yang berbeda.');
            return;
        }

        try {
            // Remove existing superior if any
            EmployeeSuperior::where('userinfo_id', $this->selectedEmployee)->delete();

            // Create new relationship
            EmployeeSuperior::create([
                'userinfo_id' => $this->selectedEmployee,
                'superior_id' => $this->selectedSuperior,
                'setupby_id' => Auth::id(),
            ]);

            // Clear all cache untuk force refresh
            $this->employeesCache = null;
            $this->superiorsCache = null;
            $this->potentialSuperiors = null;

            $this->closeModal();
            $this->loadData();

            $this->dispatch('successMessage', 'Atasan berhasil ditetapkan.');

            // Dispatch browser event to refresh
            $this->dispatch('superior-updated');

        } catch (\Exception $e) {
            $this->dispatch('errorMessage', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function removeSuperior($employeeId = null)
    {
        $employeeId = $employeeId ?: $this->selectedEmployee;

        try {
            $employ = EmployeeSuperior::where('userinfo_id', $employeeId)->first();

            if ($employ) {
                $employ->delete();

                // Clear all cache untuk force refresh
                $this->employeesCache = null;
                $this->superiorsCache = null;
                $this->potentialSuperiors = null;

                $this->loadData();
                $this->dispatch('successMessage', 'Hubungan atasan berhasil dihapus.');

                // Dispatch browser event to refresh
                $this->dispatch('superior-updated');
            } else {
                $this->dispatch('errorMessage', 'Tidak ditemukan hubungan atasan untuk dihapus.');
            }
        } catch (\Exception $e) {
            $this->dispatch('errorMessage', 'Terjadi kesalahan: ' . $e->getMessage());
        }

        $this->closeModal();
    }

    public function closeModal()
    {
        $this->showAssignModal = false;
        $this->showRemoveModal = false;
        $this->selectedEmployee = null;
        $this->selectedSuperior = null;
    }

    private function wouldCreateCircularDependency($employeeId, $superiorId)
    {
        // Check if the proposed superior is already a subordinate of the employee
        $subordinates = $this->getAllSubordinates($employeeId);
        return in_array($superiorId, $subordinates);
    }

    private function getAllSubordinates($employeeId, &$visited = [])
    {
        if (in_array($employeeId, $visited)) {
            return [];
        }

        $visited[] = $employeeId;
        $subordinates = EmployeeSuperior::where('superior_id', $employeeId)->pluck('userinfo_id')->toArray();

        $allSubordinates = $subordinates;
        foreach ($subordinates as $subordinate) {
            $allSubordinates = array_merge($allSubordinates, $this->getAllSubordinates($subordinate, $visited));
        }

        return array_unique($allSubordinates);
    }

    public function getStatsProperty()
    {
        if (!$this->department || $this->employees->isEmpty()) {
            return [
                'totalEmployees' => 0,
                'withSuperior' => 0,
                'withoutSuperior' => 0,
            ];
        }

        $totalEmployees = $this->employees->count();
        $withSuperior = $this->existingSuperiors->count();

        return [
            'totalEmployees' => $totalEmployees,
            'withSuperior' => $withSuperior,
            'withoutSuperior' => $totalEmployees - $withSuperior,
        ];
    }

    public function refreshData()
    {
        // Clear all cache
        $this->employeesCache = null;
        $this->superiorsCache = null;
        $this->potentialSuperiors = null;

        $this->loadData();
        $this->dispatch('successMessage', 'Data berhasil dimuat ulang');
    }

    public function render()
    {
        // Hanya hitung data yang benar-benar dibutuhkan untuk rendering
        return view('livewire.dropdown-bawahan', [
            'employeesWithSuperior' => $this->employeesWithSuperior,
            'employeesWithoutSuperior' => $this->employeesWithoutSuperior,
            'stats' => $this->stats,
            'potentialSuperiors' => $this->potentialSuperiors ?? collect([]), // Fallback ke empty collection
        ]);
    }
}
