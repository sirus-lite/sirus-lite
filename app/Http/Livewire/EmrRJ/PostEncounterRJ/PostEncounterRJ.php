<?php

namespace App\Http\Livewire\EmrRJ\PostEncounterRJ;

use App\Http\Traits\EmrRJ\EmrRJTrait;
use App\Http\Traits\MasterPasien\MasterPasienTrait;
use App\Http\Traits\SATUSEHAT\EncounterTrait;
use App\Http\Traits\SATUSEHAT\PatientTrait;
use App\Http\Traits\SATUSEHAT\ConditionTrait;
use App\Http\Traits\SATUSEHAT\AllergyIntoleranceTrait;
use App\Http\Traits\SATUSEHAT\ObservationTrait;
use App\Http\Traits\SATUSEHAT\ProcedureTrait;
use App\Http\Traits\SATUSEHAT\MedicationRequestTrait;
use App\Http\Traits\SATUSEHAT\MedicationDispenseTrait;
use App\Http\Traits\SATUSEHAT\ServiceRequestTrait;
use App\Http\Traits\SATUSEHAT\SpecimenTrait;
use App\Http\Traits\SATUSEHAT\DiagnosticReportTrait;





use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

use Illuminate\Support\Facades\DB;


use Livewire\Component;

class PostEncounterRJ extends Component
{
    use EmrRJTrait,
        MasterPasienTrait,
        EncounterTrait,
        PatientTrait,
        ConditionTrait,
        AllergyIntoleranceTrait,
        ObservationTrait,
        ProcedureTrait,
        MedicationRequestTrait,
        MedicationDispenseTrait,
        ServiceRequestTrait,
        SpecimenTrait,
        DiagnosticReportTrait;


    public $rjNoRef;

    public array $dataDaftarPoliRJ = [];
    public array $dataPasienRJ = [];
    public string $EncounterID;

    protected $listeners = [
        'syncronizePostEncounterRJ' => 'mount',
    ];

    private function findData($rjno): void
    {
        // Ambil data daftar kunjungan (fallback ke array kosong)
        $this->dataDaftarPoliRJ = $this->findDataRJ($rjno)['dataDaftarRJ'] ?? [];

        // Ambil array UUID, atau empty array
        $uuids = $this->dataDaftarPoliRJ['satuSehatUuidRJ'] ?? [];
        $this->EncounterID = $uuids['encounter']['uuid'] ?? '';
    }




    public function postEncounterRJ()
    {
        // 1. Validasi minimal
        // $this->validate();

        // 2. Ambil data kunjungan & pasien
        $find = $this->findDataRJ($this->rjNoRef);
        $this->dataDaftarPoliRJ = $find['dataDaftarRJ'] ?? [];
        $this->dataPasienRJ  = $find['dataPasienRJ'] ?? [];

        // --- CEK: apakah encounter sudah pernah dikirim? ---
        if (!empty($this->dataDaftarPoliRJ['satuSehatUuidRJ']['encounter']['uuid'] ?? null)) {
            toastr()
                ->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addInfo(
                    'Encounter sudah pernah dikirim (ID: '
                        . $this->dataDaftarPoliRJ['satuSehatUuidRJ']['encounter']['uuid']
                        . ').'
                );
            return;
        }

        // 3. Tentukan class_code sesuai jenis layanan
        $classMap = [
            'RAJAL' => 'AMB', // Rawat Jalan → ambulatory
            'IGD' => 'EMER', // UGD→ emergency
            'RANAP' => 'IMP', // Rawat Inap  → inpatient
        ];
        $pelayananType = 'RAJAL';
        $classCode  = $classMap[$pelayananType] ?? 'AMB';

        // 4. Proses waktu masuk ruang (taskId3)
        $rawStart = $this->dataDaftarPoliRJ['taskIdPelayanan']['taskId3'] ?? null;
        if (empty($rawStart)) {
            toastr()
                ->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addError('Waktu masuk ruang (taskId3) tidak ditemukan, proses dibatalkan.');
            return;
        }

        try {
            $startDateIso = Carbon::createFromFormat('d/m/Y H:i:s', $rawStart)
                ->toIso8601String();
        } catch (\Exception $e) {
            toastr()
                ->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addError("Format waktu taskId3 tidak valid: “{$rawStart}”. Proses dibatalkan.");
            return;
        }

        // 5. Siapkan payload Encounter
        if (empty($this->dataPasienRJ['patientUuid'] ?? null)) {
            $this->updatepatientUuid($this->dataDaftarPoliRJ['regNo']);
        }


        $payload = [
            'status'  => 'arrived', // status awal untuk encounter baru
            'patientId'  => $this->dataPasienRJ['patientUuid'] ?? null,
            'patientName' => $this->dataPasienRJ['regName'] ?? null,
            'practitionerId' => $this->dataPasienRJ['drUuid']  ?? null,
            'practitionerName' => $this->dataPasienRJ['drName']  ?? null,
            'class_code' => $classCode,
            'startDate'  => $startDateIso,
            'organizationId' => env('SATUSEHAT_ORGANIZATION_ID') ?? null,
            'locationId' => $this->dataPasienRJ['poliUuid'] ?? null
        ];

        // 6. Validasi kehadiran UUID pasien & dokter
        $validator = Validator::make($payload, [
            'patientId' => 'required',
            'practitionerId' => 'required',
            'organizationId' => 'required'
        ], [
            'patientId.required' => 'UUID pasien belum tersedia.',
            'practitionerId.required' => 'UUID dokter belum tersedia.',
            'organizationId.required' => 'UUID poli belum tersedia.',
        ]);

        if ($validator->fails()) {
            toastr()
                ->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addError($validator->errors()->first());
            return;
        }

        // 7. Kirim ke Satu Sehat
        try {
            // 1) Kirim Encounter baru
            $this->initializeSatuSehat();
            $response = $this->createNewEncounter($payload);
            $this->EncounterID = $response['id'] ?? '';

            // 2) Simpan log Encounter ke dataDaftarPoliRJ
            $this->dataDaftarPoliRJ['satuSehatUuidRJ']['encounter'] = [
                'uuid' => $this->EncounterID,
                'status'  => 'arrived',
                'start_time' => $rawStart,
                'end_time' => '',
            ];

            // 3) Pindahkan pasien ke ruang (in-progress) + set location
            $this->startRoomEncounter(
                $this->EncounterID,
                [
                    'locationId' => $this->dataPasienRJ['poliUuid'],
                    'startDate'  => $startDateIso
                ]
            );

            // 4) Update log in-progress
            $this->dataDaftarPoliRJ['satuSehatUuidRJ']['encounter'] = [
                'uuid' => $this->EncounterID,
                'status'  => 'in-progress',
                'locationId' => $this->dataPasienRJ['poliUuid'],
            ];

            // 5) Persist ke database
            $this->updateJsonRJ($this->rjNoRef, $this->dataDaftarPoliRJ);
            $this->emit('syncronizePostEncounterRJ');

            toastr()
                ->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addSuccess("Encounter terkirim (ID: {$this->EncounterID})");
        } catch (\Exception $e) {
            dd($e->getMessage());
            toastr()
                ->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addError("Gagal kirim Encounter: " . $e->getMessage());
        }
    }

    public function getEncounterRJ($encounterId)
    {
        $this->initializeSatuSehat();
        $existing = $this->getEncounter($encounterId);
        dd($existing);
    }

    private function updatepatientUuid(string $regNo = ''): void
    {

        $dataPasien = $this->findDataMasterPasien($regNo ?? '');
        // 1. Inisialisasi koneksi dan cari Patient berdasarkan NIK
        $this->initializeSatuSehat();
        $nik = $dataPasien['pasien']['identitas']['nik'] ?? '';

        $entries = collect(
            $this->searchPatient(['nik' => $nik])['entry'] ?? []
        );

        // 2. Jika tidak ada, buat pasien baru (pakai data dari $dataPasien['pasien'])
        if ($entries->isEmpty()) {
            toastr()
                ->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addWarning("Tidak ada pasien ditemukan dengan NIK: {$nik}");
            return;
        }

        // 3. Ambil UUID Patient pertama dari hasil pencarian
        $newUuid = $entries->pluck('resource.id')->first();
        $currentUuid = $dataPasien['pasien']['identitas']['patientUuid'] ?? null;

        // 4. Jika belum ada UUID tersimpan, set dan notify
        if (empty($currentUuid)) {
            $dataPasien['pasien']['identitas']['patientUuid'] = $newUuid;
            $this->dataPasienRJ['patientUuid'] = $newUuid;

            $this->updateJsonMasterPasien($regNo, $dataPasien);
            // updateDB
            DB::table('rsmst_pasiens')->where('reg_no', $regNo)
                ->update([
                    'patient_uuid' => $newUuid,
                ]);

            toastr()
                ->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addSuccess("patientUuid di-set ke {$newUuid}");
            return;
        }

        // 5. Jika UUID sudah sama, beri info
        if ($currentUuid === $newUuid) {
            toastr()
                ->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addInfo("patientUuid sudah sesuai dengan data terbaru");
            return;
        }

        // 6. Jika berbeda, cek apakah UUID lama masih ada dalam hasil pencarian
        $oldStillExists = $entries
            ->pluck('resource.id')
            ->contains($currentUuid);

        if ($oldStillExists) {
            toastr()
                ->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addSuccess("patientUuid lama ({$currentUuid}) masih ditemukan");
        } else {
            toastr()
                ->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addWarning("patientUuid lama ({$currentUuid}) tidak ada di hasil terbaru");
        }
    }










    public function postKeluhanUtamaRJ()
    {
        // 1. Validasi dan ambil data kunjungan & pasien
        $find = $this->findDataRJ($this->rjNoRef);
        $dataDaftarPoliRJ = $find['dataDaftarRJ'] ?? [];
        $dataPasienRJ = $find['dataPasienRJ'] ?? [];

        // Ambil nilai‐nilai penting
        $encounterUuid = $dataDaftarPoliRJ['satuSehatUuidRJ']['encounter']['uuid'] ?? null;
        $chiefComplaintUuid  = $dataDaftarPoliRJ['satuSehatUuidRJ']['chiefComplaint']['uuid'] ?? null;
        $keluhanUtama  = $dataDaftarPoliRJ['anamnesa']['keluhanUtama']['keluhanUtama'] ?? null;
        $onsetDate = $dataDaftarPoliRJ['taskIdPelayanan']['taskId3'] ?? null;

        // Pastikan encounter sudah terkirim
        $encounterUuid = $dataDaftarPoliRJ['satuSehatUuidRJ']['encounter']['uuid'] ?? null;
        if (empty($encounterUuid)) {
            toastr()
                ->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addError('Encounter belum terkirim, proses keluhan utama dibatalkan.');
            return;
        }

        // Cek apakah sudah pernah kirim keluhan utama
        if (!empty($chiefComplaintUuid)) {
            toastr()
                ->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addInfo(
                    'Keluhan utama telah dikirim (ID: ' .
                        $dataDaftarPoliRJ['satuSehatUuidRJ']['chiefComplaint']['uuid'] . ').'
                );
            return;
        }

        if (empty($keluhanUtama)) {
            toastr()
                ->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addWarning('Keluhan utama belum diisi. Silakan lengkapi anamnesa keluhan utama sebelum mengirim.');
            return;
        }

        if (empty($onsetDate)) {
            toastr()
                ->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addError('Waktu masuk ruang (taskId3) tidak ditemukan, proses dibatalkan.');
            return;
        }

        // Konversi waktu masuk ruang ke ISO8601
        try {
            $onsetDateIso = Carbon::createFromFormat('d/m/Y H:i:s', $onsetDate)
                ->toIso8601String();
        } catch (\Throwable $e) {
            toastr()->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addError("Format waktu “{$onsetDateIso}” tidak valid.");
            return;
        }

        $payload = [
            'patientId' => $dataPasienRJ['patientUuid'] ?? null,
            'encounterId'  => $encounterUuid,
            'snomed_code'  => $dataDaftarPoliRJ['anamnesa']['keluhanUtama']['snomedCode'] ?? null,  // '21522001' Abdominal pain (finding)
            'snomed_display'  => $dataDaftarPoliRJ['anamnesa']['keluhanUtama']['snomedDisplay'] ?? null,
            'complaint_text'  => $dataDaftarPoliRJ['anamnesa']['keluhanUtama']['keluhanUtama'] ?? null,
            'onsetDate' => $onsetDateIso,
            'recordedDate' =>  Carbon::now()->toIso8601String(),
            'severity_code' =>  $dataDaftarPoliRJ['anamnesa']['keluhanUtama']['severityCode'] ?? null, // Tingkat keparahan
            'severity_display' =>  $dataDaftarPoliRJ['anamnesa']['keluhanUtama']['severityDisplay'] ?? null,
        ];

        // 3. Validasi payload
        $validator = Validator::make($payload, [
            'patientId' => 'required|string',
            'encounterId' => 'required|string',
            // 'snomed_code' => 'required|string',
            'complaint_text' => 'required|string',
        ], [
            'patientId.required' => 'UUID pasien wajib diisi.',
            'encounterId.required' => 'UUID encounter wajib diisi.',
            // 'snomed_code.required' => 'Kode SNOMED CT untuk keluhan utama wajib diisi.',
            'complaint_text.required' => 'Deskripsi keluhan utama wajib diisi.',
        ]);

        if ($validator->fails()) {
            toastr()
                ->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addError($validator->errors()->first());
            return;
        }

        try {
            $this->initializeSatuSehat();
            $result = $this->createChiefComplaint($payload);
            $conditionId = $result['id'] ?? '';

            // Simpan log keluhan utama
            $dataDaftarPoliRJ['satuSehatUuidRJ']['chiefComplaint']['uuid'] = $conditionId;
            $this->updateJsonRJ($this->rjNoRef, $dataDaftarPoliRJ);

            toastr()
                ->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addSuccess("Keluhan utama terkirim (ID: {$conditionId})");
        } catch (\Exception $e) {
            toastr()
                ->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addError('Gagal kirim keluhan utama: ' . $e->getMessage());
        }
    }

