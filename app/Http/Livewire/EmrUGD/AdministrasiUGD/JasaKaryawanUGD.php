<?php

namespace App\Http\Livewire\EmrUGD\AdministrasiUGD;

use Illuminate\Support\Facades\DB;

use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;


use App\Http\Traits\customErrorMessagesTrait;
use App\Http\Traits\EmrUGD\EmrUGDTrait;


use App\Http\Traits\LOV\LOVJasaKaryawan\LOVJasaKaryawanTrait;
use Exception;


class JasaKaryawanUGD extends Component
{
    use WithPagination, EmrUGDTrait, LOVJasaKaryawanTrait;


    // listener from blade////////////////
    protected $listeners = [
        'storeAssessmentDokterUGD' => 'store',
        'syncronizeAssessmentDokterUGDFindData' => 'mount',
        'syncronizeAssessmentPerawatUGDFindData' => 'mount'
    ];


    //////////////////////////////
    // Ref on top bar
    //////////////////////////////
    public $rjNoRef;



    // dataDaftarUgd RJ
    public array $dataDaftarUgd = [];

    //////////////////////////////////////////////////////////////////////



    //////////////////////////////////////////////////////////////////////
    // LOV Nested
    public array $jasaKaryawan;
    // LOV Nested

    public $formEntryJasaKaryawan = [
        'jasaKaryawanId' => '',
        'jasaKaryawanDesc' => '',
        'jasaKaryawanPrice' => '',
    ];







    ////////////////////////////////////////////////
    ///////////begin////////////////////////////////
    ////////////////////////////////////////////////
    public function updated($propertyName)
    {
        // dd($propertyName);
        // $this->validateOnly($propertyName);
    }

    // insert and update record start////////////////
    public function store()
    {


        // Logic update mode start //////////
        $this->updateDataUGD($this->dataDaftarUgd['rjNo']);
        $this->emit('syncronizeAssessmentDokterUGDFindData');
        $this->emit('syncronizeAssessmentPerawatUGDFindData');
    }

    private function updateDataUGD($rjNo): void
    {

        // update table trnsaksi
        // DB::table('rstxn_ugdhdrs')
        //     ->where('rj_no', $rjNo)
        //     ->update([
        //         'datadaftarugd_json' => json_encode($this->dataDaftarUgd, true),
        //         'datadaftarugd_xml' => ArrayToXml::convert($this->dataDaftarUgd),
        //     ]);

        $this->updateJsonUGD($rjNo, $this->dataDaftarUgd);


        toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addSuccess("Jasa Karyawan berhasil disimpan.");
    }
    // insert and update record end////////////////


    private function findData($rjno): void
    {
        $findDataUGD = $this->findDataUGD($rjno);
        $this->dataDaftarUgd  = $findDataUGD;

        // jika JasaKaryawan tidak ditemukan tambah variable JasaKaryawan pda array
        if (isset($this->dataDaftarUgd['JasaKaryawan']) == false) {
            $this->dataDaftarUgd['JasaKaryawan'] = [];
        }
    }



