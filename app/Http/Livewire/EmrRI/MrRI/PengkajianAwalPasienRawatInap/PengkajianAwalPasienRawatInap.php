<?php

namespace App\Http\Livewire\EmrRI\MrRI\PengkajianAwalPasienRawatInap;

use Livewire\Component;
use Livewire\WithPagination;

use App\Http\Traits\EmrRI\EmrRITrait;
use App\Http\Traits\customErrorMessagesTrait;
use App\Http\Traits\LOV\LOVDokter\LOVDokterTrait;
use Carbon\Carbon;

use Exception;

class PengkajianAwalPasienRawatInap extends Component
{
    use WithPagination, EmrRITrait, customErrorMessagesTrait, LOVDokterTrait;

    // listener from blade////////////////
    protected $listeners = [
        'syncronizeAssessmentPerawatRIFindData' => 'mount'
    ];



    //////////////////////////////
    // Ref on top bar
    //////////////////////////////



    // dataDaftarRi RJ
    public $riHdrNoRef;

    public array $dataDaftarRi = [];

    public array $pengkajianAwalPasienRawatInap = [
        "bagian1DataUmum" => [
            "kondisiSaatMasuk" => "",
            "asalPasien" => [
                "pilihan" => "",
                "keterangan" => "" // Tambahan untuk opsi "lainnya"
            ],
            "diagnosaMasuk" => "",
            "dpjp" => "",
            "barangBerharga" => [
                "pilihan" => "",
                "catatan" => ""
            ],
            "alatBantu" => [
                "pilihan" => "",
                "keterangan" => "", // Tambahan untuk opsi "lainnya"
                "catatan" => ""
            ]
        ],
        "bagian2RiwayatPasien" => [
            "riwayatPenyakitOperasiCedera" => [
                "pilihan" => "",
                "keterangan" => "", // Tambahan untuk opsi "lainnya"
                "deskripsi" => ""
            ],
            "kebiasaan" => [
                "merokok" => [
                    "pilihan" => "",
                    "detail" => [
                        "jenis" => "",
                        "jumlahPerHari" => ""
                    ]
                ],
                "alkoholObat" => [
                    "pilihan" => "",
                    "detail" => [
                        "jenis" => "",
                        "jumlahPerHari" => ""
                    ]
                ]
            ],
            "vaksinasi" => [
                "influenza" => [
                    "pilihan" => ""
                ],
                "pneumonia" => [
                    "pilihan" => ""
                ]
            ],
            "riwayatKeluarga" => [
                "pilihan" => "",
                "keterangan" => "" // Tambahan untuk opsi "lainnya"
            ]
        ],
        "bagian3PsikososialDanEkonomi" => [
            "agamaKepercayaan" => [
                "pilihan" => "",
                "keterangan" => "" // Tambahan untuk opsi "lainnya"
            ],
            "statusPernikahan" => [
                "pilihan" => ""
            ],
            "tempatTinggal" => [
                "pilihan" => "",
                "keterangan" => "" // Tambahan untuk opsi "lainnya"
            ],
            "aktivitas" => [
                "pilihan" => ""
            ],
            "statusEmosional" => [
                "pilihan" => "",
                "keterangan" => "" // Tambahan untuk opsi "lainnya"
            ],
            "keluargaDekat" => [
                "nama" => "",
                "hubungan" => "",
                "telp" => ""
            ],
            "informasiDidapatDari" => [
                "pilihan" => "",
                "keterangan" => "" // Tambahan untuk opsi "lainnya"
            ]
        ],
        "bagian4PemeriksaanFisik" => [
            "tandaVital" => [
                "sistolik" => "", //number
                "distolik" => "", //number
                "frekuensiNafas" => "", //number
                "frekuensiNadi" => "", //number
                "suhu" => "", //number
                "spo2" => "", //number
                "gda" => "", //number
                "tb" => "",
                "bb" => "",
            ],
            "keluhanUtama" => "",
            "pemeriksaanSistemOrgan" => [
                "mataTelingaHidungTenggorokan" => [
                    "pilihan" => "",
                    "keterangan" => "" // Tambahan untuk opsi "lainnya"
                ],
                "paru" => [
                    "pilihan" => "",
                    "keterangan" => "" // Tambahan untuk opsi "lainnya"
                ],
                "jantung" => [
                    "pilihan" => "",
                    "keterangan" => "" // Tambahan untuk opsi "lainnya"
                ],
                "neurologi" => [
                    "tingkatKesadaran" => [
                        "pilihan" => ""
                    ],
                    "gcs" => ""
                ],
                "gastrointestinal" => [
                    "pilihan" => "",
                    "keterangan" => "" // Tambahan untuk opsi "lainnya"
                ],
                "genitourinaria" => [
                    "pilihan" => "",
                    "keterangan" => "" // Tambahan untuk opsi "lainnya"
                ],
                "muskuloskeletalDanKulit" => [
                    "pilihan" => "",
                    "keterangan" => "" // Tambahan untuk opsi "lainnya"
                ]
            ]
        ],
        "bagian5CatatanDanTandaTangan" => [
            "catatanUmum" => "",
            "petugasPengkaji" => "", //
            "petugasPengkajiCode" => "", //
            "jamPengkaji" => "" //
        ]
    ];

    // LOV Nested
    public array $dokter;
    // LOV Nested

