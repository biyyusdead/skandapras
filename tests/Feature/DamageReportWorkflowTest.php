<?php

namespace Tests\Feature;

use App\Enums\ApprovalStatus;
use App\Enums\DamageCategory;
use App\Enums\DamageReportStatus;
use App\Enums\IdentityStatus;
use App\Enums\RepairStatus;
use App\Enums\ReporterType;
use App\Enums\UserRole;
use App\Enums\ValidityStatus;
use App\Models\Approval;
use App\Models\DamageReport;
use App\Models\RepairReport;
use App\Models\Room;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DamageReportWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected User $wakaUser;

    protected User $kepalaUser;

    protected User $regularUser;

    protected Room $testRoom;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
        Notification::fake();

        $this->wakaUser = User::factory()->create([
            'role' => UserRole::WAKA_SARPRAS,
            'is_active' => true,
        ]);

        $this->kepalaUser = User::factory()->create([
            'role' => UserRole::KEPALA_SEKOLAH,
            'is_active' => true,
        ]);

        $this->regularUser = User::factory()->create([
            'role' => UserRole::PENGGUNA_RUANGAN,
            'is_active' => true,
        ]);

        $this->testRoom = Room::create([
            'name' => 'Laboratorium Komputer 1',
            'code' => 'LAB-KOMP-1',
            'floor' => 1,
            'is_active' => true,
        ]);
    }

    /**
     * 1. Siswa dapat mengirim laporan dan identitasnya menunggu verifikasi manual.
     */
    public function test_student_can_submit_report_and_identity_is_pending_manual_verification(): void
    {
        $response = $this->post(route('public.sarpras.pengaduan.store'), [
            'reporter_type' => ReporterType::SISWA->value,
            'reporter_name' => 'Ahmad Fauzi',
            'reporter_phone' => '081234567890',
            'reporter_identifier' => '0051234567',
            'student_class' => 'XII',
            'student_major' => 'Rekayasa Perangkat Lunak',
            'room_id' => $this->testRoom->id,
            'description' => 'AC di pojok kanan mati total dan mengeluarkan suara berdengung.',
            'evidence_photo' => UploadedFile::fake()->image('bukti_ac.jpg'),
        ]);

        $response->assertRedirect(route('public.rooms.show', $this->testRoom->code));

        $this->assertDatabaseHas('damage_reports', [
            'reporter_name' => 'Ahmad Fauzi',
            'reporter_type' => ReporterType::SISWA->value,
            'reporter_identifier' => '0051234567',
            'student_class' => 'XII',
            'student_major' => 'Rekayasa Perangkat Lunak',
            'identity_status' => IdentityStatus::MENUNGGU_VERIFIKASI->value,
            'validity_status' => ValidityStatus::MENUNGGU_PEMERIKSAAN->value,
            'status' => DamageReportStatus::MENUNGGU_VERIFIKASI->value,
        ]);
    }

    /**
     * 2. Guru dengan identitas yang cocok (NIP sesuai database guru aktif) otomatis terverifikasi.
     */
    public function test_teacher_with_matching_nip_is_automatically_verified(): void
    {
        Teacher::create([
            'name' => 'Drs. Supriyanto, M.Pd.',
            'nip' => '197505102000031002',
            'is_active' => true,
        ]);

        $response = $this->post(route('public.sarpras.pengaduan.store'), [
            'reporter_type' => ReporterType::GURU_TENDIK->value,
            'reporter_name' => 'Supriyanto',
            'reporter_phone' => '081298765432',
            'reporter_identifier' => '197505102000031002',
            'room_id' => $this->testRoom->id,
            'description' => 'Proyektor tidak mau menyala saat pembelajaran berlangsung.',
            'evidence_photo' => UploadedFile::fake()->image('proyektor.jpg'),
        ]);

        $response->assertRedirect(route('public.rooms.show', $this->testRoom->code));

        $this->assertDatabaseHas('damage_reports', [
            'reporter_identifier' => '197505102000031002',
            'identity_status' => IdentityStatus::TERVERIFIKASI->value,
            'validity_status' => ValidityStatus::MENUNGGU_PEMERIKSAAN->value,
        ]);
    }

    /**
     * 3. Guru dengan identitas tidak cocok tidak otomatis terverifikasi (menunggu verifikasi manual).
     */
    public function test_teacher_with_unmatched_nip_is_not_automatically_verified(): void
    {
        $response = $this->post(route('public.sarpras.pengaduan.store'), [
            'reporter_type' => ReporterType::GURU_TENDIK->value,
            'reporter_name' => 'Guru Magang Baru',
            'reporter_phone' => '081299998888',
            'reporter_identifier' => '999999999999999999',
            'room_id' => $this->testRoom->id,
            'description' => 'Lampu ruangan berkedip terus-menerus.',
            'evidence_photo' => UploadedFile::fake()->image('lampu.jpg'),
        ]);

        $response->assertRedirect(route('public.rooms.show', $this->testRoom->code));

        $this->assertDatabaseHas('damage_reports', [
            'reporter_identifier' => '999999999999999999',
            'identity_status' => IdentityStatus::MENUNGGU_VERIFIKASI->value,
        ]);
    }

    /**
     * 4. Pengunjung melihat arahan ke petugas/TU dan tidak dapat mengirim formulir resmi.
     */
    public function test_visitor_is_guided_to_school_officers_and_cannot_submit_official_form(): void
    {
        // A. Halaman publik pengaduan menampilkan arahan dan area kontak resmi
        $pageResponse = $this->get(route('public.sarpras.pengaduan'));
        $pageResponse->assertOk();
        $pageResponse->assertSee('Terima kasih atas kepedulian Anda terhadap fasilitas sekolah');
        $pageResponse->assertSee('Bagian Tata Usaha');
        $pageResponse->assertSee('Kontak petugas akan tersedia setelah dikonfirmasi pihak sekolah');

        // B. Percobaan pengiriman formulir resmi dengan tipe pengunjung ditolak backend
        $response = $this->post(route('public.sarpras.pengaduan.store'), [
            'reporter_type' => ReporterType::PENGUNJUNG->value,
            'reporter_name' => 'Ibu Hartini (Wali Murid)',
            'reporter_phone' => '087712345678',
            'room_id' => $this->testRoom->id,
            'description' => 'Pintu toilet umum di dekat lorong tidak dapat dikunci rapat.',
            'evidence_photo' => UploadedFile::fake()->image('pintu.jpg'),
        ]);

        $response->assertSessionHasErrors('reporter_type');

        $this->assertDatabaseMissing('damage_reports', [
            'reporter_name' => 'Ibu Hartini (Wali Murid)',
        ]);
    }

    /**
     * 5. Laporan yang belum diverifikasi atau belum dinyatakan valid tidak dapat masuk ke perbaikan.
     */
    public function test_unverified_or_unvalidated_report_cannot_enter_repair(): void
    {
        $report = DamageReport::create([
            'room_id' => $this->testRoom->id,
            'reporter_type' => ReporterType::SISWA,
            'reporter_name' => 'Siswa A',
            'reporter_phone' => '0811111111',
            'identity_status' => IdentityStatus::MENUNGGU_VERIFIKASI,
            'validity_status' => ValidityStatus::MENUNGGU_PEMERIKSAAN,
            'status' => DamageReportStatus::MENUNGGU_VERIFIKASI,
            'description' => 'Kaca jendela retak',
        ]);

        $this->assertFalse($report->canBeRepaired());

        $response = $this->actingAs($this->wakaUser)->post(route('waka.repair-reports.store'), [
            'damage_report_id' => $report->id,
            'technician_name' => 'Pak Joko',
            'technician_contact' => '081200001111',
            'repair_description' => 'Ganti kaca jendela baru',
            'started_at' => now()->format('Y-m-d'),
        ]);

        $response->assertSessionHas('error');
        $this->assertDatabaseMissing('repair_reports', [
            'damage_report_id' => $report->id,
        ]);
    }

    /**
     * 6. Waka dapat memverifikasi identitas, meminta klarifikasi, dan menolak laporan.
     */
    public function test_waka_can_manage_identity_and_validity_actions(): void
    {
        $report = DamageReport::create([
            'room_id' => $this->testRoom->id,
            'reporter_type' => ReporterType::SISWA,
            'reporter_name' => 'Siswa B',
            'reporter_phone' => '0811223344',
            'reporter_identifier' => '0061234567',
            'student_class' => 'XI',
            'student_major' => 'TKJ',
            'identity_status' => IdentityStatus::MENUNGGU_VERIFIKASI,
            'validity_status' => ValidityStatus::MENUNGGU_PEMERIKSAAN,
            'status' => DamageReportStatus::MENUNGGU_VERIFIKASI,
            'description' => 'Kabel LAN putus',
        ]);

        // A. Minta klarifikasi identitas
        $this->actingAs($this->wakaUser)
            ->post(route('waka.damage-reports.request-clarification', $report), [
                'identity_note' => 'Mohon tunjukkan kartu pelajar ke ruang Sarpras',
            ])
            ->assertRedirect();

        $report->refresh();
        $this->assertSame(IdentityStatus::PERLU_KLARIFIKASI, $report->identity_status);
        $this->assertSame('Mohon tunjukkan kartu pelajar ke ruang Sarpras', $report->identity_note);

        // B. Verifikasi identitas berhasil
        $this->actingAs($this->wakaUser)
            ->post(route('waka.damage-reports.verify-identity', $report), [
                'identity_note' => 'Kartu pelajar telah dikonfirmasi valid',
            ])
            ->assertRedirect();

        $report->refresh();
        $this->assertSame(IdentityStatus::TERVERIFIKASI, $report->identity_status);

        // C. Tolak laporan dengan alasan
        $this->actingAs($this->wakaUser)
            ->post(route('waka.damage-reports.reject', $report), [
                'rejection_reason' => 'Setelah dicek di lokasi, kabel LAN sudah dirapikan',
            ])
            ->assertRedirect();

        $report->refresh();
        $this->assertSame(ValidityStatus::DITOLAK, $report->validity_status);
        $this->assertSame(DamageReportStatus::DITOLAK, $report->status);
        $this->assertSame('Setelah dicek di lokasi, kabel LAN sudah dirapikan', $report->rejection_reason);
    }

    /**
     * 7. Waka dapat menyetujui dan menugaskan kerusakan ringan tanpa persetujuan Kepala Sekolah.
     */
    public function test_waka_can_approve_minor_damage_directly_and_assign_repair_without_kepsek(): void
    {
        $report = DamageReport::create([
            'room_id' => $this->testRoom->id,
            'reporter_type' => ReporterType::GURU_TENDIK,
            'reporter_name' => 'Guru Matematika',
            'reporter_phone' => '0812333444',
            'identity_status' => IdentityStatus::TERVERIFIKASI,
            'validity_status' => ValidityStatus::VALID,
            'status' => DamageReportStatus::DIVERIFIKASI,
            'description' => 'Engsel pintu almari arsip longgar',
        ]);

        // Waka menyetujui kerusakan ringan
        $response = $this->actingAs($this->wakaUser)
            ->post(route('waka.damage-reports.approve-minor', $report), [
                'category_note' => 'Perbaikan ringan obeng baut, segera ditugaskan ke teknisi internal',
            ]);

        $response->assertRedirect();
        $report->refresh();

        $this->assertSame(DamageCategory::RINGAN, $report->damage_category);
        $this->assertSame(DamageReportStatus::DISETUJUI, $report->status);
        $this->assertTrue($report->canBeRepaired());

        // Verifikasi approval level 1 tersimpan
        $this->assertDatabaseHas('approvals', [
            'damage_report_id' => $report->id,
            'approval_level' => 1,
            'status' => ApprovalStatus::APPROVED->value,
            'approver_id' => $this->wakaUser->id,
        ]);

        // Approval level 2 (Kepsek) TIDAK DIBUTUHKAN dan tidak ada
        $this->assertDatabaseMissing('approvals', [
            'damage_report_id' => $report->id,
            'approval_level' => 2,
        ]);

        // Waka langsung menugaskan perbaikan
        $repairResponse = $this->actingAs($this->wakaUser)->post(route('waka.repair-reports.store'), [
            'damage_report_id' => $report->id,
            'technician_name' => 'Pak Slamet (Teknisi)',
            'technician_contact' => '085600002222',
            'repair_description' => 'Kencangkan dan ganti baut engsel',
            'started_at' => now()->format('Y-m-d'),
        ]);

        $repairResponse->assertRedirect();
        $this->assertDatabaseHas('repair_reports', [
            'damage_report_id' => $report->id,
            'technician_name' => 'Pak Slamet (Teknisi)',
        ]);

        $report->refresh();
        $this->assertSame(DamageReportStatus::DIPROSES, $report->status);
    }

    /**
     * 7b. Perbaikan dapat diselesaikan langsung atau otomatis via upload foto setelah, mengubah status laporan ke SELESAI.
     */
    public function test_repair_completion_and_auto_complete_transitions_report_to_selesai(): void
    {
        $report = DamageReport::create([
            'room_id' => $this->testRoom->id,
            'reporter_type' => ReporterType::SISWA,
            'reporter_name' => 'Siswa Lab',
            'reporter_phone' => '0812345678',
            'identity_status' => IdentityStatus::TERVERIFIKASI,
            'validity_status' => ValidityStatus::VALID,
            'status' => DamageReportStatus::DIPROSES,
            'description' => 'Kursi patah kaki',
        ]);

        $repair = RepairReport::create([
            'damage_report_id' => $report->id,
            'technician_name' => 'Pak Joko',
            'status' => RepairStatus::IN_PROGRESS,
            'started_at' => now(),
        ]);

        // A. Selesaikan perbaikan melalui endpoint penyelesaian khusus
        $completeResponse = $this->actingAs($this->wakaUser)
            ->post(route('waka.repair-reports.complete', $repair), [
                'repair_evidence_after' => UploadedFile::fake()->image('hasil_perbaikan.jpg'),
                'completed_at' => now()->format('Y-m-d'),
                'repair_cost' => 50000,
                'repair_description' => 'Kaki kursi berhasil dilas dan dicat ulang rapi.',
            ]);

        $completeResponse->assertRedirect(route('waka.damage-reports.show', $report));

        $repair->refresh();
        $report->refresh();

        $this->assertSame(RepairStatus::COMPLETED, $repair->status);
        $this->assertSame(DamageReportStatus::SELESAI, $report->status);
        $this->assertNotNull($repair->repair_evidence_after);

        // B. Uji auto-complete pada update biasa ketika foto setelah diunggah tanpa manual ubah status
        $report2 = DamageReport::create([
            'room_id' => $this->testRoom->id,
            'reporter_type' => ReporterType::SISWA,
            'reporter_name' => 'Siswa 2',
            'reporter_phone' => '0812345679',
            'identity_status' => IdentityStatus::TERVERIFIKASI,
            'validity_status' => ValidityStatus::VALID,
            'status' => DamageReportStatus::DIPROSES,
            'description' => 'Stop kontak rusak',
        ]);

        $repair2 = RepairReport::create([
            'damage_report_id' => $report2->id,
            'technician_name' => 'Pak Tri',
            'status' => RepairStatus::IN_PROGRESS,
            'started_at' => now(),
        ]);

        // Form edit dikirim dengan status tetap 'in_progress', tapi ada bukti foto setelah
        $updateResponse = $this->actingAs($this->wakaUser)
            ->put(route('waka.repair-reports.update', $repair2), [
                'technician_name' => 'Pak Tri',
                'status' => 'in_progress', // user tidak ubah dropdown status
                'repair_evidence_after' => UploadedFile::fake()->image('stopkontak_beres.jpg'),
            ]);

        $updateResponse->assertRedirect();
        $repair2->refresh();
        $report2->refresh();

        // Sistem cerdas otomatis mengubah status ke COMPLETED dan laporan ke SELESAI
        $this->assertSame(RepairStatus::COMPLETED, $repair2->status);
        $this->assertSame(DamageReportStatus::SELESAI, $report2->status);
    }

    /**
     * 8. Kerusakan besar harus melalui persetujuan Kepala Sekolah sebelum bisa ditugaskan.
     */
    public function test_major_damage_requires_kepsek_approval_before_repair_assignment(): void
    {
        $report = DamageReport::create([
            'room_id' => $this->testRoom->id,
            'reporter_type' => ReporterType::SISWA,
            'reporter_name' => 'Ketua Kelas XII',
            'reporter_phone' => '08123444555',
            'identity_status' => IdentityStatus::TERVERIFIKASI,
            'validity_status' => ValidityStatus::VALID,
            'status' => DamageReportStatus::DIVERIFIKASI,
            'description' => 'Plafon bocor parah dan genteng jebol tertimpa dahan pohon',
        ]);

        // Waka mengajukan kerusakan besar ke Kepala Sekolah
        $submitResponse = $this->actingAs($this->wakaUser)
            ->post(route('waka.damage-reports.submit-major', $report), [
                'category_note' => 'Estimasi biaya renovasi genteng dan plafon membutuhkan persetujuan Kepsek',
            ]);

        $submitResponse->assertRedirect();
        $report->refresh();

        $this->assertSame(DamageCategory::BERAT, $report->damage_category);
        $this->assertSame(DamageReportStatus::MENUNGGU_PERSETUJUAN_2, $report->status);

        // Sebelum persetujuan Kepsek, canBeRepaired() HARUS false
        $this->assertFalse($report->canBeRepaired());

        // Waka mencoba menugaskan sebelum Kepsek menyetujui -> DITOLAK
        $failRepair = $this->actingAs($this->wakaUser)->post(route('waka.repair-reports.store'), [
            'damage_report_id' => $report->id,
            'technician_name' => 'Kontraktor CV Bangun',
            'technician_contact' => '081300003333',
            'repair_description' => 'Mulai renovasi atap',
            'started_at' => now()->format('Y-m-d'),
        ]);
        $failRepair->assertSessionHas('error');
        $this->assertDatabaseMissing('repair_reports', ['damage_report_id' => $report->id]);

        // Kepala Sekolah menyetujui pengajuan kerusakan besar
        $kepalaResponse = $this->actingAs($this->kepalaUser)
            ->post(route('kepala.approvals.approve', $report), [
                'notes' => 'Disetujui. Gunakan anggaran pemeliharaan darurat.',
            ]);

        $kepalaResponse->assertRedirect();
        $report->refresh();

        $this->assertSame(DamageReportStatus::DISETUJUI, $report->status);
        $this->assertTrue($report->canBeRepaired());

        $this->assertDatabaseHas('approvals', [
            'damage_report_id' => $report->id,
            'approval_level' => 2,
            'status' => ApprovalStatus::APPROVED->value,
            'approver_id' => $this->kepalaUser->id,
            'notes' => 'Disetujui. Gunakan anggaran pemeliharaan darurat.',
        ]);

        // Sekarang Waka dapat menugaskan teknisi perbaikan
        $successRepair = $this->actingAs($this->wakaUser)->post(route('waka.repair-reports.store'), [
            'damage_report_id' => $report->id,
            'technician_name' => 'Kontraktor CV Bangun',
            'technician_contact' => '081300003333',
            'repair_description' => 'Mulai renovasi atap',
            'started_at' => now()->format('Y-m-d'),
        ]);
        $successRepair->assertRedirect();
        $this->assertDatabaseHas('repair_reports', ['damage_report_id' => $report->id]);
    }

    /**
     * 9. Kerusakan besar yang ditolak Kepala Sekolah tidak dapat diteruskan ke penugasan.
     */
    public function test_rejected_major_damage_cannot_enter_repair(): void
    {
        $report = DamageReport::create([
            'room_id' => $this->testRoom->id,
            'reporter_type' => ReporterType::SISWA,
            'reporter_name' => 'Siswa C',
            'reporter_phone' => '08123444666',
            'identity_status' => IdentityStatus::TERVERIFIKASI,
            'validity_status' => ValidityStatus::VALID,
            'damage_category' => DamageCategory::BERAT,
            'status' => DamageReportStatus::MENUNGGU_PERSETUJUAN_2,
            'description' => 'Permintaan perombakan total tata letak lab',
        ]);

        // Kepala Sekolah menolak pengajuan
        $this->actingAs($this->kepalaUser)
            ->post(route('kepala.approvals.reject', $report), [
                'notes' => 'Belum ada anggaran untuk perombakan tahun ini.',
            ])
            ->assertRedirect();

        $report->refresh();
        $this->assertSame(DamageReportStatus::DITOLAK, $report->status);
        $this->assertSame(ValidityStatus::DITOLAK, $report->validity_status);
        $this->assertSame('Belum ada anggaran untuk perombakan tahun ini.', $report->rejection_reason);
        $this->assertFalse($report->canBeRepaired());

        // Coba tugaskan repair
        $this->actingAs($this->wakaUser)->post(route('waka.repair-reports.store'), [
            'damage_report_id' => $report->id,
            'technician_name' => 'Tukang',
            'repair_description' => 'Perombakan',
        ])->assertSessionHas('error');

        $this->assertDatabaseMissing('repair_reports', ['damage_report_id' => $report->id]);
    }

    /**
     * 10. Penugasan perbaikan hanya menerima yang memenuhi syarat persetujuan.
     */
    public function test_technician_repair_creation_guard_matches_approval_prerequisites(): void
    {
        $report = DamageReport::create([
            'room_id' => $this->testRoom->id,
            'reporter_type' => ReporterType::PENGUNJUNG,
            'reporter_name' => 'Pengunjung D',
            'reporter_phone' => '081299991111',
            'identity_status' => IdentityStatus::MENUNGGU_VERIFIKASI,
            'validity_status' => ValidityStatus::MENUNGGU_PEMERIKSAAN,
            'status' => DamageReportStatus::MENUNGGU_VERIFIKASI,
            'description' => 'Kerusakan umum',
        ]);

        $response = $this->actingAs($this->wakaUser)->get(route('waka.repair-reports.create', $report));
        $response->assertStatus(403);
    }

    /**
     * 11. Pengguna tanpa hak akses tidak dapat menjalankan aksi melalui URL atau request langsung.
     */
    public function test_unauthorized_users_cannot_perform_waka_or_kepala_actions(): void
    {
        $report = DamageReport::create([
            'room_id' => $this->testRoom->id,
            'reporter_type' => ReporterType::SISWA,
            'reporter_name' => 'Siswa X',
            'reporter_phone' => '0812333000',
            'identity_status' => IdentityStatus::MENUNGGU_VERIFIKASI,
            'validity_status' => ValidityStatus::MENUNGGU_PEMERIKSAAN,
            'status' => DamageReportStatus::MENUNGGU_VERIFIKASI,
            'description' => 'Test unauthorized',
        ]);

        // Regular user mencoba akses aksi Waka
        $this->actingAs($this->regularUser)
            ->post(route('waka.damage-reports.verify-identity', $report))
            ->assertForbidden();

        $this->actingAs($this->regularUser)
            ->post(route('waka.damage-reports.approve-minor', $report))
            ->assertForbidden();

        // Regular user mencoba akses aksi Kepala
        $this->actingAs($this->regularUser)
            ->post(route('kepala.approvals.approve', $report))
            ->assertForbidden();

        // Waka mencoba menyetujui aksi Kepala
        $this->actingAs($this->wakaUser)
            ->post(route('kepala.approvals.approve', $report))
            ->assertForbidden();

        // Kepala mencoba menyetujui aksi Waka
        $this->actingAs($this->kepalaUser)
            ->post(route('waka.damage-reports.approve-minor', $report))
            ->assertForbidden();
    }

    /**
     * 12. Pengaduan lama tetap dapat dibuka dan diproses dengan aman.
     */
    public function test_legacy_damage_reports_remain_readable_and_handled_safely(): void
    {
        $legacyReport = DamageReport::create([
            'room_id' => $this->testRoom->id,
            'reporter_id' => $this->regularUser->id,
            'description' => 'Pengaduan legacy sebelum penambahan kolom baru',
            'status' => DamageReportStatus::MENUNGGU_VERIFIKASI,
            'reporter_type' => null,
            'reporter_name' => null,
            'reporter_phone' => null,
            'reporter_identifier' => null,
            'identity_status' => null,
            'validity_status' => null,
            'damage_category' => null,
        ]);

        $this->assertSame($this->regularUser->name, $legacyReport->displayName());
        $this->assertSame('—', $legacyReport->maskedIdentifier());
        $this->assertFalse($legacyReport->canBeRepaired());

        $response = $this->actingAs($this->wakaUser)->get(route('waka.damage-reports.show', $legacyReport));
        $response->assertOk();
    }

    /**
     * 13. Riwayat keputusan dan alasan penolakan tersimpan dengan benar.
     */
    public function test_decision_history_and_rejection_notes_persisted_correctly(): void
    {
        $report = DamageReport::create([
            'room_id' => $this->testRoom->id,
            'reporter_type' => ReporterType::SISWA,
            'reporter_name' => 'Siswa D',
            'reporter_phone' => '081234567899',
            'identity_status' => IdentityStatus::TERVERIFIKASI,
            'validity_status' => ValidityStatus::VALID,
            'status' => DamageReportStatus::DIVERIFIKASI,
            'description' => 'Jendela pecah',
        ]);

        // Submit major
        $this->actingAs($this->wakaUser)->post(route('waka.damage-reports.submit-major', $report), [
            'category_note' => 'Perlu ganti kusen alumunium satu set',
        ]);

        $report->refresh();

        // Reject by Kepala
        $this->actingAs($this->kepalaUser)->post(route('kepala.approvals.reject', $report), [
            'notes' => 'Tunda hingga triwulan depan',
        ]);

        $report->refresh();

        $this->assertSame('Tunda hingga triwulan depan', $report->rejection_reason);
        $this->assertSame('Perlu ganti kusen alumunium satu set', $report->category_note);

        $approval1 = $report->approvals()->where('approval_level', 1)->first();
        $this->assertNotNull($approval1);
        $this->assertSame(ApprovalStatus::APPROVED, $approval1->status);

        $approval2 = $report->approvals()->where('approval_level', 2)->first();
        $this->assertNotNull($approval2);
        $this->assertSame(ApprovalStatus::REJECTED, $approval2->status);
        $this->assertSame('Tunda hingga triwulan depan', $approval2->notes);
    }

    /**
     * 14. Tidak ada penugasan duplikat akibat klik berulang.
     */
    public function test_duplicate_repair_assignment_is_prevented(): void
    {
        $report = DamageReport::create([
            'room_id' => $this->testRoom->id,
            'reporter_type' => ReporterType::GURU_TENDIK,
            'reporter_name' => 'Guru E',
            'reporter_phone' => '081200000000',
            'identity_status' => IdentityStatus::TERVERIFIKASI,
            'validity_status' => ValidityStatus::VALID,
            'damage_category' => DamageCategory::RINGAN,
            'status' => DamageReportStatus::DISETUJUI,
            'description' => 'Kran air patah',
        ]);

        // Approval level 1 exists
        Approval::create([
            'damage_report_id' => $report->id,
            'approver_id' => $this->wakaUser->id,
            'approval_level' => 1,
            'status' => ApprovalStatus::APPROVED,
        ]);

        // First repair assignment
        $response1 = $this->actingAs($this->wakaUser)->post(route('waka.repair-reports.store'), [
            'damage_report_id' => $report->id,
            'technician_name' => 'Pak Dul',
            'technician_contact' => '081233344455',
            'repair_description' => 'Ganti kran air',
            'started_at' => now()->format('Y-m-d'),
        ]);
        $response1->assertRedirect();
        $this->assertDatabaseCount('repair_reports', 1);

        // Second duplicate repair assignment attempt for the same report while active
        $response2 = $this->actingAs($this->wakaUser)->post(route('waka.repair-reports.store'), [
            'damage_report_id' => $report->id,
            'technician_name' => 'Pak Joko',
            'technician_contact' => '081233344499',
            'repair_description' => 'Ganti kran air lagi',
            'started_at' => now()->format('Y-m-d'),
        ]);

        $response2->assertSessionHas('error');
        $this->assertDatabaseCount('repair_reports', 1);
    }

    /**
     * 15. Validasi input backend mencegah pengiriman data tidak lengkap.
     */
    public function test_backend_validation_enforces_required_fields_based_on_reporter_type(): void
    {
        // Siswa tanpa nama / identitas / kelas / jurusan
        $response = $this->post(route('public.sarpras.pengaduan.store'), [
            'reporter_type' => ReporterType::SISWA->value,
            'reporter_name' => '',
            'room_id' => $this->testRoom->id,
            'description' => '',
        ]);

        $response->assertSessionHasErrors([
            'reporter_name',
            'reporter_phone',
            'reporter_identifier',
            'student_class',
            'student_major',
            'description',
        ]);
    }
}
