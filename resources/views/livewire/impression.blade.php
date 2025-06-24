<div>
    <!-- HEADER -->
    <x-header title="Disponiveis para Impressão" separator progress-indicator>

    </x-header>

    <!-- TABLE  -->
    <x-card>
        <div class="flex flex-col md:flex-row gap-4">
            <x-input placeholder="PEDIDO" wire:model.blur="idpedido"  icon="o-magnifying-glass" />
            <x-input placeholder="CODIGO" wire:model.blur="searchCode" icon="o-magnifying-glass" />
            <x-button label="Procurar" class="btn-primary" />
        </div>
        <x-table :headers="$headers" :rows="$items">

            @scope('actions', $item)
            <button    wire:click="getItem({{ json_encode($item)}})" spinner
                       class="btn-ghost btn btn-lg text-success">
                <i class="fas fa-arrow-alt-circle-right"></i>
            </button>
            @endscope

        </x-table>
    </x-card>

    <!-- FILTER DRAWER -->
    <x-drawer wire:model="drawer" title="Pesquisar" right separator with-close-button class="lg:w-1/3">
        <x-input placeholder="Pesquisar..." wire:model.live.debounce="search" icon="o-magnifying-glass"
                 @keydown.enter="$wire.drawer = false" />

        <x-slot:actions>
            <x-button label="Limpar" icon="o-x-mark" wire:click="clear" spinner />
            <x-button label="Filtrar" icon="o-check" class="btn-primary" @click="$wire.drawer = false" />
        </x-slot:actions>
    </x-drawer>

    @if(!empty($itemSelect))
    <x-modal wire:model="confirmImpression" title="Pedido: {{$itemSelect['docentry']}}" subtitle="Codigo: {{$itemSelect['itemcode']}}" separator>
        <div></div>
        @if(isset($itemSelect['is_etiqueta']) && $itemSelect['is_etiqueta'])
            <x-alert title="Tem certeza que deseja imprimir a etiqueta?" icon="o-printer" class="alert-warning" />
        @else
            <x-input placeholder="Quantidade por Etiqueta" wire:model.live.debounce="qtdEmb" icon="o-ticket" />
            <div class="flex justify-between">
                <x-button class="btn-success mt-3" wire:click="criarEtiqueta({{json_encode($itemSelect)}}, false)"><i class="fas fa-ticket-alt"></i> Criar qtd de Etiquetas acima</x-button>
                <x-button class="btn-primary mt-3" wire:click="criarEtiqueta({{json_encode($itemSelect)}}, true)"><i class="fas fa-ticket-alt"></i> Criar Todas Etiquetas</x-button>
            </div>
        @endif


        <x-card title="Etiquetas">
            <x-table :headers="$headerEtiquetas"
                     @row-selection="console.log($event.detail)"
                     wire:model="selected"
                     selectable
                     selectable-key="u_dpetique"
                     :rows="$listEtiquetas">
                @scope('actions', $item)
                <button  wire:click="print({{ json_encode($item) }})"  spinner
                           class="btn-ghost btn btn-lg text-success">
                    <i class="fas fa-print"></i>
                </button>
                @endscope
            </x-table>
        </x-card>


        <x-slot:actions >
            @if(isset($itemSelect['is_etiqueta']) && $itemSelect['is_etiqueta'])
                <x-button label="Não" class="btn-error" @click="$wire.confirmImpression = false" />
                <x-button label="Sim" class="btn-primary" wire:click="impressionItem" />
            @endif

                <x-dropdown label="Imprimir ou Excluir">
                    <x-menu-item title="Imprimir Selecionadas" icon="fas.print" wire:click="addSelect({{$itemSelect['docentry']}})" />
                    <x-menu-item title="Imprimir Todas" icon="fas.print" wire:click="printAll({{$itemSelect['docentry']}})" />
                    <x-menu-item title="Excluir Selecionadas" icon="o-trash" wire:click="excludeSelect({{$itemSelect['docentry']}})" />
                    <x-menu-item title="Excluir Todas" icon="o-trash" wire:click="excludeSelect({{$itemSelect['docentry']}})" />
                </x-dropdown>

        </x-slot:actions>
    </x-modal>
    @endif
</div>
