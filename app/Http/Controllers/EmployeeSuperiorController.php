<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\EmployeeSuperior;
use App\Models\User;

class EmployeeSuperiorController extends Controller
{
    public function atur_bawahan(Request $request)
    {
        // Check if user is authenticated
        $validated = $request->validate([
            'nip' => 'required',
        ]);

        Auth::loginUsingId($request->nip);

        $user = Auth::user();
        if (!$user) {
            return view('access-denied');
        }

        return redirect()->route('dropdown_bawahan');

        // return view('pages.superior-livewire');
    }

    public function dropdown_bawahan(Request $request)
    {
        // Check if user is authenticated
        $user = Auth::user();

        if (!$user) {
            return view('access-denied');
        }

        return view('pages.dropdown-bawahan');
    }

    public function store(Request $request)
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return response()->json(['error' => true, 'message' => 'Anda tidak memiliki akses'], 401);
        }

        $validated = $request->validate([
            'userinfo_id' => 'required|exists:userinfo,userid',
            'superior_id' => 'required|exists:users,userinfo_id',
        ]);

        // Delete existing relationship
        EmployeeSuperior::where('userinfo_id', $validated['userinfo_id'])->delete();

        // Create new relationship
        EmployeeSuperior::create([
            'userinfo_id' => $validated['userinfo_id'],
            'superior_id' => $validated['superior_id'],
            'setupby_id' => Auth::id(),
        ]);

        return response()->json(['success' => true, 'message' => 'Atasan berhasil diatur']);
    }

    public function destroy(Request $request)
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return response()->json(['error' => true, 'message' => 'Anda tidak memiliki akses'], 401);
        }

        $validated = $request->validate([
            'userinfo_id' => 'required|exists:userinfo,userid',
        ]);

        EmployeeSuperior::where('userinfo_id', $validated['userinfo_id'])->delete();

        return response()->json(['success' => true, 'message' => 'Hubungan atasan berhasil dihapus']);
    }
}