    public function postRiwayatPenyakitSekarangRJ(): void
    {
        // 1. Ambil data pasien & encounter
        $find = $this->findDataRJ($this->rjNoRef);
        $dataDaftarPoliRJ = $find['dataDaftarRJ'] ?? [];
        $dataPasienRJ  = $find['dataPasienRJ'] ?? [];

        $patientUuid = $dataPasienRJ['patientUuid'] ?? null;
        $encounterUuid = $dataDaftarPoliRJ['satuSehatUuidRJ']['encounter']['uuid'] ?? null;
        $currentCondUuid  = $dataDaftarPoliRJ['satuSehatUuidRJ']['currentCondition']['uuid'] ?? null;

        // 2. Validasi prasyarat
        if (empty($patientUuid)) {
            toastr()
                ->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addError('UUID pasien tidak ditemukan, proses dibatalkan.');
            return;
        }
        if (empty($encounterUuid)) {
            toastr()
                ->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addError('Encounter belum terkirim, proses kondisi sekarang dibatalkan.');
            return;
        }

        if (!empty($currentCondUuid)) {
            toastr()
                ->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addInfo("Kondisi sekarang telah dikirim (ID: {$currentCondUuid}).");
            return;
        }

        // 3. Ambil input kondisi sekarang
        $code = $dataDaftarPoliRJ['anamnesa']['riwayatPenyakitSekarangUmum']['snomedCode'] ?? '';
        $display = $dataDaftarPoliRJ['anamnesa']['riwayatPenyakitSekarangUmum']['snomedDisplay'] ?? '';
        $text = $dataDaftarPoliRJ['anamnesa']['riwayatPenyakitSekarangUmum']['riwayatPenyakitSekarangUmum'] ?? '';
        // atau field kamu gunakan
        if (empty($code)) {
            toastr()
                ->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addError('Data kondisi sekarang belum diisi.');
            return;
        }

        // 4. Konversi tanggal onset (opsional)
        try {
            if (!empty($anam['onsetDate'])) {
                $onsetDate = $dataDaftarPoliRJ['taskIdPelayanan']['taskId3'] ?? null;
                $onsetIso = Carbon::createFromFormat('d/m/Y H:i:s', $onsetDate)
                    ->toIso8601String();
            }
        } catch (\Throwable $e) {
            toastr()
                ->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addError('Format tanggal onset kondisi tidak valid.');
            return;
        }

        // 5. Siapkan payload
        $payload = [
            'patientId' => $patientUuid,
            'encounterId' => $encounterUuid,
            'snomed_code' => $code,
            'snomed_display' => $display,
            'complaint_text' => $text,
            'recordedDate' => now()->toIso8601String(),
        ];
        if (!empty($onsetIso)) {
            $payload['onsetDate'] = $onsetIso;
        }
        if (!empty($note)) {
            $payload['note'] = $note ?? '';
        }

        // 6. Validasi payload
        $validator = Validator::make($payload, [
            'patientId' => 'required|string',
            'encounterId' => 'required|string',
            'snomed_code' => 'nullable|string',
            'complaint_text' => 'nullable|string',
        ], [
            'patientId.required' => 'UUID pasien wajib diisi.',
            'encounterId.required' => 'UUID encounter wajib diisi.',
            'complaint_text.string' => 'Deskripsi kondisi harus berupa teks.',
        ]);
        if ($validator->fails()) {
            toastr()
                ->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addError('Validasi gagal: ' . $validator->errors()->first());
            return;
            return;
        }

        // 7. Kirim ke SatuSehat
        try {
            $this->initializeSatuSehat();
            $result  = $this->createCurrentCondition($payload);
            $conditionId = $result['id'] ?? '';

            // 8. Simpan UUID ke JSON RJ
            $dataDaftarPoliRJ['satuSehatUuidRJ']['currentCondition']['uuid'] = $conditionId;
            $this->updateJsonRJ($this->rjNoRef, $dataDaftarPoliRJ);

            toastr()
                ->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addSuccess("Kondisi sekarang berhasil dikirim (ID: {$conditionId}).");
        } catch (\Exception $e) {
            toastr()
                ->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addError('Gagal mengirim kondisi sekarang: ' . $e->getMessage());
        }
    }


    public function postRiwayatPenyakitDahuluRJ()
    {
        // 1. Validasi & ambil data kunjungan & pasien
        $find  = $this->findDataRJ($this->rjNoRef);
        $dataDaftarPoliRJ = $find['dataDaftarRJ'] ?? [];
        $dataPasienRJ = $find['dataPasienRJ'] ?? [];

        $patientUuid  = $dataPasienRJ['patientUuid'] ?? null;
        $encounterUuid = $dataDaftarPoliRJ['satuSehatUuidRJ']['encounter']['uuid'] ?? null;
        $historyData  = $dataDaftarPoliRJ['anamnesa']['riwayatPenyakitDahulu']['riwayatPenyakitDahulu'] ?? null;
        $pastHistUuid = $dataDaftarPoliRJ['satuSehatUuidRJ']['pastMedicalHistory']['uuid'] ?? null;


        // 2. Validasi prasyarat
        if (empty($patientUuid)) {
            toastr()
                ->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addError('UUID pasien tidak ditemukan, proses dibatalkan.');
            return;
        }
        if (empty($encounterUuid)) {
            toastr()
                ->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addError('Encounter belum terkirim, proses dibatalkan.');
            return;
        }
        if (!empty($pastHistUuid)) {
            toastr()
                ->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addInfo("Riwayat penyakit dahulu sudah dikirim (ID: {$pastHistUuid}).");
            return;
        }

        // Pastikan minimal ada teks atau SNOMED
        $snomedCode = $dataDaftarPoliRJ['anamnesa']['riwayatPenyakitDahulu']['snomedCode'] ?? null;
        $snomedDisplay = $dataDaftarPoliRJ['anamnesa']['riwayatPenyakitDahulu']['snomedDisplay'] ?? null;
        $historyText = $historyData ?? null;

        if (empty($snomedCode) && empty($historyText)) {
            toastr()->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addWarning('Data riwayat penyakit dahulu belum diisi.');
            return;
        }

        // 3. Konversi tanggal onset & abatement
        try {
            $onsetIso  = !empty($dataDaftarPoliRJ['taskIdPelayanan']['taskId3'])
                ? Carbon::createFromFormat('d/m/Y H:i:s', $dataDaftarPoliRJ['taskIdPelayanan']['taskId3'])->toIso8601String()
                : null;
            //isi kosong jika sembuh isi dengan tanggal pasien sembuh
            $abatementIso = !empty(null)
                ? Carbon::createFromFormat('d/m/Y H:i:s', $dataDaftarPoliRJ['taskIdPelayanan']['taskId3'])->toIso8601String()
                : null;
        } catch (\Throwable $e) {
            toastr()->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addError('Format tanggal riwayat penyakit tidak valid.');
            return;
        }

        // 4. Siapkan payload
        $payload = [
            'patientId' => $patientUuid,
            'encounterId'  => $encounterUuid,
            'snomed_code' => $snomedCode,
            'snomed_display' => $snomedDisplay,
            'history_text' => $historyText,
            'recordedDate' => now()->toIso8601String(),
        ];


        if ($onsetIso)  $payload['onsetDate']  = $onsetIso;
        if ($abatementIso) $payload['abatementDate'] = $abatementIso;
        if (!empty($historyData['note'])) {
            $payload['note'] = $historyData['note'];
        }


        // 5. Validasi payload
        $validator = Validator::make($payload, [
            'patientId' => 'required|string',
            'snomed_code'  => 'nullable|string',
            'history_text' => 'nullable|string',
        ], [
            'patientId.required' => 'UUID pasien wajib diisi.',
            'history_text.required' => 'Deskripsi riwayat penyakit wajib diisi.',
        ]);

        if ($validator->fails()) {
            toastr()->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addError($validator->errors()->first());
            return;
        }

        // 6. Kirim ke SatuSehat
        try {
            $this->initializeSatuSehat();
            $result = $this->createPastMedicalHistory($payload);
            $historyId = $result['id'] ?? '';

            // 7. Simpan UUID ke JSON RJ
            $dataDaftarPoli['satuSehatUuidRJ']['pastMedicalHistory']['uuid'] = $historyId;
            $this->updateJsonRJ($this->rjNoRef, $dataDaftarPoliRJ);

            toastr()->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addSuccess("Riwayat penyakit dahulu terkirim (ID: {$historyId}).");
        } catch (\Exception $e) {
            toastr()->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addError('Gagal kirim riwayat penyakit dahulu: ' . $e->getMessage());
        }
    }


    public function getConditionRJ($encounterId)
    {
        $this->initializeSatuSehat();
        $existing = $this->searchConditionsByEncounter($encounterId);
        dd($existing);
    }








