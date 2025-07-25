<?php

namespace App\Http\Traits\EmrRI;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Spatie\ArrayToXml\ArrayToXml;
use Exception;


trait EmrRITrait
{

    protected function findDataRI($rino): array
    {
        try {
            $findData = DB::table('rsview_rihdrs')
                ->select('datadaftarri_json', 'vno_sep')
                ->where('rihdr_no', $rino)
                ->first();

            $datadaftarri_json = isset($findData->datadaftarri_json) ? $findData->datadaftarri_json : null;
            // if meta_data_pasien_json = null
            // then cari Data Pasien By Key Collection (exception when no data found)
            //
            // else json_decode
            if ($datadaftarri_json) {
                $dataDaftarRi = json_decode($findData->datadaftarri_json, true);
            } else {

                // toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError("Data tidak dapat di proses json Ri Trait.");
                $dataDaftarRi = DB::table('rsview_rihdrs')
                    ->select(
                        DB::raw("to_char(entry_date,'dd/mm/yyyy hh24:mi:ss') AS entry_date"),
                        DB::raw("to_char(exit_date,'dd/mm/yyyy hh24:mi:ss') AS exit_date"),
                        DB::raw("to_char(exit_date,'yyyymmddhh24miss') AS exit_date1"),

                        'rihdr_no',

                        'reg_no',
                        'reg_name',
                        'sex',
                        'address',
                        'thn',
                        DB::raw("to_char(birth_date,'dd/mm/yyyy') AS birth_date"),

                        'admin_status',
                        'admin_age',
                        'police_case',

                        'entry_id',
                        'entry_desc',

                        'room_id',
                        'room_name',
                        'bed_no',
                        'bangsal_id',
                        'bangsal_name',

                        'dr_id',
                        'dr_name',

                        'klaim_id',
                        'klaim_desc',

                        'vno_sep',
                        'ri_status',

                        'datadaftarri_json'
                    )
                    ->where('rihdr_no', '=', $rino)
                    ->first();

                $dataDaftarRi = [
                    "entryDate" => $dataDaftarRi->entry_date,
                    "exitDate" => $dataDaftarRi->exit_date ?? '',

                    "riHdrNo" => $dataDaftarRi->rihdr_no,

                    "regNo" =>  $dataDaftarRi->reg_no,
                    "k14th" => [$dataDaftarRi->admin_age ? true : false], //Kurang dari 14 Th
                    "kPolisi" => [$dataDaftarRi->police_case == 'Y' ? true : false], //Kasus Polisi

                    "entryId" => $dataDaftarRi->entry_id,
                    "entryDesc" => $dataDaftarRi->entry_desc,

                    "bangsalId" => $dataDaftarRi->bangsal_id,
                    "bangsalDesc" => $dataDaftarRi->bangsal_name,

                    "roomId" => $dataDaftarRi->room_id,
                    "roomDesc" => $dataDaftarRi->room_name,

                    "bedNo" => $dataDaftarRi->bed_no,

                    "klaimId" => $dataDaftarRi->klaim_id == 'JM' ? 'JM' : 'UM',
                    "klaimDesc" => $dataDaftarRi->klaim_id == 'JM' ? 'BPJS' : 'UMUM',

                    "drId" =>  $dataDaftarRi->dr_id,
                    "drDesc" =>  $dataDaftarRi->dr_name,

                    'sep' => [
                        "noSep" =>  $dataDaftarRi->vno_sep,
                        "reqSep" => [],
                        "resSep" => [],
                    ]
                ];
            }

            // upate data array terkini
            $updatedDataDaftarRi = DB::table('rsview_rihdrs')
                ->select(
                    'room_id',
                    'room_name',
                    'bed_no',
                    'bangsal_id',
                    'bangsal_name',
                    'klaim_id',
                    'klaim_desc',
                    DB::raw("to_char(entry_date,'dd/mm/yyyy hh24:mi:ss') AS entry_date"),
                    DB::raw("to_char(exit_date,'dd/mm/yyyy hh24:mi:ss') AS exit_date"),
                    'vno_sep'
                )
                ->where('rihdr_no', '=', $rino)
                ->first();
            $dataDaftarRi['bangsalId'] = $updatedDataDaftarRi->bangsal_id;
            $dataDaftarRi['bangsalDesc'] = $updatedDataDaftarRi->bangsal_name;
            $dataDaftarRi['roomId'] = $updatedDataDaftarRi->room_id;
            $dataDaftarRi['roomDesc'] = $updatedDataDaftarRi->room_name;
            $dataDaftarRi['bedNo'] = $updatedDataDaftarRi->bed_no;
            $dataDaftarRi['klaimId'] = $updatedDataDaftarRi->klaim_id;
            $dataDaftarRi['klaimDesc'] = $updatedDataDaftarRi->klaim_desc;
            $dataDaftarRi['entryDate'] = $updatedDataDaftarRi->entry_date;
            $dataDaftarRi['exitDate'] = $updatedDataDaftarRi->exit_date ?? '';
            $dataDaftarRi['sep']['noSep'] = $updatedDataDaftarRi->vno_sep ?? '';



            return $dataDaftarRi;
        } catch (Exception $e) {
            // dd($e->getMessage());
            return ["errorMessages" => $e->getMessage()];
        }
    }

    protected function checkRIStatus($riHdrNo): bool
    {
        $lastInserted = DB::table('rstxn_rihdrs')
            ->select('ri_status')
            ->where('rihdr_no', '=', $riHdrNo)
            ->first();

        //Inap Pulang
        if ($lastInserted->ri_status !== 'I') {
            return true;
        }
        return false;
    }

    public static function updateJsonRI($riHdrNo, array $riArr): void
    {
        if (intval($riHdrNo) !== intval($riArr['riHdrNo'])) {
            dd('Data Json Tidak sesuai ' . $riHdrNo . '  /  ' . $riArr['riHdrNo']);
        }

        DB::table('rstxn_rihdrs')
            ->where('rihdr_no', $riHdrNo)
            ->update([
                'datadaftarri_json' => json_encode($riArr, true),
                // 'datadaftarri_xml' => ArrayToXml::convert($riArr),
            ]);
    }
}
