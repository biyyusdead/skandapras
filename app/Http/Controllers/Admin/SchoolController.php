<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchoolProfile;
use App\Traits\LogsActivity;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SchoolController extends Controller
{
    use LogsActivity;

    /**
     * Form edit data sekolah.
     */
    public function edit(): View
    {
        $school = SchoolProfile::current() ?? new SchoolProfile;

        return view('admin.school.edit', compact('school'));
    }

    /**
     * Update data sekolah.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'school_name' => ['required', 'string', 'max:255'],
            'npsn' => ['nullable', 'string', 'max:20'],
            'nss' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:500'],
            'postal_code' => ['nullable', 'string', 'max:10'],
            'phone' => ['nullable', 'string', 'max:30'],
            'fax' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'principal_name' => ['nullable', 'string', 'max:255'],
            'accreditation' => ['nullable', 'string', 'max:10'],
            'education_level' => ['nullable', 'string', 'max:20'],
            'status' => ['nullable', 'string', 'max:30'],
            'latitude' => ['nullable', 'string', 'max:50'],
            'longitude' => ['nullable', 'string', 'max:50'],
            'elevation' => ['nullable', 'string', 'max:20'],
            'study_hours' => ['nullable', 'string', 'max:30'],
            'description' => ['nullable', 'string'],
            'vision' => ['nullable', 'string'],
            'mission' => ['nullable', 'string'],
            'principal_message' => ['nullable', 'string'],
            'history' => ['nullable', 'string'],
            'logo' => ['nullable', 'image', 'mimes:png,jpg,jpeg,svg,webp', 'max:2048'],
            'principal_photo' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:2048'],
            'structure_image' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:3072'],
        ]);

        $school = SchoolProfile::current() ?? new SchoolProfile;

        // Handle file uploads
        foreach (['logo', 'principal_photo', 'structure_image'] as $field) {
            if ($request->hasFile($field)) {
                $validated[$field] = handle_upload($request->file($field), 'school', $school->$field);
            }
        }

        $school->fill($validated);
        $school->save();

        $this->logActivity('update', 'school', 'Memperbarui data sekolah');

        return redirect()
            ->route('admin.school.edit')
            ->with('success', 'Data sekolah berhasil diperbarui.');
    }
}