    public function insertJasaKaryawan(): void
    {

        // validate
        $this->checkUgdStatus();
        // customErrorMessages
        $rules = [
            "formEntryJasaKaryawan.jasaKaryawanId"    => 'bail|required|exists:rsmst_actemps,acte_id',
            "formEntryJasaKaryawan.jasaKaryawanDesc"  => 'bail|required',
            "formEntryJasaKaryawan.jasaKaryawanPrice" => 'bail|required|numeric',
        ];

        $messages = [
            'formEntryJasaKaryawan.jasaKaryawanId.required'   => 'ID karyawan harus diisi.',
            'formEntryJasaKaryawan.jasaKaryawanId.exists'     => 'ID karyawan tidak valid atau tidak ditemukan.',
            'formEntryJasaKaryawan.jasaKaryawanDesc.required' => 'Deskripsi jasa harus diisi.',
            'formEntryJasaKaryawan.jasaKaryawanPrice.required' => 'Harga jasa harus diisi.',
            'formEntryJasaKaryawan.jasaKaryawanPrice.numeric' => 'Harga jasa harus berupa angka.',
        ];

        // Proses Validasi///////////////////////////////////////////
        $this->validate($rules, $messages);

        // validate


        // pengganti race condition
        // start:
        try {

            $lastInserted = DB::table('rstxn_ugdactemps')
                ->select(DB::raw("nvl(max(acte_dtl)+1,1) as acte_dtl_max"))
                ->first();
            // insert into table transaksi
            DB::table('rstxn_ugdactemps')
                ->insert([
                    'acte_dtl' => $lastInserted->acte_dtl_max,
                    'rj_no' => $this->rjNoRef,
                    'acte_id' => $this->formEntryJasaKaryawan['jasaKaryawanId'],
                    'acte_price' => $this->formEntryJasaKaryawan['jasaKaryawanPrice'],
                ]);


            $this->dataDaftarUgd['JasaKaryawan'][] = [
                'JasaKaryawanId' => $this->formEntryJasaKaryawan['jasaKaryawanId'],
                'JasaKaryawanDesc' => $this->formEntryJasaKaryawan['jasaKaryawanDesc'],
                'JasaKaryawanPrice' => $this->formEntryJasaKaryawan['jasaKaryawanPrice'],
                'rjActeDtl' => $lastInserted->acte_dtl_max,
                'rjNo' => $this->rjNoRef,
                'userLog' => auth()->user()->myuser_name,
                'userLogDate' => Carbon::now(env('APP_TIMEZONE'))->format('d/m/Y H:i:s')
            ];

            $this->paketLainLainJasaKaryawan($this->formEntryJasaKaryawan['jasaKaryawanId'], $this->rjNoRef, $lastInserted->acte_dtl_max);
            $this->paketObatJasaKaryawan($this->formEntryJasaKaryawan['jasaKaryawanId'], $this->rjNoRef, $lastInserted->acte_dtl_max);

            $this->store();
            $this->resetformEntryJasaKaryawan();



            //
        } catch (Exception $e) {
            // display an error to user
            dd($e->getMessage());
        }
        // goto start;
    }

    public function removeJasaKaryawan($rjActeDtl)
    {

        $this->checkUgdStatus();


        // pengganti race condition
        // start:
        try {

            $this->removepaketLainLainJasaKaryawan($rjActeDtl);
            $this->removepaketObatJasaKaryawan($rjActeDtl);

            // remove into table transaksi
            DB::table('rstxn_ugdactemps')
                ->where('acte_dtl', $rjActeDtl)
                ->delete();


            $JasaKaryawan = collect($this->dataDaftarUgd['JasaKaryawan'])->where("rjActeDtl", '!=', $rjActeDtl)->toArray();
            $this->dataDaftarUgd['JasaKaryawan'] = $JasaKaryawan;


            $this->store();


            //
        } catch (Exception $e) {
            // display an error to user
            dd($e->getMessage());
        }
        // goto start;


    }

    // /////////////////////////////////////////////////////////////////
    // Paket Jasa Karyawan -> Lain lain
    private function paketLainLainJasaKaryawan($acteId, $rjNo, $acteDtl): void
    {
        $collection = DB::table('rsmst_acteothers')
            ->select('other_id', 'acteother_price')
            ->where('acte_id', $acteId)
            ->orderBy('acte_id')
            ->get();

        foreach ($collection as $item) {
            $this->insertLainLain($acteId, $rjNo, $acteDtl, $item->other_id, 'Paket JK', $item->acteother_price);
        }
    }

