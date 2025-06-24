<?php

namespace App\Livewire;

use Illuminate\Support\Collection;
use Livewire\Component;
use Mary\Traits\Toast;
use function Psy\debug;

class Welcome extends Component
{
    use Toast;
    public string $search = '';
    public bool $drawer = false;
    public array $sortBy = ['column' => 'name', 'direction' => 'asc'];
    public $getItens;
    public function clear(): void
    {
        $this->reset();
        $this->success('Filters cleared.', position: 'toast-bottom');
    }
    public function getItem($id): void
    {
        $this->redirect(route('pedido-itens', $id));
    }
    public function updatedSearch($value) {
        $this->HttpGetItems($value);
    }
    public function headers(): array
    {
        return [
            ['key' => 'docentry', 'label' => 'Cod.Item', 'class' => 'w-1'],
            ['key' => 'objtype', 'label' => 'Ações', 'class' => 'w-1'],
            ['key' => 'cardname', 'label' => 'Nome', 'class' => 'w-20'],
            ['key' => 'itens', 'label' => 'Items', 'class' => 'w-20'],
            ['key' => 'peso', 'label' => 'Peso', 'class' => 'w-20'],
            ['key' => 'status', 'label' => 'Status', 'class' => 'w-20'],
        ];
    }
    public function HttpGetItems($filter = null): void
    {
        $url = 'http://10.70.0.121:2501/api/Separacao/Pedidos';
        $headers = [
            'Authorization' => 'Bearer VybrXPzUrlmxWV8ss513VtHZSmxLxcB7GfEpPGDLTGuwwMVdZU',
            'accept' => 'application/json',
        ];
        $get = \Http::withHeaders($headers)->get($url);
        $data = $get->json();
        if ($filter) {
            $this->getItens = collect($data)->filter(function ($item) {
                return str_contains(strtolower($item['docentry']), strtolower($this->search)) ||
                    str_contains(strtolower($item['cardname']), strtolower($this->search)) ||
                    str_contains(strtolower($item['cardcode']), strtolower($this->search));
            });
        } else {
            $this->getItens = $data;
        }

    }
    public function mount() {
        $this->HttpGetItems();
    }
    public function render()
    {
        return view('livewire.welcome', [
            'items' => $this->getItens,
            'headers' => $this->headers()
        ]);
    }
}
