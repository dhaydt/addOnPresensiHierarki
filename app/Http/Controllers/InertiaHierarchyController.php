<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\EmployeeSuperior;
use App\Models\User;
use App\Models\Userinfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;

class InertiaHierarchyController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('auth');
    // }

    /**
     * Display the hierarchy management page
     */
    public function index(): Response
    {
        $user = Auth::user();
        $dept = $user->info->department;

        // Get employees ordered by name
        $employees = $dept->employees()->with('user')->orderBy('name')->get();

        // Get existing superior relationships
        $existingSuperiors = EmployeeSuperior::with(['user.info', 'superior.info'])
            ->whereIn('userinfo_id', $employees->pluck('userid')->toArray())
            ->get()
            ->keyBy('userinfo_id');

        // Get potential superiors (users in the same department) - ordered by name
        $potentialSuperiors = $employees->filter(function($employee) {
            return $employee->user !== null;
        })->sortBy('name')->values();

        // Build hierarchy tree
        $hierarchyTree = $this->buildHierarchyTree($employees, $existingSuperiors);

        return Inertia::render('Hierarchy/Index', [
            'hierarchyTree' => $hierarchyTree,
            'employees' => $employees->values(),
            'existingSuperiors' => $existingSuperiors,
            'potentialSuperiors' => $potentialSuperiors,
            'department' => $dept,
            'user' => $user,
            'stats' => [
                'totalEmployees' => $employees->count(),
                'withSuperior' => $existingSuperiors->count(),
                'withoutSuperior' => $employees->count() - $existingSuperiors->count(),
            ]
        ]);
    }

    /**
     * Move employee to new superior
     */
    public function moveEmployee(Request $request)
    {
        try {
            // Debug: Log request data
            Log::info('moveEmployee request data:', $request->all());

            // Check if employeeId exists in userinfo table
            $employeeExists = \App\Models\Userinfo::where('userid', $request->employeeId)->exists();
            Log::info('Employee exists check:', [
                'employeeId' => $request->employeeId,
                'exists' => $employeeExists
            ]);

            if (!$employeeExists) {
                Log::error('Employee not found in userinfo table:', [
                    'employeeId' => $request->employeeId,
                    'available_userids' => \App\Models\Userinfo::pluck('userid')->take(10)->toArray()
                ]);
            }

            $request->validate([
                'employeeId' => 'required',
                'newSuperiorId' => 'nullable',
            ]);

            $employeeId = $request->employeeId;
            $newSuperiorId = $request->newSuperiorId;

            // Validation: employee cannot be their own superior
            if ($employeeId == $newSuperiorId) {
                return back()->withErrors([
                    'message' => 'Pegawai tidak dapat menjadi atasan dirinya sendiri'
                ]);
            }

            // Check for circular dependency
            if ($newSuperiorId && $this->wouldCreateCircularDependency($employeeId, $newSuperiorId)) {
                return back()->withErrors([
                    'message' => 'Pengaturan ini akan membuat hierarki melingkar. Silakan pilih atasan yang berbeda.'
                ]);
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

            // Get updated data for response
            $user = Auth::user();
            if (!$user || !$user->info || !$user->info->department) {
                return back()->withErrors([
                    'message' => 'User atau departemen tidak ditemukan'
                ]);
            }

            $dept = $user->info->department;
            $employees = $dept->employees()->with('user')->orderBy('name')->get();
            $existingSuperiors = EmployeeSuperior::whereIn('userinfo_id', $employees->pluck('userid')->toArray())
                ->get()
                ->keyBy('userinfo_id');

            $hierarchyTree = $this->buildHierarchyTree($employees, $existingSuperiors);

            // Redirect back to index route with success message
            return redirect()->route('hierarchy.index')->with('message', 'Hierarki berhasil diperbarui');

        } catch (\Exception $e) {
            Log::error('Error in moveEmployee: ' . $e->getMessage(), [
                'request' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->withErrors([
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Remove superior relationship
     */
    public function removeSuperior(Request $request)
    {
        try {
            $request->validate([
                'employeeId' => 'required|exists:userinfo,userid',
            ]);

            EmployeeSuperior::where('userinfo_id', $request->employeeId)->delete();

            return redirect()->route('hierarchy.index')->with('message', 'Hubungan atasan berhasil dihapus');

        } catch (\Exception $e) {
            Log::error('Error in removeSuperior: ' . $e->getMessage());

            return back()->withErrors([
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Set superior relationship
     */
    public function setSuperior(Request $request)
    {
        try {
            $request->validate([
                'employeeId' => 'required|exists:userinfo,userid',
                'superiorId' => 'required|exists:userinfo,userid',
            ]);

            $employeeId = $request->employeeId;
            $superiorId = $request->superiorId;

            // Validation
            if ($employeeId == $superiorId) {
                return back()->withErrors([
                    'message' => 'Pegawai tidak dapat menjadi atasan dirinya sendiri'
                ]);
            }

            // Check for circular dependency
            if ($this->wouldCreateCircularDependency($employeeId, $superiorId)) {
                return back()->withErrors([
                    'message' => 'Pengaturan ini akan membuat hierarki melingkar. Silakan pilih atasan yang berbeda.'
                ]);
            }

            // Delete existing relationship
            EmployeeSuperior::where('userinfo_id', $employeeId)->delete();

            // Create new relationship
            EmployeeSuperior::create([
                'userinfo_id' => $employeeId,
                'superior_id' => $superiorId,
                'setupby_id' => Auth::id(),
            ]);

            return redirect()->route('hierarchy.index')->with('message', 'Atasan berhasil diatur');

        } catch (\Exception $e) {
            Log::error('Error in setSuperior: ' . $e->getMessage());

            return back()->withErrors([
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Build hierarchy tree structure
     */
    private function buildHierarchyTree($employees, $existingSuperiors)
    {
        // Find top level employees (no superior)
        $topLevelEmployees = [];
        foreach ($employees as $employee) {
            if ($employee->user && !isset($existingSuperiors[$employee->userid])) {
                $topLevelEmployees[] = $employee;
            }
        }

        // Sort top level employees by name
        usort($topLevelEmployees, function($a, $b) {
            return strcmp($a->name, $b->name);
        });

        return $this->buildTreeStructure($topLevelEmployees, $employees, $existingSuperiors, 0);
    }

    /**
     * Build tree structure recursively
     */
    private function buildTreeStructure($currentLevelEmployees, $allEmployees, $existingSuperiors, $level)
    {
        $tree = [];

        foreach ($currentLevelEmployees as $employee) {
            if (!$employee->user) continue;

            $item = [
                'id' => $employee->userid,
                'employee' => $employee,
                'level' => $level,
                'subordinates' => []
            ];

            // Find direct subordinates
            $subordinates = [];
            foreach ($existingSuperiors as $relation) {
                if ($relation->superior_id == $employee->userid) {
                    // Find the employee object for this subordinate
                    foreach ($allEmployees as $subEmployee) {
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

            // Recursively build subordinates tree
            if (!empty($subordinates)) {
                $item['subordinates'] = $this->buildTreeStructure($subordinates, $allEmployees, $existingSuperiors, $level + 1);
            }

            $tree[] = $item;
        }

        return $tree;
    }

    /**
     * Check if setting a superior would create circular dependency
     */
    private function wouldCreateCircularDependency($employeeId, $superiorId)
    {
        // Get current superior relationships
        $existingSuperiors = EmployeeSuperior::all()->keyBy('userinfo_id');

        // Check if the selected superior has the employee as their superior (directly or indirectly)
        $currentSuperiorId = $superiorId;
        $checkedIds = [];

        while ($currentSuperiorId && !in_array($currentSuperiorId, $checkedIds)) {
            $checkedIds[] = $currentSuperiorId;

            if ($currentSuperiorId == $employeeId) {
                return true;
            }

            $superiorRelation = $existingSuperiors->get($currentSuperiorId);
            $currentSuperiorId = $superiorRelation ? $superiorRelation->superior_id : null;
        }

        return false;
    }
}