    private function insertLainLain($acteId, $rjNo, $acteDtl, $otherId, $otherDesc, $otherPrice): void
    {

        // validate
        // customErrorMessages
        $messages = customErrorMessagesTrait::messages();
        // require nik ketika pasien tidak dikenal
        $collectingMyLainLain =
            [
                "LainLainId" => $otherId,
                "LainLainDesc" => $otherDesc,
                "LainLainPrice" => $otherPrice,
                "acteId" => $acteId,
                "acteDtl" => $acteDtl,
                "rjNo" => $rjNo,

            ];

        $rules = [
            "LainLainId" => 'bail|required|exists:rsmst_others ,other_id',
            "LainLainDesc" => 'bail|required|',
            "LainLainPrice" => 'bail|required|numeric|',
            "acteId" => 'bail|required||',
            "acteDtl" => 'bail|required|numeric|',
            "rjNo" => 'bail|required|numeric|',



        ];

        // Proses Validasi///////////////////////////////////////////
        $validator = Validator::make($collectingMyLainLain, $rules, $messages);

        if ($validator->fails()) {
            dd($validator->validated());
        }


        // pengganti race condition
        // start:
        try {

            $lastInserted = DB::table('rstxn_ugdothers')
                ->select(DB::raw("nvl(max(rjo_dtl)+1,1) as rjo_dtl_max"))
                ->first();
            // insert into table transaksi
            DB::table('rstxn_ugdothers')
                ->insert([
                    'rjo_dtl' => $lastInserted->rjo_dtl_max,
                    'acte_dtl' => $collectingMyLainLain['acteDtl'],
                    'rj_no' => $collectingMyLainLain['rjNo'],
                    'other_id' => $collectingMyLainLain['LainLainId'],
                    'other_price' => $collectingMyLainLain['LainLainPrice'],
                ]);


            $this->dataDaftarUgd['LainLain'][] = [
                'LainLainId' => $collectingMyLainLain['LainLainId'],
                'LainLainDesc' => $collectingMyLainLain['LainLainDesc'],
                'LainLainPrice' => $collectingMyLainLain['LainLainPrice'],
                'rjotherDtl' => $lastInserted->rjo_dtl_max,
                'rjNo' => $collectingMyLainLain['rjNo'],
                'acte_dtl' => $collectingMyLainLain['acteDtl']
            ];

            $this->store();
            //
        } catch (Exception $e) {
            // display an error to user
            dd($e->getMessage());
        }
        // goto start;
    }

    private function removepaketLainLainJasaKaryawan($rjActeDtl): void
    {
        $collection = DB::table('rstxn_ugdothers')
            ->select('rjo_dtl')
            ->where('acte_dtl', $rjActeDtl)
            ->orderBy('acte_dtl')
            ->get();

        foreach ($collection as $item) {
            $this->removeLainLain($item->rjo_dtl);
        }
    }

    private function removeLainLain($rjotherDtl): void
    {

        $this->checkUgdStatus();


        // pengganti race condition
        // start:
        try {
            // remove into table transaksi
            DB::table('rstxn_ugdothers')
                ->where('rjo_dtl', $rjotherDtl)
                ->delete();


            $LainLain = collect($this->dataDaftarUgd['LainLain'])->where("rjotherDtl", '!=', $rjotherDtl)->toArray();
            $this->dataDaftarUgd['LainLain'] = $LainLain;

            $this->store();
            //
        } catch (Exception $e) {
            // display an error to user
            dd($e->getMessage());
        }
        // goto start;
    }
    // Paket Jasa Karyawan -> Lain lain


    // /////////////////////////////////////////////////////////////////
    // Paket Jasa Karyawan -> Obat
    private function paketObatJasaKaryawan($acteId, $rjNo, $acteDtl): void
    {
        $collection = DB::table('rsmst_acteprods')
            ->select(
                'immst_products.product_id as product_id',
                'acte_id',
                'acteprod_qty',
                'immst_products.product_name as product_name',
                'immst_products.sales_price as sales_price',

            )
            ->where('acte_id', $acteId)
            ->join('immst_products', 'immst_products.product_id', 'rsmst_acteprods.product_id')
            ->orderBy('acte_id')
            ->get();

        foreach ($collection as $item) {
            $this->insertObat($acteId, $rjNo, $acteDtl, $item->product_id, 'Paket JK' . $item->product_name, $item->sales_price, $item->acteprod_qty);
        }
    }