    // Opsi untuk radio button
    public $options = [
        "kondisiSaatMasukOptions" => ["mandiri", "dibantu", "tirahBaring"],
        "asalPasienOptions" => ["poliklinik", "igd", "kamarOperasi", "lainnya"],
        "barangBerhargaOptions" => ["ada", "tidakAda"],
        "alatBantuOptions" => ["kacamata", "gigiPalsu", "alatBantuDengar", "lainnya"],
        "riwayatPenyakitOptions" => ["hipertensi", "diabetes", "asma", "stroke", "penyakitJantung", "lainnya"],
        "merokokOptions" => ["ya", "tidak", "berhenti"],
        "alkoholObatOptions" => ["ya", "tidak", "berhenti"],
        "vaksinasiOptions" => ["ya", "tidak", "menolak"],
        "riwayatKeluargaOptions" => ["penyakitJantung", "hipertensi", "diabetes", "stroke", "lainnya"],
        "agamaKepercayaanOptions" => ["islam", "kristen", "hindu", "budha", "lainnya"],
        "statusPernikahanOptions" => ["menikah", "belumMenikah", "dudaJanda"],
        "tempatTinggalOptions" => ["rumah", "panti", "lainnya"],
        "aktivitasOptions" => ["mandiri", "dibantu", "tirahBaring"],
        "statusEmosionalOptions" => ["kooperatif", "cemas", "depresi", "lainnya"],
        "informasiDidapatDariOptions" => ["pasien", "keluarga", "lainnya"],
        "mataTelingaHidungTenggorokanOptions" => ["normal", "gangguanVisus", "tuli", "lainnya"],
        "paruOptions" => ["normal", "ronki", "wheezing", "lainnya"],
        "jantungOptions" => ["normal", "takikardi", "bradikardi", "lainnya"],
        "tingkatKesadaranOptions" => ["komposMentis", "apatis", "somnolen", "sopor", "koma", "delirium"],
        "gastrointestinalOptions" => ["normal", "distensi", "diare", "konstipasi", "lainnya"],
        "genitourinariaOptions" => ["normal", "hematuria", "inkontinensia", "lainnya"],
        "muskuloskeletalDanKulitOptions" => ["normal", "deformitas", "luka", "lainnya"]
    ];
    //////////////////////////////////////////////////////////////////////


