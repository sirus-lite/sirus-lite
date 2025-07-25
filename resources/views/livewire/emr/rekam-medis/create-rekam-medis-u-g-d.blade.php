<div class="fixed inset-0 z-40">

    <div class="">

        <!-- This element is to trick the browser into transition-opacity. -->
        <div class="fixed inset-0 transition-opacity">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>

        <!-- This element is to trick the browser into transition-opacity. Body-->
        <div class="fixed inset-0 transition-opacity">
            <div class="absolute overflow-auto bg-white rounded-t-lg inset-4">

                {{-- Topbar --}}
                <div
                    class="sticky top-0 flex items-center justify-between p-4 bg-opacity-75 border-b rounded-t-lg bg-primary">

                    <!-- myTitle-->
                    <h3 class="w-full text-2xl font-semibold text-white ">
                        {{ $myTitle }}
                    </h3>

                    {{-- rjDate & Shift Input Rj --}}
                    <div id="shiftTanggal" class="flex justify-end w-full mr-4">


                        {{-- Close Modal --}}
                        <button wire:click="closeModalLayanan()"
                            class="text-gray-400 bg-gray-50 hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"
                                xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd"
                                    d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                    clip-rule="evenodd"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                {{-- Content --}}
                <div class="bg-white ">
                    <table class="w-full table-auto">
                        <tbody>
                            <tr>
                                <td class="w-1/4 text-xs text-center border-2 border-gray-900">
                                    <img src="madinahlogopersegi.png" class="object-fill h-32 mx-auto">
                                    {{-- {!! $myQueryIdentitas->int_name . '</br>' !!} --}}
                                    {!! $myQueryIdentitas->int_address . '</br>' !!}
                                    {!! $myQueryIdentitas->int_city . '</br>' !!}
                                    {!! $myQueryIdentitas->int_phone1 . '</br>' !!}
                                    {!! $myQueryIdentitas->int_phone2 . '</br>' !!}
                                    {!! $myQueryIdentitas->int_fax . '</br>' !!}
                                </td>
                                <td class="w-3/4 text-sm border-2 border-gray-900 text-start">
                                    <div>
                                        <table class="w-full table-auto">
                                            <tbody>
                                                <tr>

                                                    <td class="p-1 m-1">Nama Pasien</td>
                                                    <td class="p-1 m-1">:</td>
                                                    <td class="p-1 m-1 text-sm font-semibold">
                                                        {{ isset($dataPasien['pasien']['regName']) ? strtoupper($dataPasien['pasien']['regName']) : '-' }}/
                                                        {{ isset($dataPasien['pasien']['jenisKelamin']['jenisKelaminDesc']) ? $dataPasien['pasien']['jenisKelamin']['jenisKelaminDesc'] : '-' }}/
                                                        {{ isset($dataPasien['pasien']['thn']) ? $dataPasien['pasien']['thn'] : '-' }}
                                                    </td>
                                                    <td class="p-1 m-1">-</td>
                                                    <td class="p-1 m-1">No RM</td>
                                                    <td class="p-1 m-1">:</td>
                                                    <td class="p-1 m-1 text-lg font-semibold">
                                                        {{ isset($dataPasien['pasien']['regNo']) ? $dataPasien['pasien']['regNo'] : '-' }}
                                                    </td>
                                                </tr>
                                                <tr>

                                                    <td class="p-1 m-1">Tanggal Lahir</td>
                                                    <td class="p-1 m-1">:</td>
                                                    <td class="p-1 m-1">
                                                        {{ isset($dataPasien['pasien']['tglLahir']) ? $dataPasien['pasien']['tglLahir'] : '-' }}
                                                    </td>
                                                    <td class="p-1 m-1">-</td>
                                                    <td class="p-1 m-1">NIK</td>
                                                    <td class="p-1 m-1">:</td>
                                                    <td class="p-1 m-1">
                                                        {{ isset($dataPasien['pasien']['identitas']['nik']) ? $dataPasien['pasien']['identitas']['nik'] : '-' }}

                                                    </td>
                                                </tr>
                                                <tr>

                                                    <td class="p-1 m-1">Alamat</td>
                                                    <td class="p-1 m-1">:</td>
                                                    <td class="p-1 m-1">
                                                        {{ isset($dataPasien['pasien']['identitas']['alamat']) ? $dataPasien['pasien']['identitas']['alamat'] : '-' }}
                                                    </td>
                                                    <td class="p-1 m-1">-</td>
                                                    <td class="p-1 m-1">Id BPJS</td>
                                                    <td class="p-1 m-1">:</td>
                                                    <td class="p-1 m-1">
                                                        {{ isset($dataPasien['pasien']['identitas']['idbpjs']) ? $dataPasien['pasien']['identitas']['idbpjs'] : '-' }}
                                                    </td>
                                                </tr>
                                                <tr>

                                                    <td class="p-1 m-1"></td>
                                                    <td class="p-1 m-1">:</td>
                                                    <td class="p-1 m-1"></td>
                                                    <td class="p-1 m-1">-</td>
                                                    <td class="p-1 m-1">Klaim</td>
                                                    <td class="p-1 m-1">:</td>
                                                    <td class="p-1 m-1">
                                                        @isset($dataDaftarTxn['klaimId'])
                                                            {{ $dataDaftarTxn['klaimId'] == 'UM'
                                                                ? 'UMUM'
                                                                : ($dataDaftarTxn['klaimId'] == 'JM'
                                                                    ? 'BPJS'
                                                                    : ($dataDaftarTxn['klaimId'] == 'KR'
                                                                        ? 'Kronis'
                                                                        : 'Asuransi Lain')) }}
                                                        @endisset
                                                    </td>
                                                </tr>
                                                <tr>

                                                    <td class="p-1 m-1"></td>
                                                    <td class="p-1 m-1">:</td>
                                                    <td class="p-1 m-1"> </td>
                                                    <td class="p-1 m-1">-</td>
                                                    <td class="p-1 m-1">Tanggal Masuk</td>
                                                    <td class="p-1 m-1">:</td>
                                                    <td class="p-1 m-1">
                                                        {{ isset($dataDaftarTxn['rjDate']) ? $dataDaftarTxn['rjDate'] : '-' }}
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    {{-- assesment --}}
                    <div>
                        <table class="w-full table-auto">
                            <tbody>
                                <tr>
                                    <td
                                        class="p-2 m-2 text-2xl font-semibold text-center uppercase border-b-2 border-l-2 border-r-2 border-gray-900">
                                        assesment awal instalasi gawat darurat
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    {{-- assesment --}}

                    {{-- Pengkajian peerawatan --}}
                    <div>
                        @php
                            $tingkatKegawatan =
                                $dataDaftarTxn['anamnesa']['pengkajianPerawatan']['tingkatKegawatan'] ?? 'P0';

                            $tingkatKegawatanBgColor = match ($tingkatKegawatan) {
                                'P1' => 'bg-red-500',
                                'P2' => 'bg-yellow-500',
                                'P3' => 'bg-green-500',
                                'P0' => 'bg-gray-400',
                                default => 'bg-white',
                            };
                        @endphp

                        <table class="w-full table-auto {{ $tingkatKegawatanBgColor }}">
                            <tbody>
                                <tr>
                                    <td
                                        class="p-2 m-2 text-sm font-semibold uppercase border-b-2 border-l-2 border-gray-900 text-start">
                                        pengkajian perawat
                                    </td>

                                    <td class="p-2 m-2 text-sm border-b-2 border-r-2 border-gray-900 text-start">
                                        <span class="font-semibold">
                                            Cara Masuk IGD :
                                        </span>
                                        {{ isset($dataDaftarTxn['anamnesa']['pengkajianPerawatan']['caraMasukIgd']) ? $dataDaftarTxn['anamnesa']['pengkajianPerawatan']['caraMasukIgd'] : '-' }}
                                        /
                                        <span class="font-semibold">
                                            Tingkat Kegawatan :
                                        </span>
                                        {{ isset($dataDaftarTxn['anamnesa']['pengkajianPerawatan']['tingkatKegawatan']) ? $dataDaftarTxn['anamnesa']['pengkajianPerawatan']['tingkatKegawatan'] : '-' }}

                                        <br>

                                        <span class="font-semibold">
                                            Status Psikologis :
                                        </span>
                                        {{ isset($dataDaftarTxn['anamnesa']['statusPsikologis']['tidakAdaKelainan'])
                                            ? ($dataDaftarTxn['anamnesa']['statusPsikologis']['tidakAdaKelainan']
                                                ? 'Tidak ada kelainan'
                                                : '-')
                                            : '-' }}
                                        <span class="font-semibold">
                                            /
                                        </span>
                                        {{ isset($dataDaftarTxn['anamnesa']['statusPsikologis']['marah'])
                                            ? ($dataDaftarTxn['anamnesa']['statusPsikologis']['marah']
                                                ? 'Marah'
                                                : '-')
                                            : '-' }}
                                        <span class="font-semibold">
                                            /
                                        </span>
                                        {{ isset($dataDaftarTxn['anamnesa']['statusPsikologis']['ccemas'])
                                            ? ($dataDaftarTxn['anamnesa']['statusPsikologis']['ccemas']
                                                ? 'Cemas'
                                                : '-')
                                            : '-' }}
                                        <span class="font-semibold">
                                            /
                                        </span>
                                        {{ isset($dataDaftarTxn['anamnesa']['statusPsikologis']['takut'])
                                            ? ($dataDaftarTxn['anamnesa']['statusPsikologis']['takut']
                                                ? 'Takut'
                                                : '-')
                                            : '-' }}
                                        <span class="font-semibold">
                                            /
                                        </span>
                                        {{ isset($dataDaftarTxn['anamnesa']['statusPsikologis']['sedih'])
                                            ? ($dataDaftarTxn['anamnesa']['statusPsikologis']['sedih']
                                                ? 'Sedih'
                                                : '-')
                                            : '-' }}
                                        <span class="font-semibold">
                                            /
                                        </span>
                                        {{ isset($dataDaftarTxn['anamnesa']['statusPsikologis']['cenderungBunuhDiri'])
                                            ? ($dataDaftarTxn['anamnesa']['statusPsikologis']['cenderungBunuhDiri']
                                                ? 'Resiko Bunuh Diri'
                                                : '-')
                                            : '-' }}
                                        /
                                        <span class="font-semibold">
                                            Keterangan Status Psikologis
                                        </span>
                                        {{ isset($dataDaftarTxn['anamnesa']['statusPsikologis']['sebutstatusPsikologis']) ? $dataDaftarTxn['anamnesa']['statusPsikologis']['sebutstatusPsikologis'] : '-' }}

                                        <br>

                                        <span class="font-semibold">
                                            Status Mental :
                                        </span>
                                        {{ isset($dataDaftarTxn['anamnesa']['statusMental']['statusMental'])
                                            ? ($dataDaftarTxn['anamnesa']['statusMental']['statusMental']
                                                ? $dataDaftarTxn['anamnesa']['statusMental']['statusMental']
                                                : '-')
                                            : '-' }}
                                        /
                                        <span class="font-semibold">
                                            Keterangan Status Mental :
                                        </span>
                                        {{ isset($dataDaftarTxn['anamnesa']['statusMental']['sebutstatusPsikologis']) ? $dataDaftarTxn['anamnesa']['statusMental']['sebutstatusPsikologis'] : '-' }}
                                    </td>

                                </tr>
                            </tbody>
                        </table>
                    </div>
                    {{-- Pengkajian peerawatan --}}

                    {{-- Anamnesa Pemeriksaan Fisik --}}
                    <div>
                        <table class="w-full table-auto">
                            <tbody>
                                <tr>
                                    <td
                                        class="w-3/4 text-sm border-b-2 border-l-2 border-r-2 border-gray-900 text-start">
                                        <div>
                                            <table class="w-full table-auto">
                                                <tbody>
                                                    <tr>
                                                        <td class="w-1/4 pl-2 font-semibold uppercase align-text-top ">
                                                            {{-- Anamnesa --}}
                                                            Anamnesa

                                                        </td>
                                                        <td class="w-1/4 align-text-top">
                                                            Keluhan Utama :
                                                        </td>
                                                        <td class="w-3/4">
                                                            {!! nl2br(
                                                                e(
                                                                    isset($dataDaftarTxn['anamnesa']['keluhanUtama']['keluhanUtama'])
                                                                        ? ($dataDaftarTxn['anamnesa']['keluhanUtama']['keluhanUtama']
                                                                            ? $dataDaftarTxn['anamnesa']['keluhanUtama']['keluhanUtama']
                                                                            : '-')
                                                                        : '-',
                                                                ),
                                                            ) !!}
                                                            <br>

                                                        </td>
                                                    </tr>

                                                    <tr>
                                                        <td>
                                                        </td>
                                                        <td class="w-1/4 align-text-top">
                                                            Screening Batuk :
                                                        </td>
                                                        <td class="w-3/4">
                                                            @isset($dataDaftarTxn['anamnesa']['batuk']['riwayatDemam'])
                                                                @if ($dataDaftarTxn['anamnesa']['batuk']['riwayatDemam'])
                                                                    Riwayat Demam? :
                                                                    {!! nl2br(
                                                                        e(
                                                                            isset($dataDaftarTxn['anamnesa']['batuk']['riwayatDemam'])
                                                                                ? ($dataDaftarTxn['anamnesa']['batuk']['riwayatDemam']
                                                                                    ? 'Ya'
                                                                                    : '-')
                                                                                : '-',
                                                                        ),
                                                                    ) !!}
                                                                    /
                                                                    {!! nl2br(
                                                                        e(
                                                                            isset($dataDaftarTxn['anamnesa']['batuk']['keteranganriwayatDemam'])
                                                                                ? ($dataDaftarTxn['anamnesa']['batuk']['keteranganriwayatDemam']
                                                                                    ? 'Ya'
                                                                                    : '-')
                                                                                : '-',
                                                                        ),
                                                                    ) !!}
                                                                    <br>
                                                                @endif
                                                            @endisset

                                                            @isset($dataDaftarTxn['anamnesa']['batuk']['berkeringatMlmHari'])
                                                                @if ($dataDaftarTxn['anamnesa']['batuk']['berkeringatMlmHari'])
                                                                    Riwayat Berkeringat Malam Hari Tanpa Aktifitas? :
                                                                    {!! nl2br(
                                                                        e(
                                                                            isset($dataDaftarTxn['anamnesa']['batuk']['berkeringatMlmHari'])
                                                                                ? ($dataDaftarTxn['anamnesa']['batuk']['berkeringatMlmHari']
                                                                                    ? 'Ya'
                                                                                    : '-')
                                                                                : '-',
                                                                        ),
                                                                    ) !!}
                                                                    /
                                                                    {!! nl2br(
                                                                        e(
                                                                            isset($dataDaftarTxn['anamnesa']['batuk']['keteranganberkeringatMlmHari'])
                                                                                ? ($dataDaftarTxn['anamnesa']['batuk']['keteranganberkeringatMlmHari']
                                                                                    ? 'Ya'
                                                                                    : '-')
                                                                                : '-',
                                                                        ),
                                                                    ) !!}
                                                                    <br>
                                                                @endif
                                                            @endisset

                                                            @isset($dataDaftarTxn['anamnesa']['batuk']['bepergianDaerahWabah'])
                                                                @if ($dataDaftarTxn['anamnesa']['batuk']['bepergianDaerahWabah'])
                                                                    Riwayat Bepergian Daerah Wabah? :
                                                                    {!! nl2br(
                                                                        e(
                                                                            isset($dataDaftarTxn['anamnesa']['batuk']['bepergianDaerahWabah'])
                                                                                ? ($dataDaftarTxn['anamnesa']['batuk']['bepergianDaerahWabah']
                                                                                    ? 'Ya'
                                                                                    : '-')
                                                                                : '-',
                                                                        ),
                                                                    ) !!}
                                                                    /
                                                                    {!! nl2br(
                                                                        e(
                                                                            isset($dataDaftarTxn['anamnesa']['batuk']['KeteranganbepergianDaerahWabah'])
                                                                                ? ($dataDaftarTxn['anamnesa']['batuk']['KeteranganbepergianDaerahWabah']
                                                                                    ? 'Ya'
                                                                                    : '-')
                                                                                : '-',
                                                                        ),
                                                                    ) !!}
                                                                    <br>
                                                                @endif
                                                            @endisset

                                                            @isset($dataDaftarTxn['anamnesa']['batuk']['riwayatPakaiObatJangkaPanjangan'])
                                                                @if ($dataDaftarTxn['anamnesa']['batuk']['riwayatPakaiObatJangkaPanjangan'])
                                                                    Riwayat Pemakaian Obat dalam Jangka Panjang? :
                                                                    {!! nl2br(
                                                                        e(
                                                                            isset($dataDaftarTxn['anamnesa']['batuk']['riwayatPakaiObatJangkaPanjangan'])
                                                                                ? ($dataDaftarTxn['anamnesa']['batuk']['riwayatPakaiObatJangkaPanjangan']
                                                                                    ? 'Ya'
                                                                                    : '-')
                                                                                : '-',
                                                                        ),
                                                                    ) !!}
                                                                    /
                                                                    {!! nl2br(
                                                                        e(
                                                                            isset($dataDaftarTxn['anamnesa']['batuk']['keteranganriwayatPakaiObatJangkaPanjangan'])
                                                                                ? ($dataDaftarTxn['anamnesa']['batuk']['keteranganriwayatPakaiObatJangkaPanjangan']
                                                                                    ? 'Ya'
                                                                                    : '-')
                                                                                : '-',
                                                                        ),
                                                                    ) !!}
                                                                    <br>
                                                                @endif
                                                            @endisset

                                                            @isset($dataDaftarTxn['anamnesa']['batuk']['BBTurunTanpaSebab'])
                                                                @if ($dataDaftarTxn['anamnesa']['batuk']['BBTurunTanpaSebab'])
                                                                    Riwayat Berat Badan Turun Tanpa Sebab? :
                                                                    {!! nl2br(
                                                                        e(
                                                                            isset($dataDaftarTxn['anamnesa']['batuk']['BBTurunTanpaSebab'])
                                                                                ? ($dataDaftarTxn['anamnesa']['batuk']['BBTurunTanpaSebab']
                                                                                    ? 'Ya'
                                                                                    : '-')
                                                                                : '-',
                                                                        ),
                                                                    ) !!}
                                                                    /
                                                                    {!! nl2br(
                                                                        e(
                                                                            isset($dataDaftarTxn['anamnesa']['batuk']['keteranganBBTurunTanpaSebab'])
                                                                                ? ($dataDaftarTxn['anamnesa']['batuk']['keteranganBBTurunTanpaSebab']
                                                                                    ? 'Ya'
                                                                                    : '-')
                                                                                : '-',
                                                                        ),
                                                                    ) !!}
                                                                    <br>
                                                                @endif
                                                            @endisset

                                                            @isset($dataDaftarTxn['anamnesa']['batuk']['pembesaranGetahBening'])
                                                                @if ($dataDaftarTxn['anamnesa']['batuk']['pembesaranGetahBening'])
                                                                    Ada Pembesaran Kelenjar Getah Bening? :
                                                                    {!! nl2br(
                                                                        e(
                                                                            isset($dataDaftarTxn['anamnesa']['batuk']['pembesaranGetahBening'])
                                                                                ? ($dataDaftarTxn['anamnesa']['batuk']['pembesaranGetahBening']
                                                                                    ? 'Ya'
                                                                                    : '-')
                                                                                : '-',
                                                                        ),
                                                                    ) !!}
                                                                    /
                                                                    {!! nl2br(
                                                                        e(
                                                                            isset($dataDaftarTxn['anamnesa']['batuk']['keteranganpembesaranGetahBening'])
                                                                                ? ($dataDaftarTxn['anamnesa']['batuk']['keteranganpembesaranGetahBening']
                                                                                    ? 'Ya'
                                                                                    : '-')
                                                                                : '-',
                                                                        ),
                                                                    ) !!}
                                                                    <br>
                                                                @endif
                                                            @endisset
                                                            -
                                                        </td>
                                                    </tr>

                                                    <tr>
                                                        <td>
                                                        </td>
                                                        <td class="w-1/4 align-text-top">
                                                            Skala Nyeri :
                                                        </td>
                                                        <td class="w-3/4">
                                                            VAS :
                                                            {{ isset($dataDaftarTxn['penilaian']['nyeri']['vas']['vas'])
                                                                ? ($dataDaftarTxn['penilaian']['nyeri']['vas']['vas']
                                                                    ? $dataDaftarTxn['penilaian']['nyeri']['vas']['vas']
                                                                    : '-')
                                                                : '-' }}
                                                            /
                                                            Pencetus :
                                                            {{ isset($dataDaftarTxn['penilaian']['nyeri']['pencetus'])
                                                                ? ($dataDaftarTxn['penilaian']['nyeri']['pencetus']
                                                                    ? $dataDaftarTxn['penilaian']['nyeri']['pencetus']
                                                                    : '-')
                                                                : '-' }}
                                                            /
                                                            Durasi :
                                                            {{ isset($dataDaftarTxn['penilaian']['nyeri']['durasi'])
                                                                ? ($dataDaftarTxn['penilaian']['nyeri']['durasi']
                                                                    ? $dataDaftarTxn['penilaian']['nyeri']['durasi']
                                                                    : '-')
                                                                : '-' }}
                                                            /
                                                            Lokasi :
                                                            {{ isset($dataDaftarTxn['penilaian']['nyeri']['lokasi'])
                                                                ? ($dataDaftarTxn['penilaian']['nyeri']['lokasi']
                                                                    ? $dataDaftarTxn['penilaian']['nyeri']['lokasi']
                                                                    : '-')
                                                                : '-' }}



                                                        </td>
                                                    </tr>

                                                    <tr>
                                                        <td>
                                                        </td>
                                                        <td class="w-1/4 align-text-top">
                                                            Resiko Jatuh :
                                                        </td>
                                                        <td class="w-3/4">

                                                            Skala Humpty Dumpty
                                                            /
                                                            Total Skor :
                                                            {{ isset($this->dataDaftarTxn['penilaian']['resikoJatuh']['skalaHumptyDumpty']['skalaHumptyDumptyScore'])
                                                                ? ($this->dataDaftarTxn['penilaian']['resikoJatuh']['skalaHumptyDumpty']['skalaHumptyDumptyScore']
                                                                    ? $this->dataDaftarTxn['penilaian']['resikoJatuh']['skalaHumptyDumpty']['skalaHumptyDumptyScore']
                                                                    : '-')
                                                                : '-' }}
                                                            /
                                                            {{ isset($this->dataDaftarTxn['penilaian']['resikoJatuh']['skalaHumptyDumpty']['skalaHumptyDumptyDesc'])
                                                                ? ($this->dataDaftarTxn['penilaian']['resikoJatuh']['skalaHumptyDumpty']['skalaHumptyDumptyDesc']
                                                                    ? $this->dataDaftarTxn['penilaian']['resikoJatuh']['skalaHumptyDumpty']['skalaHumptyDumptyDesc']
                                                                    : '-')
                                                                : '-' }}
                                                            <br>
                                                            Skala Morse
                                                            /
                                                            Total Skor :
                                                            {{ isset($this->dataDaftarTxn['penilaian']['resikoJatuh']['skalaMorse']['skalaMorseScore'])
                                                                ? ($this->dataDaftarTxn['penilaian']['resikoJatuh']['skalaMorse']['skalaMorseScore']
                                                                    ? $this->dataDaftarTxn['penilaian']['resikoJatuh']['skalaMorse']['skalaMorseScore']
                                                                    : '-')
                                                                : '-' }}
                                                            /
                                                            {{ isset($this->dataDaftarTxn['penilaian']['resikoJatuh']['skalaMorse']['skalaMorseDesc'])
                                                                ? ($this->dataDaftarTxn['penilaian']['resikoJatuh']['skalaMorse']['skalaMorseDesc']
                                                                    ? $this->dataDaftarTxn['penilaian']['resikoJatuh']['skalaMorse']['skalaMorseDesc']
                                                                    : '-')
                                                                : '-' }}
                                                        </td>
                                                    </tr>

                                                    <tr>
                                                        <td></td>
                                                        <td class="align-text-top">
                                                            Riwayat Penyakit Sekarang :
                                                        </td>
                                                        <td class="w-3/4">
                                                            {!! nl2br(
                                                                e(
                                                                    isset($dataDaftarTxn['anamnesa']['riwayatPenyakitSekarangUmum']['riwayatPenyakitSekarangUmum'])
                                                                        ? ($dataDaftarTxn['anamnesa']['riwayatPenyakitSekarangUmum']['riwayatPenyakitSekarangUmum']
                                                                            ? $dataDaftarTxn['anamnesa']['riwayatPenyakitSekarangUmum']['riwayatPenyakitSekarangUmum']
                                                                            : '-')
                                                                        : '-',
                                                                ),
                                                            ) !!}
                                                            <br>

                                                        </td>
                                                    </tr>

                                                    <tr>
                                                        <td></td>
                                                        <td class="align-text-top">
                                                            Riwayat Penyakit Dahulu :
                                                        </td>
                                                        <td class="w-3/4">
                                                            {!! nl2br(
                                                                e(
                                                                    isset($dataDaftarTxn['anamnesa']['riwayatPenyakitDahulu']['riwayatPenyakitDahulu'])
                                                                        ? ($dataDaftarTxn['anamnesa']['riwayatPenyakitDahulu']['riwayatPenyakitDahulu']
                                                                            ? $dataDaftarTxn['anamnesa']['riwayatPenyakitDahulu']['riwayatPenyakitDahulu']
                                                                            : '-')
                                                                        : '-',
                                                                ),
                                                            ) !!}
                                                        </td>
                                                    </tr>

                                                    <tr>
                                                        <td></td>
                                                        <td class="align-text-top">
                                                            Alergi :
                                                        </td>
                                                        <td class="w-3/4">
                                                            {!! nl2br(
                                                                e(
                                                                    isset($dataDaftarTxn['anamnesa']['alergi']['alergi'])
                                                                        ? ($dataDaftarTxn['anamnesa']['alergi']['alergi']
                                                                            ? $dataDaftarTxn['anamnesa']['alergi']['alergi']
                                                                            : '-')
                                                                        : '-',
                                                                ),
                                                            ) !!}
                                                            <br>
                                                        </td>
                                                    </tr>

                                                    <tr>
                                                        <td></td>
                                                        <td class="align-text-top">
                                                            Rekonsiliasi Obat :
                                                        </td>
                                                        <td class="w-3/4">
                                                            {{-- rekonsiliasiObat --}}
                                                            <table class="w-full table-auto">
                                                                <thead class="text-xs text-gray-900 uppercase ">
                                                                    <tr>
                                                                        <th>
                                                                            Nama Obat
                                                                        </th>

                                                                        <th>
                                                                            Dosis
                                                                        </th>

                                                                        <th>
                                                                            Rute
                                                                        </th>
                                                                    </tr>
                                                                </thead>
                                                                @isset($dataDaftarTxn['anamnesa']['rekonsiliasiObat'])
                                                                    @foreach ($dataDaftarTxn['anamnesa']['rekonsiliasiObat'] as $key => $rekonsiliasiObat)
                                                                        <tbody>
                                                                            <tr>
                                                                                <td class="w-1/3 text-center">
                                                                                    {{ $rekonsiliasiObat['namaObat'] }}
                                                                                </td>

                                                                                <td class="w-1/3 text-center">
                                                                                    {{ $rekonsiliasiObat['dosis'] }}
                                                                                </td>

                                                                                <td class="w-1/3 text-center">
                                                                                    {{ $rekonsiliasiObat['rute'] }}
                                                                                </td>
                                                                            </tr>
                                                                        </tbody>
                                                                    @endforeach
                                                                @endisset
                                                            </table>
                                                            {{-- rekonsiliasiObat --}}

                                                        </td>
                                                    </tr>

                                                </tbody>
                                            </table>
                                        </div>
                                    </td>
                                    <td
                                        class="w-1/4 text-sm border-b-2 border-l-2 border-r-2 border-gray-900 text-start">
                                        <div>
                                            <table class="w-full table-auto">
                                                <tbody>
                                                    <tr>
                                                        <td class="w-1/2 font-semibold uppercase align-text-top">
                                                            {{-- Perawat / Terapis --}}
                                                            Perawat / Terapis :
                                                        </td>
                                                        <td class="w-1/2">
                                                            .
                                                            <br>
                                                            <div class="pl-[0px]">
                                                                @php
                                                                    $drPemeriksa = isset(
                                                                        $dataDaftarTxn['anamnesa'][
                                                                            'pengkajianPerawatan'
                                                                        ]['perawatPenerima'],
                                                                    )
                                                                        ? ($dataDaftarTxn['anamnesa'][
                                                                            'pengkajianPerawatan'
                                                                        ]['perawatPenerima']
                                                                            ? $dataDaftarTxn['anamnesa'][
                                                                                'pengkajianPerawatan'
                                                                            ]['perawatPenerima']
                                                                            : 'Perawat Penerima')
                                                                        : 'Perawat Penerima';
                                                                @endphp
                                                                {!! DNS2D::getBarcodeHTML($drPemeriksa, 'QRCODE', 3, 3) !!}
                                                            </div>
                                                            ttd
                                                            <br>
                                                            {{ isset($dataDaftarTxn['anamnesa']['pengkajianPerawatan']['perawatPenerima'])
                                                                ? ($dataDaftarTxn['anamnesa']['pengkajianPerawatan']['perawatPenerima']
                                                                    ? strtoupper($dataDaftarTxn['anamnesa']['pengkajianPerawatan']['perawatPenerima'])
                                                                    : 'Perawat Penerima')
                                                                : 'Perawat Penerima' }}
                                                        </td>
                                                    </tr>

                                                    <tr>
                                                        <td class="w-1/2 font-semibold uppercase">
                                                            {{-- Tanda Vital --}}
                                                            Tanda Vital :
                                                        </td>
                                                        <td class="w-1/2">

                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="pr-4 text-end">
                                                            TD :
                                                        </td>
                                                        <td>
                                                            {{ isset($dataDaftarTxn['pemeriksaan']['tandaVital']['sistolik'])
                                                                ? ($dataDaftarTxn['pemeriksaan']['tandaVital']['sistolik']
                                                                    ? $dataDaftarTxn['pemeriksaan']['tandaVital']['sistolik']
                                                                    : '-')
                                                                : '-' }}
                                                            /
                                                            {{ isset($dataDaftarTxn['pemeriksaan']['tandaVital']['distolik'])
                                                                ? ($dataDaftarTxn['pemeriksaan']['tandaVital']['distolik']
                                                                    ? $dataDaftarTxn['pemeriksaan']['tandaVital']['distolik']
                                                                    : '-')
                                                                : '-' }}
                                                            mmhg
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="pr-4 text-end">
                                                            Nadi :
                                                        </td>
                                                        <td>
                                                            {{ isset($dataDaftarTxn['pemeriksaan']['tandaVital']['frekuensiNadi'])
                                                                ? ($dataDaftarTxn['pemeriksaan']['tandaVital']['frekuensiNadi']
                                                                    ? $dataDaftarTxn['pemeriksaan']['tandaVital']['frekuensiNadi']
                                                                    : '-')
                                                                : '-' }}
                                                            x/mnt
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="pr-4 text-end">
                                                            Suhu :
                                                        </td>
                                                        <td>
                                                            {{ isset($dataDaftarTxn['pemeriksaan']['tandaVital']['suhu'])
                                                                ? ($dataDaftarTxn['pemeriksaan']['tandaVital']['suhu']
                                                                    ? $dataDaftarTxn['pemeriksaan']['tandaVital']['suhu']
                                                                    : '-')
                                                                : '-' }}
                                                            &degC
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="pr-4 text-end">
                                                            Pernafasan :
                                                        </td>
                                                        <td>
                                                            {{ isset($dataDaftarTxn['pemeriksaan']['tandaVital']['frekuensiNafas'])
                                                                ? ($dataDaftarTxn['pemeriksaan']['tandaVital']['frekuensiNafas']
                                                                    ? $dataDaftarTxn['pemeriksaan']['tandaVital']['frekuensiNafas']
                                                                    : '-')
                                                                : '-' }}
                                                            x/mnt
                                                        </td>
                                                    </tr>
                                                    {{-- <tr>
                                                        <td class="pr-4 text-end">
                                                            Saturasi O2 :
                                                        </td>
                                                        <td>
                                                            {{ isset($dataDaftarTxn['pemeriksaan']['tandaVital']['saturasiO2'])
                                                                ? ($dataDaftarTxn['pemeriksaan']['tandaVital']['saturasiO2']
                                                                    ? $dataDaftarTxn['pemeriksaan']['tandaVital']['saturasiO2']
                                                                    : '-')
                                                                : '-' }}
                                                            Saturasi
                                                        </td>
                                                    </tr> --}}
                                                    {{-- <tr>
                                                        <td class="pr-4 text-end">
                                                            Berat Badan :
                                                        </td>
                                                        <td>
                                                            {{ isset($dataDaftarTxn['pemeriksaan']['nutrisi']['bb'])
                                                                ? ($dataDaftarTxn['pemeriksaan']['nutrisi']['bb']
                                                                    ? $dataDaftarTxn['pemeriksaan']['nutrisi']['bb']
                                                                    : '-')
                                                                : '-' }}
                                                            kg
                                                        </td>
                                                    </tr> --}}
                                                    <tr>
                                                        <td class="pr-4 text-end">
                                                            SPO2 :
                                                        </td>
                                                        <td>
                                                            {{ isset($dataDaftarTxn['pemeriksaan']['tandaVital']['spo2'])
                                                                ? ($dataDaftarTxn['pemeriksaan']['tandaVital']['spo2']
                                                                    ? $dataDaftarTxn['pemeriksaan']['tandaVital']['spo2']
                                                                    : '-')
                                                                : '-' }}
                                                            %
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="pr-4 text-end">
                                                            GDA :
                                                        </td>
                                                        <td>
                                                            {{ isset($dataDaftarTxn['pemeriksaan']['tandaVital']['gda'])
                                                                ? ($dataDaftarTxn['pemeriksaan']['tandaVital']['gda']
                                                                    ? $dataDaftarTxn['pemeriksaan']['tandaVital']['gda']
                                                                    : '-')
                                                                : '-' }}
                                                            mg/dL
                                                        </td>
                                                    </tr>

                                                    <tr>
                                                        <td class="w-1/2 font-semibold uppercase">
                                                            {{-- Nutrisi --}}
                                                            Nutrisi :
                                                        </td>
                                                        <td class="w-1/2">

                                                        </td>
                                                    </tr>

                                                    <tr>
                                                        <td class="pr-4 text-end">
                                                            Berat Badan :
                                                        </td>
                                                        <td>
                                                            {{ isset($dataDaftarTxn['pemeriksaan']['nutrisi']['bb'])
                                                                ? ($dataDaftarTxn['pemeriksaan']['nutrisi']['bb']
                                                                    ? $dataDaftarTxn['pemeriksaan']['nutrisi']['bb']
                                                                    : '-')
                                                                : '-' }}
                                                            Kg
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="pr-4 text-end">
                                                            Tinggi Badan :
                                                        </td>
                                                        <td>
                                                            {{ isset($dataDaftarTxn['pemeriksaan']['nutrisi']['tb'])
                                                                ? ($dataDaftarTxn['pemeriksaan']['nutrisi']['tb']
                                                                    ? $dataDaftarTxn['pemeriksaan']['nutrisi']['tb']
                                                                    : '-')
                                                                : '-' }}
                                                            cm
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="pr-4 text-end">
                                                            Index Masa Tubuh :
                                                        </td>
                                                        <td>
                                                            {{ isset($dataDaftarTxn['pemeriksaan']['nutrisi']['imt'])
                                                                ? ($dataDaftarTxn['pemeriksaan']['nutrisi']['imt']
                                                                    ? $dataDaftarTxn['pemeriksaan']['nutrisi']['imt']
                                                                    : '-')
                                                                : '-' }}
                                                            Kg/M2
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="pr-4 text-end">
                                                            Lingkar Kepala :
                                                        </td>
                                                        <td>
                                                            {{ isset($dataDaftarTxn['pemeriksaan']['nutrisi']['lk'])
                                                                ? ($dataDaftarTxn['pemeriksaan']['nutrisi']['lk']
                                                                    ? $dataDaftarTxn['pemeriksaan']['nutrisi']['lk']
                                                                    : '-')
                                                                : '-' }}
                                                            cm
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="pr-4 text-end">
                                                            Lingkar Lengan Atas :
                                                        </td>
                                                        <td>
                                                            {{ isset($dataDaftarTxn['pemeriksaan']['nutrisi']['lila'])
                                                                ? ($dataDaftarTxn['pemeriksaan']['nutrisi']['lila']
                                                                    ? $dataDaftarTxn['pemeriksaan']['nutrisi']['lila']
                                                                    : '-')
                                                                : '-' }}
                                                            cm
                                                        </td>
                                                    </tr>




                                                </tbody>
                                            </table>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    {{-- Anamnesa Pemeriksaan Fisik --}}

                    {{-- Keadaan Umum --}}
                    <div>
                        <table class="w-full table-auto">
                            <tbody>
                                <tr>
                                    <td
                                        class="w-1/4 p-2 m-2 text-sm font-semibold uppercase border-b-2 border-l-2 border-r-2 border-gray-900 text-start">
                                        keadaan umum
                                    </td>
                                    <td
                                        class="w-3/4 p-2 m-2 text-sm text-center border-b-2 border-l-2 border-r-2 border-gray-900">
                                        {{-- <span class="font-semibold">
                                            Keadaan Umum :
                                        </span> --}}
                                        {{ isset($dataDaftarTxn['pemeriksaan']['tandaVital']['keadaanUmum'])
                                            ? ($dataDaftarTxn['pemeriksaan']['tandaVital']['keadaanUmum']
                                                ? $dataDaftarTxn['pemeriksaan']['tandaVital']['keadaanUmum']
                                                : '-')
                                            : '-' }}
                                        /
                                        <span class="font-semibold">
                                            Tingkat Kesadaran :
                                        </span>
                                        {{ isset($dataDaftarTxn['pemeriksaan']['tandaVital']['tingkatKesadaran'])
                                            ? ($dataDaftarTxn['pemeriksaan']['tandaVital']['tingkatKesadaran']
                                                ? $dataDaftarTxn['pemeriksaan']['tandaVital']['tingkatKesadaran']
                                                : '-')
                                            : '-' }}
                                    </td>

                                </tr>

                                <tr>
                                    <td
                                        class="w-1/4 p-2 m-2 text-sm font-semibold uppercase border-b-2 border-l-2 border-r-2 border-gray-900 text-start">
                                        abcd
                                    </td>
                                    <td
                                        class="p-2 m-2 text-sm text-center border-b-2 border-l-2 border-r-2 border-gray-900">
                                        <span class="font-semibold">
                                            Jalan Nafas (A) :
                                        </span>
                                        {{ isset($dataDaftarTxn['pemeriksaan']['tandaVital']['jalanNafas']['jalanNafas'])
                                            ? ($dataDaftarTxn['pemeriksaan']['tandaVital']['jalanNafas']['jalanNafas']
                                                ? $dataDaftarTxn['pemeriksaan']['tandaVital']['jalanNafas']['jalanNafas']
                                                : '-')
                                            : '-' }}
                                        /
                                        <span class="font-semibold">
                                            Pernafasan (B) :
                                        </span>
                                        {{ isset($dataDaftarTxn['pemeriksaan']['tandaVital']['pernafasan']['pernafasan'])
                                            ? ($dataDaftarTxn['pemeriksaan']['tandaVital']['pernafasan']['pernafasan']
                                                ? $dataDaftarTxn['pemeriksaan']['tandaVital']['pernafasan']['pernafasan']
                                                : '-')
                                            : '-' }}
                                        <span class="font-semibold">
                                            Gerak Dada :
                                        </span>
                                        {{ isset($dataDaftarTxn['pemeriksaan']['tandaVital']['gerakDada']['gerakDada'])
                                            ? ($dataDaftarTxn['pemeriksaan']['tandaVital']['gerakDada']['gerakDada']
                                                ? $dataDaftarTxn['pemeriksaan']['tandaVital']['gerakDada']['gerakDada']
                                                : '-')
                                            : '-' }}


                                        <br>
                                        <span class="font-semibold">
                                            Sirkulasi (C) :
                                        </span>
                                        {{ isset($dataDaftarTxn['pemeriksaan']['tandaVital']['sirkulasi']['sirkulasi'])
                                            ? ($dataDaftarTxn['pemeriksaan']['tandaVital']['sirkulasi']['sirkulasi']
                                                ? $dataDaftarTxn['pemeriksaan']['tandaVital']['sirkulasi']['sirkulasi']
                                                : '-')
                                            : '-' }}
                                        /
                                        <span class="font-semibold">
                                            Neurologi (D) :
                                        </span>
                                        Mata :
                                        {{ isset($dataDaftarTxn['pemeriksaan']['tandaVital']['e'])
                                            ? ($dataDaftarTxn['pemeriksaan']['tandaVital']['e']
                                                ? $dataDaftarTxn['pemeriksaan']['tandaVital']['e']
                                                : '-')
                                            : '-' }}
                                        -
                                        Verbal:
                                        {{ isset($dataDaftarTxn['pemeriksaan']['tandaVital']['v'])
                                            ? ($dataDaftarTxn['pemeriksaan']['tandaVital']['v']
                                                ? $dataDaftarTxn['pemeriksaan']['tandaVital']['v']
                                                : '-')
                                            : '-' }}
                                        -
                                        Motorik :
                                        {{ isset($dataDaftarTxn['pemeriksaan']['tandaVital']['m'])
                                            ? ($dataDaftarTxn['pemeriksaan']['tandaVital']['m']
                                                ? $dataDaftarTxn['pemeriksaan']['tandaVital']['m']
                                                : '-')
                                            : '-' }}
                                        -
                                        GCS:
                                        {{ isset($dataDaftarTxn['pemeriksaan']['tandaVital']['gcs'])
                                            ? ($dataDaftarTxn['pemeriksaan']['tandaVital']['gcs']
                                                ? $dataDaftarTxn['pemeriksaan']['tandaVital']['gcs']
                                                : '-')
                                            : '-' }}

                                    </td>
                                </tr>
                                {{-- fungsional --}}
                                <tr>
                                    <td
                                        class="p-2 m-2 text-sm font-semibold uppercase border-b-2 border-l-2 border-r-2 border-gray-900 text-start">
                                        fungsional
                                    </td>

                                    <td
                                        class="p-2 m-2 text-sm text-center border-b-2 border-l-2 border-r-2 border-gray-900">
                                        <span class="font-semibold">
                                            Alat Bantu :
                                        </span>
                                        {{ isset($dataDaftarTxn['pemeriksaan']['fungsional']['alatBantu'])
                                            ? ($dataDaftarTxn['pemeriksaan']['fungsional']['alatBantu']
                                                ? $dataDaftarTxn['pemeriksaan']['fungsional']['alatBantu']
                                                : '-')
                                            : '-' }}
                                        /
                                        <span class="font-semibold">
                                            Prothesa :
                                        </span>
                                        {{ isset($dataDaftarTxn['pemeriksaan']['fungsional']['prothesa'])
                                            ? ($dataDaftarTxn['pemeriksaan']['fungsional']['prothesa']
                                                ? $dataDaftarTxn['pemeriksaan']['fungsional']['prothesa']
                                                : '-')
                                            : '-' }}
                                        /
                                        <span class="font-semibold">
                                            Cacat Tubuh :
                                        </span>
                                        {{ isset($dataDaftarTxn['pemeriksaan']['fungsional']['cacatTubuh'])
                                            ? ($dataDaftarTxn['pemeriksaan']['fungsional']['cacatTubuh']
                                                ? $dataDaftarTxn['pemeriksaan']['fungsional']['cacatTubuh']
                                                : '-')
                                            : '-' }}
                                    </td>
                                </tr>
                                {{-- pemeriksaan fisik --}}
                                <tr>
                                    <td
                                        class="p-2 m-2 text-sm font-semibold uppercase border-b-2 border-l-2 border-r-2 border-gray-900 text-start">
                                        pemeriksaan fisik
                                    </td>

                                    <td
                                        class="p-2 m-2 text-sm text-center border-b-2 border-l-2 border-r-2 border-gray-900">


                                        <table class="w-full table-auto">
                                            <tbody>
                                                <tr>
                                                    <td>
                                                        <span class="font-semibold">
                                                            Fisik :
                                                        </span>
                                                        <br>
                                                        {!! nl2br(
                                                            e(
                                                                isset($dataDaftarTxn['pemeriksaan']['fisik'])
                                                                    ? ($dataDaftarTxn['pemeriksaan']['fisik']
                                                                        ? $dataDaftarTxn['pemeriksaan']['fisik']
                                                                        : '-')
                                                                    : '-',
                                                            ),
                                                        ) !!}
                                                    </td>
                                                    <td>
                                                        <span class="font-semibold">
                                                            Anatomi :
                                                        </span>
                                                        <br>
                                                        @isset($dataDaftarTxn['pemeriksaan']['anatomi'])
                                                            @foreach ($dataDaftarTxn['pemeriksaan']['anatomi'] as $key => $pAnatomi)
                                                                @php
                                                                    $kelainan = isset(
                                                                        $dataDaftarTxn['pemeriksaan']['anatomi'][$key][
                                                                            'kelainan'
                                                                        ],
                                                                    )
                                                                        ? ($dataDaftarTxn['pemeriksaan']['anatomi'][
                                                                            $key
                                                                        ]['kelainan']
                                                                            ? $dataDaftarTxn['pemeriksaan']['anatomi'][
                                                                                $key
                                                                            ]['kelainan']
                                                                            : false)
                                                                        : false;
                                                                @endphp
                                                                @if ($kelainan && $kelainan !== 'Tidak Diperiksa')
                                                                    <span class="font-normal">
                                                                        {{ strtoupper($key) }} :
                                                                        {{ $kelainan }}
                                                                    </span>
                                                                    /
                                                                    {!! nl2br(
                                                                        e(
                                                                            isset($dataDaftarTxn['pemeriksaan']['anatomi'][$key]['desc'])
                                                                                ? ($dataDaftarTxn['pemeriksaan']['anatomi'][$key]['desc']
                                                                                    ? $dataDaftarTxn['pemeriksaan']['anatomi'][$key]['desc']
                                                                                    : '-')
                                                                                : '-',
                                                                        ),
                                                                    ) !!}
                                                                    <br>
                                                                @endif
                                                            @endforeach
                                                        @endisset
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                                {{-- pemeriksaan penunjang --}}
                                <tr>
                                    <td
                                        class="p-2 m-2 text-sm font-semibold uppercase border-b-2 border-l-2 border-r-2 border-gray-900 text-start">
                                        pemeriksaan penunjang
                                    </td>

                                    <td
                                        class="p-2 m-2 text-sm text-center border-b-2 border-l-2 border-r-2 border-gray-900">
                                        <span class="font-semibold">

                                            Pemeriksaan Penunjang Lab / Foto / EKG / Lan-lain :
                                        </span>
                                        <br>
                                        {!! nl2br(
                                            e(
                                                isset($dataDaftarTxn['pemeriksaan']['penunjang'])
                                                    ? ($dataDaftarTxn['pemeriksaan']['penunjang']
                                                        ? $dataDaftarTxn['pemeriksaan']['penunjang']
                                                        : '-')
                                                    : '-',
                                            ),
                                        ) !!}

                                    </td>
                                </tr>
                                {{-- diagnosis --}}
                                <tr>
                                    <td
                                        class="p-2 m-2 text-sm font-semibold uppercase border-b-2 border-l-2 border-r-2 border-gray-900 text-start">
                                        diagnosis
                                    </td>

                                    <td
                                        class="p-2 m-2 text-sm text-center border-b-2 border-l-2 border-r-2 border-gray-900">
                                        <table class="w-full text-sm table-auto">
                                            <thead>
                                                <tr>
                                                    {{-- <th>
                                                        Kode (ICD 10)
                                                    </th> --}}

                                                    <th>
                                                        Diagnosa
                                                    </th>

                                                    {{-- <th>
                                                        Kategori
                                                    </th> --}}
                                                </tr>
                                            </thead>
                                            <tbody>

                                                {{-- @isset($dataDaftarTxn['diagnosis'])
                                                    @foreach ($dataDaftarTxn['diagnosis'] as $key => $diag)
                                                        <tr>

                                                            <td>
                                                                {{ $diag['icdX'] }}
                                                            </td>

                                                            <td>
                                                                {{ $diag['diagDesc'] }}
                                                            </td>



                                                            <td>
                                                                {{ $diag['kategoriDiagnosa'] }}
                                                            </td>


                                                        </tr>
                                                    @endforeach
                                                @endisset --}}

                                                <tr>
                                                    {{-- <td>
                                                        Freetext Dokter
                                                    </td> --}}
                                                    <td>
                                                        {!! nl2br(
                                                            e(
                                                                isset($dataDaftarTxn['diagnosisFreeText'])
                                                                    ? ($dataDaftarTxn['diagnosisFreeText']
                                                                        ? $dataDaftarTxn['diagnosisFreeText']
                                                                        : '-')
                                                                    : '-',
                                                            ),
                                                        ) !!}
                                                    </td>
                                                    {{-- <td>
                                                    </td> --}}
                                                </tr>

                                            </tbody>
                                        </table>


                                    </td>
                                </tr>
                                {{-- prosedur --}}
                                <tr>
                                    <td
                                        class="p-2 m-2 text-sm font-semibold uppercase border-b-2 border-l-2 border-r-2 border-gray-900 text-start">
                                        prosedur
                                    </td>

                                    <td
                                        class="p-2 m-2 text-sm text-center border-b-2 border-l-2 border-r-2 border-gray-900">
                                        <table class="w-full text-sm table-auto">
                                            <thead>
                                                <tr>
                                                    {{-- <th>
                                                        Kode (ICD 9 CM)
                                                    </th> --}}

                                                    <th>
                                                        Prosedur
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody>

                                                {{-- @isset($dataDaftarTxn['procedure'])
                                                    @foreach ($dataDaftarTxn['procedure'] as $key => $procedure)
                                                        <tr>

                                                            <td>
                                                                {{ $procedure['procedureId'] }}
                                                            </td>

                                                            <td>
                                                                {{ $procedure['procedureDesc'] }}
                                                            </td>

                                                        </tr>
                                                    @endforeach
                                                @endisset --}}

                                                <tr>
                                                    {{-- <td>
                                                        Freetext Dokter
                                                    </td> --}}
                                                    <td>
                                                        {!! nl2br(
                                                            e(
                                                                isset($dataDaftarTxn['procedureFreeText'])
                                                                    ? ($dataDaftarTxn['procedureFreeText']
                                                                        ? $dataDaftarTxn['procedureFreeText']
                                                                        : '-')
                                                                    : '-',
                                                            ),
                                                        ) !!}
                                                    </td>
                                                </tr>


                                            </tbody>
                                        </table>



                                    </td>
                                </tr>
                                {{-- status medik dan tindak lanjut --}}
                                <tr>
                                    <td
                                        class="p-2 m-2 text-sm font-semibold uppercase border-b-2 border-l-2 border-r-2 border-gray-900 text-start">
                                        status medik dan tindak lanjut
                                    </td>

                                    <td
                                        class="p-2 m-2 text-sm text-center border-b-2 border-l-2 border-r-2 border-gray-900">
                                        <span class="font-semibold">
                                            Status Medik :
                                        </span>
                                        {{ isset($dataDaftarTxn['penilaian']['statusMedik']['statusMedik'])
                                            ? ($dataDaftarTxn['penilaian']['statusMedik']['statusMedik']
                                                ? $dataDaftarTxn['penilaian']['statusMedik']['statusMedik']
                                                : '-')
                                            : '-' }}
                                        <br>
                                        <span class="font-semibold">
                                            Tindak Lanjut :
                                        </span>
                                        {{ isset($dataDaftarTxn['perencanaan']['tindakLanjut']['tindakLanjut'])
                                            ? ($dataDaftarTxn['perencanaan']['tindakLanjut']['tindakLanjut']
                                                ? $dataDaftarTxn['perencanaan']['tindakLanjut']['tindakLanjut']
                                                : '-')
                                            : '-' }}
                                        /
                                        {{ isset($dataDaftarTxn['perencanaan']['tindakLanjut']['keteranganTindakLanjut'])
                                            ? ($dataDaftarTxn['perencanaan']['tindakLanjut']['keteranganTindakLanjut']
                                                ? $dataDaftarTxn['perencanaan']['tindakLanjut']['keteranganTindakLanjut']
                                                : '-')
                                            : '-' }}


                                        {{-- <br>
                                        @inject('carbon', 'Carbon\Carbon')
                                        <span class="font-semibold">
                                            Waktu Datang :
                                        </span>
                                        {{ isset($dataDaftarTxn['anamnesa']['pengkajianPerawatan']['jamDatang'])
                                            ? ($dataDaftarTxn['anamnesa']['pengkajianPerawatan']['jamDatang']
                                                ? $carbon
                                                    ::createFromFormat('d/m/Y H:i:s', $dataDaftarTxn['anamnesa']['pengkajianPerawatan']['jamDatang'],env('APP_TIMEZONE'))->format('H:i:s')
                                                : '-')
                                            : '-' }}
                                        /
                                        <span class="font-semibold">
                                            Waktu Pemeriksaan :
                                        </span>
                                        {{ isset($dataDaftarTxn['perencanaan']['pengkajianMedis']['waktuPemeriksaan'])
                                            ? ($dataDaftarTxn['perencanaan']['pengkajianMedis']['waktuPemeriksaan']
                                                ? $carbon
                                                    ::createFromFormat('d/m/Y H:i:s', $dataDaftarTxn['perencanaan']['pengkajianMedis']['waktuPemeriksaan'],env('APP_TIMEZONE'))->format('H:i:s')
                                                : '-')
                                            : '-' }}
                                        /
                                        <span class="font-semibold">
                                            Selesai Pemeriksaan :
                                        </span>
                                        {{ isset($dataDaftarTxn['perencanaan']['pengkajianMedis']['selesaiPemeriksaan'])
                                            ? ($dataDaftarTxn['perencanaan']['pengkajianMedis']['selesaiPemeriksaan']
                                                ? $carbon
                                                    ::createFromFormat('d/m/Y H:i:s', $dataDaftarTxn['perencanaan']['pengkajianMedis']['selesaiPemeriksaan'],env('APP_TIMEZONE'))->format('H:i:s')
                                                : '-')
                                            : '-' }}
                                        <br>
                                        @isset($dataDaftarTxn['perencanaan']['pengkajianMedis']['waktuPemeriksaan'])
                                            @isset($dataDaftarTxn['anamnesa']['pengkajianPerawatan']['jamDatang'])
                                                <span class="font-semibold">
                                                    Waktu Response untu pasien {{ $dataPasien['pasien']['regName'] }}
                                                    adalah
                                                    {{ $carbon
                                                        ::createFromFormat('d/m/Y H:i:s', $dataDaftarTxn['perencanaan']['pengkajianMedis']['waktuPemeriksaan'])->diff($carbon::createFromFormat('d/m/Y H:i:s', $dataDaftarTxn['anamnesa']['pengkajianPerawatan']['jamDatang']))->format('%H:%I:%S') }}
                                                </span>
                                            @endisset
                                        @endisset --}}


                                    </td>
                                </tr>
                                {{-- terapi --}}
                                <tr>
                                    <td
                                        class="p-2 m-2 text-sm font-semibold uppercase border-b-2 border-l-2 border-r-2 border-gray-900 text-start">
                                        terapi
                                    </td>
                                    <td
                                        class="p-2 m-2 text-sm border-b-2 border-l-2 border-r-2 border-gray-900 text-start">

                                        <table class="w-full text-sm table-auto">
                                            <tbody>
                                                <td class="w-3/4">

                                                    {!! nl2br(
                                                        e(
                                                            isset($dataDaftarTxn['perencanaan']['terapi']['terapi'])
                                                                ? ($dataDaftarTxn['perencanaan']['terapi']['terapi']
                                                                    ? $dataDaftarTxn['perencanaan']['terapi']['terapi']
                                                                    : '-')
                                                                : '-',
                                                        ),
                                                    ) !!}

                                                </td>
                                                @inject('carbon', 'Carbon\Carbon')
                                                <td class="w-1/4 text-center">
                                                    Tulungagung,
                                                    {{ isset($dataDaftarTxn['perencanaan']['pengkajianMedis']['selesaiPemeriksaan'])
                                                        ? ($dataDaftarTxn['perencanaan']['pengkajianMedis']['selesaiPemeriksaan']
                                                            ? $dataDaftarTxn['perencanaan']['pengkajianMedis']['selesaiPemeriksaan']
                                                            : 'Tanggal')
                                                        : 'Tanggal' }}

                                                    <br>
                                                    <div class="pl-[85px]">
                                                        @php
                                                            $drPemeriksa = isset(
                                                                $dataDaftarTxn['perencanaan']['pengkajianMedis'][
                                                                    'drPemeriksa'
                                                                ],
                                                            )
                                                                ? ($dataDaftarTxn['perencanaan']['pengkajianMedis'][
                                                                    'drPemeriksa'
                                                                ]
                                                                    ? $dataDaftarTxn['perencanaan']['pengkajianMedis'][
                                                                        'drPemeriksa'
                                                                    ]
                                                                    : 'Dokter Pemeriksa')
                                                                : 'Dokter Pemeriksa';
                                                        @endphp
                                                        {!! DNS2D::getBarcodeHTML($drPemeriksa, 'QRCODE', 3, 3) !!}
                                                    </div>
                                                    ttd
                                                    <br>
                                                    {{ isset($dataDaftarTxn['perencanaan']['pengkajianMedis']['drPemeriksa'])
                                                        ? ($dataDaftarTxn['perencanaan']['pengkajianMedis']['drPemeriksa']
                                                            ? $dataDaftarTxn['perencanaan']['pengkajianMedis']['drPemeriksa']
                                                            : 'Dokter Pemeriksa')
                                                        : 'Dokter Pemeriksa' }}
                                                </td>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>

                            </tbody>
                        </table>
                    </div>
                    {{-- Keadaan Umum --}}

                </div>
                {{-- End Content --}}

                <div class="sticky bottom-0 flex justify-between px-4 py-3 bg-opacity-75 bg-gray-50 sm:px-6">
                    <div class="">
                        {{-- null --}}
                    </div>

                    <div wire:loading wire:target="cetakRekamMedisUGD">
                        <x-loading />
                    </div>

                    <x-green-button :disabled=$disabledPropertyRjStatus wire:click.prevent="cetakRekamMedisUGD()"
                        type="button" wire:loading.remove>
                        Cetak RM UGD
                    </x-green-button>
                </div>
            </div>

        </div>

    </div>
