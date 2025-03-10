<div class="">



    <div class="px-1 pt-7 ">
        <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm">
            <div class="w-full h-screen mb-1 overflow-y-auto">

                <div class="md:flex md:justify-between">

                    {{-- search --}}

                    {{-- end search --}}

                    {{-- two button --}}
                    <div class="flex justify-between mt-2 md:mt-0">
                        <div wire:loading wire:target="updateDataHfisBpjsToRsAll">
                            <x-loading />
                        </div>
                        <x-primary-button wire:click="updateDataHfisBpjsToRsAll()" class="flex justify-center flex-auto"
                            wire:loading.remove>
                            <svg class="w-5 h-5 mr-2 -ml-1" fill="currentColor" viewBox="0 0 20 20"
                                xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd"
                                    d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            Update {{ $myProgram }}
                        </x-primary-button>

                    </div>
                    {{-- end two button --}}

                </div>


                <div id="TopBarMenuMaster" class="">

                    <div class="mb-2">
                        <h3 class="text-2xl font-bold text-midnight dark:text-white">{{ $myTitle }}</h3>
                        <span class="text-base font-normal text-gray-500 dark:text-gray-400">{{ $mySnipt }}</span>
                    </div>


                    <div class="flex justify-between">

                        <div class="w-full">
                            <label for="mobile-search" class="sr-only">Search</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-500" fill="currentColor" viewBox="0 0 20 20"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd"
                                            d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <x-text-input type="text" class="pl-10 p-2.5" placeholder="Cari Data"
                                    wire:model="search" autofocus />

                                <div wire:loading wire:target="search">
                                    <x-loading />
                                </div>

                                @include('livewire.setup-hfis-bpjs.list-of-value-hfis-bpjs')
                            </div>

                            <div class="relative mt-2 ">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <svg aria-hidden="true" class="w-5 h-5 text-gray-900 dark:text-gray-400"
                                        fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd"
                                            d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                </div>


                                <x-text-input id="dateRef" datepicker datepicker-autohide
                                    datepicker-format="dd/mm/yyyy" type="text" class="p-2 pl-10 sm:w-1/6"
                                    placeholder="dd/mm/yyyy" wire:model.lazy="dateRef" />
                            </div>

                        </div>
                    </div>
                </div>



                <!-- Table -->
                <div class="flex flex-col mt-6">
                    <div class="overflow-x-auto rounded-lg">
                        <div class="inline-block min-w-full align-middle">
                            <div class="overflow-hidden shadow sm:rounded-lg">
                                <table class="w-full text-sm text-left text-gray-500 table-auto dark:text-gray-400">
                                    <thead
                                        class="text-xs text-gray-700 uppercase bg-gray-100 dark:bg-gray-700 dark:text-gray-400">
                                        <tr>

                                            <th scope="col" class="w-1/4 px-4 py-3">

                                                <x-sort-link :active=false wire:click.prevent="" role="button"
                                                    href="#">
                                                    Dokter
                                                </x-sort-link>

                                            </th>

                                            <th scope="col" class="w-1/3 px-4 py-3">

                                                <x-sort-link :active=false wire:click.prevent="" role="button"
                                                    href="#">
                                                    Jadwal Hfiz
                                                </x-sort-link>

                                            </th>

                                            <th scope="col" class="w-1/3 px-4 py-3">

                                                <x-sort-link :active=false wire:click.prevent="" role="button"
                                                    href="#">
                                                    Status Kirim Antrian
                                                </x-sort-link>

                                            </th>




                                            <th scope="col" class="w-8 px-4 py-3 text-center">Action
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white ">


                                        @foreach ($jadwal_dokter as $jd)
                                            <tr class="border-b group">
                                                <td
                                                    class="w-1/4 px-4 py-3 font-medium group-hover:bg-gray-50 whitespace-nowrap dark:text-white">
                                                    <div class="pl-0">
                                                        <div class="font-semibold text-gray-700 ">
                                                            {{ $jd['namadokter'] }}</div>
                                                        <div class="font-semibold text-primary">
                                                            {{ $jd['kodedokter'] . ' / (' . $jd['kodesubspesialis'] . ')' }}
                                                        </div>
                                                        <div class="font-normal text-gray-700">
                                                            {{ $jd['namapoli'] }}
                                                        </div>
                                                    </div>
                                                </td>

                                                <td
                                                    class="w-1/3 px-4 py-3 font-medium group-hover:bg-gray-50 whitespace-nowrap dark:text-white">
                                                    <div class="pl-0">
                                                        <div class="font-semibold text-gray-700 ">
                                                            {{ 'Kapasitas : ' . $jd['kapasitaspasien'] }}</div>
                                                        <div class="font-semibold text-primary">
                                                            {{ $jd['hari'] . ' / (Hari :' . $jd['namahari'] . ')' }}
                                                        </div>
                                                        <div class="font-normal text-gray-700">
                                                            {{ $jd['jadwal'] }}
                                                        </div>
                                                    </div>
                                                </td>

                                                <td
                                                    class="w-1/3 px-4 py-3 font-medium group-hover:bg-gray-50 whitespace-nowrap dark:text-white">
                                                    <div class="pl-0">
                                                        <div class="font-semibold text-gray-700 ">
                                                            {{ 'Jml Pasien RS : ' . $jd['kapasitaspasien'] }}</div>
                                                        <div class="font-semibold text-primary">
                                                            {{ 'Antrian Lengkap : ' . $jd['hari'] . ' / (Hari :' . $jd['namahari'] . ')' }}
                                                        </div>
                                                        <div class="font-normal text-gray-700">
                                                            {{ 'Antrian Tidak Lengkap : ' . $jd['jadwal'] }}
                                                        </div>
                                                    </div>
                                                </td>


                                                <td
                                                    class="flex items-center justify-center px-4 py-3 group-hover:bg-gray-50 group-hover:text-blue-700">

                                                    <div>
                                                        <x-primary-button
                                                            wire:click.prevent="updateJadwalRS('{{ $jd['kodesubspesialis'] }}', '{{ $jd['kodedokter'] }}','{{ $jd['kodedokter'] }}', '{{ $jd['hari'] }}', '{{ $jd['jadwal'] }}', '{{ $jd['kapasitaspasien'] }}')"
                                                            type="button" wire:loading.remove>
                                                            Update Jadwal RS dari Hfis
                                                        </x-primary-button>
                                                        <div wire:loading wire:target="copyResep">
                                                            <x-loading />
                                                        </div>
                                                    </div>


                                                </td>
                                            </tr>
                                        @endforeach



                                    </tbody>
                                </table>



                                @if (count($jadwal_dokter) == 0)
                                    <div class="w-full p-4 text-sm text-center text-gray-500 dark:text-gray-400">
                                        {{ 'Data ' . $myProgram . ' Tidak ditemukan' }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="pb-2 mb-2 bg-gray-400 bg-opacity-25 rounded-lg">
                    {{-- Jadwal Aktif RS --}}
                    <div class="my-4 text-3xl font-bold text-center">
                        Jadwal Dokter {{ env('SATUSEHAT_ORGANIZATION_NAME') }}
                    </div>
                    <div class="grid grid-cols-2 gap-4 ">
                        @foreach ($myHari as $hari)
                            <table class="w-full text-sm text-left text-gray-500 table-auto dark:text-gray-400">
                                <thead
                                    class="text-xs text-gray-700 uppercase bg-gray-100 dark:bg-gray-700 dark:text-gray-400">
                                    <tr>

                                        <th scope="col" class="w-1/4 px-4 py-3">

                                            <x-sort-link :active=false wire:click.prevent="" role="button"
                                                href="#">
                                                {{ $hari['day_desc'] }}
                                            </x-sort-link>

                                        </th>

                                        <th scope="col" class="w-1/3 px-4 py-3">

                                            <x-sort-link :active=false wire:click.prevent="" role="button"
                                                href="#">
                                                Jadwal Hfiz
                                            </x-sort-link>

                                        </th>

                                        <th scope="col" class="w-1/3 px-4 py-3">

                                            <x-sort-link :active=false wire:click.prevent="" role="button"
                                                href="#">
                                                Kuota
                                            </x-sort-link>

                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white ">

                                    @foreach ($hari['jadwalDokter'] as $key => $jadwalDokter)
                                        <tr class="border-b group dark:border-gray-700">
                                            <td
                                                class="px-4 py-3 group-hover:bg-gray-100 whitespace-nowrap dark:text-white">
                                                <span class="font-semibold text-primary">
                                                    {{ $key + 1 . ' ' . $jadwalDokter['dr_name'] }}
                                                </span>
                                                </br>
                                                @if (in_array($jadwalDokter['poli_desc'], ['POLI UMUM', 'OK']))
                                                    {{ $jadwalDokter['poli_desc'] }}
                                                @else
                                                    <x-badge> {{ $jadwalDokter['poli_desc'] }}</x-badge>
                                                @endif

                                            </td>
                                            <td
                                                class="px-4 py-3 group-hover:bg-gray-100 whitespace-nowrap dark:text-white">
                                                <span
                                                    class="font-semibold text-gray-700">{{ $jadwalDokter['mulai_praktek'] . '-' . $jadwalDokter['selesai_praktek'] }}</span>
                                                </br>
                                                {{ 'Shift ' . $jadwalDokter['shift'] }}
                                            </td>
                                            <td
                                                class="px-4 py-3 group-hover:bg-gray-100 whitespace-nowrap dark:text-white">
                                                {{ 'Kuota ' . $jadwalDokter['kuota'] }}

                                            </td>
                                        </tr>
                                    @endforeach

                                </tbody>
                            </table>
                            {{-- <div>{{ $i }}</div> --}}
                        @endforeach

                    </div>
                </div>



                <div class="p-4 my-4 bg-red-100 rounded-lg">

                    <div class="my-4 text-3xl font-bold text-center text-red-500">
                        Dokter Blm Masuk Jadwal {{ env('SATUSEHAT_ORGANIZATION_NAME') }}
                    </div>
                    <div class="grid grid-cols-1 gap-4 ">
                        <table class="w-full text-sm text-left text-gray-500 table-auto dark:text-gray-400">
                            <thead
                                class="text-xs text-gray-700 uppercase bg-gray-100 dark:bg-gray-700 dark:text-gray-400">
                                <tr>

                                    <th scope="col" class="w-1/4 px-4 py-3">

                                        <x-sort-link :active=false wire:click.prevent="" role="button"
                                            href="#">
                                            Dokter
                                        </x-sort-link>

                                    </th>

                                    <th scope="col" class="w-1/3 px-4 py-3">

                                        <x-sort-link :active=false wire:click.prevent="" role="button"
                                            href="#">
                                            Jadwal
                                        </x-sort-link>

                                    </th>

                                    <th scope="col" class="w-1/3 px-4 py-3">

                                        <x-sort-link :active=false wire:click.prevent="" role="button"
                                            href="#">
                                            Kuota
                                        </x-sort-link>

                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white ">

                                @foreach ($myDokterBlmTerJadwalHari as $key => $dokterBlmTerJadwalHari)
                                    <tr class="border-b group dark:border-gray-700">
                                        <td
                                            class="px-4 py-3 group-hover:bg-gray-100 whitespace-nowrap dark:text-white">
                                            <span class="font-semibold text-red-500">
                                                {{ $key + 1 . ' ' . $dokterBlmTerJadwalHari['dr_name'] }}
                                            </span>
                                            </br>
                                            @if (in_array($dokterBlmTerJadwalHari['poli_desc'], ['POLI UMUM', 'OK']))
                                                {{ $dokterBlmTerJadwalHari['poli_desc'] }}
                                            @else
                                                <x-badge> {{ $dokterBlmTerJadwalHari['poli_desc'] }}</x-badge>
                                            @endif

                                        </td>
                                        <td
                                            class="px-4 py-3 group-hover:bg-gray-100 whitespace-nowrap dark:text-white">
                                            <span
                                                class="font-semibold text-gray-700">{{ '00:00:00' . '-' . '00:00:00' }}</span>
                                            </br>
                                            {{ 'Shift ' . 'xxx' }}
                                        </td>
                                        <td
                                            class="px-4 py-3 group-hover:bg-gray-100 whitespace-nowrap dark:text-white">
                                            {{ 'Kuota ' . '0' }}

                                        </td>
                                    </tr>
                                @endforeach

                            </tbody>
                        </table>


                    </div>
                </div>


            </div>
        </div>
    </div>




























    {{-- push start ///////////////////////////////// --}}
    @push('scripts')
        {{-- script start --}}
        <script src="{{ url('assets/js/jquery.min.js') }}"></script>
        <script src="{{ url('assets/plugins/toastr/toastr.min.js') }}"></script>
        {{-- script end --}}










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
                    window.livewire.emit('confirm_remove_record_jd', key, name);
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