    protected $rules = [
        // Bagian 1: Data Umum
        'dataDaftarRi.pengkajianAwalPasienRawatInap.bagian1DataUmum.kondisiSaatMasuk' => 'nullable|string|in:mandiri,dibantu,tirahBaring',
        'dataDaftarRi.pengkajianAwalPasienRawatInap.bagian1DataUmum.asalPasien.pilihan' => 'nullable|string|in:poliklinik,gd,kamarOperasi,lainnya',
        'dataDaftarRi.pengkajianAwalPasienRawatInap.bagian1DataUmum.asalPasien.keterangan' => 'required_if:dataDaftarRi.pengkajianAwalPasienRawatInap.bagian1DataUmum.asalPasien.pilihan,lainnya|nullable|string',
        'dataDaftarRi.pengkajianAwalPasienRawatInap.bagian1DataUmum.diagnosaMasuk' => 'nullable|string',
        'dataDaftarRi.pengkajianAwalPasienRawatInap.bagian1DataUmum.dpjp' => 'nullable|string',
        'dataDaftarRi.pengkajianAwalPasienRawatInap.bagian1DataUmum.barangBerharga.pilihan' => 'nullable|string|in:ada,tidakAda',
        'dataDaftarRi.pengkajianAwalPasienRawatInap.bagian1DataUmum.barangBerharga.catatan' => 'nullable|string',
        'dataDaftarRi.pengkajianAwalPasienRawatInap.bagian1DataUmum.alatBantu.pilihan' => 'nullable|string|in:kacamata,gigiPalsu,alatBantuDengar,lainnya',
        'dataDaftarRi.pengkajianAwalPasienRawatInap.bagian1DataUmum.alatBantu.keterangan' => 'required_if:dataDaftarRi.pengkajianAwalPasienRawatInap.bagian1DataUmum.alatBantu.pilihan,lainnya|nullable|string',
        'dataDaftarRi.pengkajianAwalPasienRawatInap.bagian1DataUmum.alatBantu.catatan' => 'nullable|string',

        // Bagian 2: Riwayat Pasien
        'dataDaftarRi.pengkajianAwalPasienRawatInap.bagian2RiwayatPasien.riwayatPenyakitOperasiCedera.pilihan' => 'nullable|string|in:hipertensi,diabetes,asma,stroke,penyakitJantung,lainnya',
        'dataDaftarRi.pengkajianAwalPasienRawatInap.bagian2RiwayatPasien.riwayatPenyakitOperasiCedera.keterangan' => 'required_if:dataDaftarRi.pengkajianAwalPasienRawatInap.bagian2RiwayatPasien.riwayatPenyakitOperasiCedera.pilihan,lainnya|nullable|string',
        'dataDaftarRi.pengkajianAwalPasienRawatInap.bagian2RiwayatPasien.riwayatPenyakitOperasiCedera.deskripsi' => 'nullable|string',
        'dataDaftarRi.pengkajianAwalPasienRawatInap.bagian2RiwayatPasien.kebiasaan.merokok.pilihan' => 'nullable|string|in:ya,tidak,berhenti',
        'dataDaftarRi.pengkajianAwalPasienRawatInap.bagian2RiwayatPasien.kebiasaan.merokok.detail.jenis' => 'nullable|string',
        'dataDaftarRi.pengkajianAwalPasienRawatInap.bagian2RiwayatPasien.kebiasaan.merokok.detail.jumlahPerHari' => 'nullable|numeric',
        'dataDaftarRi.pengkajianAwalPasienRawatInap.bagian2RiwayatPasien.kebiasaan.alkoholObat.pilihan' => 'nullable|string|in:ya,tidak,berhenti',
        'dataDaftarRi.pengkajianAwalPasienRawatInap.bagian2RiwayatPasien.kebiasaan.alkoholObat.detail.jenis' => 'nullable|string',
        'dataDaftarRi.pengkajianAwalPasienRawatInap.bagian2RiwayatPasien.kebiasaan.alkoholObat.detail.jumlahPerHari' => 'nullable|numeric',
        'dataDaftarRi.pengkajianAwalPasienRawatInap.bagian2RiwayatPasien.vaksinasi.influenza.pilihan' => 'nullable|string|in:ya,tidak,menolak',
        'dataDaftarRi.pengkajianAwalPasienRawatInap.bagian2RiwayatPasien.vaksinasi.pneumonia.pilihan' => 'nullable|string|in:ya,tidak,menolak',
        'dataDaftarRi.pengkajianAwalPasienRawatInap.bagian2RiwayatPasien.riwayatKeluarga.pilihan' => 'nullable|string|in:penyakitJantung,hipertensi,diabetes,stroke,lainnya',
        'dataDaftarRi.pengkajianAwalPasienRawatInap.bagian2RiwayatPasien.riwayatKeluarga.keterangan' => 'required_if:dataDaftarRi.pengkajianAwalPasienRawatInap.bagian2RiwayatPasien.riwayatKeluarga.pilihan,lainnya|nullable|string',

        // Bagian 3: Psikososial dan Ekonomi
        'dataDaftarRi.pengkajianAwalPasienRawatInap.bagian3PsikososialDanEkonomi.agamaKepercayaan.pilihan' => 'nullable|string|in:islam,kristen,hindu,budha,lainnya',
        'dataDaftarRi.pengkajianAwalPasienRawatInap.bagian3PsikososialDanEkonomi.agamaKepercayaan.keterangan' => 'required_if:dataDaftarRi.pengkajianAwalPasienRawatInap.bagian3PsikososialDanEkonomi.agamaKepercayaan.pilihan,lainnya|nullable|string',
        'dataDaftarRi.pengkajianAwalPasienRawatInap.bagian3PsikososialDanEkonomi.statusPernikahan.pilihan' => 'nullable|string|in:menikah,belumMenikah,dudaJanda',
        'dataDaftarRi.pengkajianAwalPasienRawatInap.bagian3PsikososialDanEkonomi.tempatTinggal.pilihan' => 'nullable|string|in:rumah,panti,lainnya',
        'dataDaftarRi.pengkajianAwalPasienRawatInap.bagian3PsikososialDanEkonomi.tempatTinggal.keterangan' => 'required_if:dataDaftarRi.pengkajianAwalPasienRawatInap.bagian3PsikososialDanEkonomi.tempatTinggal.pilihan,lainnya|nullable|string',
        'dataDaftarRi.pengkajianAwalPasienRawatInap.bagian3PsikososialDanEkonomi.aktivitas.pilihan' => 'nullable|string|in:mandiri,dibantu,tirahBaring',
        'dataDaftarRi.pengkajianAwalPasienRawatInap.bagian3PsikososialDanEkonomi.statusEmosional.pilihan' => 'nullable|string|in:kooperatif,cemas,depresi,lainnya',
        'dataDaftarRi.pengkajianAwalPasienRawatInap.bagian3PsikososialDanEkonomi.statusEmosional.keterangan' => 'required_if:dataDaftarRi.pengkajianAwalPasienRawatInap.bagian3PsikososialDanEkonomi.statusEmosional.pilihan,lainnya|nullable|string',
        'dataDaftarRi.pengkajianAwalPasienRawatInap.bagian3PsikososialDanEkonomi.keluargaDekat.nama' => 'nullable|string',
        'dataDaftarRi.pengkajianAwalPasienRawatInap.bagian3PsikososialDanEkonomi.keluargaDekat.hubungan' => 'nullable|string',
        'dataDaftarRi.pengkajianAwalPasienRawatInap.bagian3PsikososialDanEkonomi.keluargaDekat.telp' => 'nullable|string',
        'dataDaftarRi.pengkajianAwalPasienRawatInap.bagian3PsikososialDanEkonomi.informasiDidapatDari.pilihan' => 'nullable|string|in:pasien,keluarga,lainnya',
        'dataDaftarRi.pengkajianAwalPasienRawatInap.bagian3PsikososialDanEkonomi.informasiDidapatDari.keterangan' => 'required_if:dataDaftarRi.pengkajianAwalPasienRawatInap.bagian3PsikososialDanEkonomi.informasiDidapatDari.pilihan,lainnya|nullable|string',

        // Bagian 4: Pemeriksaan Fisik
        'dataDaftarRi.pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.tandaVital.sistolik' => 'nullable|string',
        'dataDaftarRi.pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.tandaVital.distolik' => 'nullable|string',

        'dataDaftarRi.pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.tandaVital.frekuensiNadi' => 'nullable|string',
        'dataDaftarRi.pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.tandaVital.frekuensiNafas' => 'nullable|string',
        'dataDaftarRi.pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.tandaVital.suhu' => 'nullable|string',
        'dataDaftarRi.pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.tandaVital.spo2' => 'nullable|string',
        'dataDaftarRi.pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.tandaVital.tb' => 'nullable|string',
        'dataDaftarRi.pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.tandaVital.bb' => 'nullable|string',
        'dataDaftarRi.pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.keluhanUtama' => 'nullable|string',
        'dataDaftarRi.pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.pemeriksaanSistemOrgan.mataTelingaHidungTenggorokan.pilihan' => 'nullable|string|in:normal,gangguanVisus,tuli,lainnya',
        'dataDaftarRi.pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.pemeriksaanSistemOrgan.mataTelingaHidungTenggorokan.keterangan' => 'required_if:dataDaftarRi.pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.pemeriksaanSistemOrgan.mataTelingaHidungTenggorokan.pilihan,lainnya|nullable|string',
        'dataDaftarRi.pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.pemeriksaanSistemOrgan.paru.pilihan' => 'nullable|string|in:normal,ronki,wheezing,lainnya',
        'dataDaftarRi.pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.pemeriksaanSistemOrgan.paru.keterangan' => 'required_if:dataDaftarRi.pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.pemeriksaanSistemOrgan.paru.pilihan,lainnya|nullable|string',
        'dataDaftarRi.pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.pemeriksaanSistemOrgan.jantung.pilihan' => 'nullable|string|in:normal,takikardi,bradikardi,lainnya',
        'dataDaftarRi.pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.pemeriksaanSistemOrgan.jantung.keterangan' => 'required_if:dataDaftarRi.pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.pemeriksaanSistemOrgan.jantung.pilihan,lainnya|nullable|string',
        'dataDaftarRi.pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.pemeriksaanSistemOrgan.neurologi.tingkatKesadaran.pilihan' => 'nullable|string|in:komposMentis,apatis,somnolen,sopor,koma,delirium',
        'dataDaftarRi.pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.pemeriksaanSistemOrgan.neurologi.gcs' => 'nullable|string',
        'dataDaftarRi.pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.pemeriksaanSistemOrgan.gastrointestinal.pilihan' => 'nullable|string|in:normal,distensi,diare,konstipasi,lainnya',
        'dataDaftarRi.pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.pemeriksaanSistemOrgan.gastrointestinal.keterangan' => 'required_if:dataDaftarRi.pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.pemeriksaanSistemOrgan.gastrointestinal.pilihan,lainnya|nullable|string',
        'dataDaftarRi.pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.pemeriksaanSistemOrgan.genitourinaria.pilihan' => 'nullable|string|in:normal,hematuria,inkontinensia,lainnya',
        'dataDaftarRi.pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.pemeriksaanSistemOrgan.genitourinaria.keterangan' => 'required_if:dataDaftarRi.pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.pemeriksaanSistemOrgan.genitourinaria.pilihan,lainnya|nullable|string',
        'dataDaftarRi.pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.pemeriksaanSistemOrgan.muskuloskeletalDanKulit.pilihan' => 'nullable|string|in:normal,deformitas,luka,lainnya',
        'dataDaftarRi.pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.pemeriksaanSistemOrgan.muskuloskeletalDanKulit.keterangan' => 'required_if:dataDaftarRi.pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.pemeriksaanSistemOrgan.muskuloskeletalDanKulit.pilihan,lainnya|nullable|string',

        // Bagian 5: Catatan dan Tanda Tangan
        'dataDaftarRi.pengkajianAwalPasienRawatInap.bagian5CatatanDanTandaTangan.catatanUmum' => 'nullable|string',
        'dataDaftarRi.pengkajianAwalPasienRawatInap.bagian5CatatanDanTandaTangan.petugasPengkaji' => 'nullable|string',
        'dataDaftarRi.pengkajianAwalPasienRawatInap.bagian5CatatanDanTandaTangan.jamPengkaji' => 'nullable|date_format:d/m/Y H:i:s',
    ];