    private function insertObat($acteId, $rjNo, $acteDtl, $ObatId, $ObatDesc, $ObatPrice, $Obatqty): void
    {

        // validate
        // customErrorMessages
        $messages = customErrorMessagesTrait::messages();
        // require nik ketika pasien tidak dikenal
        $collectingMyObat = [
            "productId" => $ObatId,
            "productName" => $ObatDesc,
            "signaX" => 1,
            "signaHari" => 1,
            "qty" => $Obatqty,
            "productPrice" => $ObatPrice,
            "catatanKhusus" => '-',
            "acteDtl" => $acteDtl,
            "acteId" => $acteId,
            "rjNo" => $rjNo
        ];

        $rules = [
            "productId" => 'bail|required|exists:immst_products ,product_id',
            "productName" => 'bail|required|',
            "signaX" => 'bail|required|numeric|min:1|max:5',
            "signaHari" => 'bail|required|numeric|min:1|max:5',
            "qty" => 'bail|required|digits_between:1,3|',
            "productPrice" => 'bail|required|numeric|',
            "catatanKhusus" => 'bail|',
            "acteDtl" => 'bail|required|numeric|',
            "acteId" => 'bail|required|',
            "rjNo" => 'bail|required|numeric|',
        ];

        // Proses Validasi///////////////////////////////////////////
        $validator = Validator::make($collectingMyObat, $rules, $messages);

        if ($validator->fails()) {
            dd($validator->validated());
        }


        // pengganti race condition
        // start:
        try {

            $lastInserted = DB::table('rstxn_ugdobats')
                ->select(DB::raw("nvl(max(rjobat_dtl)+1,1) as rjobat_dtl_max"))
                ->first();
            // insert into table transaksi
            DB::table('rstxn_ugdobats')
                ->insert([
                    'rjobat_dtl' => $lastInserted->rjobat_dtl_max,
                    'acte_dtl' => $collectingMyObat['acteDtl'],
                    'rj_no' => $collectingMyObat['rjNo'],
                    'product_id' => $collectingMyObat['productId'],
                    'qty' => $collectingMyObat['qty'],
                    'price' => $collectingMyObat['productPrice'],
                    'rj_carapakai' => $collectingMyObat['signaX'],
                    'rj_kapsul' => $collectingMyObat['signaHari'],
                    'rj_takar' => 'Tablet',
                    'catatan_khusus' => $collectingMyObat['catatanKhusus'],
                    'exp_date' => DB::raw("to_date('" . $this->dataDaftarUgd['rjDate'] . "','dd/mm/yyyy hh24:mi:ss')+30"),
                    'etiket_status' => 0,
                ]);


            // $this->dataDaftarUgd['eresep'][] = [
            //     'productId' => $this->collectingMyProduct['productId'],
            //     'productName' => $this->collectingMyProduct['productName'],
            //     'jenisKeterangan' => 'NonRacikan', //Racikan non racikan
            //     'signaX' => $this->collectingMyProduct['signaX'],
            //     'signaHari' => $this->collectingMyProduct['signaHari'],
            //     'qty' => $this->collectingMyProduct['qty'],
            //     'productPrice' => $this->collectingMyProduct['productPrice'],
            //     'catatanKhusus' => $this->collectingMyProduct['catatanKhusus'],
            //     'rjObatDtl' => $lastInserted->rjobat_dtl_max,
            //     'rjNo' => $this->rjNoRef,
            // ];

            $this->store();
            //
        } catch (Exception $e) {
            // display an error to user
            dd($e->getMessage());
        }
        // goto start;
    }

    private function removepaketObatJasaKaryawan($rjActeDtl): void
    {
        $collection = DB::table('rstxn_ugdobats')
            ->select('rjobat_dtl')
            ->where('acte_dtl', $rjActeDtl)
            ->orderBy('acte_dtl')
            ->get();

        foreach ($collection as $item) {
            $this->removeObat($item->rjobat_dtl);
        }
    }

