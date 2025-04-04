<div>
    @php
        $headerResep = isset($dataDaftarRi['eresepHdr'][$resepIndexRef]['tandaTanganDokter']['dokterPeresep']);
        $disabledPropertyResepTtdDokter = $headerResep ? true : false;
        $headerResepTtd = isset($dataDaftarRi['eresepHdr'][$resepIndexRef]['tandaTanganDokter']['dokterPeresep']);
    @endphp
    <div class="w-full mb-1">
        <div id="TransaksiRawatInap" class="p-2">
            <div class="p-2 rounded-lg bg-gray-50">
                <div id="TransaksiRawatInap" class="px-4">
                    @role(['Dokter', 'Admin'])
                        {{-- Jika belum ada produk yang dipilih --}}
                        @if (empty($headerResepTtd))
                            @if (empty($collectingMyProduct))
                                <div>
                                    @include('livewire.component.l-o-v.list-of-value-product.list-of-value-product')
                                </div>
                            @else
                                {{-- Jika produk sudah dipilih, tampilkan data produk yang sudah diambil --}}
                                <div class="flex items-baseline space-x-2" x-data>
                                    <!-- Hidden field untuk productId -->
                                    <div class="hidden">
                                        <x-input-label for="formEntryEresepRINonRacikan.productId" :value="__('Kode Obat')"
                                            :required="true" />
                                        <x-text-input id="formEntryEresepRINonRacikan.productId" placeholder="Kode Obat"
                                            class="mt-1 ml-2" :errorshas="__($errors->has('formEntryEresepRINonRacikan.productId'))" :disabled="true"
                                            wire:model="formEntryEresepRINonRacikan.productId" />
                                        @error('formEntryEresepRINonRacikan.productId')
                                            <x-input-error :messages="$message" />
                                        @enderror
                                    </div>
                                    <!-- Nama Obat -->
                                    <div class="basis-3/6">
                                        <x-input-label for="formEntryEresepRINonRacikan.productName" :value="__('Nama Obat')"
                                            :required="true" />
                                        <x-text-input id="formEntryEresepRINonRacikan.productName" placeholder="Nama Obat"
                                            class="mt-1 ml-2" :errorshas="__($errors->has('formEntryEresepRINonRacikan.productName'))" :disabled="true"
                                            wire:model="formEntryEresepRINonRacikan.productName" />
                                        @error('formEntryEresepRINonRacikan.productName')
                                            <x-input-error :messages="$message" />
                                        @enderror
                                    </div>
                                    <!-- Jumlah -->
                                    <div class="basis-1/12">
                                        <x-input-label for="formEntryEresepRINonRacikan.qty" :value="__('Jml')"
                                            :required="true" />
                                        <x-text-input id="formEntryEresepRINonRacikan.qty" placeholder="Jml Obat"
                                            class="mt-1 ml-2" :errorshas="__($errors->has('formEntryEresepRINonRacikan.qty'))" :disabled="$disabledPropertyResepTtdDokter"
                                            wire:model="formEntryEresepRINonRacikan.qty" x-ref="collectingMyProductQty"
                                            x-on:keyup.enter="$nextTick(() => {
                                                if($refs.collectingMyProductSignaX){
                                                    $refs.collectingMyProductSignaX.focus();
                                                }
                                            })" />
                                        @error('formEntryEresepRINonRacikan.qty')
                                            <x-input-error :messages="$message" />
                                        @enderror
                                    </div>
                                    <!-- Signa1 -->
                                    <div class="basis-1/12">
                                        <x-input-label for="formEntryEresepRINonRacikan.signaX" :value="__('Signa')"
                                            :required="false" />
                                        <x-text-input id="formEntryEresepRINonRacikan.signaX" placeholder="Signa1"
                                            class="mt-1 ml-2" :errorshas="__($errors->has('formEntryEresepRINonRacikan.signaX'))" :disabled="$disabledPropertyResepTtdDokter"
                                            wire:model="formEntryEresepRINonRacikan.signaX"
                                            x-ref="collectingMyProductSignaX"
                                            x-on:keyup.enter="$nextTick(() => {
                                                if($refs.collectingMyProductSignaHari){
                                                    $refs.collectingMyProductSignaHari.focus();
                                                }
                                            })" />
                                        @error('formEntryEresepRINonRacikan.signaX')
                                            <x-input-error :messages="$message" />
                                        @enderror
                                    </div>
                                    <!-- Signa2 -->
                                    <div class="basis-1/12">
                                        <x-input-label for="formEntryEresepRINonRacikan.signaHari" :value="__('Signa2')"
                                            :required="false" />
                                        <x-text-input id="formEntryEresepRINonRacikan.signaHari" placeholder="Signa2"
                                            class="mt-1 ml-2" :errorshas="__($errors->has('formEntryEresepRINonRacikan.signaHari'))" :disabled="$disabledPropertyResepTtdDokter"
                                            wire:model="formEntryEresepRINonRacikan.signaHari"
                                            x-ref="collectingMyProductSignaHari"
                                            x-on:keyup.enter="$nextTick(() => {
                                                if($refs.collectingMyProductCatatanKhusus){
                                                    $refs.collectingMyProductCatatanKhusus.focus();
                                                }
                                            })" />
                                        @error('formEntryEresepRINonRacikan.signaHari')
                                            <x-input-error :messages="$message" />
                                        @enderror
                                    </div>
                                    <!-- Catatan Khusus -->
                                    <div class="basis-3/6">
                                        <x-input-label for="formEntryEresepRINonRacikan.catatanKhusus" :value="__('Catatan Khusus')"
                                            :required="false" />
                                        <x-text-input id="formEntryEresepRINonRacikan.catatanKhusus"
                                            placeholder="Catatan Khusus" class="mt-1 ml-2" :errorshas="__($errors->has('formEntryEresepRINonRacikan.catatanKhusus'))"
                                            :disabled="$disabledPropertyResepTtdDokter" wire:model="formEntryEresepRINonRacikan.catatanKhusus"
                                            x-ref="collectingMyProductCatatanKhusus"
                                            x-on:keyup.enter="$wire.insertProduct(); $nextTick(() => {
                                                if($refs.collectingMyProductQty){
                                                    $refs.collectingMyProductQty.focus();
                                                }
                                            })" />
                                        @error('formEntryEresepRINonRacikan.catatanKhusus')
                                            <x-input-error :messages="$message" />
                                        @enderror
                                    </div>
                                    <!-- Tombol Hapus -->
                                    <div class="basis-1/6">
                                        <x-input-label for="" :value="__('Hapus')" :required="false" />
                                        <x-alternative-button class="inline-flex ml-2"
                                            wire:click="resetFormEntryEresepRINonRacikan()">
                                            <svg class="w-5 h-5 text-gray-800 dark:text-white" aria-hidden="true"
                                                xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 18 20">
                                                <path
                                                    d="M17 4h-4V2a2 2 0 0 0-2-2H7a2 2 0 0 0-2 2v2H1a1 1 0 0 0 0 2h1v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V6h1a1 1 0 1 0 0-2ZM7 2h4v2H7V2Zm1 14a1 1 0 1 1-2 0V8a1 1 0 0 1 2 0v8Zm4 0a1 1 0 0 1-2 0V8a1 1 0 0 1 2 0v8Z" />
                                            </svg>
                                        </x-alternative-button>
                                    </div>
                                </div>
                            @endif
                        @endif
                    @endrole

                    {{-- Tampilkan Tabel Data Non Racikan yang sudah ditambahkan --}}
                    <div class="flex flex-col my-2">
                        <div class="overflow-x-auto rounded-lg">
                            <div class="inline-block min-w-full align-middle">
                                <div class="overflow-hidden shadow sm:rounded-lg">
                                    <table class="w-full text-sm text-left text-gray-500 table-auto dark:text-gray-400">
                                        <thead
                                            class="text-xs text-gray-700 uppercase bg-gray-100 dark:bg-gray-700 dark:text-gray-400">
                                            <tr>
                                                <th scope="col" class="px-4 py-3">
                                                    <x-sort-link :active="false" role="button" href="#">
                                                        Non Racikan
                                                    </x-sort-link>
                                                </th>
                                                <th scope="col" class="px-4 py-3">
                                                    <x-sort-link :active="false" role="button" href="#">
                                                        Obat
                                                    </x-sort-link>
                                                </th>
                                                <th scope="col" class="w-8 px-4 py-3 text-center">
                                                    Action
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white dark:bg-gray-800">
                                            @isset($dataDaftarRi['eresepHdr'][$resepIndexRef]['eresep'])
                                                @foreach ($dataDaftarRi['eresepHdr'][$resepIndexRef]['eresep'] as $key => $eresep)
                                                    <tr wire:key="non-racikan-{{ $resepIndexRef }}-{{ $key }}"
                                                        class="border-b group dark:border-gray-700">
                                                        <td
                                                            class="px-4 py-3 font-normal text-gray-700 group-hover:bg-gray-50 whitespace-nowrap dark:text-white">
                                                            {{ $eresep['jenisKeterangan'] }}
                                                        </td>
                                                        <td
                                                            class="px-4 py-3 font-normal text-gray-700 group-hover:bg-gray-50 whitespace-nowrap dark:text-white">
                                                            @php
                                                                $catatanKhusus = $eresep['catatanKhusus']
                                                                    ? ' (' . $eresep['catatanKhusus'] . ')'
                                                                    : '';
                                                            @endphp
                                                            {{ 'R/' . ' ' . $eresep['productName'] . ' | No. ' . $eresep['qty'] . ' | S ' . $eresep['signaX'] . 'dd' . $eresep['signaHari'] . $catatanKhusus }}
                                                        </td>

                                                        <td
                                                            class="px-4 py-3 font-normal text-gray-700 group-hover:bg-gray-50 whitespace-nowrap dark:text-white">
                                                            @role(['Dokter', 'Admin'])
                                                                <x-alternative-button class="inline-flex" :disabled="$disabledPropertyResepTtdDokter"
                                                                    wire:click="removeProduct('{{ $eresep['riObatDtl'] }}','{{ $resepIndexRef }}')">
                                                                    <svg class="w-5 h-5 text-gray-800 dark:text-white"
                                                                        aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                                                        fill="currentColor" viewBox="0 0 18 20">
                                                                        <path
                                                                            d="M17 4h-4V2a2 2 0 0 0-2-2H7a2 2 0 0 0-2 2v2H1a1 1 0 0 0 0 2h1v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V6h1a1 1 0 1 0 0-2ZM7 2h4v2H7V2Zm1 14a1 1 0 1 1-2 0V8a1 1 0 0 1 2 0v8Zm4 0a1 1 0 0 1-2 0V8a1 1 0 0 1 2 0v8Z" />
                                                                    </svg>
                                                                </x-alternative-button>
                                                            @endrole
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @endisset

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        {{-- End Tabel --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