    protected $messages = [
        // Bagian 1: Data Umum
        'dataDaftarRi.pengkajianAwalPasienRawatInap.bagian1DataUmum.kondisiSaatMasuk.required' => 'Kondisi saat masuk harus diisi.',
        'dataDaftarRi.pengkajianAwalPasienRawatInap.bagian1DataUmum.kondisiSaatMasuk.in' => 'Kondisi saat masuk harus berupa mandiri, dibantu, atau tirahBaring.',
        'dataDaftarRi.pengkajianAwalPasienRawatInap.bagian1DataUmum.asalPasien.pilihan.required' => 'Asal pasien harus diisi.',
        'dataDaftarRi.pengkajianAwalPasienRawatInap.bagian1DataUmum.asalPasien.pilihan.in' => 'Asal pasien harus berupa poliklinik, gd, kamarOperasi, atau lainnya.',
        'dataDaftarRi.pengkajianAwalPasienRawatInap.bagian1DataUmum.asalPasien.keterangan.required_if' => 'Keterangan asal pasien harus diisi jika pilihan adalah lainnya.',
        'dataDaftarRi.pengkajianAwalPasienRawatInap.bagian1DataUmum.diagnosaMasuk.required' => 'Diagnosa masuk harus diisi.',
        'dataDaftarRi.pengkajianAwalPasienRawatInap.bagian1DataUmum.dpjp.required' => 'DPJP harus diisi.',
        'dataDaftarRi.pengkajianAwalPasienRawatInap.bagian1DataUmum.barangBerharga.pilihan.required' => 'Pilihan barang berharga harus diisi.',
        'dataDaftarRi.pengkajianAwalPasienRawatInap.bagian1DataUmum.barangBerharga.pilihan.in' => 'Pilihan barang berharga harus berupa ada atau tidakAda.',
        'dataDaftarRi.pengkajianAwalPasienRawatInap.bagian1DataUmum.alatBantu.pilihan.in' => 'Pilihan alat bantu harus berupa kacamata, gigiPalsu, alatBantuDengar, atau lainnya.',
        'dataDaftarRi.pengkajianAwalPasienRawatInap.bagian1DataUmum.alatBantu.keterangan.required_if' => 'Keterangan alat bantu harus diisi jika pilihan adalah lainnya.',

        // Bagian 2: Riwayat Pasien
        'dataDaftarRi.pengkajianAwalPasienRawatInap.bagian2RiwayatPasien.riwayatPenyakitOperasiCedera.pilihan.in' => 'Pilihan riwayat penyakit/operasi/cedera tidak valid.',
        'dataDaftarRi.pengkajianAwalPasienRawatInap.bagian2RiwayatPasien.riwayatPenyakitOperasiCedera.keterangan.required_if' => 'Keterangan riwayat penyakit/operasi/cedera harus diisi jika pilihan adalah lainnya.',
        'dataDaftarRi.pengkajianAwalPasienRawatInap.bagian2RiwayatPasien.kebiasaan.merokok.pilihan.required' => 'Pilihan kebiasaan merokok harus diisi.',
        'dataDaftarRi.pengkajianAwalPasienRawatInap.bagian2RiwayatPasien.kebiasaan.merokok.pilihan.in' => 'Pilihan kebiasaan merokok harus berupa ya, tidak, atau berhenti.',
        'dataDaftarRi.pengkajianAwalPasienRawatInap.bagian2RiwayatPasien.kebiasaan.merokok.detail.jumlahPerHari.numeric' => 'Jumlah rokok per hari harus berupa angka.',
        'dataDaftarRi.pengkajianAwalPasienRawatInap.bagian2RiwayatPasien.kebiasaan.alkoholObat.pilihan.required' => 'Pilihan kebiasaan alkohol/obat harus diisi.',
        'dataDaftarRi.pengkajianAwalPasienRawatInap.bagian2RiwayatPasien.kebiasaan.alkoholObat.pilihan.in' => 'Pilihan kebiasaan alkohol/obat harus berupa ya, tidak, atau berhenti.',
        'dataDaftarRi.pengkajianAwalPasienRawatInap.bagian2RiwayatPasien.kebiasaan.alkoholObat.detail.jumlahPerHari.numeric' => 'Jumlah alkohol/obat per hari harus berupa angka.',
        'dataDaftarRi.pengkajianAwalPasienRawatInap.bagian2RiwayatPasien.vaksinasi.influenza.pilihan.in' => 'Pilihan vaksinasi influenza tidak valid.',
        'dataDaftarRi.pengkajianAwalPasienRawatInap.bagian2RiwayatPasien.vaksinasi.pneumonia.pilihan.in' => 'Pilihan vaksinasi pneumonia tidak valid.',
        'dataDaftarRi.pengkajianAwalPasienRawatInap.bagian2RiwayatPasien.riwayatKeluarga.pilihan.in' => 'Pilihan riwayat keluarga tidak valid.',
        'dataDaftarRi.pengkajianAwalPasienRawatInap.bagian2RiwayatPasien.riwayatKeluarga.keterangan.required_if' => 'Keterangan riwayat keluarga harus diisi jika pilihan adalah lainnya.',

        // Bagian 3: Psikososial dan Ekonomi
        'dataDaftarRi.pengkajianAwalPasienRawatInap.bagian3PsikososialDanEkonomi.agamaKepercayaan.pilihan.required' => 'Pilihan agama/kepercayaan harus diisi.',
        'dataDaftarRi.pengkajianAwalPasienRawatInap.bagian3PsikososialDanEkonomi.agamaKepercayaan.pilihan.in' => 'Pilihan agama/kepercayaan tidak valid.',
        'dataDaftarRi.pengkajianAwalPasienRawatInap.bagian3PsikososialDanEkonomi.agamaKepercayaan.keterangan.required_if' => 'Keterangan agama/kepercayaan harus diisi jika pilihan adalah lainnya.',
        'dataDaftarRi.pengkajianAwalPasienRawatInap.bagian3PsikososialDanEkonomi.statusPernikahan.pilihan.required' => 'Status pernikahan harus diisi.',
        'dataDaftarRi.pengkajianAwalPasienRawatInap.bagian3PsikososialDanEkonomi.statusPernikahan.pilihan.in' => 'Status pernikahan tidak valid.',
        'dataDaftarRi.pengkajianAwalPasienRawatInap.bagian3PsikososialDanEkonomi.tempatTinggal.pilihan.in' => 'Pilihan tempat tinggal tidak valid.',
        'dataDaftarRi.pengkajianAwalPasienRawatInap.bagian3PsikososialDanEkonomi.tempatTinggal.keterangan.required_if' => 'Keterangan tempat tinggal harus diisi jika pilihan adalah lainnya.',
        'dataDaftarRi.pengkajianAwalPasienRawatInap.bagian3PsikososialDanEkonomi.aktivitas.pilihan.required' => 'Pilihan aktivitas harus diisi.',
        'dataDaftarRi.pengkajianAwalPasienRawatInap.bagian3PsikososialDanEkonomi.aktivitas.pilihan.in' => 'Pilihan aktivitas tidak valid.',
        'dataDaftarRi.pengkajianAwalPasienRawatInap.bagian3PsikososialDanEkonomi.statusEmosional.pilihan.in' => 'Pilihan status emosional tidak valid.',
        'dataDaftarRi.pengkajianAwalPasienRawatInap.bagian3PsikososialDanEkonomi.statusEmosional.keterangan.required_if' => 'Keterangan status emosional harus diisi jika pilihan adalah lainnya.',
        'dataDaftarRi.pengkajianAwalPasienRawatInap.bagian3PsikososialDanEkonomi.keluargaDekat.nama.required' => 'Nama keluarga dekat harus diisi.',
        'dataDaftarRi.pengkajianAwalPasienRawatInap.bagian3PsikososialDanEkonomi.keluargaDekat.hubungan.required' => 'Hubungan keluarga dekat harus diisi.',
        'dataDaftarRi.pengkajianAwalPasienRawatInap.bagian3PsikososialDanEkonomi.keluargaDekat.telp.required' => 'Nomor telepon keluarga dekat harus diisi.',
        'dataDaftarRi.pengkajianAwalPasienRawatInap.bagian3PsikososialDanEkonomi.informasiDidapatDari.pilihan.required' => 'Pilihan sumber informasi harus diisi.',
        'dataDaftarRi.pengkajianAwalPasienRawatInap.bagian3PsikososialDanEkonomi.informasiDidapatDari.pilihan.in' => 'Pilihan sumber informasi tidak valid.',
        'dataDaftarRi.pengkajianAwalPasienRawatInap.bagian3PsikososialDanEkonomi.informasiDidapatDari.keterangan.required_if' => 'Keterangan sumber informasi harus diisi jika pilihan adalah lainnya.',

        // Bagian 4: Pemeriksaan Fisik
        'dataDaftarRi.pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.tandaVital.sistolik.required' => 'Tekanan darah sistolik harus diisi.',
        'dataDaftarRi.pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.tandaVital.distolik.required' => 'Tekanan darah diastolik harus diisi.',
        'dataDaftarRi.pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.tandaVital.frekuensiNadi.required' => 'Frekuensi nadi harus diisi.',
        'dataDaftarRi.pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.tandaVital.frekuensiNafas.required' => 'Frekuensi nafas harus diisi.',
        'dataDaftarRi.pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.tandaVital.suhu.required' => 'Suhu tubuh harus diisi.',
        'dataDaftarRi.pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.tandaVital.spo2.required' => 'Saturasi oksigen (SpO2) harus diisi.',
        'dataDaftarRi.pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.tandaVital.tb.required' => 'Tinggi badan harus diisi.',
        'dataDaftarRi.pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.tandaVital.bb.required' => 'Berat badan harus diisi.',
        'dataDaftarRi.pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.keluhanUtama.required' => 'Keluhan utama harus diisi.',
        'dataDaftarRi.pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.pemeriksaanSistemOrgan.mataTelingaHidungTenggorokan.pilihan.in' => 'Pilihan pemeriksaan mata, telinga, hidung, dan tenggorokan tidak valid.',
        'dataDaftarRi.pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.pemeriksaanSistemOrgan.mataTelingaHidungTenggorokan.keterangan.required_if' => 'Keterangan pemeriksaan mata, telinga, hidung, dan tenggorokan harus diisi jika pilihan adalah lainnya.',
        'dataDaftarRi.pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.pemeriksaanSistemOrgan.paru.pilihan.in' => 'Pilihan pemeriksaan paru tidak valid.',
        'dataDaftarRi.pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.pemeriksaanSistemOrgan.paru.keterangan.required_if' => 'Keterangan pemeriksaan paru harus diisi jika pilihan adalah lainnya.',
        'dataDaftarRi.pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.pemeriksaanSistemOrgan.jantung.pilihan.in' => 'Pilihan pemeriksaan jantung tidak valid.',
        'dataDaftarRi.pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.pemeriksaanSistemOrgan.jantung.keterangan.required_if' => 'Keterangan pemeriksaan jantung harus diisi jika pilihan adalah lainnya.',
        'dataDaftarRi.pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.pemeriksaanSistemOrgan.neurologi.tingkatKesadaran.pilihan.in' => 'Pilihan tingkat kesadaran tidak valid.',
        'dataDaftarRi.pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.pemeriksaanSistemOrgan.neurologi.gcs.required' => 'Nilai GCS harus diisi.',
        'dataDaftarRi.pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.pemeriksaanSistemOrgan.gastrointestinal.pilihan.in' => 'Pilihan pemeriksaan gastrointestinal tidak valid.',
        'dataDaftarRi.pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.pemeriksaanSistemOrgan.gastrointestinal.keterangan.required_if' => 'Keterangan pemeriksaan gastrointestinal harus diisi jika pilihan adalah lainnya.',
        'dataDaftarRi.pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.pemeriksaanSistemOrgan.genitourinaria.pilihan.in' => 'Pilihan pemeriksaan genitourinaria tidak valid.',
        'dataDaftarRi.pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.pemeriksaanSistemOrgan.genitourinaria.keterangan.required_if' => 'Keterangan pemeriksaan genitourinaria harus diisi jika pilihan adalah lainnya.',
        'dataDaftarRi.pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.pemeriksaanSistemOrgan.muskuloskeletalDanKulit.pilihan.in' => 'Pilihan pemeriksaan muskuloskeletal dan kulit tidak valid.',
        'dataDaftarRi.pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.pemeriksaanSistemOrgan.muskuloskeletalDanKulit.keterangan.required_if' => 'Keterangan pemeriksaan muskuloskeletal dan kulit harus diisi jika pilihan adalah lainnya.',

        // Bagian 5: Catatan dan Tanda Tangan
        'dataDaftarRi.pengkajianAwalPasienRawatInap.bagian5CatatanDanTandaTangan.petugasPengkaji.required' => 'Nama petugas pengkaji harus diisi.',
        'dataDaftarRi.pengkajianAwalPasienRawatInap.bagian5CatatanDanTandaTangan.jamPengkaji.required' => 'Jam pengkajian harus diisi.',
        'dataDaftarRi.pengkajianAwalPasienRawatInap.bagian5CatatanDanTandaTangan.jamPengkaji.date_format' => 'Format jam pengkajian harus sesuai dengan d/m/Y H:i:s.',
    ];