    private function removeObat($rjObatDtl): void
    {

        $this->checkUgdStatus();


        // pengganti race condition
        // start:
        try {
            // remove into table transaksi
            DB::table('rstxn_ugdobats')
                ->where('rjobat_dtl', $rjObatDtl)
                ->delete();


            // $LainLain = collect($this->dataDaftarUgd['LainLain'])->where("rjotherDtl", '!=', $rjotherDtl)->toArray();
            // $this->dataDaftarUgd['LainLain'] = $LainLain;

            $this->store();
            //
        } catch (Exception $e) {
            // display an error to user
            dd($e->getMessage());
        }
        // goto start;
    }
    // Paket Jasa Karyawan -> Obat
    // /////////////////////////////////////////////////////////////////


    public function resetformEntryJasaKaryawan()
    {
        $this->reset([
            'formEntryJasaKaryawan',
            'collectingMyJasaKaryawan'
        ]);
        $this->resetValidation();
    }

    public function checkUgdStatus()
    {
        $lastInserted = DB::table('rstxn_ugdhdrs')
            ->select('rj_status')
            ->where('rj_no', $this->rjNoRef)
            ->first();

        if ($lastInserted->rj_status !== 'A') {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError("Pasien Sudah Pulang, Trasaksi Terkunci.");
            return (dd('Pasien Sudah Pulang, Trasaksi Terkuncixx.' . $this->rjNoRef));
        }
    }


    // when new form instance
    public function mount()
    {
        $this->findData($this->rjNoRef);
    }

    private function syncDataFormEntry(): void
    {
        // Synk Lov JasaKaryawan
        $this->formEntryJasaKaryawan['jasaKaryawanId'] = $this->jasaKaryawan['JasaKaryawanId'] ?? '';
        $this->formEntryJasaKaryawan['jasaKaryawanDesc'] = $this->jasaKaryawan['JasaKaryawanDesc'] ?? '';
        // $this->formEntryJasaKaryawan['jasaKaryawanPrice'] = $this->jasaKaryawan['JasaKaryawanPrice'] ?? '';

        // Jika 'jasaKaryawanPrice' belum tersedia atau kosong, tentukan harga berdasarkan status klaim
        if (!isset($this->formEntryJasaKaryawan['jasaKaryawanPrice']) || empty($this->formEntryJasaKaryawan['jasaKaryawanPrice'])) {
            // Ambil klaim_status dari rsmst_klaimtypes dengan default 'UMUM' jika NULL
            $klaimStatus = DB::table('rsmst_klaimtypes')
                ->where('klaim_id', $this->dataDaftarUgd['klaimId'] ?? '')
                ->value('klaim_status') ?? 'UMUM';

            // Berdasarkan status klaim, ambil harga yang sesuai dari tabel rsmst_actemps
            if ($klaimStatus === 'BPJS') {
                $JasaKaryawanPrice = DB::table('rsmst_actemps')
                    ->where('acte_id', $this->jasaKaryawan['JasaKaryawanId'] ?? '')
                    ->value('acte_price_bpjs');
            } else {
                $JasaKaryawanPrice = DB::table('rsmst_actemps')
                    ->where('acte_id', $this->jasaKaryawan['JasaKaryawanId'] ?? '')
                    ->value('acte_price');
            }

            // Set JasaKaryawanPrice jika ditemukan, jika tidak set ke 0
            $this->formEntryJasaKaryawan['jasaKaryawanPrice'] = $JasaKaryawanPrice ?? 0;
        }
    }
    private function syncLOV(): void
    {
        $this->jasaKaryawan = $this->collectingMyJasaKaryawan;
    }


    // select data start////////////////
    public function render()
    {

        // LOV
        $this->syncLOV();
        // FormEntry
        $this->syncDataFormEntry();

        return view(
            'livewire.emr-u-g-d.administrasi-u-g-d.jasa-karyawan-u-g-d',
            [
                // 'RJpasiens' => $query->paginate($this->limitPerPage),
                'myTitle' => 'Data Pasien Unit Gawat Darurat',
                'mySnipt' => 'Rekam Medis Pasien',
                'myProgram' => 'Jasa Karyawan',
            ]
        );
    }
    // select data end////////////////


}
