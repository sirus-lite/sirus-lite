<?php

namespace App\Http\Livewire\EmrRJ;

use Illuminate\Support\Facades\DB;

use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;
use Spatie\ArrayToXml\ArrayToXml;
use App\Http\Traits\BPJS\AntrianTrait;
use App\Http\Traits\EmrRJ\EmrRJTrait;
use App\Http\Traits\BPJS\iCareTrait;


class EmrRJ extends Component
{
    use WithPagination, EmrRJTrait, iCareTrait;

    // primitive Variable
    public string $myTitle = 'Rekam Medis Rawat Jalan';
    public string $mySnipt = 'Rekam Medis Pasien';
    public string $myProgram = 'Pasien Rawat Jalan';

    public array $myLimitPerPages = [5, 10, 15, 20, 100];
    // limit record per page -resetExcept////////////////
    public int $limitPerPage = 10;

    // my Reg No

    public string $regNo = '';

    public bool $callMasterPasien = false;





    // my Top Bar
    public array $myTopBar = [
        'refDate' => '',

        'refShiftId' => '1',
        'refShiftDesc' => '1',
        'refShiftOptions' => [
            ['refShiftId' => '1', 'refShiftDesc' => '1'],
            ['refShiftId' => '2', 'refShiftDesc' => '2'],
            ['refShiftId' => '3', 'refShiftDesc' => '3'],
        ],

        'refStatusId' => 'A',
        'refStatusDesc' => 'Antrian',
        'refStatusOptions' => [
            ['refStatusId' => 'A', 'refStatusDesc' => 'Antrian'],
            ['refStatusId' => 'L', 'refStatusDesc' => 'Selesai'],
            ['refStatusId' => 'I', 'refStatusDesc' => 'Transfer'],
        ],

        'drId' => 'All',
        'drName' => 'All',
        'drOptions' => [
            [
                'drId' => 'All',
                'drName' => 'All'
            ]
        ],
        'klaimStatusId' => 'All',
        'klaimStatusName' => 'All',
        'klaimStatusOptions' => [
            [
                'klaimStatusId' => 'All',
                'klaimStatusName' => 'All'
            ],
            [
                'klaimStatusId' => 'UMUM',
                'klaimStatusName' => 'UMUM'
            ],
            [
                'klaimStatusId' => 'BPJS',
                'klaimStatusName' => 'BPJS'
            ],
            [
                'klaimStatusId' => 'KRONIS',
                'klaimStatusName' => 'KRONIS'
            ],
        ],
    ];

    public string $refFilter = '';
    // search logic -resetExcept////////////////
    protected $queryString = [
        'refFilter' => ['except' => '', 'as' => 'cariData'],
        'page' => ['except' => 1, 'as' => 'p'],
    ];

    public $dataDaftarPoliRJ = [];

    // reset page when myTopBar Change
    public function updatedReffilter()
    {
        $this->resetPage();
    }

    public function updatedMytopbarRefdate()
    {
        $this->resetPage();
    }

    public function updatedMytopbarRefstatusid()
    {
        $this->resetPage();
    }