    public function postAlergiRJ(): void
    {
        $find = $this->findDataRJ($this->rjNoRef);
        $dataDaftarPoliRJ = $find['dataDaftarRJ'] ?? [];
        $dataPasienRJ = $find['dataPasienRJ'] ?? [];

        $patientUuid = $dataPasienRJ['patientUuid'] ?? null;
        $encounterUuid = $dataDaftarPoliRJ['satuSehatUuidRJ']['encounter']['uuid'] ?? null;
        $alergies = $dataDaftarPoliRJ['anamnesa']['alergi']['alergiSnomed'] ?? [];
        $sentRecords = $dataDaftarPoliRJ['satuSehatUuidRJ']['allergyIntolerance'] ?? [];
        $recorderUuid  = $dataPasienRJ['drUuid'] ?? null;
        // 1) Cek Patient UUID
        if (empty($patientUuid)) {
            toastr()
                ->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addError('UUID pasien belum tersedia. Proses alergi dibatalkan.');
            return;
        }

        // 2) Cek Encounter UUID
        if (empty($encounterUuid)) {
            toastr()
                ->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addError('Encounter belum terkirim. Proses alergi dibatalkan.');
            return;
        }

        // 3) Cek minimal satu alergi
        if (empty($alergies)) {
            toastr()
                ->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addWarning('Silakan pilih minimal satu alergi sebelum mengirim.');
            return;
        }

        // 4) Cek Practitioner/Recorder UUID
        if (empty($recorderUuid)) {
            toastr()
                ->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addError('UUID practitioner belum tersedia. Proses alergi dibatalkan.');
            return;
        }

        $this->initializeSatuSehat();

        // Siapkan tempat simpan UUID alergi
        $idAlergies = [];
        foreach ($alergies as $i => $alergi) {

            // Jika sudah pernah dikirim (berdasarkan snomedCode), skip
            $sent = collect($sentRecords)
                ->firstWhere('snomedCode', $alergi['snomedCode']);

            if ($sent) {
                toastr()
                    ->closeOnHover(true)
                    ->closeDuration(3)
                    ->positionClass('toast-top-left')
                    ->addInfo(
                        "{$alergi['snomedDisplay']} telah dikirim sebelumnya (UUID: {$sent['uuid']})."
                    );

                continue;
            }

            $payload = [
                'patientId' => $patientUuid,
                'encounterId'  => $encounterUuid, // wajib
                'recorderId' => $recorderUuid,  // wajib (Practitioner/{id})
                'code' => $alergi['snomedCode'],
                'display' => $alergi['snomedDisplay'],
                'category'  => 'food', // atau 'food', 'environment'
                'criticality'  => 'low',  // optional, sesuai trait default
                'onset'  => now()->toIso8601String(),
                'note' => $alergi['note'] ?? '',
            ];
            try {
                $res = $this->createAllergyIntolerance($payload);
                $id  = $res['id'] ?? null;

                if ($id) {
                    $idAlergies[] = $id;
                    // Simpan ke array satuSehatUuidRJ
                    $dataDaftarPoliRJ['satuSehatUuidRJ']['allergyIntolerance'][] = [
                        'uuid' => $id,
                        'snomedCode' => $alergi['snomedCode'],
                        'snomedDisplay' => $alergi['snomedDisplay'],

                    ];
                }
            } catch (\Exception $e) {
                toastr()
                    ->closeOnHover(true)
                    ->closeDuration(3)
                    ->positionClass('toast-top-left')
                    ->addError("Gagal kirim alergi ke-" . ($i + 1) . ": " . $e->getMessage());
            }
        }
        // 7. Persist JSON RJ sekali saja
        $this->updateJsonRJ($this->rjNoRef, $dataDaftarPoliRJ);

        // 8. Notifikasi sukses
        if (!empty($idAlergies)) {
            toastr()
                ->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addSuccess('Alergi terkirim (ID: ' . implode(', ', $idAlergies) . ').');
        }
    }

    public function getAlergiRJ(): void
    {
        $find = $this->findDataRJ($this->rjNoRef);
        $dataPasienRJ = $find['dataPasienRJ'] ?? [];

        $patientUuid = $dataPasienRJ['patientUuid'] ?? null;
        $alergiList = $this->fetchAllergyIntoleranceByPatient($patientUuid);

        dd($alergiList);
    }




    public function postTtvRJ()
    {
        // 1) Ambil data kunjungan & pasien
        $find = $this->findDataRJ($this->rjNoRef);
        $dataDaftarPoliRJ = $find['dataDaftarRJ'] ?? [];
        $dataPasienRJ = $find['dataPasienRJ'] ?? [];
        $patientUuid = $dataPasienRJ['patientUuid'] ?? null;
        $encounterUuid = $dataDaftarPoliRJ['satuSehatUuidRJ']['encounter']['uuid'] ?? null;
        $ttv = $dataDaftarPoliRJ['pemeriksaan']['tandaVital'] ?? [];
        $sentRecords = $dataDaftarPoliRJ['satuSehatUuidRJ']['vitalSigns'] ?? [];
        $performerId  = $dataPasienRJ['drUuid'] ?? null; //dokter / perawat yang melakukan ttv sementara pake dokter dulu

        // 2) Validasi prasyarat
        if (!$patientUuid) {
            toastr()
                ->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addError('UUID pasien belum tersedia. Proses TTV dibatalkan.');
            return;
        }

        if (!$performerId) {
            toastr()
                ->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addError('UUID performer (dokter/perawat) belum tersedia. Proses TTV dibatalkan.');
            return;
        }

        if (!$encounterUuid) {
            toastr()
                ->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addError('Encounter belum terkirim. Proses TTV dibatalkan.');
            return;
        }

        // 3) Cek minimal satu nilai TTV terisi
        $ttvKeys = [
            'sistolik',
            'distolik',
            'frekuensiNadi',
            'frekuensiNafas',
            'suhu',
            'spo2',
            'gda',
        ];

        $hasAny = false;
        foreach ($ttvKeys as $key) {
            $value = $ttv[$key] ?? null;

            if (!empty($value) && $value !== '0') {
                $hasAny = true;
                break;
            }
        }

        if (!$hasAny) {
            toastr()
                ->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addWarning('Isi minimal satu nilai tanda‐tanda vital sebelum mengirim.');
            return;
        }

        // 4) Definisi map komponen TTV
        $vitalMap = [
            [
                'key' => 'sistolik',
                'code' => '8480-6',
                'display' => 'Systolic blood pressure',
                'unitCode' => 'mm[Hg]',
                'unitDisplay' => 'mmHg',
                'value' => $ttv['sistolik'] ?? null
            ],
            [
                'key' => 'diastolik',
                'code' => '8462-4',
                'display' => 'Diastolic blood pressure',
                'unitCode' => 'mm[Hg]',
                'unitDisplay' => 'mmHg',
                'value' => $ttv['distolik'] ?? null
            ],
            [
                'key' => 'frekuensiNadi',
                'code' => '8867-4',
                'display' => 'Heart rate',
                'unitCode' => '/min',
                'unitDisplay' => 'beats/minute',
                'value' => $ttv['frekuensiNadi'] ?? null
            ],
            [
                'key' => 'frekuensiNafas',
                'code' => '9279-1',
                'display' => 'Respiratory rate',
                'unitCode' => '/min',
                'unitDisplay' => 'breaths/minute',
                'value' => $ttv['frekuensiNafas'] ?? null
            ],
            [
                'key' => 'suhu',
                'code' => '8310-5',
                'display' => 'Body temperature',
                'unitCode' => 'Cel',
                'unitDisplay' => '°C',
                'value' => $ttv['suhu'] ?? null
            ],
            [
                'key' => 'spo2',
                'code' => '2708-6',
                'display' => 'Oxygen saturation',
                'unitCode' => '%',
                'unitDisplay' => '%',
                'value' => $ttv['spo2'] ?? null
            ],
            [
                'key' => 'gda',
                'code' => '15074-8',
                'display' => 'Blood glucose, random',
                'unitCode' => 'mg/dL',
                'unitDisplay' => 'mg/dL',
                'value' => $ttv['gda'] ?? null
            ],
        ];

        try {
            //Waktu yang dipakai waktu masuk poli
            $effective = Carbon::createFromFormat('d/m/Y H:i:s', $dataDaftarPoliRJ['taskIdPelayanan']['taskId4'])->toIso8601String();
        } catch (\Throwable $e) {
            toastr()
                ->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addInfo('Task Id 4 tidak valid (Waktu Masuk Poli).');
            return;
        }

        $this->initializeSatuSehat();
        // 5) Loop untuk kirim tiap komponen TTV
        foreach ($vitalMap as $item) {
            // skip null / zero
            if (empty($item['value']) || $item['value'] === '0') {
                continue;
            }
            // cast ke numeric
            $numeric = is_numeric($item['value'])
                ? (strpos($item['value'], '.') !== false ? (float)$item['value'] : (int)$item['value'])
                : null;
            if ($numeric === null) {
                continue;
            }
            // cek sudah dikirim?
            if (collect($sentRecords)->firstWhere('code', $item['code'])) {
                toastr()
                    ->closeOnHover(true)
                    ->closeDuration(3)
                    ->positionClass('toast-top-left')
                    ->addInfo("{$item['display']} sudah terkirim.");
                continue;
            }


            // payload per-komponen
            $payload = [
                'patientId'   => $patientUuid,
                'encounterId' => $encounterUuid,
                'performerId' => $performerId,
                'effectiveDate' => $effective, // dari kode kamu sebelumnya
                // bangun code + valueQuantity, bukan components array
                'code' => [
                    'system'  => 'http://loinc.org',
                    'code'    => $item['code'],
                    'display' => $item['display'],
                ],
                'valueQuantity' => [
                    'value'  => $numeric,
                    'unit'   => $item['unitDisplay'],
                    'system' => 'http://unitsofmeasure.org',
                    'code'   => $item['unitCode'],
                ],
            ];

            try {
                $result = $this->createObservation($payload);
                $obsId  = $result['id'] ?? null;
                if (!$obsId) {
                    continue;
                }

                // simpan tiap UUID
                $dataDaftarPoliRJ['satuSehatUuidRJ']['vitalSigns'][] = [
                    'uuid'        => $obsId,
                    'code'        => $item['code'],
                    'unitCode'    => $item['unitCode'],
                    'unitDisplay' => $item['unitDisplay'],
                    'value'       => $numeric,
                ];

                toastr()
                    ->closeOnHover(true)
                    ->closeDuration(3)
                    ->positionClass('toast-top-left')
                    ->addSuccess("{$item['display']} terkirim (UUID: {$obsId}).");
            } catch (\Exception $e) {
                toastr()
                    ->closeOnHover(true)
                    ->closeDuration(3)
                    ->positionClass('toast-top-left')
                    ->addError("Gagal kirim {$item['display']}: " . $e->getMessage());
            }

            $this->updateJsonRJ($this->rjNoRef, $dataDaftarPoliRJ);
        }
    }



