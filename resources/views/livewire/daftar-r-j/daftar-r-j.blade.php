<div>

    <div class="px-1 pt-7">
        <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm">
            <!-- Card header -->



            <div class="w-full mb-1">
                <div class="">


                    {{-- text --}}
                    <div class="mb-5">
                        <h3 class="text-3xl font-bold text-gray-900 dark:text-white">{{ $myTitle }}</h3>
                        <span class="text-base font-normal text-gray-900 dark:text-gray-400">{{ $mySnipt }}</span>
                    </div>
                    {{-- end text --}}



                    <div class="md:flex md:justify-between">

                        {{-- search --}}
                        <div class="relative pointer-events-auto md:w-1/2">
                            <div class="absolute inset-y-0 left-0 flex items-center p-5 pl-3 pointer-events-none">
                                <svg width="24" height="24" fill="none" aria-hidden="true"
                                    class="flex-none mr-3">
                                    <path d="m19 19-3.5-3.5" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round"></path>
                                    <circle cx="11" cy="11" r="6" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round"></circle>
                                </svg>
                            </div>
                            <x-text-input id="simpleSearch" name="namesimpleSearch" type="text" class="p-2 pl-10"
                                autofocus autocomplete="simpleSearch" placeholder="Cari Data"
                                wire:model.lazy="search" />
                        </div>
                        {{-- end search --}}

                        {{-- two button --}}
                        <div class="flex justify-between mt-2 md:mt-0">
                            <x-primary-button wire:click="create()" class="flex justify-center flex-auto">
                                <svg class="w-5 h-5 mr-2 -ml-1" fill="currentColor" viewBox="0 0 20 20"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd"
                                        d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                Daftar {{ $myProgram }}
                            </x-primary-button>


                            <x-dropdown align="right" :width="__('20')">
                                <x-slot name="trigger">
                                    {{-- Button myLimitPerPage --}}
                                    <x-alternative-button class="inline-flex">
                                        <svg class="-ml-1 mr-1.5 w-5 h-5" fill="currentColor" viewbox="0 0 20 20"
                                            xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                            <path clip-rule="evenodd" fill-rule="evenodd"
                                                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" />
                                        </svg>
                                        Tampil ({{ $limitPerPage }})
                                    </x-alternative-button>
                                </x-slot>
                                {{-- Open myLimitPerPagecontent --}}
                                <x-slot name="content">

                                    @foreach ($myLimitPerPages as $myLimitPerPage)
                                        <x-dropdown-link wire:click="setLimitPerPage({{ $myLimitPerPage }})">
                                            {{ __($myLimitPerPage) }}
                                        </x-dropdown-link>
                                    @endforeach
                                </x-slot>
                            </x-dropdown>
                        </div>
                        {{-- end two button --}}

                    </div>


                    <div class="flex rounded-lg bg-gray-50">

                        {{-- date --}}
                        <div class="relative w-1/5 mt-2 ">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <svg aria-hidden="true" class="w-5 h-5 text-gray-900 dark:text-gray-400"
                                    fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd"
                                        d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
                                        clip-rule="evenodd"></path>
                                </svg>
                            </div>


                            <x-text-input id="dateRjRef" datepicker datepicker-autohide datepicker-format="dd/mm/yyyy"
                                type="text" class="p-2 pl-10 " placeholder="dd/mm/yyyy" wire:model="dateRjRef" />
                        </div>

                        {{-- radio --}}
                        <div class="flex mt-2 ml-2">
                            @foreach ($statusRjRef['statusOptions'] as $sRj)
                                {{-- @dd($sRj) --}}
                                <x-radio-button :label="__($sRj['statusDesc'])" value="{{ $sRj['statusId'] }}"
                                    wire:model="statusRjRef.statusId" />
                            @endforeach
                        </div>



                        {{-- Dokter --}}
                        <div class="mt-2 ml-0">
                            <x-dropdown align="right" :width="__('80')" :contentclasses="__('overflow-auto max-h-[150px] py-1 bg-white dark:bg-gray-700')">
                                <x-slot name="trigger">
                                    {{-- Button Dokter --}}
                                    <x-alternative-button class="inline-flex">
                                        <svg class="-ml-1 mr-1.5 w-5 h-5" fill="currentColor" viewbox="0 0 20 20"
                                            xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                            <path clip-rule="evenodd" fill-rule="evenodd"
                                                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" />
                                        </svg>
                                        <span>{{ 'Dokter ( ' . $drRjRef['drName'] . ' )' }}</span>
                                    </x-alternative-button>
                                </x-slot>
                                {{-- Open shiftcontent --}}
                                <x-slot name="content">

                                    @foreach ($drRjRef['drOptions'] as $dr)
                                        <x-dropdown-link
                                            wire:click="setdrRjRef('{{ sprintf($dr['drId']) }}','{{ sprintf($dr['drName']) }}')">
                                            {{ __($dr['drName']) }}
                                        </x-dropdown-link>
                                    @endforeach
                                </x-slot>
                            </x-dropdown>
                        </div>

                    </div>

                    @if ($isOpen)
                        @include('livewire.daftar-r-j.create')
                    @endif

                    @if ($formRujukanRefBPJSStatus)
                        @include('livewire.daftar-r-j.form-rujukanRefBpjs')
                    @endif





                </div>



                <!-- Table -->
                <div class="flex flex-col mt-2">
                    <div class="overflow-x-auto rounded-lg">
                        <div class="inline-block min-w-full align-middle">
                            <div class="overflow-hidden shadow sm:rounded-lg">
                                <table class="w-full text-sm text-left text-gray-900 table-auto dark:text-gray-400">
                                    <thead
                                        class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                        <tr>
                                            <th scope="col" class="w-1/5 px-4 py-3 ">
                                                Pasien
                                            </th>
                                            <th scope="col" class="w-1/5 px-4 py-3 ">
                                                Poli
                                            </th>
                                            <th scope="col" class="w-1/5 px-4 py-3 ">
                                                Status Layanan
                                            </th>
                                            @role(['Mr', 'Admin', 'Perawat'])
                                                <th scope="col" class="w-1/5 px-4 py-3 ">
                                                    Tindak Lanjut
                                                </th>
                                            @endrole
                                            <th scope="col" class="w-1/5 px-4 py-3 ">
                                                Action
                                            </th>
                                        </tr>

                                    </thead>
                                    <tbody class="bg-white dark:bg-gray-800">
                                        @inject('carbon', 'Carbon\Carbon')

                                        @foreach ($RJpasiens as $RJp)
                                            @php
                                                $datadaftar_json = json_decode($RJp->datadaftarpolirj_json, true);
                                                $anamnesa = isset($datadaftar_json['anamnesa']) ? 1 : 0;
                                                $pemeriksaan = isset($datadaftar_json['pemeriksaan']) ? 1 : 0;
                                                $penilaian = isset($datadaftar_json['penilaian']) ? 1 : 0;
                                                $procedure = isset($datadaftar_json['procedure']) ? 1 : 0;
                                                $diagnosis = isset($datadaftar_json['diagnosis']) ? 1 : 0;
                                                $perencanaan = isset($datadaftar_json['perencanaan']) ? 1 : 0;
                                                $prosentaseEMR =
                                                    (($anamnesa +
                                                        $pemeriksaan +
                                                        $penilaian +
                                                        $procedure +
                                                        $diagnosis +
                                                        $perencanaan) /
                                                        6) *
                                                    100;

                                                $bgSelesaiPemeriksaan = isset(
                                                    $datadaftar_json['perencanaan']['pengkajianMedis']['drPemeriksa'],
                                                )
                                                    ? ($datadaftar_json['perencanaan']['pengkajianMedis']['drPemeriksa']
                                                        ? 'bg-green-100'
                                                        : '')
                                                    : '';

                                                $badgecolorEmr = $prosentaseEMR >= 80 ? 'green' : 'red';

                                                $badgecolorStatus = isset($RJp->rj_status)
                                                    ? ($RJp->rj_status === 'A'
                                                        ? 'red'
                                                        : ($RJp->rj_status === 'L'
                                                            ? 'green'
                                                            : ($RJp->rj_status === 'I'
                                                                ? 'green'
                                                                : ($RJp->rj_status === 'F'
                                                                    ? 'yellow'
                                                                    : 'default'))))
                                                    : '';

                                                $badgecolorKlaim =
                                                    $RJp->klaim_id == 'UM'
                                                        ? 'green'
                                                        : ($RJp->klaim_id == 'JM'
                                                            ? 'default'
                                                            : ($RJp->klaim_id == 'KR'
                                                                ? 'yellow'
                                                                : 'red'));

                                                $badgecolorAdministrasiRj = isset($datadaftar_json['AdministrasiRj'])
                                                    ? 'green'
                                                    : 'red';

                                                $taskId3 =
                                                    $datadaftar_json['taskIdPelayanan']['taskId3'] ??
                                                    'xxxx-xx-xx xx:xx:xx';
                                                $taskId4 =
                                                    $datadaftar_json['taskIdPelayanan']['taskId4'] ??
                                                    'xxxx-xx-xx xx:xx:xx';
                                                $taskId5 =
                                                    $datadaftar_json['taskIdPelayanan']['taskId5'] ??
                                                    'xxxx-xx-xx xx:xx:xx';

                                                $eresep = isset($datadaftar_json['eresep']) ? 1 : 0;
                                                $prosentaseEMREresep = ($eresep / 1) * 100;
                                                $badgecolorEresep = $eresep ? 'green' : 'red';

                                                $noReferensi = $datadaftar_json['noReferensi'] ?? '-';
                                                $rjNoJson = $datadaftar_json['rjNo'] ?? '-';
                                                $bgChekJsonRjNo =
                                                    $RJp->rj_no === $rjNoJson ? 'bg-green-100' : 'bg-red-100';

                                                $tglRujukan = isset(
                                                    $datadaftar_json['sep']['reqSep']['request']['t_sep']['rujukan'][
                                                        'tglRujukan'
                                                    ],
                                                )
                                                    ? ($datadaftar_json['sep']['reqSep']['request']['t_sep']['rujukan'][
                                                        'tglRujukan'
                                                    ]
                                                        ? $datadaftar_json['sep']['reqSep']['request']['t_sep'][
                                                            'rujukan'
                                                        ]['tglRujukan']
                                                        : $carbon::now(env('APP_TIMEZONE'))->format('Y-m-d'))
                                                    : $carbon::now(env('APP_TIMEZONE'))->format('Y-m-d');
                                                $tglRujukanAwal = $carbon::createFromFormat('Y-m-d', $tglRujukan);
                                                $tglBatasRujukan = $carbon
                                                    ::createFromFormat('Y-m-d', $tglRujukan)
                                                    ->addMonths(3);

                                                $diffInDays = $tglBatasRujukan->diffInDays(
                                                    $carbon::now(env('APP_TIMEZONE')),
                                                );
                                                $propertyDiffInDays =
                                                    $diffInDays <= 20
                                                        ? 'bg-red-100'
                                                        : ($diffInDays <= 30
                                                            ? 'bg-yellow-400'
                                                            : '');
                                            @endphp


                                            <tr class="border-b group {{ $bgSelesaiPemeriksaan }}">


                                                <td class="px-4 py-3 group-hover:bg-gray-100">
                                                    <div class="">
                                                        <div class="font-normal text-gray-700">
                                                            {{ 'No. Antrian ' }} <span
                                                                class="text-5xl font-semibold text-gray-700">{{ $RJp->no_antrian }}</span>
                                                        </div>
                                                        <div class="font-semibold text-primary">
                                                            {{ $RJp->reg_no }}
                                                        </div>
                                                        <div class="font-semibold text-gray-900">
                                                            {{ $RJp->reg_name . ' / (' . $RJp->sex . ')' . ' / ' . $RJp->thn }}
                                                        </div>
                                                        <div class="font-normal text-gray-900">
                                                            {{ $RJp->address }}
                                                        </div>

                                                        <div class="text-xs text-gray-900 {{ $bgChekJsonRjNo }}">
                                                            {{ $RJp->rj_no . ' / ' . $rjNoJson }}
                                                        </div>
                                                    </div>
                                                </td>


                                                <td class="px-4 py-3 group-hover:bg-gray-100">
                                                    <div class="">
                                                        <div class="font-semibold text-primary">
                                                            {{ $RJp->poli_desc }}
                                                        </div>
                                                        <div class="font-semibold text-gray-900">
                                                            {{ $RJp->dr_name . ' / ' }}
                                                            <x-badge :badgecolor="__($badgecolorKlaim)">
                                                                {{ $RJp->klaim_id == 'UM'
                                                                    ? 'UMUM'
                                                                    : ($RJp->klaim_id == 'JM'
                                                                        ? 'BPJS'
                                                                        : ($RJp->klaim_id == 'KR'
                                                                            ? 'Kronis'
                                                                            : 'Asuransi Lain')) }}
                                                            </x-badge>

                                                        </div>

                                                        <div class="font-normal">
                                                            {{ $RJp->vno_sep }}
                                                        </div>

                                                        <div class="flex my-2 space-x-2">
                                                            @if ($RJp->lab_status)
                                                                <x-badge :badgecolor="__('default')">
                                                                    {{ 'Laborat' }}
                                                                </x-badge>
                                                            @endif

                                                            @if ($RJp->rad_status)
                                                                <x-badge :badgecolor="__('default')">
                                                                    {{ 'Radiologi' }}
                                                                </x-badge>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </td>

                                                <td class="px-4 py-3 group-hover:bg-gray-100">
                                                    <div class="">
                                                        <div class="font-semibold text-primary">
                                                            {{ $RJp->rj_date }}
                                                            {{ '| Shift : ' . $RJp->shift }}
                                                        </div>
                                                        <div class="flex italic font-semibold text-gray-900">
                                                            <x-badge :badgecolor="__($badgecolorStatus)">
                                                                {{ isset($RJp->rj_status)
                                                                    ? ($RJp->rj_status === 'A'
                                                                        ? 'Pelayanan'
                                                                        : ($RJp->rj_status === 'L'
                                                                            ? 'Selesai Pelayanan'
                                                                            : ($RJp->rj_status === 'I'
                                                                                ? 'Transfer Inap'
                                                                                : ($RJp->rj_status === 'F'
                                                                                    ? 'Batal Transaksi'
                                                                                    : ''))))
                                                                    : '' }}
                                                            </x-badge>
                                                            <x-badge :badgecolor="__($badgecolorEmr)">
                                                                Emr: {{ $prosentaseEMR . '%' }}
                                                            </x-badge>
                                                        </div>
                                                        <div class="font-normal text-gray-900">
                                                            {{ '' . $RJp->nobooking }}
                                                        </div>
                                                        <div>
                                                            <x-badge :badgecolor="__($badgecolorEresep)">
                                                                E-Resep: {{ $prosentaseEMREresep . '%' }}
                                                            </x-badge>
                                                        </div>

                                                        @if ($noReferensi)
                                                            <div>
                                                                No Ref:{{ $noReferensi }}
                                                            </div>
                                                        @endif
                                                        {{-- <div>
                                                            <x-primary-button
                                                                wire:click="cekSep('{{ json_encode($datadaftar_json['sep'] ?? [], true) }}')">
                                                                Cek SEP
                                                            </x-primary-button>
                                                        </div> --}}
                                                        @if ($RJp->klaim_id == 'JM')
                                                            @isset($datadaftar_json['sep']['reqSep']['request']['t_sep']['rujukan']['tglRujukan'])
                                                                <div>
                                                                    <p
                                                                        class="mt-2 rounded-lg text-gray-900 text-xs {{ $propertyDiffInDays }}">
                                                                        Masa berlaku Rujukan
                                                                        {{ $tglRujukanAwal->format('d/m/Y') }} s/d
                                                                        {{ $tglBatasRujukan->format('d/m/Y') }}{{ '- - - sisa :' . $diffInDays . ' hari' }}
                                                                    </p>
                                                                </div>
                                                            @endisset
                                                        @endif
                                                    </div>
                                                </td>
                                                @role(['Mr', 'Admin', 'Perawat'])
                                                    <td class="px-4 py-3 group-hover:bg-gray-100">
                                                        <div class="font-normal text-gray-700">
                                                            <p class="font-semibold text-primary">Administrasi :</p>
                                                            <div class="font-semibold text-gray-900">
                                                                @isset($datadaftar_json['AdministrasiRj'])
                                                                    <x-badge :badgecolor="__('green')">
                                                                        {{ $datadaftar_json['AdministrasiRj']['userLog'] }}</x-badge>
                                                                @else
                                                                    <x-badge :badgecolor="__('red')"> {{ '-' }}</x-badge>
                                                                @endisset
                                                            </div>
                                                        </div>
                                                        <div class="italic font-normal text-gray-900">
                                                            <x-badge :badgecolor="__('green')">
                                                                {{ 'TaskId3 ' . $taskId3 }}
                                                            </x-badge>
                                                        </div>
                                                        <div class="italic font-normal text-gray-900">
                                                            <x-badge :badgecolor="__('green')">
                                                                {{ 'TaskId4 ' . $taskId4 }}
                                                            </x-badge>
                                                        </div>
                                                        <div class="italic font-normal text-gray-900">
                                                            <x-badge :badgecolor="__('green')">
                                                                {{ 'TaskId5 ' . $taskId5 }}
                                                            </x-badge>
                                                        </div>
                                                        <div class="mt-1 font-normal">
                                                            Tindak Lanjut :
                                                            {{ isset($datadaftar_json['perencanaan']['tindakLanjut']['tindakLanjut'])
                                                                ? ($datadaftar_json['perencanaan']['tindakLanjut']['tindakLanjut']
                                                                    ? $datadaftar_json['perencanaan']['tindakLanjut']['tindakLanjut']
                                                                    : '-')
                                                                : '-' }}
                                                            </br>
                                                            Tanggal :
                                                            {{ isset($datadaftar_json['kontrol']['tglKontrol'])
                                                                ? ($datadaftar_json['kontrol']['tglKontrol']
                                                                    ? $datadaftar_json['kontrol']['tglKontrol']
                                                                    : '-')
                                                                : '-' }}
                                                            </br>
                                                            {{ isset($datadaftar_json['kontrol']['noSKDPBPJS'])
                                                                ? ($datadaftar_json['kontrol']['noSKDPBPJS']
                                                                    ? $datadaftar_json['kontrol']['noSKDPBPJS']
                                                                    : '-')
                                                                : '-' }}
                                                        </div>
                                                    </td>
                                                @endrole

                                                <td class="px-4 py-3 group-hover:bg-gray-100 group-hover:text-primary">


                                                    <div class="inline-flex">

                                                        <livewire:cetak.cetak-etiket :regNo="$RJp->reg_no"
                                                            :wire:key="'cetak-etiket-'.$RJp->rj_no">

                                                            <!-- Dropdown Action menu Flowbite-->
                                                            <div>
                                                                <x-light-button id="dropdownButton{{ $RJp->rj_no }}"
                                                                    class="inline-flex"
                                                                    wire:click="$emit('pressDropdownButton','{{ $RJp->rj_no }}')">
                                                                    <svg class="w-5 h-5" aria-hidden="true"
                                                                        fill="currentColor" viewbox="0 0 20 20"
                                                                        xmlns="http://www.w3.org/2000/svg">
                                                                        <path
                                                                            d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM16 12a2 2 0 100-4 2 2 0 000 4z" />
                                                                    </svg>
                                                                </x-light-button>

                                                                <div id="dropdownMenu{{ $RJp->rj_no }}"
                                                                    class="z-10 hidden w-auto bg-white divide-y divide-gray-100 rounded-lg shadow dark:bg-gray-700">

                                                                    @role(['Admin', 'Mr', 'Perawat'])
                                                                        @if ($RJp->lab_status || $RJp->rad_status)
                                                                            <div class="grid grid-cols-2 p-2">

                                                                                <x-yellow-button
                                                                                    wire:click="masukPoli('{{ addslashes($RJp->rj_no) }}')"
                                                                                    wire:loading.remove>
                                                                                    4.Masuk Poli
                                                                                </x-yellow-button>
                                                                                <div wire:loading wire:target="masukPoli">
                                                                                    <x-loading />
                                                                                </div>

                                                                                <x-primary-button
                                                                                    wire:click="keluarPoli('{{ addslashes($RJp->rj_no) }}')"
                                                                                    wire:loading.remove>
                                                                                    5.Keluar Poli
                                                                                </x-primary-button>
                                                                                <div wire:loading wire:target="keluarPoli">
                                                                                    <x-loading />
                                                                                </div>

                                                                            </div>
                                                                        @endif
                                                                    @endrole

                                                                    <ul class="py-2 text-sm text-gray-700 dark:text-gray-200"
                                                                        aria-labelledby="dropdownButton{{ $RJp->rj_no }}">
                                                                        @role(['Admin', 'Mr', 'Perawat'])
                                                                            {{-- <li>
                                                                                <x-dropdown-link
                                                                                    wire:click="tampil('{{ $RJp->rj_no }}')">
                                                                                    {{ __('Tampil | ' . $RJp->reg_name) }}
                                                                                </x-dropdown-link>
                                                                            </li> --}}
                                                                            <li>
                                                                                <x-dropdown-link
                                                                                    wire:click="edit('{{ $RJp->rj_no }}')">
                                                                                    {{ __('Ubah | ' . $RJp->reg_name) }}
                                                                                </x-dropdown-link>
                                                                            </li>
                                                                            {{-- <li>
                                                                                <x-dropdown-link
                                                                                    wire:click="$emit('confirm_remove_record', '{{ $RJp->rj_no }}', '{{ $RJp->reg_name }}')">
                                                                                    {{ __('Hapus') }}
                                                                                </x-dropdown-link>
                                                                            </li> --}}
                                                                        @endrole
                                                                    </ul>






                                                                </div>


                                                                @role(['Admin', 'Mr', 'Perawat'])
                                                                    <!-- Dropdown Action menu Flowbite-->
                                                                    <div>
                                                                        <x-light-button
                                                                            id="dropdownButtonSendData{{ $RJp->rj_no }}"
                                                                            class="inline-flex"
                                                                            wire:click="$emit('pressDropdownButtonSendData','{{ $RJp->rj_no }}')">
                                                                            <svg class="w-6 h-6 text-gray-800 dark:text-white"
                                                                                aria-hidden="true"
                                                                                xmlns="http://www.w3.org/2000/svg"
                                                                                width="24" height="24"
                                                                                fill="none" viewBox="0 0 24 24">
                                                                                <path stroke="currentColor"
                                                                                    stroke-linecap="round"
                                                                                    stroke-linejoin="round"
                                                                                    stroke-width="2"
                                                                                    d="M15 17h3a3 3 0 0 0 0-6h-.025a5.56 5.56 0 0 0 .025-.5A5.5 5.5 0 0 0 7.207 9.021C7.137 9.017 7.071 9 7 9a4 4 0 1 0 0 8h2.167M12 19v-9m0 0-2 2m2-2 2 2" />
                                                                            </svg>

                                                                        </x-light-button>

                                                                        <!-- Dropdown Action Open menu -->
                                                                        <div id="dropdownMenuSendData{{ $RJp->rj_no }}"
                                                                            class="z-10 hidden w-auto bg-white divide-y divide-gray-100 rounded-lg shadow dark:bg-gray-700">

                                                                            <ul class="py-2 text-sm text-gray-700 dark:text-gray-200"
                                                                                aria-labelledby="dropdownButtonSendData{{ $RJp->rj_no }}">

                                                                                <div class="grid grid-cols-1 gap-2">
                                                                                    <livewire:emr-r-j.post-encounter-r-j.post-encounter-r-j
                                                                                        :rjNoRef="$RJp->rj_no"
                                                                                        :wire:key="'post-encounter-r-j-'.$RJp->rj_no">

                                                                                        <livewire:emr-r-j.post-satu-data-kesehatan-r-j.post-satu-data-kesehatan-r-j
                                                                                            :rjNoRef="$RJp->rj_no"
                                                                                            :wire:key="'post-satu-data-kesehatan-r-j-'.$RJp->rj_no">
                                                                                </div>

                                                                            </ul>
                                                                        </div>
                                                                    </div>
                                                                    <!-- End Dropdown Action Open menu -->
                                                                @endrole

                                                            </div>


                                                    </div>

                                                </td>
                                            </tr>
                                        @endforeach



                                    </tbody>
                                </table>



                                {{-- no data found start --}}
                                @if ($RJpasiens->count() == 0)
                                    <div class="w-full p-4 text-sm text-center text-gray-900 dark:text-gray-400">
                                        {{ 'Data ' . $myProgram . ' Tidak ditemukan' }}
                                    </div>
                                @endif
                                {{-- no data found end --}}



                            </div>
                        </div>
                    </div>
                </div>



                <!-- Pagination start -->
                <div class="flex items-center justify-end pt-3 sm:pt-6">
                    {{-- {{ $RJpasiens->links() }} --}}
                    {{ $RJpasiens->links('vendor.livewire.tailwind') }}
                </div>
                <!-- Pagination end -->



            </div>
        </div>
    </div>






    {{-- call MasterPasien --}}
    @if ($callMasterPasien)
        @livewire('master-pasien.master-pasien', [
            'isOpen' => true,
            'isOpenMode' => 'insert',
            'dataPasienBPJSSearch' => isset($dataPasien['pasien']['cariDataPasien']) ? $dataPasien['pasien']['cariDataPasien'] : '',
        ])
    @endif

    @if ($callRJskdp)
        @if ($dataDaftarPoliRJ['postInap'])
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
                                <h3 class="text-2xl font-semibold text-white dark:text-white">
                                    Data SKDP Rawat Jalan Post Inap
                                </h3>

                                {{-- Close Modal --}}
                                <button wire:click="$set('callRJskdp',false)"
                                    class="text-gray-400 bg-gray-50 hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-600 dark:hover:text-white">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd"
                                            d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                </button>

                            </div>

                            {{-- call Program --}}
                            @livewire('r-iskdp.r-iskdp', [
                                // 'isOpen' => true,
                                // 'isOpenMode' => 'insert',
                                'regNoRef' => isset($dataPasien['pasien']['regNo']) ? $dataPasien['pasien']['regNo'] : '',
                            ])

                        </div>
                    </div>



                </div>




            </div>
        @else
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
                                <h3 class="text-2xl font-semibold text-white dark:text-white">
                                    Data SKDP Rawat Jalan
                                </h3>

                                {{-- Close Modal --}}
                                <button wire:click="$set('callRJskdp',false)"
                                    class="text-gray-400 bg-gray-50 hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-600 dark:hover:text-white">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd"
                                            d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                </button>

                            </div>

                            {{-- call Program --}}
                            @livewire('r-jskdp.r-jskdp', [
                                // 'isOpen' => true,
                                // 'isOpenMode' => 'insert',
                                'regNoRef' => isset($dataPasien['pasien']['regNo']) ? $dataPasien['pasien']['regNo'] : '',
                            ])

                        </div>
                    </div>



                </div>




            </div>
        @endif
    @endif




















    {{-- push start ///////////////////////////////// --}}
    @push('scripts')
        {{-- script start --}}
        <script src="{{ url('assets/js/jquery.min.js') }}"></script>
        <script src="{{ url('assets/plugins/toastr/toastr.min.js') }}"></script>
        <script src="{{ url('assets/flowbite/dist/datepicker.js') }}"></script>

        {{-- script end --}}





        {{-- Disabling enter key for form --}}
        <script type="text/javascript">
            $(document).on("keydown", "form", function(event) {
                return event.key != "Enter";
            });
        </script>





        {{-- Global Livewire JavaScript Object start --}}
        <script type="text/javascript">
            window.livewire.on('toastr-success', message => toastr.success(message));
            window.Livewire.on('toastr-info', (message) => {
                toastr.info(message)
            });
            window.livewire.on('toastr-error', message => toastr.error(message));













            // confirmation message remove record
            window.livewire.on('confirm_remove_record', (key, name) => {

                let cfn = confirm('Apakah anda ingin menghapus data ini ' + name + '?');

                if (cfn) {
                    window.livewire.emit('confirm_remove_record_RJp', key, name);
                }
            });


            // confirmation message doble record
            window.livewire.on('confirm_doble_record', (key, name) => {

                let cfn = confirm('Pasien Sudah terdaftar, Apakah anda ingin tetap menyimpan data ini ' + name + '?');

                if (cfn) {
                    window.livewire.emit('confirm_doble_record_RJp', key, name);
                }
            });












            // confirmation cari_Data_Pasien_Tidak_Ditemukan_Confirmation
            window.livewire.on('cari_Data_Pasien_Tidak_Ditemukan_Confirmation', (msg) => {
                let cfn = confirm('Data ' + msg +
                    ' tidak ditemuka, apakah anda ingin menambahkan menjadi pasien baru ?');

                if (cfn) {
                    @this.set('callMasterPasien', true);
                }
            });




            // confirmation rePush_Data_Antrian_Confirmation
            window.livewire.on('rePush_Data_Antrian_Confirmation', () => {
                let cfn = confirm('Apakah anda ingin mengulaingi Proses Kirim data Antrian ?');

                if (cfn) {
                    // emit ke controller
                    window.livewire.emit('rePush_Data_Antrian');
                }
            });













            // press_dropdownButton flowbite
            window.Livewire.on('pressDropdownButton', (key) => {
                // set the dropdown menu element
                const $targetEl = document.getElementById('dropdownMenu' + key);

                // set the element that trigger the dropdown menu on click
                const $triggerEl = document.getElementById('dropdownButton' + key);

                // options with default values
                const options = {
                    placement: 'left',
                    triggerType: 'click',
                    offsetSkidding: 0,
                    offsetDistance: 10,
                    delay: 300,
                    onHide: () => {
                        console.log('dropdown has been hidden');

                    },
                    onShow: () => {
                        console.log('dropdown has been shown');
                    },
                    onToggle: () => {
                        console.log('dropdown has been toggled');
                    }
                };

                /*
                 * $targetEl: required
                 * $triggerEl: required
                 * options: optional
                 */
                const dropdown = new Dropdown($targetEl, $triggerEl, options);

                dropdown.show();

            });


            // press_dropdownButtonSendData flowbite
            window.Livewire.on('pressDropdownButtonSendData', (key) => {
                    // set the dropdown menu element
                    const $targetEl = document.getElementById('dropdownMenuSendData' + key);

                    // set the element that trigger the dropdown menu on click
                    const $triggerEl = document.getElementById('dropdownButtonSendData' + key);

                    // options with default values
                    const options = {
                        placement: 'left',
                        triggerType: 'click',
                        offsetSkidding: 0,
                        offsetDistance: 10,
                        delay: 300,
                        onHide: () => {
                            console.log('dropdown has been hidden');

                        },
                        onShow: () => {
                            console.log('dropdown has been shown');
                        },
                        onToggle: () => {
                            console.log('dropdown has been toggled');
                        }
                    };

                    /*
                     * $targetEl: required
                     * $triggerEl: required
                     * options: optional
                     */
                    const dropdown = new Dropdown($targetEl, $triggerEl, options);

                    dropdown.show();

                }

            );
        </script>
        <script>
            // $("#dateRjRef").change(function() {
            //     const datepickerEl = document.getElementById('dateRjRef');
            //     console.log(datepickerEl);
            // });
        </script>
        {{-- Global Livewire JavaScript Object end --}}
    @endpush













    @push('styles')
        {{-- stylesheet start --}}
        <link rel="stylesheet" href="{{ url('assets/plugins/toastr/toastr.min.css') }}">

        {{-- stylesheet end --}}
    @endpush
    {{-- push end --}}
</div>