    // setter myTopBar Shift and myTopBar refDate
    private function settermyTopBarShiftandmyTopBarrefDate(): void
    {
        // dd/mm/yyyy hh24:mi:ss
        $this->myTopBar['refDate'] = Carbon::now(env('APP_TIMEZONE'))->format('d/m/Y');
        // dd(Carbon::now(env('APP_TIMEZONE'))->format('H:i:s'));

        // shift
        $findShift = DB::table('rstxn_shiftctls')->select('shift')
            ->whereRaw("'" . Carbon::now(env('APP_TIMEZONE'))->format('H:i:s') . "' between
             shift_start and shift_end")
            ->first();
        $this->myTopBar['refShiftId'] = isset($findShift->shift) && $findShift->shift ? $findShift->shift : 3;
    }

    public function settermyTopBarklaimStatusOptions($klaimStatusId, $klaimStatusName): void
    {

        $this->myTopBar['klaimStatusId'] = $klaimStatusId;
        $this->myTopBar['klaimStatusName'] = $klaimStatusName;
        $this->resetPage();
    }


    private function gettermyTopBardrOptions(): void
    {
        $myRefdate = $this->myTopBar['refDate'];

        // Query
        $query = DB::table('rsview_rjkasir')
            ->select(
                'dr_id',
                'dr_name',
            )
            ->where(DB::raw("to_char(rj_date,'dd/mm/yyyy')"), '=', $myRefdate)
            ->groupBy('dr_id')
            ->groupBy('dr_name')
            ->orderBy('dr_name', 'desc')
            ->get();

        // loop and set Ref
        $query->each(function ($item, $key) {
            $this->myTopBar['drOptions'][$key + 1]['drId'] = $item->dr_id;
            $this->myTopBar['drOptions'][$key + 1]['drName'] = $item->dr_name;
        })->toArray();
    }

    public function settermyTopBardrOptions($drId, $drName): void
    {

        $this->myTopBar['drId'] = $drId;
        $this->myTopBar['drName'] = $drName;
        $this->resetPage();
    }



    // open and close modal start////////////////
    //  modal status////////////////
    public bool $isOpen = false;
    public string $isOpenMode = 'insert';

    public bool $isOpenDokter = false;
    public string $isOpenModeDokter = 'insert';

    public bool $isOpenGeneralConsentPasienRJ = false;
    public string $isOpenModeGeneralConsentPasienRJ = 'insert';

    public bool $isOpenScreening = false;
    public string $isOpenModeScreening = 'insert';

    public bool $forceInsertRecord = false;

    public int $rjNoRef;
    public string $regNoRef;

    //
    private function openModal(): void
    {
        $this->isOpen = true;
        $this->isOpenMode = 'insert';
    }
    private function openModalEdit($rjNo, $regNoRef): void
    {
        $this->isOpen = true;
        $this->isOpenMode = 'update';
        $this->rjNoRef = $rjNo;
        $this->regNoRef = $regNoRef;
    }
    private function openModalEditDokter($rjNo, $regNoRef): void
    {
        $this->isOpenDokter = true;
        $this->isOpenModeDokter = 'update';
        $this->rjNoRef = $rjNo;
        $this->regNoRef = $regNoRef;

        //iCare
        $this->findData($rjNo);
        $klaimId = $this->dataDaftarPoliRJ['klaimId'] ?? '';
        if ($klaimId  === 'JM') {
            $sep = $this->dataDaftarPoliRJ['sep']['noSep'] ?? '';
            $nomorKartu = $this->dataDaftarPoliRJ['sep']['reqSep']['request']['t_sep']['noKartu'] ?? '';
            // $this->myiCare($nomorKartu, $sep);
        }
        //iCare

    }

    private function openModalEditGeneralConsentPasienRJ($rjNo, $regNoRef): void
    {
        $this->isOpenGeneralConsentPasienRJ = true;
        $this->isOpenModeGeneralConsentPasienRJ = 'update';
        $this->rjNoRef = $rjNo;
        $this->regNoRef = $regNoRef;
    }

    private function openModalEditScreening($rjNo, $regNoRef): void
    {
        $this->isOpenScreening = true;
        $this->isOpenModeScreening = 'update';
        $this->rjNoRef = $rjNo;
        $this->regNoRef = $regNoRef;
    }

    private function openModalTampil(): void
    {
        $this->isOpen = true;
        $this->isOpenMode = 'tampil';
    }

    public function closeModal(): void
    {
        $this->isOpen = false;
        $this->isOpenMode = 'insert';
        $this->resetInputFields();
    }

    public function closeModalDokter(): void
    {
        $this->isOpenDokter = false;
        $this->isOpenModeDokter = 'insert';
        $this->resetInputFields();
    }

    public function closeModalGeneralConsentPasienRJ(): void
    {
        $this->isOpenGeneralConsentPasienRJ = false;
        $this->isOpenModeGeneralConsentPasienRJ = 'insert';
        $this->resetInputFields();
    }

    public function closeModalScreening(): void
    {
        $this->isOpenScreening = false;
        $this->isOpenModeScreening = 'insert';
        $this->resetInputFields();
    }
    // open and close modal end////////////////


    // resert input private////////////////
    private function resetInputFields(): void
    {

        // resert validation
        $this->resetValidation();
        // resert input

    }
    // resert input private////////////////

    // is going to edit data/////////////////
    public function edit($rjNo, $regNoRef)
    {
        $this->openModalEdit($rjNo, $regNoRef);
        // $this->findData($id);
    }

    public function editDokter($rjNo, $regNoRef)
    {
        $this->openModalEditDokter($rjNo, $regNoRef);
        // $this->findData($id);
    }

    public function editGeneralConsentPasienRJ($rjNo, $regNoRef)
    {
        $this->openModalEditGeneralConsentPasienRJ($rjNo, $regNoRef);
        // $this->findData($id);
    }

    public function editScreening($rjNo, $regNoRef)
    {
        $this->openModalEditScreening($rjNo, $regNoRef);
        // $this->findData($id);
    }





    // listener from blade////////////////
    protected $listeners = [
        // 'ListenerisOpenRJ' => 'ListenerisOpenRJ',
        'confirm_remove_record_RJp' => 'delete'
    ];

    // public function ListenerisOpenRJ($ListenerisOpenRJ): void
    // {
    //     // dd($ListenerisOpenRJ);
    //     $this->isOpen = $ListenerisOpenRJ['isOpen'];
    //     $this->isOpenMode = $ListenerisOpenRJ['isOpenMode'];
    //     $this->render();
    // }


    ////////////////////////////////////////////////
    ///////////begin////////////////////////////////
    ////////////////////////////////////////////////

    // is going to insert data////////////////
    public function create()
    {
        $this->openModal();
    }

    public function tampil()
    {
        toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError('Fitur dalam masa pengembangan');
    }

    public function delete()
    {
        toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError('Fitur dalam masa pengembangan');
    }


    public function callFormPasien(): void
    {
        // set Call MasterPasien True
        $this->callMasterPasien = true;
    }

    public string $activeTab = "rekamMedis";
    public string $activeTabDokter = "assessmentDokter";
    public string $activeTabGeneralConsentPasienRJ = "generalConsentPasienRJ";





    public array $EmrMenu = [
        // [
        //     'ermMenuId' => 'keperawatan',
        //     'ermMenuName' => 'Keperawatan'
        // ],
        [
            'ermMenuId' => 'anamnesa',
            'ermMenuName' => 'Anamnesa'
        ],
        [
            'ermMenuId' => 'pemeriksaan',
            'ermMenuName' => 'Pemeriksaan'
        ],
        [
            'ermMenuId' => 'penilaian',
            'ermMenuName' => 'Penilaian'
        ],
        [
            'ermMenuId' => 'diagnosis',
            'ermMenuName' => 'Diagnosis (ICD)'
        ],
        // [
        //     'ermMenuId' => 'penandaanGbr',
        //     'ermMenuName' => 'Penandaan Gambar'
        // ],
        [
            'ermMenuId' => 'perencanaan',
            'ermMenuName' => 'Perencanaan'
        ],
        [
            'ermMenuId' => 'administrasi',
            'ermMenuName' => 'Administrasi'
        ],
        [
            'ermMenuId' => 'suket',
            'ermMenuName' => 'Surat Keterangan (Sehat/Istirahat)'
        ],
        [
            'ermMenuId' => 'rekamMedis',
            'ermMenuName' => 'Resume Medis'
        ],

    ];


    public array $EmrMenuDokter = [
        [
            'ermMenuId' => 'assessmentDokter',
            'ermMenuName' => 'Assessment Dokter'
        ],
        [
            'ermMenuId' => 'pelayananPenunjang',
            'ermMenuName' => 'Pelayanan Penunjang'
        ],
        [
            'ermMenuId' => 'rekamMedis',
            'ermMenuName' => 'Resume Medis'
        ],

    ];

    public array $EmrMenuGeneralConsentPasienRJ = [
        [
            'ermMenuId' => 'generalConsentPasienRJ',
            'ermMenuName' => 'General Consent Pasien RJ'
        ],
        [
            'ermMenuId' => 'informConsentPasienRJ',
            'ermMenuName' => 'Inform Consent Pasien RJ'
        ]
    ];



    public function masukPoli($rjNo)
    {

        $this->findData($rjNo);

        // Cek apakah waktu_masuk_poli sudah ada
        $waktuMasukPoliDB = DB::table('rstxn_rjhdrs')
            ->where('rj_no', $rjNo)
            ->value('waktu_masuk_poli');


        // ketika cek_waktu_masuk_poli kosong lalu update
        if (!$waktuMasukPoliDB) {
            $waktuMasukPoli = Carbon::now(env('APP_TIMEZONE'))->format('d/m/Y H:i:s');

            DB::table('rstxn_rjhdrs')
                ->where('rj_no', $rjNo)
                ->update([
                    'waktu_masuk_poli' => DB::raw("to_date('" . $waktuMasukPoli . "','dd/mm/yyyy hh24:mi:ss')"), //waktu masuk = rjdate
                ]);
        }


        // Ambil waktu_masuk_poli yang sudah diformat dari database
        $waktuMasukPoli = DB::table('rstxn_rjhdrs')
            ->where('rj_no', $rjNo)
            ->select(DB::raw("to_char(waktu_masuk_poli, 'dd/mm/yyyy hh24:mi:ss') as waktu_masuk_poli"))
            ->value('waktu_masuk_poli');

        // Update TaskId 4 jika belum ada nilainya
        if (empty($this->dataDaftarPoliRJ['taskIdPelayanan']['taskId4'])) {
            $this->dataDaftarPoliRJ['taskIdPelayanan']['taskId4'] = $waktuMasukPoli;
            $this->updateDataRJ($rjNo);
            toastr()
                ->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addSuccess("Masuk Poli " . $this->dataDaftarPoliRJ['taskIdPelayanan']['taskId4']);
        } else {
            toastr()
                ->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addError("Masuk Poli " . $this->dataDaftarPoliRJ['taskIdPelayanan']['taskId4']);
        }


        // cari no Booking
        $noBooking =  $this->dataDaftarPoliRJ['noBooking'];

        $waktu = Carbon::createFromFormat('d/m/Y H:i:s', $this->dataDaftarPoliRJ['taskIdPelayanan']['taskId4'], env('APP_TIMEZONE'))->timestamp * 1000; //waktu dalam timestamp milisecond

        $cekPoliSpesialis = DB::table('rsmst_polis')
            ->select('poli_id', 'poli_desc', 'kd_poli_bpjs', 'spesialis_status')
            ->where('spesialis_status', '=', '1')
            ->where('poli_id', '=', $this->dataDaftarPoliRJ['poliId'])
            ->exists();

        if (($cekPoliSpesialis)) {
            $this->pushDataTaskId($noBooking, 4, $waktu);
        }
    }


    public function keluarPoli($rjNo)
    {
        $this->findData($rjNo);

        // Cek apakah pasien sudah melalui pelayanan Poli (TaskId 4)
        if (empty($this->dataDaftarPoliRJ['taskIdPelayanan']['taskId4'])) {
            toastr()
                ->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addError("Status Pasien Belum melalui pelayanan Poli");
            return;
        }

        // Jika TaskId 5 sudah ada, tampilkan pesan error dan hentikan proses
        if (!empty($this->dataDaftarPoliRJ['taskIdPelayanan']['taskId5'])) {
            toastr()
                ->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addError("Keluar Poli sudah tercatat pada " . $this->dataDaftarPoliRJ['taskIdPelayanan']['taskId5']);
            // return;
        }

        // Set waktu keluar poli saat ini
        if (!$this->dataDaftarPoliRJ['taskIdPelayanan']['taskId5']) {
            $keluarPoli = Carbon::now(env('APP_TIMEZONE'))->format('d/m/Y H:i:s');
            $this->dataDaftarPoliRJ['taskIdPelayanan']['taskId5'] = $keluarPoli;
            // Update data ke database
            $this->updateDataRJ($rjNo);
        }

        // Konversi waktu TaskId 5 ke timestamp (milisecond)
        $waktu = Carbon::createFromFormat('d/m/Y H:i:s', $this->dataDaftarPoliRJ['taskIdPelayanan']['taskId5'], env('APP_TIMEZONE'))->timestamp * 1000;


        // Cek apakah Poli Spesialis mengirim data ke BPJS menggunakan method exists() untuk efisiensi
        $cekPoliSpesialis = DB::table('rsmst_polis')
            ->where('spesialis_status', '1')
            ->where('poli_id', $this->dataDaftarPoliRJ['poliId'])
            ->exists();

        if ($cekPoliSpesialis) {
            $this->pushDataTaskId($this->dataDaftarPoliRJ['noBooking'], 5, $waktu);
        }

        toastr()
            ->closeOnHover(true)
            ->closeDuration(3)
            ->positionClass('toast-top-left')
            ->addSuccess("Keluar Poli " . $keluarPoli);
    }

    private function findData($rjNo): void
    {
        $findDataRJ = $this->findDataRJ($rjNo);
        $this->dataDaftarPoliRJ  = $findDataRJ['dataDaftarRJ'];
    }

    private function updateDataRJ($rjNo): void
    {
        $this->updateJsonRJ($rjNo, $this->dataDaftarPoliRJ);

        toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addSuccess("Json Berhasil di update.");
    }

    private function pushDataTaskId($noBooking, $taskId, $time): void
    {
        //////////////////////////////////////////////////////////
        /////////////////////////////////////////////////////////
        // Update Task Id $kodebooking, $taskid, $waktu, $jenisresep

        $waktu = $time;
        $HttpGetBpjs =  AntrianTrait::update_antrean($noBooking, $taskId, $waktu, "")->getOriginalContent();

        // set http response to public
        // $this->HttpGetBpjsStatus = $HttpGetBpjs['metadata']['code']; //status 200 201 400 ..
        // $this->HttpGetBpjsJson = json_encode($HttpGetBpjs, true); //Return Response Tambah Antrean

        // metadata d kecil
        if ($HttpGetBpjs['metadata']['code'] == 200) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addSuccess('Task Id' . $taskId . ' ' . $HttpGetBpjs['metadata']['code'] . ' ' . $HttpGetBpjs['metadata']['message']);
        } else {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError('Task Id' . $taskId . ' ' .  $HttpGetBpjs['metadata']['code'] . ' ' . $HttpGetBpjs['metadata']['message']);

            // Ulangi Proses pushTaskId;
            // $this->emit('rePush_Data_TaskId_Confirmation');
        }
    }


