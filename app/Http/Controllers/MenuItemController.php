<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\MenuItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class MenuItemController extends Controller
{
    public function index(Request $request): View
    {
        $items = MenuItem::with('category')
            ->when($request->filled('search'), fn ($q) => $q->where('name', 'like', '%'.$request->string('search').'%'))
            ->when($request->filled('category'), fn ($q) => $q->where('category_id', $request->integer('category')))
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        $categories = Category::orderBy('name')->get();

        return view('menu.index', compact('items', 'categories'));
    }

    public function create(): View
    {
        return view('menu.form', [
            'item' => new MenuItem(['is_available' => true]),
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('menu', 'public');
        }

        MenuItem::create($data);

        return redirect()->route('menu.index')->with('status', 'Menu item created.');
    }

    public function edit(MenuItem $menu): View
    {
        return view('menu.form', [
            'item' => $menu,
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, MenuItem $menu): RedirectResponse
    {
        $data = $this->validated($request, $menu);

        if ($request->hasFile('image')) {
            if ($menu->image_path) {
                Storage::disk('public')->delete($menu->image_path);
            }
            $data['image_path'] = $request->file('image')->store('menu', 'public');
        }

        $menu->update($data);

        return redirect()->route('menu.index')->with('status', 'Menu item updated.');
    }

    public function destroy(MenuItem $menu): RedirectResponse
    {
        if ($menu->image_path) {
            Storage::disk('public')->delete($menu->image_path);
        }

        $menu->delete();

        return redirect()->route('menu.index')->with('status', 'Menu item deleted.');
    }

    private function validated(Request $request, ?MenuItem $menu = null): array
    {
        $data = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:40', 'unique:menu_items,code'.($menu ? ','.$menu->id : '')],
            'description' => ['nullable', 'string', 'max:1000'],
            'price' => ['required', 'numeric', 'min:0'],
            'image' => ['nullable', 'image', 'max:2048'],
        ]);

        unset($data['image']);
        $data['is_available'] = $request->boolean('is_available');

        return $data;
    }
}