    ////////////////////////////////////////////////
    ///////////begin////////////////////////////////
    ////////////////////////////////////////////////
    public function updated($propertyName)
    {
        // dd($propertyName);
        $this->validateOnly($propertyName);
        $this->store();
    }





    // ////////////////
    // RJ Logic
    // ////////////////


    // validate Data RJ//////////////////////////////////////////////////
    private function validatePengkajianAwalPasienRawatInap(): void
    {


        // Proses Validasi///////////////////////////////////////////
        try {
            $this->validate($this->rules, $this->messages);
        } catch (\Illuminate\Validation\ValidationException $e) {

            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError("Lakukan Pengecekan kembali Input Data.");
            $this->validate($this->rules,  $this->messages);
        }
    }


    // insert and update record start////////////////
    public function store()
    {
        // Validate RJ
        $this->validatePengkajianAwalPasienRawatInap();

        // Logic update mode start //////////
        $this->updateDataRi($this->dataDaftarRi['riHdrNo']);

        $this->emit('syncronizeAssessmentPerawatRIFindData');
    }

    private function updateDataRi($riHdrNo): void
    {
        $this->updateJsonRI($riHdrNo, $this->dataDaftarRi);

        toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addSuccess("Anamnesa berhasil disimpan.");
    }
    // insert and update record end////////////////