    public function postAntropometriRJ()
    {

        // 1) Ambil data kunjungan & pasien
        $find = $this->findDataRJ($this->rjNoRef);
        $dataDaftarPoliRJ = $find['dataDaftarRJ'] ?? [];
        $dataPasienRJ     = $find['dataPasienRJ'] ?? [];

        $patientUuid   = $dataPasienRJ['patientUuid'] ?? null;
        $encounterUuid = $dataDaftarPoliRJ['satuSehatUuidRJ']['encounter']['uuid'] ?? null;
        $sentNutrition = $dataDaftarPoliRJ['satuSehatUuidRJ']['antropometri'] ?? [];
        $performerId   = $dataPasienRJ['drUuid'] ?? null;
        // 2) Validasi prasyarat
        if (!$patientUuid || !$performerId || !$encounterUuid) {
            $msg = !$patientUuid   ? 'UUID pasien belum tersedia.'
                : (!$performerId  ? 'UUID performer belum tersedia.'
                    : 'Encounter belum terkirim.');
            toastr()->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addError("{$msg} Proses nutrisi dibatalkan.");
            return;
        }

        $rawNutrisi = $dataDaftarPoliRJ['pemeriksaan']['nutrisi'] ?? [];
        // 3) Kumpulkan input nutrisi dan cek minimal satu terisi
        $nutrisi = [
            'berat_badan'         => $rawNutrisi['bb']   ?? null, // bb = Berat Badan (kg)
            'tinggi_badan'        => $rawNutrisi['tb']   ?? null, // tb = Tinggi Badan (cm)
            'bmi'                 => $rawNutrisi['imt']  ?? null, // imt = Index Masa Tubuh
            'lingkar_kepala'      => $rawNutrisi['lk']   ?? null, // lk = Lingkar Kepala (cm)
            'lingkar_lengan_atas' => $rawNutrisi['lila'] ?? null, // lila = Lingkar Lengan Atas (cm)
        ];

        $hasAny = false;
        $nutrisiKeys = [
            'berat_badan',
            'tinggi_badan',
            'bmi',
            'lingkar_kepala',
            'lingkar_lengan_atas'
        ];
        foreach ($nutrisiKeys as $key) {
            $value = $nutrisi[$key] ?? null;

            if (!empty($value) && $value !== '0') {
                $hasAny = true;
                break;
            }
        }

        if (!$hasAny) {
            toastr()
                ->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addWarning('Isi minimal satu nilai nutrisi sebelum mengirim.');
            return;
        }

        // 4) Definisi map nutrisi ke LOINC + unit
        $nutrisiMap = [
            [
                'key'          => 'berat_badan',
                'code'         => '29463-7',
                'display'      => 'Body weight Measured',
                'unitCode'     => 'kg',
                'unitDisplay'  => 'kg',
                'value'        => $rawNutrisi['bb'] ?? null,  // bb sesuai field JSON
            ],
            [
                'key'          => 'tinggi_badan',
                'code'         => '8302-2',
                'display'      => 'Body height',
                'unitCode'     => 'cm',
                'unitDisplay'  => 'cm',
                'value'        => $rawNutrisi['tb'] ?? null,
            ],
            [
                'key'          => 'bmi',
                'code'         => '39156-5',
                'display'      => 'Body mass index',
                'unitCode'     => 'kg/m2',
                'unitDisplay'  => 'kg/m2',
                'value'        => $rawNutrisi['imt'] ?? null,
            ],
            [
                'key'          => 'lingkar_kepala',
                'code'         => '8287-5',
                'display'      => 'Head circumference',
                'unitCode'     => 'cm',
                'unitDisplay'  => 'cm',
                'value'        => $rawNutrisi['lk'] ?? null,
            ],
            [
                'key'          => 'lingkar_lengan_atas',
                'code'         => '8289-1',
                'display'      => 'Upper arm circumference',
                'unitCode'     => 'cm',
                'unitDisplay'  => 'cm',
                'value'        => $rawNutrisi['lila'] ?? null,
            ],
        ];


        // 5) Tentukan effectiveDate (mis. waktu masuk poli)
        try {
            $effective = Carbon::createFromFormat('d/m/Y H:i:s', $dataDaftarPoliRJ['taskIdPelayanan']['taskId4'])
                ->toIso8601String();
        } catch (\Throwable $e) {
            toastr()->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addInfo('Waktu nutrisi tidak valid, gunakan waktu sekarang.');
            return;
        }

        $this->initializeSatuSehat();

        // 6) Loop kirim tiap observasi nutrisi
        foreach ($nutrisiMap as $item) {
            // skip null / zero
            if (empty($item['value']) || $item['value'] === '0') {
                continue;
            }

            // cast ke numeric
            $numeric = is_numeric($item['value'])
                ? (strpos($item['value'], '.') !== false ? (float)$item['value'] : (int)$item['value'])
                : null;
            if ($numeric === null) {
                continue;
            }

            // cek sudah dikirim?
            if (collect($sentNutrition)->firstWhere('code', $item['code'])) {
                toastr()
                    ->closeOnHover(true)
                    ->closeDuration(3)
                    ->positionClass('toast-top-left')
                    ->addInfo("{$item['display']} sudah terkirim.");
                continue;
            }

            // build payload per-observasi nutrisi
            $payload = [
                'patientId'     => $patientUuid,
                'encounterId'   => $encounterUuid,
                'performerId'   => $performerId,
                'effectiveDate' => $effective,
                'code' => [
                    'system'  => 'http://loinc.org',
                    'code'    => $item['code'],
                    'display' => $item['display'],
                ],
                'valueQuantity' => [
                    'value'  => $numeric,
                    'unit'   => $item['unitDisplay'],
                    'system' => 'http://unitsofmeasure.org',
                    'code'   => $item['unitCode'],
                ],
            ];

            try {
                $res   = $this->createObservation($payload);
                $obsId = $res['id'] ?? null;
                if (!$obsId) {
                    continue;
                }

                // simpan tiap UUID
                $dataDaftarPoliRJ['satuSehatUuidRJ']['antropometri'][] = [
                    'uuid'        => $obsId,
                    'code'        => $item['code'],
                    'unitCode'    => $item['unitCode'],
                    'unitDisplay' => $item['unitDisplay'],
                    'value'       => $numeric,
                ];

                toastr()
                    ->closeOnHover(true)
                    ->closeDuration(3)
                    ->positionClass('toast-top-left')
                    ->addSuccess("{$item['display']} terkirim (UUID: {$obsId}).");
            } catch (\Exception $e) {
                toastr()
                    ->closeOnHover(true)
                    ->closeDuration(3)
                    ->positionClass('toast-top-left')
                    ->addError("Gagal kirim {$item['display']}: " . $e->getMessage());
            }
            // update JSON lokal setelah tiap kirim
            $this->updateJsonRJ($this->rjNoRef, $dataDaftarPoliRJ);
        }
    }


    public function getObservationRJ($encounterId)
    {
        // 1) Inisialisasi koneksi SatuSehat
        $this->initializeSatuSehat();

        // 2) Cari semua Observation untuk encounter tersebut
        $existing = $this->searchObservationsByEncounter($encounterId);

        // 3) Tampilkan hasil untuk debug
        dd($existing);
    }







    public function postDiagnosaRJ()
    {
        // 1) Ambil data
        $find             = $this->findDataRJ($this->rjNoRef);
        $dataDaftarPoliRJ = $find['dataDaftarRJ'] ?? [];
        $dataPasienRJ     = $find['dataPasienRJ'] ?? [];

        $patientUuid   = $dataPasienRJ['patientUuid']       ?? null;
        $encounterUuid = $dataDaftarPoliRJ['satuSehatUuidRJ']['encounter']['uuid'] ?? null;
        $diagnoses     = $dataDaftarPoliRJ['diagnosis']     ?? [];
        $sentDiag      = $dataDaftarPoliRJ['satuSehatUuidRJ']['diagnosis'] ?? [];

        // 2) Validasi
        if (!$patientUuid) {
            toastr()
                ->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addError('UUID pasien belum tersedia. Proses diagnosa dibatalkan.');
            return;
        }

        // Validasi UUID encounter
        if (!$encounterUuid) {
            toastr()
                ->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addError('UUID encounter belum tersedia. Proses diagnosa dibatalkan.');
            return;
        }

        // Validasi ada data diagnosa
        if (empty($diagnoses)) {
            toastr()
                ->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addInfo('Tidak ada data diagnosa untuk dikirim.');
            return;
        }

        $this->initializeSatuSehat();
        // 3) Loop kirim setiap diagnosa
        foreach ($diagnoses as $diag) {
            $icd10      = $diag['icdX']        ?? null;
            $desc       = $diag['diagDesc']    ?? '';
            $textLocal  = $diag['kategoriDiagnosa'] ?? $desc;
            // skip jika sudah pernah dikirim
            $existing = collect($sentDiag)->firstWhere('icd10Code', $icd10);
            if ($existing) {
                $uuidSent = $existing['uuid'] ?? '';
                toastr()
                    ->closeOnHover(true)
                    ->closeDuration(3)
                    ->positionClass('toast-top-left')
                    ->addInfo("Diagnosa {$desc} sudah terkirim (UUID: {$uuidSent}).");
                continue;
            }

            // ambil Task ID 5 (format "d/m/Y H:i:s") lalu convert ke ISO8601
            try {
                $recorded = Carbon::createFromFormat('d/m/Y H:i:s', $dataDaftarPoliRJ['taskIdPelayanan']['taskId5'])->toIso8601String();
            } catch (\Throwable $e) {
                // fallback ke now() jika parsing gagal
                toastr()
                    ->closeOnHover(true)
                    ->closeDuration(3)
                    ->positionClass('toast-top-left')
                    ->addError('Format Task ID 5 tidak valid. Proses diagnosa dibatalkan.');
                return;
            }

            // build payload
            $payload = [
                'patientId'      => $patientUuid,
                'encounterId'    => $encounterUuid,
                'icd10_code'     => $icd10,
                'icd10_display'  => $desc,
                'diagnosis_text' => $textLocal,
                'recordedDate'   => $recorded,
                // hanya tambahkan SNOMED kalau ada:
                // 'snomed_code'    => $snomedCode,
                // 'snomed_display' => $snomedDisplay,
            ];
            try {
                $result = $this->createFinalDiagnosis($payload);
                $condId = $result['id'] ?? null;
                if ($condId) {
                    // catat ke JSON lokal
                    $dataDaftarPoliRJ['satuSehatUuidRJ']['diagnosis'][] = [
                        'uuid' => $condId,
                        'icd10Code' => $icd10,
                    ];
                    toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                        ->addSuccess("Diagnosa {$desc} terkirim (UUID: {$condId}).");
                }
            } catch (\Exception $e) {
                toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                    ->addError("Gagal kirim diagnosa {$desc}: " . $e->getMessage());
            }
        }
        // simpan perubahan
        $this->updateJsonRJ($this->rjNoRef, $dataDaftarPoliRJ);
    }

    public function postTindakanRJ()
    {
        // 1) Ambil data
        $find             = $this->findDataRJ($this->rjNoRef);
        $dataDaftarPoliRJ = $find['dataDaftarRJ'] ?? [];
        $dataPasienRJ     = $find['dataPasienRJ'] ?? [];

        $patientUuid   = $dataPasienRJ['patientUuid'] ?? null;
        $encounterUuid = $dataDaftarPoliRJ['satuSehatUuidRJ']['encounter']['uuid'] ?? null;
        $procedures    = $dataDaftarPoliRJ['procedure'] ?? [];
        $sentProc      = $dataDaftarPoliRJ['satuSehatUuidRJ']['procedures'] ?? [];
        // 2) Validasi UUID pasien
        if (!$patientUuid) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError('UUID pasien belum tersedia. Proses tindakan dibatalkan.');
            return;
        }

