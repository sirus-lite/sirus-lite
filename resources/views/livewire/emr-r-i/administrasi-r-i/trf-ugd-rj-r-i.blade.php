<div>

    <div class="flex flex-col my-2">
        <div class="overflow-x-auto rounded-lg">
            <div class="inline-block min-w-full align-middle">
                <div class="overflow-hidden shadow sm:rounded-lg">
                    <table class="w-full text-sm text-left text-gray-500 table-auto dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-100 dark:bg-gray-700 dark:text-gray-400">
                            <tr>

                                <th scope="col" class="px-4 py-3 ">
                                    <x-sort-link :active=false wire:click.prevent="" role="button" href="#">
                                        Tgl
                                    </x-sort-link>
                                </th>

                                <th scope="col" class="px-4 py-3 ">
                                    <x-sort-link :active=false wire:click.prevent="" role="button" href="#">
                                        Trf UGD / RJ
                                    </x-sort-link>
                                </th>

                                <th scope="col" class="px-4 py-3 ">
                                    <x-sort-link :active=false wire:click.prevent="" role="button" href="#">
                                        Tarif
                                    </x-sort-link>
                                </th>

                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800">

                            @php
                                use Carbon\Carbon;

                                $sortedTrfUgdRj = collect($dataTrfUgdRj['riTrfUgdRj'] ?? [])
                                    ->sortByDesc(function ($item) {
                                        $date = $item['tempadm_date'] ?? '';
                                        // Jika kosong, anggap paling bawah
                                        if (!$date) {
                                            return 0;
                                        }
                                        try {
                                            // Parse dengan format d/m/Y H:i:s
                                            return Carbon::createFromFormat(
                                                'd/m/Y H:i:s',
                                                $date,
                                                env('APP_TIMEZONE'),
                                            )->timestamp;
                                        } catch (\Exception $e) {
                                            // Jika parsing gagal, juga jadikan paling bawah
                                            return 0;
                                        }
                                    })
                                    ->values();
                            @endphp

                            @if ($sortedTrfUgdRj->isNotEmpty())
                                @foreach ($sortedTrfUgdRj as $key => $TrfUgdRj)
                                    <tr class="border-b group dark:border-gray-700">

                                        <td
                                            class="px-4 py-3 font-normal text-gray-700 group-hover:bg-gray-50 whitespace-nowrap dark:text-white">
                                            {{ $TrfUgdRj['tempadm_date'] }}
                                        </td>

                                        <td
                                            class="px-4 py-3 font-normal text-gray-700 group-hover:bg-gray-50 whitespace-nowrap dark:text-white">
                                            {{ $TrfUgdRj['tempadm_flag'] }}
                                        </td>


                                        <td
                                            class="px-4 py-3 font-normal text-gray-700 group-hover:bg-gray-50 whitespace-nowrap dark:text-white">
                                            {{ number_format(
                                                $TrfUgdRj['rj_admin'] +
                                                    $TrfUgdRj['poli_price'] +
                                                    $TrfUgdRj['acte_price'] +
                                                    $TrfUgdRj['actp_price'] +
                                                    $TrfUgdRj['actd_price'] +
                                                    $TrfUgdRj['obat'] +
                                                    $TrfUgdRj['lab'] +
                                                    $TrfUgdRj['rad'] +
                                                    $TrfUgdRj['other'] +
                                                    $TrfUgdRj['rs_admin'],
                                            ) }}
                                        </td>

                                    </tr>
                                @endforeach
                            @endif


                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>

</div>