    private function findData($riHdrNo): void
    {

        $this->dataDaftarRi = $this->findDataRI($riHdrNo);
        // dd($this->dataDaftarRi);
        // jika pengkajianAwalPasienRawatInap tidak ditemukan tambah variable pengkajianAwalPasienRawatInap pda array
        if (isset($this->dataDaftarRi['pengkajianAwalPasienRawatInap']) == false) {
            $this->dataDaftarRi['pengkajianAwalPasienRawatInap'] = $this->pengkajianAwalPasienRawatInap;
        }

        // menyamakan Variabel keluhan utama
        $this->matchingMyVariable();
    }




    private function matchingMyVariable()
    {

        // keluhanUtama
        // $this->dataDaftarRi['pengkajianAwalPasienRawatInap']['keluhanUtama']['keluhanUtama'] =
        //     ($this->dataDaftarRi['pengkajianAwalPasienRawatInap']['keluhanUtama']['keluhanUtama'])
        //     ? $this->dataDaftarRi['pengkajianAwalPasienRawatInap']['keluhanUtama']['keluhanUtama']
        //     : ((isset($this->dataDaftarRi['screening']['keluhanUtama']) && !$this->dataDaftarRi['pengkajianAwalPasienRawatInap']['keluhanUtama']['keluhanUtama'])
        //         ? $this->dataDaftarRi['screening']['keluhanUtama']
        //         : "");
    }