    // when new form instance
    public function mount()
    {
        $this->settermyTopBarShiftandmyTopBarrefDate();
    }

    // //Otomatis Buka Icare
    // public bool $isOpenRekamMedisicare;
    // public string $icareUrlResponse;
    // private function myiCare($nomorKartu, $sep)
    // {
    //     if (!$sep) {
    //         toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError("Belum Terbit SEP.");
    //         return;
    //     }

    //     $kodeDokter = DB::table('rsmst_doctors')
    //         ->select('kd_dr_bpjs')
    //         ->where('rsmst_doctors.dr_id', $this->dataDaftarPoliRJ['drId'])
    //         ->first();


    //     if (!$kodeDokter || $kodeDokter->kd_dr_bpjs == null) {
    //         toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError("Dokter tidak memiliki hak akses untuk I-Care.");
    //         return;
    //     }

    //     // trait
    //     $HttpGetBpjs  = $this->icare($nomorKartu, $kodeDokter->kd_dr_bpjs)->getOriginalContent();
    //     // $HttpGetBpjs =  iCareTrait::icare($nomorKartu, $kodeDokter)->getOriginalContent();
    //     // set http response to public
    //     $HttpGetBpjsStatus = $HttpGetBpjs['metadata']['code']; //status 200 201 400 ..
    //     $HttpGetBpjsJson = $HttpGetBpjs; //Return Response
    //     if ($HttpGetBpjsStatus == 200) {
    //         $this->icareUrlResponse = $HttpGetBpjsJson['response']['url'];
    //         // offkan modalIcare hanya untuk penilaian BPJS
    //         // $this->openModalicare();
    //         // return redirect()->to($HttpGetBpjsJson['response']['url']);
    //     } else {
    //         toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError(json_encode($HttpGetBpjsJson['metadata']['message'], true));
    //         return;
    //     }
    // }


