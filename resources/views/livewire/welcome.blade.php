<div>
    <!-- HEADER -->
    <x-header title="Lista de Pedidos" separator progress-indicator>
        <x-slot:middle class="!justify-end">
            <x-input placeholder="Pesquisar..." wire:model.live.debounce="search" clearable icon="o-magnifying-glass" />
        </x-slot:middle>
        <x-slot:actions>
            <x-button label="Filters" @click="$wire.drawer = true" responsive icon="o-funnel" class="btn-primary" />
        </x-slot:actions>
    </x-header>

    <!-- TABLE  -->
    <x-card>
        <x-table :headers="$headers" :rows="$items" :sort-by="$sortBy">
            @scope('cell_objtype', $item)
                <button    wire:click="getItem({{ $item['docentry'] }})" spinner class="btn-ghost btn btn-lg">
                    <i class="fas fa-arrow-alt-circle-right"
                       @if($item['usuario'] == Auth::user()->sap)
                           style="color: yellow !important;"
                       @endif
                       @if($item['usuario'] == 0)
                           style="color: green !important;"
                       @endif
                       @if($item['usuario'] != Auth::user()->sap)
                           style="color: red !important;"
                       @endif
                       @if($item['usuario'] == '')
                           style="color: green !important;"
                        @endif
                       ></i>
                </button>
            @endscope

        </x-table>
    </x-card>

    <!-- FILTER DRAWER -->
    <x-drawer wire:model="drawer" title="Filters" right separator with-close-button class="lg:w-1/3">
        <x-input placeholder="Pesquisar..." wire:model.live.debounce="search" icon="o-magnifying-glass"
            @keydown.enter="$wire.drawer = false" />

        <x-slot:actions>
            <x-button label="Limpar" icon="o-x-mark" wire:click="clear" spinner />
            <x-button label="Filtrar" icon="o-check" class="btn-primary" @click="$wire.drawer = false" />
        </x-slot:actions>
    </x-drawer>
</div>