    public function setJamPengkaji($jam)
    {
        $this->dataDaftarRi['pengkajianAwalPasienRawatInap']['bagian5CatatanDanTandaTangan']['jamPengkaji'] = $jam;
    }


    public function setPetugasPengkaji()
    {
        // Ambil data user yang sedang login
        $myUserCodeActive = auth()->user()->myuser_code;
        $myUserNameActive = auth()->user()->myuser_name;

        // Validasi apakah pengguna memiliki peran yang sesuai
        if (auth()->user()->hasRole('Perawat')) {
            // Isi data petugas pengkaji
            $this->dataDaftarRi['pengkajianAwalPasienRawatInap']['bagian5CatatanDanTandaTangan']['petugasPengkaji'] = $myUserNameActive;
            $this->dataDaftarRi['pengkajianAwalPasienRawatInap']['bagian5CatatanDanTandaTangan']['petugasPengkajiCode'] = $myUserCodeActive;

            // Simpan perubahan
            $this->store();

            // Notifikasi sukses
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addSuccess("TTD Petugas Pengkaji berhasil diisi oleh " . $myUserNameActive);
        } else {
            // Notifikasi error jika peran tidak sesuai
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError("Anda tidak dapat melakukan TTD karena User Role " . $myUserNameActive . ' bukan Petugas Pengkaji.');
            return;
        }
    }



    //////////////////////levelingDokter////////////////////////////
    public array $levelingDokter = [
        'drId' => '',
        'drName' => '',
        'poliId' => '',
        'poliDesc' => '',
        'tglEntry' => '',
        'levelDokter' => 'Utama', //Utama/RawatGabung
    ];


    public function addLevelingDokter()
    {
        $this->levelingDokter['tglEntry'] = Carbon::now(env('APP_TIMEZONE'))->format('d/m/Y H:i:s');
        // validasi
        $this->validateDataLevelingDokterRi();
        // check exist
        $cekdLevelingDokter = collect($this->dataDaftarRi['pengkajianAwalPasienRawatInap']['levelingDokter'] ?? [])
            ->where("tglEntry", '=', $this->levelingDokter['tglEntry'])
            ->count();
        if (!$cekdLevelingDokter) {
            $this->dataDaftarRi['pengkajianAwalPasienRawatInap']['levelingDokter'][] = [
                "drId" => $this->levelingDokter['drId'],
                "drName" => $this->levelingDokter['drName'],
                "poliId" => $this->levelingDokter['poliId'],
                "poliDesc" => $this->levelingDokter['poliDesc'],
                "tglEntry" => $this->levelingDokter['tglEntry'],
                "levelDokter" => $this->levelingDokter['levelDokter'],
            ];

            $this->dataDaftarRi['pengkajianAwalPasienRawatInap']['levelingDokter']['levelingDokterLog'] =
                [
                    'userLogDesc' => 'Form Entry levelingDokter ' . $this->levelingDokter['drName'] . $this->levelingDokter['levelDokter'],
                    'userLog' => auth()->user()->myuser_name,
                    'userLogDate' => Carbon::now(env('APP_TIMEZONE'))->format('d/m/Y H:i:s')
                ];

            $this->store();
            // reset levelingDokter
            $this->reset(['levelingDokter']);
        } else {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError("LevelingDokter Sudah ada.");
        }
    }

    public function removeLevelingDokter($tglEntry)
    {

        $levelingDokter = collect($this->dataDaftarRi['pengkajianAwalPasienRawatInap']['levelingDokter'])->where("tglEntry", '!=', $tglEntry)->toArray();
        $this->dataDaftarRi['pengkajianAwalPasienRawatInap']['levelingDokter'] = $levelingDokter;

        $this->dataDaftarRi['pengkajianAwalPasienRawatInap']['levelingDokter']['levelingDokterLog'] =
            [
                'userLogDesc' => 'Hapus levelingDokter',
                'userLog' => auth()->user()->myuser_name,
                'userLogDate' => Carbon::now(env('APP_TIMEZONE'))->format('d/m/Y H:i:s')
            ];
        $this->store();
    }


    public function setLevelingDokterUtama($index, $level = "Utama")
    {
        if ($this->dataDaftarRi['pengkajianAwalPasienRawatInap']['levelingDokter'][$index]['levelDokter'] === $level) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError("Status Dokter " . $level);
            return;
        }

        $this->dataDaftarRi['pengkajianAwalPasienRawatInap']['levelingDokter']['levelingDokterLog'] =
            [
                'userLogDesc' => 'Ubah levelingDokter' . $this->dataDaftarRi['pengkajianAwalPasienRawatInap']['levelingDokter'][$index]['drName'] . ' dari ' . $this->dataDaftarRi['pengkajianAwalPasienRawatInap']['levelingDokter'][$index]['levelDokter'] . 'ke' . $level,
                'userLog' => auth()->user()->myuser_name,
                'userLogDate' => Carbon::now(env('APP_TIMEZONE'))->format('d/m/Y H:i:s')
            ];

