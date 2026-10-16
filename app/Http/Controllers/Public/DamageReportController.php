<?php

namespace App\Http\Controllers\Public;

use App\Enums\DamageReportStatus;
use App\Enums\IdentityStatus;
use App\Enums\UserRole;
use App\Enums\ValidityStatus;
use App\Helpers\NotificationHelper;
use App\Http\Controllers\Controller;
use App\Models\DamageReport;
use App\Models\ItemRoom;
use App\Models\Room;
use App\Models\SchoolProfile;
use App\Models\Teacher;
use App\Rules\TurnstileRule;
use App\Services\TurnstileService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class DamageReportController extends Controller
{
    public function create(TurnstileService $turnstile): View
    {
        $rooms = Room::active()
            ->with(['itemRooms.item:id,name,code'])
            ->orderBy('name')
            ->get();

        // Transform data untuk Alpine (di controller, bukan di Blade)
        $roomsData = $rooms->map(function ($r) {
            return [
                'id' => $r->id,
                'name' => $r->name,
                'items' => $r->itemRooms->map(function ($ir) {
                    return [
                        'id' => $ir->id,
                        'name' => $ir->item->name,
                        'quantity' => $ir->quantity,
                    ];
                })->values()->all(),
            ];
        })->values()->all();

        $turnstileConfig = [
            'configured' => $turnstile->isConfigured(),
            'siteKey' => $turnstile->siteKey(),
            'action' => $turnstile->expectedAction(),
        ];

        $schoolProfile = SchoolProfile::current();
        $officerContact = config('sarpras.officer_contact', []);

        return view('public.sarpras.pengaduan', compact('rooms', 'roomsData', 'turnstileConfig', 'schoolProfile', 'officerContact'));
    }

    public function store(Request $request, TurnstileService $turnstileService): JsonResponse|RedirectResponse
    {
        // Pengunjung umum diarahkan ke petugas sekolah/TU, bukan formulir pengaduan resmi
        if ($request->reporter_type === 'pengunjung') {
            $visitorMsg = 'Formulir pengaduan resmi hanya ditujukan untuk warga sekolah (Siswa dan Guru/Tenaga Kependidikan). Untuk pengunjung umum, silakan hubungi petugas sekolah atau bagian Tata Usaha.';

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => $visitorMsg,
                    'errors' => ['reporter_type' => [$visitorMsg]],
                ], 422);
            }

            return back()->withErrors(['reporter_type' => $visitorMsg])->withInput();
        }

        $validated = $request->validate([
            'reporter_type' => ['required', 'string', Rule::in(['siswa', 'guru_tendik'])],
            'reporter_name' => ['required', 'string', 'max:255'],
            'reporter_phone' => ['required', 'string', 'max:30', 'regex:/^[0-9\+\-\s\(\)]+$/'],
            'reporter_identifier' => [
                Rule::requiredIf(fn () => in_array($request->reporter_type, ['siswa', 'guru_tendik'])),
                'nullable',
                'string',
                'max:50',
            ],
            'student_class' => [
                Rule::requiredIf(fn () => $request->reporter_type === 'siswa'),
                'nullable',
                'string',
                'max:50',
            ],
            'student_major' => [
                Rule::requiredIf(fn () => $request->reporter_type === 'siswa'),
                'nullable',
                'string',
                'max:100',
            ],
            'room_id' => ['required', 'exists:rooms,id'],
            'item_room_id' => ['nullable', 'exists:item_rooms,id'],
            'description' => ['required', 'string', 'min:10', 'max:2000'],
            'evidence_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'cf-turnstile-response' => [
                new TurnstileRule($turnstileService, $request->ip(), $request->getHost()),
            ],
        ], [
            'reporter_type.required' => 'Status pelapor wajib dipilih.',
            'reporter_name.required' => 'Nama lengkap wajib diisi.',
            'reporter_phone.required' => 'Nomor telepon/kontak wajib diisi.',
            'reporter_phone.regex' => 'Format nomor telepon tidak valid.',
            'reporter_identifier.required' => 'Nomor identitas (NISN/NIP) wajib diisi untuk status ini.',
            'student_class.required' => 'Kelas wajib diisi untuk siswa.',
            'student_major.required' => 'Jurusan wajib diisi untuk siswa.',
            'room_id.required' => 'Ruangan wajib dipilih.',
            'description.required' => 'Deskripsi masalah wajib diisi.',
            'description.min' => 'Deskripsi masalah minimal 10 karakter.',
        ]);

        // Pencegahan pengiriman ganda (double-submission lock) dalam jendela 5 detik
        $submissionKey = 'damage_report_lock:'.sha1(
            $request->ip().'|'.
            ($validated['room_id'] ?? '').'|'.
            ($validated['reporter_phone'] ?? '').'|'.
            substr($validated['description'] ?? '', 0, 50)
        );

        if (! Cache::add($submissionKey, true, now()->addSeconds(5))) {
            $duplicateMsg = 'Pengaduan Anda sedang diproses atau baru saja dikirim. Mohon tunggu beberapa detik.';

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => $duplicateMsg,
                    'errors' => ['cf-turnstile-response' => [$duplicateMsg]],
                ], 429);
            }

            return back()->withErrors(['cf-turnstile-response' => $duplicateMsg])->withInput();
        }

        // Cek item_room kalau dipilih
        if (! empty($validated['item_room_id'])) {
            $itemRoom = ItemRoom::findOrFail($validated['item_room_id']);
            if ($itemRoom->room_id != $validated['room_id']) {
                $msg = 'Barang tidak berada di ruangan yang dipilih.';

                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => $msg,
                        'errors' => ['item_room_id' => [$msg]],
                    ], 422);
                }

                return back()->withErrors(['item_room_id' => $msg])->withInput();
            }
        }

        // Verifikasi identitas sesuai sumber data resmi
        $identityStatus = IdentityStatus::MENUNGGU_VERIFIKASI;
        $identityNote = null;

        if ($validated['reporter_type'] === 'guru_tendik') {
            $nipInput = trim($validated['reporter_identifier'] ?? '');
            $matchedTeacher = Teacher::where('nip', $nipInput)
                ->where('is_active', true)
                ->first();

            if ($matchedTeacher) {
                $identityStatus = IdentityStatus::TERVERIFIKASI;
                $identityNote = "Terverifikasi otomatis sesuai data guru: {$matchedTeacher->name}".($matchedTeacher->position ? " ({$matchedTeacher->position})" : '');
            } else {
                $identityStatus = IdentityStatus::MENUNGGU_VERIFIKASI;
                $identityNote = 'NIP/Nomor identitas tidak cocok dengan database guru resmi. Menunggu verifikasi manual oleh Waka.';
            }
        } elseif ($validated['reporter_type'] === 'siswa') {
            $identityStatus = IdentityStatus::MENUNGGU_VERIFIKASI;
            $identityNote = "Data siswa (NIS/NISN: {$validated['reporter_identifier']}, Kelas: {$validated['student_class']}) menunggu verifikasi manual oleh Waka.";
        } else {
            $identityStatus = IdentityStatus::MENUNGGU_VERIFIKASI;
            $identityNote = 'Identitas pengunjung umum, menunggu verifikasi manual oleh Waka.';
        }

        // Simpan foto
        if ($request->hasFile('evidence_photo')) {
            $validated['evidence_photo'] = $request->file('evidence_photo')
                ->store('damage-reports', 'public');
        }

        // Buat laporan dalam DB Transaction
        $report = DB::transaction(function () use ($validated, $identityStatus, $identityNote) {
            return DamageReport::create([
                'report_code' => DamageReport::generateReportCode(),
                'room_id' => $validated['room_id'],
                'item_room_id' => $validated['item_room_id'] ?? null,
                'reporter_id' => auth()->id() ?? null,
                'reporter_type' => $validated['reporter_type'],
                'reporter_name' => $validated['reporter_name'],
                'reporter_phone' => $validated['reporter_phone'],
                'reporter_identifier' => $validated['reporter_identifier'] ?? null,
                'student_class' => $validated['student_class'] ?? null,
                'student_major' => $validated['student_major'] ?? null,
                'description' => $validated['description'],
                'evidence_photo' => $validated['evidence_photo'] ?? null,
                'reported_at' => now(),
                'status' => DamageReportStatus::MENUNGGU_VERIFIKASI,
                'identity_status' => $identityStatus,
                'identity_note' => $identityNote,
                'validity_status' => ValidityStatus::MENUNGGU_PEMERIKSAAN,
            ]);
        });

        // Notifikasi ke Waka Sarpras
        NotificationHelper::toRole(
            UserRole::WAKA_SARPRAS,
            'Laporan Kerusakan Baru',
            "Laporan {$report->report_code} di {$report->room->name} oleh {$report->displayName()}",
            route('waka.damage-reports.show', $report),
            'alert-triangle',
            'yellow'
        );

        // ============================================================
        // RESPONSE — AJAX vs normal request
        // ============================================================
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Laporan berhasil dikirim. Tim Sarpras akan menindaklanjuti.',
                'report_code' => $report->report_code,
            ]);
        }

        return redirect()
            ->route('public.rooms.show', $report->room->code)
            ->with('success', 'Laporan berhasil dikirim. Tim Sarpras akan menindaklanjuti.');
    }
}
