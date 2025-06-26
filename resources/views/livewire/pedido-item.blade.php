<div>
    <!-- HEADER -->
    <x-header title="Lista de Itens" separator progress-indicator>
        <x-slot:middle class="!justify-end">
            <x-input placeholder="Pesquisar..." wire:model.live.debounce="search" clearable icon="o-magnifying-glass"/>
        </x-slot:middle>

        <x-slot:actions>
            <x-button label="Filters" @click="$wire.drawer = true" responsive icon="o-funnel" class="btn-primary"/>
        </x-slot:actions>
    </x-header>

    <!-- TABLE  -->

    @foreach($items as $item)
        <x-card :separator="true" class="mt-5">
            <div class="flex">
                <div>
                    <div class="flex">
                        <div class="mr-5">
                            <a href="{{route('impressao', ['idpedido' => $item['id']])}}"><x-badge value="{{$item['id']}}" class="badge-primary"/></a>
                        </div>
                        <div class="ml-5">

                            Deposito: {{$item['u_dplocpdr']}}
                            <br>
                            Item Code: {{$item['raw']['itemcode']}}
                            <br>
                            Qtd: {{$item['raw']['quantity']}}
                            <br>
                            Etiquetas: {{$item['raw']['U_DPQtdade'] ?? ''}}
                        </div>
                    </div>
                </div>
                <div>
                    <button wire:click="locarItem({{ json_encode($item) }})" spinner
                            class="btn-ghost btn btn-lg text-success">
                        <i class="fas fa-arrow-alt-circle-right"></i>
                    </button>
                </div>
            </div>
        </x-card>
    @endforeach

    <!-- FILTER DRAWER -->
    <x-drawer wire:model="drawer" title="Filters" right separator with-close-button class="lg:w-1/1">
        <x-input placeholder="Pesquisar..." wire:model.live.debounce="search" icon="o-magnifying-glass"
                 @keydown.enter="$wire.drawer = false"/>

        <x-slot:actions>
            <x-button label="Limpar" icon="o-x-mark" wire:click="clear" spinner/>
            <x-button label="Filtrar" icon="o-check" class="btn-primary" @click="$wire.drawer = false"/>
        </x-slot:actions>
    </x-drawer>

    <x-drawer wire:model="loc" title="Alocar Item" left separator with-close-button class="w-full">

        @if($loc)
            <div class="justify-center flex my-3">
                <x-barcodevanilla wire:model="barcode"></x-barcodevanilla>
            </div>
        @endif

        <x-input class="mt-3" placeholder="Codigo de Barra" wire:model="barcode" @keydown.enter="$wire.drawer = false"/>
        <div class="flex mt-4 justify-end">
            <x-button label="Codigo de Barra" icon="o-qr-code" class="btn-primary mr-5" wire:click="startScanning"/>
            <x-button label="Procurar" wire:click="SendAlocacao({{json_encode($alocaItem)}})" icon="o-check" class="btn-primary"/>
        </div>

        @if(count($lotesAlocar) > 0)

            @php
                $count = 0;
            @endphp
            <x-card>
                
                <div>
                    <p class="badge">Item Code: {{$lotesAlocar['dpItens']['ItemLote']}}</p>
                    <p class="badge">Quantidade: {{$alocaItem['raw']['quantity']}}</p>
                </div>
                <x-table :headers="$headersAlocar" :rows="$lotesAlocar['lotes']">
                    @foreach ($lotesAlocar['lotes'] as $index => $item)
                        @scope('cell_Alocar', $item, $index)
                        <x-mary-input placeholder="Qtd Alocar" wire:model.live="qtd.(text){{$item['Lote']}}" style="min-width: 120px" class="w-20"/>
                        @endscope
                        @scope('actions', $item)
                        <div class="flex">
                            <x-button wire:click="alocarMaisItens({{ json_encode($item) }})" label="Alocar"
                                      class="btn btn-success mx-5">
                            </x-button>
                        </div>
                        @endscope
                    @endforeach
                </x-table>
            </x-card>
        @endif

            <x-slot:actions>
                <x-button label="Fechar" class="btn btn-error" wire:click="closeLoc"/>
            </x-slot:actions>

    </x-drawer>

</div>

@push('styles')

@endpush

@push('scripts')
    <script src="{{asset('html5barcode.js')}}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.addEventListener('livewire:init', function () {
                console.log('entrou no evento de load');

                Livewire.on('startScanning', () => {
                    console.log('startou a camera');

                    function onScanSuccess(decodedText, decodedResult) {
                        console.log('achou algo', decodedText);
                        const remover = [']C1', '(', ')', 'GS'];

                        function escapeRegExp(string) {
                            return string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
                        }

                        const removerPattern = new RegExp(remover.map(escapeRegExp).join('|'), 'g');
                        const textoLimpo = decodedText.replace(removerPattern, '');
                        @this.
                        set('barcode', textoLimpo);
                        document.getElementById("html5-qrcode-button-camera-stop").click();
                    }

                    function onScanError(errorMessage) {
                        // handle on error condition, with error message
                    }

                    config = {
                        fps: 10,
                        qrbox: {width: 300, height: 100},
                        rememberLastUsedCamera: true,
                        supportedScanTypes: [Html5QrcodeScanType.SCAN_TYPE_CAMERA]
                    };


                    var html5QrcodeScanner = new Html5QrcodeScanner(
                        "reader",
                        config
                    );

                    html5QrcodeScanner.render(onScanSuccess, onScanError);

                });

                Livewire.on('closecam', r => {
                    console.log('fechando a camera!');
                    document.getElementById("html5-qrcode-button-camera-stop").click();
                })

            });
        });
    </script>

@endpush
