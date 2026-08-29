<?php

namespace App\Http\Controllers;

use App\Models\CafeTable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CafeTableController extends Controller
{
    public function index(): View
    {
        $tables = CafeTable::with(['orders' => fn ($q) => $q->where('status', 'open')])
            ->orderBy('name')->get();

        return view('tables.index', compact('tables'));
    }

    public function create(): View
    {
        return view('tables.form', ['table' => new CafeTable(['capacity' => 4, 'status' => CafeTable::STATUS_AVAILABLE])]);
    }

    public function store(Request $request): RedirectResponse
    {
        CafeTable::create($this->validated($request));

        return redirect()->route('tables.index')->with('status', 'Table added.');
    }

    public function edit(CafeTable $table): View
    {
        return view('tables.form', compact('table'));
    }

    public function update(Request $request, CafeTable $table): RedirectResponse
    {
        $table->update($this->validated($request, $table));

        return redirect()->route('tables.index')->with('status', 'Table updated.');
    }

    public function destroy(CafeTable $table): RedirectResponse
    {
        if ($table->orders()->where('status', 'open')->exists()) {
            return back()->with('error', 'This table has an open order.');
        }

        $table->delete();

        return redirect()->route('tables.index')->with('status', 'Table deleted.');
    }

    private function validated(Request $request, ?CafeTable $table = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:50', 'unique:cafe_tables,name'.($table ? ','.$table->id : '')],
            'capacity' => ['required', 'integer', 'min:1', 'max:50'],
            'location' => ['nullable', 'string', 'max:60'],
            'status' => ['required', 'in:available,occupied,reserved,out_of_service'],
        ]);
    }
}