    // select data start////////////////
    public function render()
    {
        $this->gettermyTopBardrOptions();

        // set mySearch
        $mySearch = $this->refFilter;
        $myRefdate = $this->myTopBar['refDate'];
        // $myRefshift = $this->myTopBar['refShiftId'];
        $myRefstatusId = $this->myTopBar['refStatusId'];
        $myRefdrId = $this->myTopBar['drId'];
        $myRefklaimStatusId = $this->myTopBar['klaimStatusId'];



        //////////////////////////////////////////
        // Query ///////////////////////////////
        //////////////////////////////////////////
        $query = DB::table('rsview_rjkasir')
            ->select(
                DB::raw("to_char(rj_date,'dd/mm/yyyy hh24:mi:ss') AS rj_date"),
                DB::raw("to_char(rj_date,'yyyymmddhh24miss') AS rj_date1"),
                'rj_no',
                'reg_no',
                'reg_name',
                'sex',
                'address',
                'thn',
                DB::raw("to_char(birth_date,'dd/mm/yyyy') AS birth_date"),
                'poli_id',
                'poli_desc',
                'dr_id',
                'dr_name',
                'klaim_id',
                'shift',
                'vno_sep',
                'no_antrian',
                'rj_status',
                'nobooking',
                'push_antrian_bpjs_status',
                'push_antrian_bpjs_json',
                'datadaftarpolirj_json',
                DB::raw("(select count(*) from lbtxn_checkuphdrs where status_rjri='RJ' and checkup_status!='B' and ref_no = rsview_rjkasir.rj_no) AS lab_status"),
                DB::raw("(select count(*) from rstxn_rjrads where rj_no = rsview_rjkasir.rj_no) AS rad_status")
            )
            ->where(DB::raw("nvl(erm_status,'A')"), '=', $myRefstatusId)
            ->where('rj_status', '!=', 'F')
            ->where('klaim_id', '!=', 'KR')

            // ->where('shift', '=', $myRefshift)
            ->where(DB::raw("to_char(rj_date,'dd/mm/yyyy')"), '=', $myRefdate);


        if ($myRefklaimStatusId != 'All') {
            $query->whereIn('klaim_id', function ($query) use ($myRefklaimStatusId) {
                $query->select('klaim_id')
                    ->from('rsmst_klaimtypes')
                    ->where('klaim_status', '=', $myRefklaimStatusId);
            });
        }
        // Jika where dokter tidak kosong
        if ($myRefdrId != 'All') {
            $query->where('dr_id', $myRefdrId);
        }

        $query->where(function ($q) use ($mySearch) {
            $q->Where(DB::raw('upper(reg_name)'), 'like', '%' . strtoupper($mySearch) . '%')
                ->orWhere(DB::raw('upper(reg_no)'), 'like', '%' . strtoupper($mySearch) . '%')
                ->orWhere(DB::raw('upper(dr_name)'), 'like', '%' . strtoupper($mySearch) . '%');
        })
            ->orderBy('dr_name',  'asc')
            ->orderBy('no_antrian',  'desc')
            ->orderBy('rj_date1',  'desc');

        ////////////////////////////////////////////////
        // end Query
        ///////////////////////////////////////////////



        return view(
            'livewire.emr-r-j.emr-r-j',
            ['myQueryData' => $query->paginate($this->limitPerPage)]
        );
    }
    // select data end////////////////


}