        $this->dataDaftarRi['pengkajianAwalPasienRawatInap']['levelingDokter'][$index]['levelDokter'] = $level;

        $this->store();
    }

    public function setLevelingDokterRawatGabung($index, $level = "RawatGabung")
    {

        if ($this->dataDaftarRi['pengkajianAwalPasienRawatInap']['levelingDokter'][$index]['levelDokter'] === $level) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError("Status Dokter " . $level);
            return;
        }

        $this->dataDaftarRi['pengkajianAwalPasienRawatInap']['levelingDokter']['levelingDokterLog'] =
            [
                'userLogDesc' => 'Ubah levelingDokter' . $this->dataDaftarRi['pengkajianAwalPasienRawatInap']['levelingDokter'][$index]['drName'] . ' dari ' . $this->dataDaftarRi['pengkajianAwalPasienRawatInap']['levelingDokter'][$index]['levelDokter'] . 'ke' . $level,
                'userLog' => auth()->user()->myuser_name,
                'userLogDate' => Carbon::now(env('APP_TIMEZONE'))->format('d/m/Y H:i:s')
            ];

        $this->dataDaftarRi['pengkajianAwalPasienRawatInap']['levelingDokter'][$index]['levelDokter'] = $level;

        $this->store();
    }
    public function resetlevelingDokter()
    {
        $this->reset([
            'levelingDokter',
            'collectingMyDokter', //Reset LOV / render  / empty NestLov

        ]);
        $this->resetValidation();
    }

    private function validateDataLevelingDokterRi(): void
    {
        $rules = [
            'levelingDokter.drId' => 'required|string|max:10',
            'levelingDokter.drName' => 'required|string|max:200',
            'levelingDokter.poliId' => 'required|string|max:10',
            'levelingDokter.poliDesc' => 'required|string|max:50',
            'levelingDokter.tglEntry' => 'required|date_format:d/m/Y H:i:s',
            'levelingDokter.levelDokter' => 'required|in:Utama,RawatGabung',
        ];
        $messages = [
            'levelingDokter.drId.required' => 'ID Dokter pada form Entry Leveling Dokter harus diisi.',
            'levelingDokter.drId.string' => 'ID Dokter pada form Entry Leveling Dokter harus berupa string.',
            'levelingDokter.drId.max' => 'ID Dokter pada form Entry Leveling Dokter maksimal 10 karakter.',

            'levelingDokter.drName.required' => 'Nama Dokter pada form Entry Leveling Dokter harus diisi.',
            'levelingDokter.drName.string' => 'Nama Dokter pada form Entry Leveling Dokter harus berupa string.',
            'levelingDokter.drName.max' => 'Nama Dokter pada form Entry Leveling Dokter maksimal 200 karakter.',

            'levelingDokter.poliId.required' => 'ID Poli pada form Entry Leveling Dokter harus diisi.',
            'levelingDokter.poliId.string' => 'ID Poli pada form Entry Leveling Dokter harus berupa string.',
            'levelingDokter.poliId.max' => 'ID Poli pada form Entry Leveling Dokter maksimal 10 karakter.',

            'levelingDokter.poliDesc.required' => 'Nama Poli pada form Entry Leveling Dokter harus diisi.',
            'levelingDokter.poliDesc.string' => 'Nama Poli pada form Entry Leveling Dokter harus berupa string.',
            'levelingDokter.poliDesc.max' => 'Nama Poli pada form Entry Leveling Dokter maksimal 50 karakter.',

            'levelingDokter.tglEntry.required' => 'Tanggal Entry pada form Entry Leveling Dokter harus diisi.',
            'levelingDokter.tglEntry.date_format' => 'Format Tanggal Entry pada form Entry Leveling Dokter tidak valid. Gunakan format: d/m/Y H:i:s.',

            'levelingDokter.levelDokter.required' => 'Level Dokter pada form Entry Leveling Dokter harus diisi.',
            'levelingDokter.levelDokter.in' => 'Level Dokter pada form Entry Leveling Dokter hanya boleh "Utama" atau "RawatGabung".',
        ];

        // Proses Validasi///////////////////////////////////////////
        try {
            $this->validate($rules, $messages);
        } catch (\Illuminate\Validation\ValidationException $e) {

            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError("Lakukan Pengecekan kembali Input Data." . $e->getMessage());
            $this->validate($rules, $messages);
        }
    }

    //////////////////////////////////////////////////////////////

    // when new form instance
    public function mount()
    {

        $this->findData($this->riHdrNoRef);
    }

    private function syncLOV(): void
    {
        $this->dokter = $this->collectingMyDokter;
    }

    private function syncDataFormEntry(): void
    {
        $this->levelingDokter['drId'] = $this->dokter['DokterId'] ?? '';
        $this->levelingDokter['drName'] = $this->dokter['DokterDesc'] ?? '';
        $this->levelingDokter['poliId'] = $this->dokter['PoliId'] ?? '';
        $this->levelingDokter['poliDesc'] = $this->dokter['PoliDesc'] ?? '';
        $this->levelingDokter['kdpolibpjs'] =  $this->dokter['kdPoliBpjs'] ?? '';
        $this->levelingDokter['kddrbpjs'] =  $this->dokter['kdDokterBpjs'] ?? '';
    }

    // select data start////////////////
    public function render()
    {

        // LOV
        $this->syncLOV();
        // FormEntry
        $this->syncDataFormEntry();
        return view(
            'livewire.emr-r-i.mr-r-i.pengkajian-awal-pasien-rawat-inap.pengkajian-awal-pasien-rawat-inap',
            [
                // 'RJpasiens' => $query->paginate($this->limitPerPage),
                'myTitle' => 'Pengkajian Awal Pasien Rawat Inap',
                'mySnipt' => 'Rekam Medis Pasien',
                'myProgram' => 'Pasien Rawat Inap',
            ]
        );
    }
    // select data end////////////////


}
