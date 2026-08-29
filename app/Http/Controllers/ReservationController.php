<?php

namespace App\Http\Controllers;

use App\Models\CafeTable;
use App\Models\Reservation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReservationController extends Controller
{
    public function index(Request $request): View
    {
        $reservations = Reservation::with('table')
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->when($request->filled('date'), fn ($q) => $q->whereDate('reserved_at', $request->date('date')))
            ->orderByDesc('reserved_at')
            ->paginate(15)
            ->withQueryString();

        return view('reservations.index', compact('reservations'));
    }

    public function create(): View
    {
        return view('reservations.form', [
            'reservation' => new Reservation(['guests' => 2, 'status' => 'booked', 'reserved_at' => now()->addHour()]),
            'tables' => CafeTable::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['created_by'] = $request->user()->id;

        $reservation = Reservation::create($data);
        $this->syncTableStatus($reservation);

        return redirect()->route('reservations.index')->with('status', 'Reservation saved.');
    }

    public function edit(Reservation $reservation): View
    {
        return view('reservations.form', [
            'reservation' => $reservation,
            'tables' => CafeTable::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Reservation $reservation): RedirectResponse
    {
        $reservation->update($this->validated($request));
        $this->syncTableStatus($reservation);

        return redirect()->route('reservations.index')->with('status', 'Reservation updated.');
    }

    public function destroy(Reservation $reservation): RedirectResponse
    {
        $reservation->delete();

        return redirect()->route('reservations.index')->with('status', 'Reservation deleted.');
    }

    private function syncTableStatus(Reservation $reservation): void
    {
        $table = $reservation->table;

        if (! $table) {
            return;
        }

        if ($reservation->status === 'booked' && $table->status === CafeTable::STATUS_AVAILABLE) {
            $table->update(['status' => CafeTable::STATUS_RESERVED]);
        }

        if (in_array($reservation->status, ['cancelled', 'completed', 'no_show'], true)
            && $table->status === CafeTable::STATUS_RESERVED) {
            $table->update(['status' => CafeTable::STATUS_AVAILABLE]);
        }
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'cafe_table_id' => ['nullable', 'exists:cafe_tables,id'],
            'customer_name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'guests' => ['required', 'integer', 'min:1', 'max:100'],
            'reserved_at' => ['required', 'date'],
            'status' => ['required', 'in:booked,seated,completed,cancelled,no_show'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);
    }
}
