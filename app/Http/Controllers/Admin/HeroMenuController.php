<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HeroMenu;
use App\Traits\LogsActivity;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HeroMenuController extends Controller
{
    use LogsActivity;

    public function index(): View
    {
        $heroMenus = HeroMenu::orderBy('order')->paginate(15);

        return view('admin.hero-menus.index', compact('heroMenus'));
    }

    public function create(): View
    {
        return view('admin.hero-menus.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateData($request);
        $validated['is_active'] = $request->boolean('is_active');

        HeroMenu::create($validated);
        $this->logActivity('create', 'hero-menus', "Menambahkan menu hero: {$validated['title']}");

        return redirect()->route('admin.hero-menus.index')
            ->with('success', 'Menu hero berhasil ditambahkan.');
    }

    public function edit(HeroMenu $heroMenu): View
    {
        return view('admin.hero-menus.edit', compact('heroMenu'));
    }

    public function update(Request $request, HeroMenu $heroMenu): RedirectResponse
    {
        $validated = $this->validateData($request);
        $validated['is_active'] = $request->boolean('is_active');

        $heroMenu->update($validated);
        $this->logActivity('update', 'hero-menus', "Memperbarui menu hero: {$heroMenu->title}");

        return redirect()->route('admin.hero-menus.index')
            ->with('success', 'Menu hero berhasil diperbarui.');
    }

    public function destroy(HeroMenu $heroMenu): RedirectResponse
    {
        $title = $heroMenu->title;
        $heroMenu->delete();
        $this->logActivity('delete', 'hero-menus', "Menghapus menu hero: {$title}");

        return redirect()->route('admin.hero-menus.index')
            ->with('success', 'Menu hero berhasil dihapus.');
    }

    protected function validateData(Request $request): array
    {
        return $request->validate([
            'label' => ['required', 'string', 'max:50'],
            'title' => ['required', 'string', 'max:100'],
            'description' => ['required', 'string', 'max:200'],
            'icon' => ['required', 'string', 'max:50'],
            'route_name' => ['required', 'string', 'max:100'],
            'color_from' => ['required', 'string', 'max:30'],
            'color_to' => ['required', 'string', 'max:30'],
            'pattern' => ['required', 'in:dots,grid,stripes,waves,cross'],
            'order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['boolean'],
        ]);
    }
}
