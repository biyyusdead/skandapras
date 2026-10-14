<?php

namespace App\Traits;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

trait BulkActionable
{
    /**
     * Bulk action: activate, deactivate, delete.
     */
    protected function handleBulkAction(
        Request $request,
        string $modelClass,
        string $module,
        array $options = []
    ): RedirectResponse {
        $validated = $request->validate([
            'ids'         => ['required', 'string'],
            'bulk_action' => ['required', 'in:activate,deactivate,delete'],
        ]);

        $ids = json_decode($validated['ids'], true);

        if (empty($ids) || !is_array($ids)) {
            return back()->with('error', 'Tidak ada data yang dipilih.');
        }

        $query = $modelClass::whereIn('id', $ids);

        // Proteksi: jangan hapus diri sendiri
        if (!empty($options['exclude_self']) && auth()->check()) {
            $query->where('id', '!=', auth()->id());
        }

        // Proteksi: cek foreign key (kalau ada)
        if (!empty($options['protect_relations'])) {
            $protectedIds = [];
            foreach ($options['protect_relations'] as $relation) {
                $withRelation = $modelClass::whereIn('id', $ids)
                    ->whereHas($relation)
                    ->pluck('id')
                    ->toArray();
                $protectedIds = array_merge($protectedIds, $withRelation);
            }

            if (!empty($protectedIds)) {
                $query->whereNotIn('id', $protectedIds);
            }
        }

        $action = $validated['bulk_action'];

        if ($action === 'delete') {
            // Hapus file terkait dulu (kalau ada)
            if (!empty($options['delete_files'])) {
                $items = $query->get();
                foreach ($items as $item) {
                    foreach ($options['delete_files'] as $field) {
                        if ($item->$field && !str_starts_with($item->$field, 'http')) {
                            Storage::disk('public')->delete($item->$field);
                        }
                    }
                }
            }

            $count = $query->delete();

            $this->logActivity('delete', $module, "Bulk delete {$count} data");

            return back()->with('success', "{$count} data berhasil dihapus." . (!empty($protectedIds) ? " " . count($protectedIds) . " data tidak bisa dihapus karena masih terkait." : ''));
        }

        if ($action === 'activate') {
            $count = $query->update(['is_active' => true]);
            $this->logActivity('update', $module, "Bulk activate {$count} data");

            return back()->with('success', "{$count} data berhasil diaktifkan.");
        }

        if ($action === 'deactivate') {
            $count = $query->update(['is_active' => false]);
            $this->logActivity('update', $module, "Bulk deactivate {$count} data");

            return back()->with('success', "{$count} data berhasil dinonaktifkan.");
        }

        return back();
    }
}