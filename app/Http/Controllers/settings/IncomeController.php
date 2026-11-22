<?php

namespace App\Http\Controllers\settings;

use App\Http\Controllers\Controller;
use App\Models\Collections\Income;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class IncomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        if ($request->user()->cannot('VIEW_INCOME_LIST_SUBMENU')) {
            return back()->with('fail', $this->permissionDeniedMessage('VIEW_INCOME_LIST_SUBMENU'));
        }

        $incomes = Income::orderBy('gfs_code')->get();

        return view('settings.income', compact('incomes'));
    }

    public function store(Request $request)
    {
        if ($request->user()->cannot('CREATE_INCOME_LIST')) {
            return back()->with('fail', $this->permissionDeniedMessage('CREATE_INCOME_LIST'));
        }

        $data = $request->validateWithBag('newGfsCode', [
            'gfs_code' => ['required', 'string', 'max:32', 'unique:incomes,gfs_code'],
            'income_name' => ['required', 'string', 'max:255', 'unique:incomes,income_name'],
            'description' => ['nullable', 'string', 'max:255'],
            'is_space_fee' => ['required', 'boolean'],
        ]);

        Income::create($data);

        return back()->with('success', 'Service GFS code registered.');
    }

    public function update(Request $request, string $id)
    {
        if ($request->user()->cannot('UPDATE_INCOME_LIST')) {
            return back()->with('fail', $this->permissionDeniedMessage('UPDATE_INCOME_LIST'));
        }

        $income = Income::findOrFail($id);
        $data = $request->validate([
            'gfs_code' => ['sometimes', 'required', 'string', 'max:32', Rule::unique('incomes', 'gfs_code')->ignore($income)],
            'income_name' => ['required', 'string', 'max:255', Rule::unique('incomes', 'income_name')->ignore($income)],
            'description' => ['nullable', 'string', 'max:255'],
            'is_space_fee' => ['required', 'boolean'],
        ]);
        if (isset($data['gfs_code']) && $data['gfs_code'] !== $id && $this->codeInUse($id)) {
            return back()->with('fail', 'This GFS code is in use and cannot be changed.');
        }

        $income->update($data);

        return back()->with('success', 'Service GFS code updated.');
    }

    public function destroy(Request $request, string $id)
    {
        if ($request->user()->cannot('DELETE_INCOME_LIST')) {
            return back()->with('fail', $this->permissionDeniedMessage('DELETE_INCOME_LIST'));
        }

        $income = Income::findOrFail($id);
        if ($this->codeInUse($id)) {
            return back()->with('fail', 'This GFS code is in use and cannot be deleted.');
        }

        $income->delete();

        return back()->with('success', 'Service GFS code deleted.');
    }

    private function codeInUse(string $code): bool
    {
        foreach (['additional_services', 'particulars', 'sponsorship_packages'] as $table) {
            if (DB::table($table)->where('gfs_code', $code)->exists()) {
                return true;
            }
        }

        return false;
    }
}