        // Validasi UUID encounter
        if (!$encounterUuid) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError('UUID encounter belum tersedia. Proses tindakan dibatalkan.');
            return;
        }

        // Validasi ada data tindakan
        if (empty($procedures)) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addInfo('Tidak ada data tindakan untuk dikirim.');
            return;
        }

        // Siapkan koneksi SatuSehat
        $this->initializeSatuSehat();

        // 3) Loop kirim tiap tindakan
        foreach ($procedures as $proc) {
            $code    = $proc['procedureId'] ?? null;
            $display = $proc['procedureDesc'] ?? '';

            // skip jika sudah dikirim
            $existing = collect($sentProc)->firstWhere('icd9cmCode', $code);
            if ($existing) {
                $uuidSent = $existing['uuid'] ?? '';
                toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                    ->addInfo("Tindakan {$display} sudah terkirim (UUID: {$uuidSent}).");
                continue;
            }

            // Ambil Task ID 5 sebagai performedDateTime
            try {
                $performedDateTime = Carbon::createFromFormat(
                    'd/m/Y H:i:s',
                    $dataDaftarPoliRJ['taskIdPelayanan']['taskId5']
                )->toIso8601String();
            } catch (\Throwable $e) {
                toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                    ->addError('Format Task ID 5 tidak valid. Proses tindakan dibatalkan.');
                return;
            }

            // 4) Bangun payload sesuai createProcedure()
            $procedureData = [
                'patientId'        => $patientUuid,
                'encounterId'      => $encounterUuid,
                // Override default SNOMED, gunakan ICD-9-CM
                'codeSystem'       => 'http://hl7.org/fhir/sid/icd-9-cm',
                'code'             => $code,        // misal "003.0"
                'display'          => $display,     // misal "Salmonella gastroenteritis"
                'performedDateTime' => $performedDateTime,
                'performerId'      => $dataPasienRJ['drUuid'] ?? null,
                'performerRole'    => 'Practitioner',
            ];


            try {
                $result = $this->createProcedure($procedureData);
                $procId = $result['id'] ?? null;

                if ($procId) {
                    // catat UUID & icd9cmCode
                    $dataDaftarPoliRJ['satuSehatUuidRJ']['procedures'][] = [
                        'uuid'         => $procId,
                        'icd9cmCode'  => $code,
                    ];
                    toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                        ->addSuccess("Tindakan {$display} terkirim (UUID: {$procId}).");
                }
            } catch (\Exception $e) {
                toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                    ->addError("Gagal kirim tindakan {$display}: " . $e->getMessage());
            }

            // simpan perubahan lokal
            $this->updateJsonRJ($this->rjNoRef, $dataDaftarPoliRJ);
        }
    }

















    public function postResepRJ()
    {
        // 1) Ambil data dasar
        $find             = $this->findDataRJ($this->rjNoRef);
        $dataDaftarPoliRJ = $find['dataDaftarRJ'] ?? [];
        $dataPasienRJ     = $find['dataPasienRJ'] ?? [];
        $patientUuid      = $dataPasienRJ['patientUuid'] ?? null;
        $encounterUuid    = $dataDaftarPoliRJ['satuSehatUuidRJ']['encounter']['uuid'] ?? null;
        $eresep           = $dataDaftarPoliRJ['eresep'] ?? [];
        $sentRx           = collect($dataDaftarPoliRJ['satuSehatUuidRJ']['medicationRequests'] ?? []);

        // UUID dokter & nama
        $requesterId   = $dataPasienRJ['drUuid'] ?? null;
        $requesterName = $dataPasienRJ['drName'] ?? null;

        // Task ID 6 = waktu resep
        $authoredOnRaw = $dataDaftarPoliRJ['taskIdPelayanan']['taskId6'] ?? null;

        // Validasi prasyarat
        if (!$authoredOnRaw || !$requesterId || !$patientUuid || !$encounterUuid) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError('Data resep tidak lengkap. Proses dibatalkan.');
            return;
        }

        try {
            $authoredOn = Carbon::createFromFormat('d/m/Y H:i:s', $authoredOnRaw, 'Asia/Jakarta')->toIso8601String();
        } catch (\Exception $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError("Format waktu resep tidak valid ({$authoredOnRaw}).");
            return;
        }

        $this->initializeSatuSehat();
        $orgId          = env('SATUSEHAT_ORGANIZATION_ID');
        $prescriptionId = 'RESEP-RJ-' . $this->rjNoRef . '-' . now()->format('YmdHis');

        // mapping bentuk sediaan
        $formMapping = [
            'tablet'          => ['code' => 'BS066', 'display' => 'Tablet'],
            'capsule'         => ['code' => 'CA030', 'display' => 'Capsule'],
            'kaplet'          => ['code' => 'KL030', 'display' => 'Kaplet Salut Selaput'],
            'pill'            => ['code' => 'PL010', 'display' => 'Pil'],
            'chewable_tablet' => ['code' => 'CHEWTAB', 'display' => 'Chewable Tablet'],
            'syrup'           => ['code' => 'SY010', 'display' => 'Syrup'],
            'suspension'      => ['code' => 'SS020', 'display' => 'Suspension'],
            'injection'       => ['code' => 'IN010', 'display' => 'Injection'],
            // dst.
        ];
        $dosageMapping = [
            'tablet'          => ['code' => 'TAB',     'display' => 'Tablet'],
            'capsule'         => ['code' => 'CAP',     'display' => 'Capsule'],
            'pill'            => ['code' => 'PILL',    'display' => 'Pill'],
            'oral_capsule'    => ['code' => 'ORCAP',   'display' => 'Oral Capsule'],
            'caplet'          => ['code' => 'CAPLET',  'display' => 'Caplet'],
            'chewable_tablet' => ['code' => 'CHEWTAB', 'display' => 'Chewable Tablet'],
            'syrup'           => ['code' => 'SYRUP',   'display' => 'Syrup'],
            'suspension'      => ['code' => 'SUSP',    'display' => 'Suspension'],
            'injection'       => ['code' => 'INJ',     'display' => 'Injection'],
        ];
        foreach ($eresep as $item) {
            $localId = $item['rjObatDtl'];
            if ($sentRx->firstWhere('localId', $localId)) {
                toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                    ->addInfo("Obat {$item['productName']} sudah dikirim.");
                continue;
            }

            // Data obat lokal
            $product = DB::table('immst_products')
                ->select('product_id_satusehat', 'product_name_satusehat')
                ->where('product_id', $item['productId'])
                ->first();
            if (!$product) {
                toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                    ->addError("Mapping SatuSehat untuk produk {$item['productId']} tidak ditemukan.");
                continue;
            }

            $code    = $product->product_id_satusehat;
            $display = $product->product_name_satusehat;
            $qty     = (float) $item['qty'];
            $x       = (int) $item['signaX'];
            $days    = (int) $item['signaHari'];

            // bentuk sediaan default
            $mappingDataForm = $formMapping['tablet'];

            // build containedMedication
            $medContainedId = "{$prescriptionId}-{$localId}";
            $containedMed = [
                'resourceType' => 'Medication',
                'id'           => $medContainedId,
                'meta'         => ['profile' => ['https://fhir.kemkes.go.id/r4/StructureDefinition/Medication']],
                'identifier'   => [[
                    'system' => "http://sys-ids.kemkes.go.id/medication/{$orgId}",
                    'use'    => 'official',
                    'value'  => $medContainedId,
                ]],
                'code'   => ['coding' => [[
                    'system'  => 'http://sys-ids.kemkes.go.id/kfa',
                    'code'    => $code,
                    'display' => $display,
                ]]],
                'status' => 'active',
                'form'   => ['coding' => [[
                    'system'  => 'http://terminology.kemkes.go.id/CodeSystem/medication-form',
                    'code'    => $mappingDataForm['code'],
                    'display' => $mappingDataForm['display'],
                ]]],
                'extension' => [[
                    'url' => 'https://fhir.kemkes.go.id/r4/StructureDefinition/MedicationType',
                    'valueCodeableConcept' => ['coding' => [[
                        'system'  => 'http://terminology.kemkes.go.id/CodeSystem/medication-type',
                        'code'    => 'NC',
                        'display' => 'Non-compound',
                    ]]],
                ]],
            ];


            // bentuk sediaan default
            $mappingDataDosage = $dosageMapping['tablet'];
            // dosageInstruction & dispenseRequest
            $dosageInstruction = [[
                'sequence'           => 1,
                'patientInstruction' => "{$x}× sehari, selama {$days} hari",
                'timing'             => ['repeat' => ['frequency' => $x, 'period' => 1, 'periodUnit' => 'd']],
                'route'              => ['coding' => [['system' => 'http://www.whocc.no/atc', 'code' => 'O', 'display' => 'Oral']]],
                'doseAndRate'        => [[
                    'doseQuantity' => [
                        'value'  => 1,
                        'unit'   => $mappingDataDosage['display'],
                        'system' => 'http://terminology.hl7.org/CodeSystem/v3-orderableDrugForm',
                        'code'   => $mappingDataDosage['code'],
                    ],
                ]],
            ]];

            $dispenseRequest = [
                'performer' => ['reference' => "Organization/{$orgId}"],
                'quantity'  => ['value' => $qty, 'unit' => $mappingDataDosage['display'], 'system' => 'http://terminology.hl7.org/CodeSystem/v3-orderableDrugForm', 'code' => $mappingDataDosage['code']],
                'expectedSupplyDuration' => ['value' => $days, 'unit' => 'days', 'system' => 'http://unitsofmeasure.org', 'code' => 'd'],
                'validityPeriod' => ['start' => $authoredOn, 'end' => Carbon::parse($authoredOn)->addDays($days)->toIso8601String()],
                'numberOfRepeatsAllowed' => 0,
            ];

            // susun request data
            $dataReq = [
                'registrationId'       => $prescriptionId,
                'orgId'                => $orgId,
                'patientId'            => $patientUuid,
                'patientName'          => $dataPasienRJ['patientName'] ?? '',
                'encounterId'          => $encounterUuid,
                'authoredOn'           => $authoredOn,
                'requesterId'          => $requesterId,
                'requesterName'        => $requesterName,
                'prescriptionId'       => $prescriptionId,
                'medContainedId'       => $medContainedId,
                'medicationCode'       => $code,
                'medicationDisplay'    => $display,
                'medicationTypeCode'   => 'NC',   //NC – Non-compound   CP – Compound
                'medicationTypeDisplay' => 'Non-compound',
                'medicationFormCode'   => $mappingDataForm['code'],
                'medicationFormDisplay' => $mappingDataForm['display'],
                'category'             => 'community',
                'containedMedication'  => $containedMed,
                'reasonReference'      => [[
                    'reference' => 'Condition/' . $dataDaftarPoliRJ['satuSehatUuidRJ']['chiefComplaint']['uuid'] ?? '', //uuid keluhan utama
                    'display' => $dataDaftarPoliRJ['anamnesa']['keluhanUtama']['snomedDisplay'] ?? ''
                ]],
                'dosageInstruction'    => $dosageInstruction,
                'dispenseRequest'      => $dispenseRequest,
                'note'                 => "Resep ditulis oleh {$requesterName}",
            ];

            // kirim
            try {
                $resp = $this->createMedicationRequest($dataReq);
                $mrId = $resp['id'] ?? null;
                if (!$mrId) throw new \Exception('UUID tidak diterima');

                // simpan ke local
                $dataDaftarPoliRJ['satuSehatUuidRJ']['medicationRequests'][] = ['uuid' => $mrId, 'localId' => $localId];
                $this->updateJsonRJ($this->rjNoRef, $dataDaftarPoliRJ);

                toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                    ->addSuccess("Obat {$display} terkirim (UUID: {$mrId}).");
            } catch (\Exception $e) {
                toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                    ->addError("Gagal kirim {$display}: {$e->getMessage()}");
            }
        }
    }


    /**
     * Terima resep rawat jalan berdasarkan Task ID 7 (penyerahan obat)
     *
     * @return void
     */
    public function postPenerimaanResepRJ()
    {
        // 1) Ambil data dasar
        $find             = $this->findDataRJ($this->rjNoRef);
        $dataDaftarPoliRJ = $find['dataDaftarRJ'] ?? [];
        $dataPasienRJ     = $find['dataPasienRJ'] ?? [];
        $patientUuid      = $dataPasienRJ['patientUuid'] ?? null;
        $encounterUuid    = $dataDaftarPoliRJ['satuSehatUuidRJ']['encounter']['uuid'] ?? null;
        $eresep           = $dataDaftarPoliRJ['eresep'] ?? [];
        $sentDispenses    = collect($dataDaftarPoliRJ['satuSehatUuidRJ']['medicationDispenses'] ?? []);
        $authorizingPrescription    = collect($dataDaftarPoliRJ['satuSehatUuidRJ']['medicationRequests'] ?? []);


        // UUID petugas penerima (apoteker)
        $dispenserId   = $dataPasienRJ['drUuid']  ?? null;
        $dispenserName = $dataPasienRJ['drName'] ?? 'Apoteker';

        // Validasi prasyarat satu per satu
        if (!$patientUuid) {
            toastr()->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addError('UUID pasien belum tersedia. Proses dibatalkan.');
            return;
        }
        if (!$encounterUuid) {
            toastr()->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addError('UUID encounter belum tersedia. Proses dibatalkan.');
            return;
        }
        if (!$dispenserId) {
            toastr()->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addError('UUID petugas penerima belum tersedia. Proses dibatalkan.');
            return;
        }

        // Inisialisasi FHIR client
        $this->initializeSatuSehat();
        $orgId = env('SATUSEHAT_ORGANIZATION_ID');

        // Mapping sediaan dan dosage
        $formMapping = [
            'tablet'          => ['code' => 'BS066', 'display' => 'Tablet'],
            'capsule'         => ['code' => 'CA030', 'display' => 'Capsule'],
            'kaplet'          => ['code' => 'KL030', 'display' => 'Kaplet Salut Selaput'],
            'pill'            => ['code' => 'PL010', 'display' => 'Pil'],
            'chewable_tablet' => ['code' => 'CHEWTAB', 'display' => 'Chewable Tablet'],
            'syrup'           => ['code' => 'SY010', 'display' => 'Syrup'],
            'suspension'      => ['code' => 'SS020', 'display' => 'Suspension'],
            'injection'       => ['code' => 'IN010', 'display' => 'Injection'],
            // dst.
        ];

        $dosageMapping = [
            'tablet'          => ['code' => 'TAB',     'display' => 'Tablet'],
            'capsule'         => ['code' => 'CAP',     'display' => 'Capsule'],
            'pill'            => ['code' => 'PILL',    'display' => 'Pill'],
            'oral_capsule'    => ['code' => 'ORCAP',   'display' => 'Oral Capsule'],
            'caplet'          => ['code' => 'CAPLET',  'display' => 'Caplet'],
            'chewable_tablet' => ['code' => 'CHEWTAB', 'display' => 'Chewable Tablet'],
            'syrup'           => ['code' => 'SYRUP',   'display' => 'Syrup'],
            'suspension'      => ['code' => 'SUSP',    'display' => 'Suspension'],
            'injection'       => ['code' => 'INJ',     'display' => 'Injection'],
        ];

        foreach ($eresep as $item) {
            $localId  = $item['rjObatDtl'];
            $exists   = $sentDispenses->firstWhere('localId', $localId);
            if ($exists) {
                toastr()->closeOnHover(true)
                    ->closeDuration(3)
                    ->positionClass('toast-top-left')
                    ->addInfo("Item {$localId} sudah diterima sebelumnya.");
                continue;
            }

            // Generate unique dispenseId
            $dispenseId = "DISPENSE-RJ-{$this->rjNoRef}-{$localId}-" . now('Asia/Jakarta')->format('YmdHis');

            // Ambil waktu penyerahan dari Task ID
            $preperadOnRaw = $dataDaftarPoliRJ['taskIdPelayanan']['taskId6'] ?? null;
            if (!$preperadOnRaw) {
                toastr()->closeOnHover(true)
                    ->closeDuration(3)
                    ->positionClass('toast-top-left')
                    ->addError('Waktu penyerahan (Task 6) belum tersedia.');
                return;
            }
            try {
                $whenPrepared = \Carbon\Carbon::createFromFormat('d/m/Y H:i:s', $preperadOnRaw, 'Asia/Jakarta')
                    ->toIso8601String();
            } catch (\Exception $e) {
                toastr()->closeOnHover(true)
                    ->closeDuration(3)
                    ->positionClass('toast-top-left')
                    ->addError("Format waktu tidak valid: {$preperadOnRaw}");
                return;
            }

            // Ambil waktu penyerahan dari Task ID 7
            $handedOnRaw = $dataDaftarPoliRJ['taskIdPelayanan']['taskId7'] ?? null;
            if (!$handedOnRaw) {
                toastr()->closeOnHover(true)
                    ->closeDuration(3)
                    ->positionClass('toast-top-left')
                    ->addError('Waktu penyerahan (Task 7) belum tersedia.');
                return;
            }
            try {
                $whenHanded = \Carbon\Carbon::createFromFormat('d/m/Y H:i:s', $handedOnRaw, 'Asia/Jakarta')
                    ->toIso8601String();
            } catch (\Exception $e) {
                toastr()->closeOnHover(true)
                    ->closeDuration(3)
                    ->positionClass('toast-top-left')
                    ->addError("Format waktu tidak valid: {$handedOnRaw}");
                return;
            }

            // Ambil data produk dari DB
            $product = DB::table('immst_products')
                ->select('product_id_satusehat', 'product_name_satusehat')
                ->where('product_id', $item['productId'])
                ->first();
            if (!$product) {
                toastr()->closeOnHover(true)
                    ->closeDuration(3)
                    ->positionClass('toast-top-left')
                    ->addError("Produk {$item['productId']} belum terdaftar di SatuSehat.");
                continue;
            }

            // Tentukan mapping
            $form   = $formMapping['tablet'];
            $dosage = $dosageMapping['tablet'];
            $x       = (int) $item['signaX'];
            $days    = (int) $item['signaHari'];

            $uuidRequest = $authorizingPrescription
                ->firstWhere('localId', $localId)['uuid'] ?? null;
            $dosageInstruction = [[
                'sequence'           => 1,
                'patientInstruction' => "{$x}× sehari, selama {$days} hari",
                'timing'             => ['repeat' => ['frequency' => $x, 'period' => 1, 'periodUnit' => 'd']],
                'route'              => ['coding' => [['system' => 'http://www.whocc.no/atc', 'code' => 'O', 'display' => 'Oral']]],
                'doseAndRate'        => [[
                    'doseQuantity' => [
                        'value'  => 1,
                        'unit'   => $dosage['display'],
                        'system' => 'http://terminology.hl7.org/CodeSystem/v3-orderableDrugForm',
                        'code'   => $dosage['code'],
                    ],
                ]],
            ]];

            // Siapkan data untuk dispense
            $dispenseData = [
                'registrationId'           => $dispenseId,
                'prescriptionItemId'       => "{$dispenseId}-{$localId}",
                'orgId'                    => $orgId,
                'medContainedId'           => "{$dispenseId}-{$localId}",
                'medicationCode'           => $product->product_id_satusehat,
                'medicationDisplay'        => $product->product_name_satusehat,
                'medicationFormCode'       => $form['code'],
                'medicationFormDisplay'    => $form['display'],
                'medicationTypeCode'       => 'NC',
                'medicationTypeDisplay'    => 'Non-compound',
                'dosageInstruction'        => $dosageInstruction,

                'patientId'                => $patientUuid,
                'patientName'              => $dataPasienRJ['patientName'] ?? '',
                'encounterId'              => $encounterUuid,
                'authorizingPrescription'  => ['reference' => "MedicationRequest/{$uuidRequest}"],
                'receiver'                 => ['reference' => "Patient/{$patientUuid}"],
                'whenPrepared'             => $whenPrepared,
                'whenHandedOver'           => $whenHanded,
                'category'                 => 'community',
                'performer'                => [[
                    'actor'    => [
                        'reference' => "Practitioner/{$dispenserId}",
                        'display'   => $dispenserName
                    ],
                    'function' => [
                        'coding' => [[
                            'system'  => 'http://terminology.hl7.org/CodeSystem/medicationdispense-performer-function',
                            'code'    => 'PHARM',
                            'display' => 'Pharmacist'
                        ]]
                    ]
                ]],

                'quantity'                 => [
                    'value'  => (float) $item['qty'],
                    'unit'   => (string) $item['qty'],
                    'system' => 'http://terminology.hl7.org/CodeSystem/v3-orderableDrugForm',
                    'code'   => $dosage['code'],
                ],
                'daysSupply'               => [
                    'value'  => (int) $item['signaHari'],
                    'unit'   => 'days',
                    'system' => 'http://unitsofmeasure.org',
                    'code'   => 'd',
                ],

                'note'                     => "Diterima oleh {\$dispenserName} pada {\$whenHanded}",

                // untuk internal tracking
                'localId'                  => $localId,
            ];

            try {
                $resp      = $this->createMedicationDispense($dispenseData);
                $dispUuid  = $resp['id'] ?? null;
                if (!$dispUuid) {
                    throw new \Exception('UUID dispense tidak diterima');
                }
                // Simpan ke JSON RJ
                $dataDaftarPoliRJ['satuSehatUuidRJ']['medicationDispenses'][] = [
                    'uuid'    => $dispUuid,
                    'localId' => $localId,
                ];
                $this->updateJsonRJ($this->rjNoRef, $dataDaftarPoliRJ);
                toastr()->closeOnHover(true)
                    ->closeDuration(3)
                    ->positionClass('toast-top-left')
                    ->addSuccess("Penerimaan {$localId} berhasil (UUID: {$dispUuid}).");
            } catch (\Exception $e) {
                toastr()->closeOnHover(true)
                    ->closeDuration(3)
                    ->positionClass('toast-top-left')
                    ->addError("Gagal menerima {$localId}: " . $e->getMessage());
            }
        }
    }
































    /**
     * Kirim permintaan pemeriksaan laboratorium (hematologi) ke SatuSehat
     */
    public function postLaboratRJ()
    {
        // 1) Ambil data dasar
        $find             = $this->findDataRJ($this->rjNoRef);
        $dataDaftarPoliRJ = $find['dataDaftarRJ'] ?? [];
        $dataPasienRJ  = $find['dataPasienRJ'] ?? [];
        $patientUuid = $dataPasienRJ['patientUuid'] ?? null;
        $encounterUuid = $dataDaftarPoliRJ['satuSehatUuidRJ']['encounter']['uuid'] ?? null;
        $requesterId = $dataPasienRJ['drUuid'] ?? null;
        $requesterName = $dataPasienRJ['drName'] ?? null;

        $sentRecords = $dataDaftarPoliRJ['satuSehatUuidRJ']['serviceRequests'] ?? [];

        // Validasi prasyarat
        if (!$patientUuid) {
            toastr()
                ->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addError('Patient UUID belum tersedia. Proses dibatalkan.');
            return;
        }

        if (!$encounterUuid) {
            toastr()
                ->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addError('Encounter UUID belum tersedia. Proses dibatalkan.');
            return;
        }

        if (!$requesterId) {
            toastr()
                ->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addError('Requester ID belum tersedia. Proses dibatalkan.');
            return;
        }

        // Inisialisasi client SatuSehat
        $this->initializeSatuSehat();
        $orgId = env('SATUSEHAT_ORGANIZATION_ID');

        // Buat identifier unik untuk ServiceRequest

        $pemeriksaanLab = DB::table('lbtxn_checkuphdrs as a')
            ->join('lbtxn_checkupdtls as b', 'a.checkup_no', '=', 'b.checkup_no')
            ->join('lbmst_clabitems as c', 'b.clabitem_id', '=', 'c.clabitem_id')
            ->join('lbmst_clabs as d', 'c.clab_id', '=', 'd.clab_id')
            ->where('a.status_rjri', 'RJ')
            ->whereNotNull('c.price')
            // ->where('b.checkup_status', '!=',  'B')
            ->where('a.ref_no', $this->rjNoRef)
            ->select([
                DB::raw("to_char(a.checkup_date, 'dd/mm/yyyy hh24:mi:ss') as checkup_date"),
                'c.clabitem_id',
                'b.checkup_dtl',
                'd.clab_id',
                'c.clabitem_id_satusehat',
                'c.clabitem_desc_satusehat',
            ])
            ->distinct()
            ->get();
        // Siapkan data ServiceRequest
        foreach ($pemeriksaanLab as $lab) {
            $localId = $lab->checkup_dtl;

            if (
                empty($lab->clabitem_id_satusehat) ||
                empty($lab->clabitem_desc_satusehat)

            ) {
                toastr()
                    ->closeOnHover(true)
                    ->closeDuration(3)
                    ->positionClass('toast-top-left')
                    ->addWarning("Data mapping SATUSEHAT belum lengkap untuk item {$lab->checkup_dtl}, dilewati.");
                continue;
            }

            $sent = collect($sentRecords)
                ->firstWhere('localId', $localId);

            if ($sent) {
                toastr()
                    ->closeOnHover(true)
                    ->closeDuration(3)
                    ->positionClass('toast-top-left')
                    ->addInfo(
                        "{$localId} telah dikirim sebelumnya (UUID: {$sent['uuid']})."
                    );

                continue;
            }


            $identifierValue = "LAB-{$localId}-" . now('Asia/Jakarta')->format('YmdHis');

            $authoredOn = $lab->checkup_date ?? null;
            if (empty($authoredOn)) {
                toastr()
                    ->closeOnHover(true)
                    ->closeDuration(3)
                    ->positionClass('toast-top-left')
                    ->addError('Waktu masuk ruang (laborat) tidak ditemukan, proses dibatalkan.');
                return;
            }

            try {
                $authoredOnIso = Carbon::createFromFormat('d/m/Y H:i:s', $authoredOn)
                    ->toIso8601String();
            } catch (\Exception $e) {
                toastr()
                    ->closeOnHover(true)
                    ->closeDuration(3)
                    ->positionClass('toast-top-left')
                    ->addError("Format waktu laborat tidak valid: “{$authoredOn}”. Proses dibatalkan.");
                return;
            }


            $srData = [
                'identifier'         => [
                    'system' => "http://sys-ids.kemkes.go.id/servicerequest/{$orgId}",
                    'value'  => $identifierValue,
                ],
                'status'             => 'active',
                'intent'             => 'original-order',
                'priority'           => 'routine',
                'category'           => [
                    'system'  => 'http://snomed.info/sct',
                    'code'    => '108252007',
                    'display' => 'Laboratory procedure',
                ],
                'code'               => [
                    'system'  => 'http://loinc.org',
                    'code'    => $lab->clabitem_id_satusehat,   // CBC panel
                    'display' => $lab->clabitem_desc_satusehat,
                ],
                'subject'            => "Patient/{$patientUuid}",
                'encounter'          => "Encounter/{$encounterUuid}",
                'occurrenceDateTime' => $authoredOnIso,
                'authoredOn'         => $authoredOnIso,
                'requester'          => "Practitioner/{$requesterId}",
                'requesterDisplay'   => $requesterName,

                'performer'          => "Practitioner/{$requesterId}", //sementara pakai dokter. . .ini adalah petugas lab
                'performerDisplay'   => $requesterName,
                'reasonCode'         => [
                    ['text' => $lab->clabitem_desc_satusehat]
                ],
            ];

            // Kirim ServiceRequest via trait
            try {

                $response = $this->postServiceRequest($srData);
                $srUuid   = $response['id'] ?? null;
                if (!$srUuid) {
                    throw new \Exception('UUID ServiceRequest tidak diterima.');
                }

                toastr()
                    ->closeOnHover(true)
                    ->closeDuration(3)
                    ->positionClass('toast-top-left')
                    ->addSuccess("Permintaan lab hematologi berhasil (UUID: {$srUuid})");

                // Simpan hasil ke struktur JSON RJ jika perlu
                $dataDaftarPoliRJ['satuSehatUuidRJ']['serviceRequests'][] = [
                    'uuid'    => $srUuid,
                    'localId' => $localId,
                ];
                $this->updateJsonRJ($this->rjNoRef, $dataDaftarPoliRJ);
            } catch (\Exception $e) {
                toastr()
                    ->closeOnHover(true)
                    ->closeDuration(3)
                    ->positionClass('toast-top-left')
                    ->addError('Gagal mengirim permintaan lab: ' . $e->getMessage());
            }
        }
    }


    public function postSpecimenLaboratRJ()
    {
        // 1) Ambil data dasar
        $find             = $this->findDataRJ($this->rjNoRef);
        $dataDaftarPoliRJ = $find['dataDaftarRJ'] ?? [];
        $dataPasienRJ     = $find['dataPasienRJ'] ?? [];
        $patientUuid      = $dataPasienRJ['patientUuid'] ?? null;
        $encounterUuid    = $dataDaftarPoliRJ['satuSehatUuidRJ']['encounter']['uuid'] ?? null;
        $sentSR           = $dataDaftarPoliRJ['satuSehatUuidRJ']['serviceRequests'] ?? [];
        $sentSpecimens    = $dataDaftarPoliRJ['satuSehatUuidRJ']['specimens'] ?? [];

        // Validasi prasyarat
        // Cek Patient UUID
        if (!$patientUuid) {
            toastr()
                ->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addError('Patient UUID belum tersedia. Proses dibatalkan.');
            return;
        }

        // Cek Encounter UUID
        if (!$encounterUuid) {
            toastr()
                ->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addError('Encounter UUID belum tersedia. Proses dibatalkan.');
            return;
        }

        // Inisialisasi client SatuSehat
        $this->initializeSatuSehat();
        $orgId = env('SATUSEHAT_ORGANIZATION_ID');

        // Ambil daftar laboratorium
        $pemeriksaanLab = DB::table('lbtxn_checkuphdrs as a')
            ->join('lbtxn_checkupdtls as b', 'a.checkup_no', '=', 'b.checkup_no')
            ->join('lbmst_clabitems as c', 'b.clabitem_id', '=', 'c.clabitem_id')
            ->join('lbmst_clabs as d', 'c.clab_id', '=', 'd.clab_id')
            ->where('a.status_rjri', 'RJ')
            ->whereNotNull('c.price')
            ->where('a.ref_no', $this->rjNoRef)
            ->select([
                'b.checkup_dtl',
                'd.clab_id_satusehat_specimen',
                'd.clab_desc_satusehat_specimen',
                DB::raw("to_char(a.checkup_date, 'dd/mm/yyyy HH24:MI:SS') as checkup_date"),
            ])
            ->distinct()
            ->get();

        foreach ($pemeriksaanLab as $lab) {
            $localId = $lab->checkup_dtl;

            if (
                empty($lab->clab_id_satusehat_specimen) ||
                empty($lab->clab_desc_satusehat_specimen)

            ) {
                toastr()
                    ->closeOnHover(true)
                    ->closeDuration(3)
                    ->positionClass('toast-top-left')
                    ->addWarning("Data mapping SATUSEHAT belum lengkap untuk item {$lab->checkup_dtl}, dilewati.");
                continue;
            }

            // Cek apakah Specimen sudah dikirim
            $already = collect($sentSpecimens)
                ->firstWhere('localId', $localId);

            if ($already) {
                toastr()->closeOnHover(true)
                    ->closeDuration(3)
                    ->positionClass('toast-top-left')
                    ->addInfo("Specimen {$localId} sudah dikirim (UUID: {$already['uuid']}).");
                continue;
            }

            // Cari ServiceRequest UUID untuk specimen ini
            $sr = collect($sentSR)->firstWhere('localId', $localId);
            if (!$sr) {
                toastr()->closeOnHover(true)
                    ->closeDuration(3)
                    ->positionClass('toast-top-left')
                    ->addError("ServiceRequest untuk {$localId} belum ada. Kirim ServiceRequest dulu.");
                continue;
            }
            $srRef = "ServiceRequest/{$sr['uuid']}";

            // Konversi waktu
            try {
                $collectedIso = Carbon::createFromFormat('d/m/Y H:i:s', $lab->checkup_date, 'Asia/Jakarta')
                    ->toIso8601String();
            } catch (\Exception $e) {
                toastr()->closeOnHover(true)
                    ->closeDuration(3)
                    ->positionClass('toast-top-left')
                    ->addError("Format waktu tidak valid: {$lab->checkup_date}");
                continue;
            }

            // Bangun payload Specimen
            $identifierValue = "SPEC-{$localId}-" . now('Asia/Jakarta')->format('YmdHis');
            $specimenData = [
                'identifier'   => [
                    'system'   => "http://sys-ids.kemkes.go.id/specimen/{$orgId}",
                    'value'    => $identifierValue,
                    'assigner' => "Organization/{$orgId}",
                ],
                'status'       => 'available',
                'subject'      => "Patient/{$patientUuid}",
                'type'         => [
                    'system'  => 'http://snomed.info/sct',
                    'code'    => $lab->clab_id_satusehat_specimen,
                    'display' => $lab->clab_desc_satusehat_specimen,
                ],
                'collection'   => [
                    'collectedDateTime' => $collectedIso,
                    'method'            => [
                        'system'  => 'http://snomed.info/sct',
                        'code'    => $lab->clab_id_satusehat_specimen,
                        'display' => $lab->clab_desc_satusehat_specimen,
                    ],
                ],
                'receivedTime' => $collectedIso,
                'request'      => [$srRef],
            ];

            // Kirim Specimen
            try {
                $resp       = $this->postSpecimen($specimenData);
                $specimenId = $resp['id'] ?? null;
                if (!$specimenId) {
                    throw new \Exception('UUID Specimen tidak diterima.');
                }

                toastr()->closeOnHover(true)
                    ->closeDuration(3)
                    ->positionClass('toast-top-left')
                    ->addSuccess("Specimen {$localId} berhasil dikirim (UUID: {$specimenId}).");

                // Simpan ke JSON RJ
                $dataDaftarPoliRJ['satuSehatUuidRJ']['specimens'][] = [
                    'uuid'    => $specimenId,
                    'localId' => $localId,
                ];
                $this->updateJsonRJ($this->rjNoRef, $dataDaftarPoliRJ);
            } catch (\Exception $e) {
                toastr()->closeOnHover(true)
                    ->closeDuration(3)
                    ->positionClass('toast-top-left')
                    ->addError("Gagal kirim Specimen {$localId}: " . $e->getMessage());
            }
        }
    }



    public function postObservationLaboratRJ()
    {
        // 1) Ambil data kunjungan & pasien
        $find = $this->findDataRJ($this->rjNoRef);
        $dataDaftarPoliRJ = $find['dataDaftarRJ'] ?? [];
        $dataPasienRJ = $find['dataPasienRJ'] ?? [];
        $patientUuid = $dataPasienRJ['patientUuid'] ?? null;
        $encounterUuid = $dataDaftarPoliRJ['satuSehatUuidRJ']['encounter']['uuid'] ?? null;
        $sentRecords = $dataDaftarPoliRJ['satuSehatUuidRJ']['labObservations'] ?? [];
        $performerId = $dataPasienRJ['drUuid'] ?? null; // Dokter yang melakukan pemeriksaan


        // 2) Validasi prasyarat
        if (!$patientUuid) {
            toastr()
                ->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addError('Patient UUID belum tersedia. Proses LabObservation dibatalkan.');
            return;
        }

        if (!$performerId) {
            toastr()
                ->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addError('UUID performer (dokter) belum tersedia. Proses LabObservation dibatalkan.');
            return;
        }

        if (!$encounterUuid) {
            toastr()
                ->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addError('Encounter UUID belum tersedia. Proses LabObservation dibatalkan.');
            return;
        }

        $this->initializeSatuSehat();
        $orgId = env('SATUSEHAT_ORGANIZATION_ID');
        // 3) Ambil data pemeriksaan laboratorium
        $pemeriksaanLab = DB::table('lbtxn_checkuphdrs as a')
            ->join('lbtxn_checkupdtls as b', 'a.checkup_no', '=', 'b.checkup_no')
            ->join('lbmst_clabitems as c', 'b.clabitem_id', '=', 'c.clabitem_id')
            ->join('lbmst_clabs as d', 'c.clab_id', '=', 'd.clab_id')
            ->where('a.status_rjri', 'RJ')
            ->whereNotNull('c.price')
            ->where('a.ref_no', $this->rjNoRef)
            ->select([
                'b.checkup_dtl',
                'd.clab_id_satusehat_specimen',
                'd.clab_desc_satusehat_specimen',
                'c.clabitem_id_satusehat',
                'c.clabitem_desc_satusehat',
                DB::raw("to_char(a.checkup_date, 'dd/mm/yyyy HH24:MI:SS') as checkup_date"),
            ])
            ->distinct()
            ->get();


        foreach ($pemeriksaanLab as $lab) {
            $localId = $lab->checkup_dtl;

            // Cek apakah data sudah dikirim sebelumnya
            $alreadySent = collect($sentRecords)->firstWhere('localId', $localId);
            if ($alreadySent) {
                toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                    ->addInfo("LabObservation {$localId} sudah terkirim (UUID: {$alreadySent['uuid']}).");
                continue;
            }

            // 4) Ambil data lengkap hasil laboratorium
            $dataDaftarTxn = DB::table('lbtxn_checkuphdrs as a')
                ->join('lbtxn_checkupdtls as b', 'a.checkup_no', '=', 'b.checkup_no')
                ->join('rsmst_pasiens as c', 'a.reg_no', '=', 'c.reg_no')
                ->join('lbmst_clabitems as d', 'b.clabitem_id', '=', 'd.clabitem_id')
                ->join('lbmst_clabs as e', 'd.clab_id', '=', 'e.clab_id')
                ->join('rsmst_doctors as f', 'a.dr_id', '=', 'f.dr_id')
                ->where('b.checkup_dtl', $localId)
                ->whereRaw("NVL(d.hidden_status,'N') = 'N'")
                ->select([
                    'a.emp_id',
                    'a.checkup_no',
                    'a.checkup_date',
                    'a.reg_no',
                    'c.reg_name',
                    'a.dr_id',
                    'f.dr_name',
                    'c.sex',
                    'c.birth_date',
                    'c.address',
                    'e.app_seq',
                    'e.clab_desc',
                    'b.clabitem_id',
                    DB::raw("'  ' || d.clabitem_desc AS clabitem_desc"),
                    'a.checkup_kesimpulan',
                    'd.normal_f',
                    'd.normal_m',
                    'b.lab_result',
                    'd.item_seq',
                    'd.unit_desc',
                    'd.unit_convert',
                    'd.item_code',
                    'd.high_limit_m',
                    'd.high_limit_f',
                    'd.low_limit_m',
                    'd.low_limit_f',
                    'd.lowhigh_status',
                    'b.lab_result_status',
                    'a.waktu_selesai_pelayanan',
                    DB::raw("to_char(a.checkup_date,'dd/mm/yyyy') AS checkup_date1x"),
                ])
                ->first();

            if (empty($dataDaftarTxn) || empty($dataDaftarTxn->lab_result)) {
                toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                    ->addInfo("Data hasil laboratorium untuk {$localId} kosong. Dilewati.");
                continue;
            }

            // Tentukan faktor konversi jika ada
            $unitConvert = $dataDaftarTxn->lowhigh_status === 'Y'
                ? ($dataDaftarTxn->unit_convert ?: 1)
                : 1;

            // Hitung nilai laboratorium
            $rawValue = $dataDaftarTxn->lab_result * $unitConvert;
            $value = (fmod($rawValue, 1) !== 0.0) ? $rawValue : number_format($rawValue);


            try {
                $effective = Carbon::createFromFormat('d/m/Y H:i:s', $lab->checkup_date, 'Asia/Jakarta')->toIso8601String();
            } catch (\Exception $e) {
                toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                    ->addError("Format waktu tidak valid: {$lab->checkup_date}");
                continue;
            }

            // payload per-komponen
            $payload = [
                'patientId'   => $patientUuid,
                'encounterId' => $encounterUuid,
                'performerId' => $performerId,
                'effectiveDate' => $effective, // dari kode kamu sebelumnya
                // bangun code + valueQuantity, bukan components array
                'code' => [
                    'system'  => 'http://loinc.org',
                    'code'    => $lab->clabitem_id_satusehat,
                    'display' => $lab->clabitem_desc_satusehat,
                ],
                // 'valueQuantity' => [
                //     'value'  => $value,
                //     'unit'   => $item['unitDisplay'],
                //     'system' => 'http://unitsofmeasure.org',
                //     'code'   => $item['unitCode'],
                // ],
            ];

            // Kirimkan data Observation ke API
            try {
                $result = $this->createObservation($payload);
                $observationId = $result['id'] ?? null;

                if ($observationId) {
                    toastr()
                        ->closeOnHover(true)
                        ->closeDuration(3)
                        ->positionClass('toast-top-left')
                        ->addSuccess("LabObservation untuk {$localId} berhasil dikirim (UUID: {$observationId}).");
                    // Simpan ke JSON RJ
                    $dataDaftarPoliRJ['satuSehatUuidRJ']['labObservations'][] = [
                        'uuid'    => $observationId,
                        'localId' => $localId,
                        'checkupNo' => $dataDaftarTxn->checkup_no,
                    ];
                    $this->updateJsonRJ($this->rjNoRef, $dataDaftarPoliRJ);
                }
            } catch (\Exception $e) {
                toastr()
                    ->closeOnHover(true)
                    ->closeDuration(3)
                    ->positionClass('toast-top-left')
                    ->addError("Gagal kirim LabObservation untuk {$localId}: " . $e->getMessage());
            }
        }
    }


    public function postDiagnosticReportLaboratRJ()
    {
        // 1) Ambil data dasar
        $find             = $this->findDataRJ($this->rjNoRef);
        $dataDaftarPoliRJ = $find['dataDaftarRJ'] ?? [];
        $dataPasienRJ     = $find['dataPasienRJ'] ?? [];
        $patientUuid      = $dataPasienRJ['patientUuid'] ?? null;
        $encounterUuid    = $dataDaftarPoliRJ['satuSehatUuidRJ']['encounter']['uuid'] ?? null;
        $sentSR           = $dataDaftarPoliRJ['satuSehatUuidRJ']['serviceRequests'] ?? [];
        $sentSpecimens    = $dataDaftarPoliRJ['satuSehatUuidRJ']['specimens'] ?? [];
        $sentDiagnostics  = $dataDaftarPoliRJ['satuSehatUuidRJ']['diagnosticReports'] ?? [];
        $sentLabObservations = $dataDaftarPoliRJ['satuSehatUuidRJ']['labObservations'] ?? [];


        if (!$patientUuid) {
            toastr()
                ->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addError('Patient UUID belum tersedia. Proses dibatalkan.');
            return;
        }

        if (!$encounterUuid) {
            toastr()
                ->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addError('Encounter UUID belum tersedia. Proses dibatalkan.');
            return;
        }

        $this->initializeSatuSehat();
        $orgId = env('SATUSEHAT_ORGANIZATION_ID');

        $pemeriksaanLab = DB::table('lbtxn_checkuphdrs as a')
            ->join('lbtxn_checkupdtls as b', 'a.checkup_no', '=', 'b.checkup_no')
            ->join('lbmst_clabitems as c', 'b.clabitem_id', '=', 'c.clabitem_id')
            ->join('lbmst_clabs as d', 'c.clab_id', '=', 'd.clab_id')
            ->where('a.status_rjri', 'RJ')
            ->whereNotNull('c.price')
            ->where('a.ref_no', $this->rjNoRef)
            ->select([
                'a.checkup_no',
                'b.checkup_dtl',
                'd.clab_id_satusehat_specimen',
                'd.clab_desc_satusehat_specimen',
                'c.clabitem_id_satusehat',
                'c.clabitem_desc_satusehat',

                // 'c.result_id_satusehat',
                // 'c.result_desc_satusehat',
                DB::raw("to_char(a.checkup_date, 'dd/mm/yyyy HH24:MI:SS') as checkup_date"),
            ])
            ->distinct()
            ->get();

        foreach ($pemeriksaanLab as $lab) {
            $localId = $lab->checkup_dtl;

            if (
                empty($lab->clab_id_satusehat_specimen) ||
                empty($lab->clab_desc_satusehat_specimen) ||
                empty($lab->clabitem_id_satusehat) ||
                empty($lab->clabitem_desc_satusehat)
            ) {
                toastr()
                    ->closeOnHover(true)
                    ->closeDuration(3)
                    ->positionClass('toast-top-left')
                    ->addWarning("Data mapping SATUSEHAT belum lengkap untuk item {$lab->checkup_dtl}, dilewati.");
                continue;
            }

            // 1) CEK APAKAH DIAGNOSTICREPORT SUDAH DIKIRIM
            $alreadyDiag = collect($sentDiagnostics)->firstWhere('localId', $localId);
            if ($alreadyDiag) {
                toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                    ->addInfo("DiagnosticReport {$localId} sudah dikirim (UUID: {$alreadyDiag['uuid']}).");
                continue;
            }

            $sSpecimen = collect($sentSpecimens)->firstWhere('localId', $localId);
            if (!$sSpecimen) {
                toastr()
                    ->closeOnHover(true)
                    ->closeDuration(3)
                    ->positionClass('toast-top-left')
                    ->addInfo("Specimen {$localId} belum ada. Kirim Specimen dulu.");
                continue;
            }

            $sr = collect($sentSR)->firstWhere('localId', $localId);
            if (!$sr) {
                toastr()
                    ->closeOnHover(true)
                    ->closeDuration(3)
                    ->positionClass('toast-top-left')
                    ->addError("ServiceRequest untuk {$localId} belum ada. Kirim ServiceRequest dulu.");
                continue;
            }

            try {
                $collectedIso = Carbon::createFromFormat('d/m/Y H:i:s', $lab->checkup_date, 'Asia/Jakarta')->toIso8601String();
            } catch (\Exception $e) {
                toastr()
                    ->closeOnHover(true)
                    ->closeDuration(3)
                    ->positionClass('toast-top-left')
                    ->addError("Format waktu tidak valid: {$lab->checkup_date}");
                continue;
            }

            $slo = collect($sentLabObservations)
                ->where('checkupNo', $lab->checkup_no)  // Menyaring berdasarkan checkupNo
                ->pluck('uuid')  // Mengambil hanya nilai dari kolom 'uuid'
                ->toArray();  // Mengubah hasilnya menjadi array

            if (empty($slo)) {
                toastr()
                    ->closeOnHover(true)
                    ->closeDuration(3)
                    ->positionClass('toast-top-left')
                    ->addError("ServiceRequest untuk {$localId} belum ada. Kirim ServiceRequest dulu.");
                continue;
            }
            try {
                // === Kirim DiagnosticReport ===
                $diagIdentifier = "DIAG-{$localId}-" . now('Asia/Jakarta')->format('YmdHis');
                $diagnosticData = [
                    'identifier'      => [[
                        'system' => "http://sys-ids.kemkes.go.id/diagnostic/{$orgId}/lab",
                        'use'    => 'official',
                        'value'  => $diagIdentifier,
                    ]],
                    'status'          => 'final',
                    'categoryCode'    => 'LAB',
                    'categoryDisplay' => 'Laboratory',
                    'codeSystem'      => 'http://loinc.org',
                    'code'            => $lab->clabitem_id_satusehat,
                    'display'         => $lab->clabitem_desc_satusehat,
                    'patientId'       => $patientUuid,

                    // tambahkan conclusionCode hardcode di sini () //program belum beres, nanti sesuaikan dengan hasil lab yang di setup (mbak anis)
                    'conclusionCode'  => [[
                        'coding' => [[
                            'system'  => 'http://snomed.info/sct',
                            'code'    => '443938003',
                            'display' => 'Normal hematology report',
                        ]]
                    ]],

                    'encounterId'     => $encounterUuid,
                    'effectiveDate'   => $collectedIso,
                    'issued'          => $collectedIso,
                    'performer'       => ["Organization/{$orgId}"],
                    'specimen'        => ["Specimen/{$sSpecimen['uuid']}"],
                    'observationIds'  => $slo ?? [],
                    'basedOn'         => [$sr['uuid']] ?? [],
                ];

                $responseDiag = $this->createDiagnosticReport($diagnosticData);
                $diagId       = $responseDiag['id'] ?? null;
                if ($diagId) {
                    toastr()->addSuccess("DiagnosticReport untuk {$localId} berhasil dikirim (UUID: {$diagId}).");
                    // Simpan ke JSON RJ
                    $dataDaftarPoliRJ['satuSehatUuidRJ']['diagnosticReports'][] = [
                        'uuid'    => $diagId,
                        'localId' => $localId,
                        'checkupNo' => $lab->checkup_no
                    ];
                    $this->updateJsonRJ($this->rjNoRef, $dataDaftarPoliRJ);
                } else {
                    throw new \Exception('UUID DiagnosticReport tidak diterima.');
                }
            } catch (\Exception $e) {
                toastr()->addError("Gagal kirim Specimen / DiagnosticReport {$localId}: " . $e->getMessage());
            }
        }
    }


    public function mount()
    {
        $this->findData($this->rjNoRef);
    }
    public function render()
    {
        return view('livewire.emr-r-j.post-encounter-r-j.post-encounter-r-j');
    }
}
