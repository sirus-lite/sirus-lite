<div>

    {{-- Start Coding  --}}

    {{-- Canvas
    Main BgColor /
    Size H/W --}}
    <div class="w-full h-[calc(100vh-68px)] bg-white border border-gray-200 px-4 pt-6">

        {{-- Title  --}}
        <div class="mb-2">
            <h3 class="text-3xl font-bold text-gray-900 ">{{ $myTitle }}</h3>
            <span class="text-base font-normal text-gray-700">{{ $mySnipt }}</span>
        </div>
        {{-- Title --}}

        {{-- Top Bar --}}
        <div class="flex justify-between">

            <div class="flex w-full">
                {{-- Cari Data --}}
                <div class="relative w-1/3 mr-2 pointer-events-auto">
                    <div class="absolute inset-y-0 left-0 flex items-center p-5 pl-3 pointer-events-none ">
                        <svg width="24" height="24" fill="none" aria-hidden="true" class="flex-none mr-3 ">
                            <path d="m19 19-3.5-3.5" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round"></path>
                            <circle cx="11" cy="11" r="6" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round"></circle>
                        </svg>
                    </div>

                    <x-text-input type="text" class="w-full p-2 pl-10" placeholder="Cari Data" autofocus
                        wire:model="refFilter" />
                </div>
                {{-- Cari Data --}}

                {{-- Tanggal --}}
                <div class="relative w-[150px] mr-2">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <svg aria-hidden="true" class="w-5 h-5 text-gray-900 dark:text-gray-400" fill="currentColor"
                            viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd"
                                d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
                                clip-rule="evenodd"></path>
                        </svg>
                    </div>

                    <x-text-input type="text" class="p-2 pl-10 " placeholder="[dd/mm/yyyy]"
                        wire:model="myTopBar.refDate" />
                </div>
                {{-- Tanggal --}}

                {{-- Shift --}}
                {{-- <div class="relative w-[75px]">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <svg class="w-5 h-5 text-gray-800 " aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                            <path
                                d="M1 5h1.424a3.228 3.228 0 0 0 6.152 0H19a1 1 0 1 0 0-2H8.576a3.228 3.228 0 0 0-6.152 0H1a1 1 0 1 0 0 2Zm18 4h-1.424a3.228 3.228 0 0 0-6.152 0H1a1 1 0 1 0 0 2h10.424a3.228 3.228 0 0 0 6.152 0H19a1 1 0 0 0 0-2Zm0 6H8.576a3.228 3.228 0 0 0-6.152 0H1a1 1 0 0 0 0 2h1.424a3.228 3.228 0 0 0 6.152 0H19a1 1 0 0 0 0-2Z" />
                        </svg>
                    </div>

                    <x-text-input type="text" class="w-full p-2 pl-10 " placeholder="[Shift 1/2/3]"
                        wire:model="myTopBar.refShiftId" />
                </div> --}}
                {{-- Shift --}}

                {{-- Status Transaksi --}}
                <div class="flex ml-2">
                    @foreach ($myTopBar['refStatusOptions'] as $refStatus)
                        {{-- @dd($refStatus) --}}
                        <x-radio-button :label="__($refStatus['refStatusDesc'])" value="{{ $refStatus['refStatusId'] }}"
                            wire:model="myTopBar.refStatusId" />
                    @endforeach
                </div>
                {{-- Status Transaksi --}}

                {{-- Dokter --}}
                <div>
                    <x-dropdown align="right" :width="__('80')" :contentclasses="__('overflow-auto max-h-[150px] py-1 bg-white dark:bg-gray-700')">
                        <x-slot name="trigger">
                            {{-- Button Dokter --}}
                            <x-alternative-button class="inline-flex whitespace-nowrap">
                                <svg class="-ml-1 mr-1.5 w-5 h-5" fill="currentColor" viewbox="0 0 20 20"
                                    xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                    <path clip-rule="evenodd" fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" />
                                </svg>
                                <span>{{ $myTopBar['drName'] }}</span>
                            </x-alternative-button>
                        </x-slot>
                        {{-- Open shiftcontent --}}
                        <x-slot name="content">

                            @foreach ($myTopBar['drOptions'] as $dr)
                                <x-dropdown-link
                                    wire:click="settermyTopBardrOptions('{{ $dr['drId'] }}','{{ $dr['drName'] }}')">
                                    {{ __($dr['drName']) }}
                                </x-dropdown-link>
                            @endforeach
                        </x-slot>
                    </x-dropdown>
                </div>




            </div>



            <div class="flex justify-end w-1/2">
                <div class="flex ml-2">
                    <p class="text-sm">AutoRefresh :</p>
                    @foreach ($myTopBar['autoRefreshOptions'] as $autoRefresh)
                        <x-radio-button :label="__($autoRefresh['autoRefresh'])" value="{{ $autoRefresh['autoRefresh'] }}"
                            wire:model="myTopBar.autoRefresh" />
                    @endforeach
                </div>


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
                            <x-dropdown-link wire:click="$set('limitPerPage', '{{ $myLimitPerPage }}')">
                                {{ __($myLimitPerPage) }}
                            </x-dropdown-link>
                        @endforeach
                    </x-slot>
                </x-dropdown>
            </div>


            @if ($isOpenAdministrasi)
                @include('livewire.emr-r-j.telaah-resep-r-j.create-administrasi-rj')
            @endif

            @if ($isOpenTelaahResep)
                @include('livewire.emr-r-j.telaah-resep-r-j.create-telaahresep-rj')
            @endif



        </div>
        {{-- Top Bar --}}


        @if ($myTopBar['autoRefresh'] == 'Ya')
            <div wire:poll.20s="render" class="h-[calc(100vh-250px)] mt-2 overflow-auto">
            @else
                <div class="h-[calc(100vh-250px)] mt-2 overflow-auto">
        @endif

        <p class="text-xs">Data Terakhir: {{ now()->format('d-m-y H:i:s') }}</p>
        <!-- Table -->
        <table class="w-full text-sm text-left text-gray-700 table-auto ">
            <thead class="sticky top-0 text-xs text-gray-900 uppercase bg-gray-100 ">
                <tr>
                    <th scope="col" class="w-1/4 px-4 py-3 ">
                        Pasien
                    </th>

                    <th scope="col" class="w-1/4 px-4 py-3 ">
                        Poli
                    </th>
                    <th scope="col" class="w-1/4 px-2 py-3 ">
                        Status Layanan
                    </th>
                    <th scope="col" class="w-1/4 px-4 py-3 ">
                        Action
                    </th>
                </tr>
            </thead>

            <tbody class="bg-white ">

                @foreach ($myQueryData as $myQData)
                    @php
                        $datadaftar_json = json_decode($myQData->datadaftarpolirj_json, true);

                        $eresep = isset($datadaftar_json['eresep']) ? 1 : 0;
                        $noAntrianFarmasi = isset($datadaftar_json['noAntrianApotek']['noAntrian'])
                            ? $datadaftar_json['noAntrianApotek']['noAntrian']
                            : 0;

                        $eresepRacikan = collect(
                            isset($datadaftar_json['eresepRacikan']) ? $datadaftar_json['eresepRacikan'] : [],
                        )->count();
                        $jenisResep = $eresepRacikan ? 'racikan' : 'non racikan';

                        $prosentaseEMR = ($eresep / 1) * 100;

                        $badgecolorStatus = isset($myQData->rj_status)
                            ? ($myQData->rj_status === 'A'
                                ? 'red'
                                : ($myQData->rj_status === 'L'
                                    ? 'green'
                                    : ($myQData->rj_status === 'I'
                                        ? 'green'
                                        : ($myQData->rj_status === 'F'
                                            ? 'yellow'
                                            : 'default'))))
                            : '';

                        $badgecolorEresep = $eresep ? 'green' : 'red';

                        $badgecolorKlaim =
                            $myQData->klaim_id == 'UM'
                                ? 'green'
                                : ($myQData->klaim_id == 'JM'
                                    ? 'default'
                                    : ($myQData->klaim_id == 'KR'
                                        ? 'yellow'
                                        : 'red'));

                        $badgecolorAdministrasiRj = isset($datadaftar_json['AdministrasiRj']) ? 'green' : 'red';

                        $taskId5 = $datadaftar_json['taskIdPelayanan']['taskId5'] ?? 'xxxx-xx-xx xx:xx:xx';
                        $taskId6 = $datadaftar_json['taskIdPelayanan']['taskId6'] ?? 'xxxx-xx-xx xx:xx:xx';
                        $taskId7 = $datadaftar_json['taskIdPelayanan']['taskId7'] ?? 'xxxx-xx-xx xx:xx:xx';

                        $telaahResepStatus = isset($datadaftar_json['telaahResep']['penanggungJawab'])
                            ? ($datadaftar_json['telaahResep']['penanggungJawab']
                                ? true
                                : false)
                            : false;
                        $telaahObatStatus = isset($datadaftar_json['telaahObat']['penanggungJawab'])
                            ? ($datadaftar_json['telaahObat']['penanggungJawab']
                                ? true
                                : false)
                            : false;

                        $statusPRB = $datadaftar_json['statusPRB']['penanggungJawab']['statusPRB'] ?? 0;
                    @endphp


                    <tr class="border-b group ">


                        <td class="px-4 py-3 group-hover:bg-gray-100 whitespace-nowrap ">
                            <div class="">
                                <div>
                                    <p>Antrian Farmasi
                                        <span class="text-5xl font-semibold text-gray-700">
                                            {{ $noAntrianFarmasi }}
                                        </span>
                                    </p>
                                </div>
                                <div class="font-semibold text-primary">
                                    {{ $myQData->reg_no }}
                                </div>
                                <div class="font-semibold text-gray-900">
                                    {{ $myQData->reg_name . ' / (' . $myQData->sex . ')' . ' / ' . $myQData->thn }}
                                </div>
                                <div class="font-normal text-gray-700">
                                    {{ $myQData->address }}
                                </div>
                            </div>
                        </td>

                        <td class="px-4 py-3 group-hover:bg-gray-100 whitespace-nowrap ">
                            <div class="">
                                <div class="font-semibold text-primary">
                                    {{ $myQData->poli_desc }}
                                </div>
                                <div class="font-semibold text-gray-900">
                                    {{ $myQData->dr_name . ' / ' }}
                                    <x-badge :badgecolor="__($badgecolorKlaim)">
                                        {{ $myQData->klaim_id == 'UM'
                                            ? 'UMUM'
                                            : ($myQData->klaim_id == 'JM'
                                                ? 'BPJS'
                                                : ($myQData->klaim_id == 'KR'
                                                    ? 'Kronis'
                                                    : 'Asuransi Lain')) }}
                                    </x-badge>
                                </div>
                                <div class="font-normal">
                                    {{ $myQData->vno_sep }}
                                </div>

                                <div class="flex my-2 space-x-2">
                                    @if ($myQData->lab_status)
                                        <x-badge :badgecolor="__('default')">
                                            {{ 'Laborat' }}
                                        </x-badge>
                                    @endif

                                    @if ($myQData->rad_status)
                                        <x-badge :badgecolor="__('default')">
                                            {{ 'Radiologi' }}
                                        </x-badge>
                                    @endif
                                </div>

                                <div>
                                    @if ($statusPRB)
                                        <x-badge :badgecolor="__('red')">
                                            {{ 'PRB' }}
                                        </x-badge>
                                    @endif

                                </div>


                            </div>
                        </td>

                        <td class="px-4 py-3 group-hover:bg-gray-100 whitespace-nowrap ">
                            <div class="w-full overflow-auto">

                                <div class="font-semibold text-gray-900">
                                    {{ 'Nomer Pelayanan : ' . $myQData->rj_no }}
                                </div>
                                <div class = "flex space-x-1">
                                    <x-badge :badgecolor="__($badgecolorStatus)">
                                        {{ isset($myQData->rj_status)
                                            ? ($myQData->rj_status === 'A'
                                                ? 'Pelayanan'
                                                : ($myQData->rj_status === 'L'
                                                    ? 'Selesai Pelayanan'
                                                    : ($myQData->rj_status === 'I'
                                                        ? 'Transfer Inap'
                                                        : ($myQData->rj_status === 'F'
                                                            ? 'Batal Transaksi'
                                                            : ''))))
                                            : '' }}
                                    </x-badge>
                                    <x-badge :badgecolor="__($badgecolorEresep)">
                                        E-Resep: {{ $prosentaseEMR . '%' }}
                                    </x-badge>

                                    @php
                                        // Ambil statusResep, default: kosong
                                        $statusResep = $datadaftar_json['statusResep']['status'] ?? 'DITUNGGU';
                                        // Map status ke warna badge
                                        $badgeColors = [
                                            'DITUNGGU' => 'green',
                                            'DITINGGAL' => 'yellow',
                                        ];
                                        $badgeColorStatusResep = $badgeColors[$statusResep] ?? 'gray';
                                        // Label yang ingin ditampilkan
                                        $labelStatusResep =
                                            $statusResep === 'DITUNGGU'
                                                ? 'Ditunggu'
                                                : ($statusResep === 'DITINGGAL'
                                                    ? 'Ditinggal'
                                                    : '');
                                    @endphp

                                    {{-- Badge Status Resep (hanya tampil kalau sudah diset) --}}
                                    <x-badge :badgecolor="__($badgeColorStatusResep)">
                                        {{ $labelStatusResep }}
                                    </x-badge>
                                </div>



                                <div>


                                </div>

                                <div class="font-normal text-gray-700">
                                    {{ $myQData->rj_date }}
                                    {{ '| Shift : ' . $myQData->shift }}
                                </div>

                                <div>
                                    @if ($jenisResep == 'racikan' && $eresep > 0)
                                        <x-badge :badgecolor="__('default')"> {{ $jenisResep }}</x-badge>
                                    @elseif($jenisResep == 'non racikan' && $eresep > 0)
                                        <x-badge :badgecolor="__('green')"> {{ $jenisResep }}</x-badge>
                                    @else
                                        <x-badge :badgecolor="__('red')"> {{ '---' }}</x-badge>
                                    @endif
                                </div>

                                <div class="font-normal text-gray-700">
                                    {{ '' . $myQData->nobooking }}
                                </div>

                                <div class="font-normal text-gray-700">
                                    <x-badge :badgecolor="__($badgecolorAdministrasiRj)">
                                        Administrasi :
                                        @isset($datadaftar_json['AdministrasiRj'])
                                            {{ $datadaftar_json['AdministrasiRj']['userLog'] }}
                                        @else
                                            {{ '---' }}
                                        @endisset
                                    </x-badge>
                                </div>

                                <div class="italic font-normal text-gray-900">
                                    {{ 'TaskId5 ' . $taskId5 }}
                                </div>
                                <div class="italic font-normal text-gray-900">
                                    {{ 'TaskId6 ' . $taskId6 }}
                                </div>
                                <div class="italic font-normal text-gray-900">
                                    {{ 'TaskId7 ' . $taskId7 }}
                                </div>


                            </div>
                        </td>

                        <td class="px-4 py-3 group-hover:bg-gray-100 group-hover:text-primary">

                            <div class="grid grid-cols-2 gap-2">

                                <x-red-button wire:click="masukApotek('{{ $myQData->rj_no }}')">
                                    Mulai Pelayanan Apotek
                                </x-red-button>
                                <x-red-button wire:click="keluarApotek('{{ $myQData->rj_no }}')">
                                    Selesai Pelayanan Apotek
                                </x-red-button>

                            </div>

                            <div class="grid grid-cols-2 gap-2">

                                @if ($telaahResepStatus && $telaahObatStatus)
                                    <x-green-button
                                        wire:click="editTelaahResep('{{ $eresep }}','{{ $myQData->rj_no }}','{{ $myQData->reg_no }}')">Telaah
                                        Resep</x-green-button>
                                @else
                                    <x-light-button
                                        wire:click="editTelaahResep('{{ $eresep }}','{{ $myQData->rj_no }}','{{ $myQData->reg_no }}')">Telaah
                                        Resep</x-light-button>
                                @endif

                                <x-green-button
                                    wire:click="editAdministrasi('{{ $myQData->rj_no }}','{{ $myQData->reg_no }}')">Admin
                                    RJ
                                </x-green-button>

                            </div>
                            <div>
                                <livewire:cetak.cetak-eresep-r-j :rjNoRef="$myQData->rj_no"
                                    wire:key="cetak.cetak-eresep-r-j-{{ $myQData->rj_no }}">

                            </div>


                        </td>
                    </tr>
                @endforeach

            </tbody>
        </table>
        {{-- no data found start --}}
        @if ($myQueryData->count() == 0)
            <div class="w-full p-4 text-sm text-center text-gray-900 dark:text-gray-400">
                {{ 'Data ' . $myProgram . ' Tidak ditemukan' }}
            </div>
        @endif
        {{-- no data found end --}}

    </div>


    {{ $myQueryData->links() }}








</div>



{{-- Canvas
    Main BgColor /
    Size H/W --}}

{{-- End Coding --}}




















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
        toastr.options = {
            "closeButton": false,
            "debug": false,
            "newestOnTop": false,
            "progressBar": false,
            "positionClass": "toast-top-left",
            "preventDuplicates": false,
            "onclick": null,
            "showDuration": "300",
            "hideDuration": "1000",
            "timeOut": "5000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        }

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

    {{-- Global Livewire JavaScript Object start --}}
    <script type="text/javascript">
        // confirmation message doble record
        window.livewire.on('confirm_doble_recordUGD', (key, name) => {
            console.log('x')
            let cfn = confirm('Pasien Sudah terdaftar, Apakah anda ingin tetap menyimpan data ini ' + name + '?');

            if (cfn) {
                window.livewire.emit('confirm_doble_record_UGDp', key, name);
            }
        });
    </script>
@endpush













@push('styles')
    {{-- stylesheet start --}}
    <link rel="stylesheet" href="{{ url('assets/plugins/toastr/toastr.min.css') }}">

    {{-- stylesheet end --}}
@endpush
{{-- push end --}}

</div>
