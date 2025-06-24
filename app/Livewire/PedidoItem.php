<?php

namespace App\Livewire;

use App\Models\impressao;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Livewire\Component;
use Mary\Traits\Toast;

class PedidoItem extends Component
{
    use Toast;
    public string $search = '';
    public int $id;
    public int $conta = 0;
    public bool $loc = false;
    public bool $drawer = false;
    public array $getItens = [];
    public $tableHeader;
    public $tableData;
    public $qtd = [];
    public array $sortBy = ['column' => 'name', 'direction' => 'asc'];
    public array $alocaItem = [];
    public $barcode = '';
    protected $listeners = ['barcodeDetected'];
    public $lotesAlocar = [];

    public function barcodeDetected($barcode)
    {
        $this->barcode = $barcode;
    }

    public function closeLoc() {

        $this->loc = false;
        $this->qtd = [];
        $this->lotesAlocar = [];
        $this->alocaItem = [];
    }
    public function updatedLoc($v)
    {
        $this->barcode = null;
        $this->qtd = [];
        $this->lotesAlocar = [];
        $this->alocaItem = [];
    }

    public function startScanning()
    {
        $this->dispatch('startScanning');
    }

    public function clear(): void
    {
        $this->search = '';
        $this->drawer = false;
        $this->success('Pesquisa Limpa', position: 'toast-bottom');
    }
    public function mount()
    {
        $this->HttpGetItem($this->id);
        $this->headers();
        $this->dataTable();
    }
    public function headers(): void
    {
        $this->tableHeader = [
            ['key' => 'docentry', 'label' => '# Pedido', 'class' => 'w-20'],
            ['key' => 'cardcode', 'label' => 'Item Code', 'class' => 'w-64'],
            ['key' => 'u_dplocpdr', 'label' => 'Local', 'class' => 'w-20'],
            ['key' => 'quantity', 'label' => 'Qtd', 'class' => 'w-20'],
            ['key' => 'Separacao', 'label' => 'Separação', 'class' => 'w-20'],
        ];
    }
    public function dataTable()
    {
        return collect($this->getItens)
            ->map(function ($item) {
                return [
                    'id' => $item['docentry'],
                    'itemname' => $item['itemname'],
                    'u_dplocpdr' => $item['u_dplocpdr'],
                    'Separacao' => $item['Separacao'],
                    'raw' => $item
                ];
            })
            ->sortBy([...array_values($this->sortBy)])
            ->when($this->search, function (Collection $collection) {
                return $collection->filter(function (array $item) {
                    $searchString = strtolower(preg_replace('/\s+/', ' ', $this->search));
                    foreach ($item as $key => $value) {
                        if ($key === 'raw') {
                            continue;
                        }
                        $value = strtolower(preg_replace('/\s+/', ' ', $value));
                        if (str_contains($value, $searchString)) {
                            return true;
                        }
                    }
                    return false;
                });
            });
    }

    public function HttpGetItem($id): void
    {
        $url = 'http://10.70.0.121:2501/api/Separacao/Pedido/'. $id;
        $headers = [
            'Authorization' => 'Bearer VybrXPzUrlmxWV8ss513VtHZSmxLxcB7GfEpPGDLTGuwwMVdZU',
            'accept' => 'application/json',
        ];
            $get = \Http::withHeaders($headers)->get($url);

        $responseBody = $get->body();
        if(substr($responseBody, 0, 3) == pack("CCC", 0xef, 0xbb, 0xbf)) {
            $responseBody = substr($responseBody, 3);
        }
        $data = json_decode($responseBody, true);
            $this->getItens = $data;
    }
    public function locarItem($item) {
        $this->loc = true;
        $this->barcode = null;
        $this->alocaItem = $item;
        $this->lotesAlocar = [];
        $this->sendReserva($item);
    }

    public function getAviso($id) {
        $url = 'http://10.70.0.121:2501/api/Separacao/Pedido/'. $id.'/Aviso';
        $headers = [
            'Authorization' => 'Bearer VybrXPzUrlmxWV8ss513VtHZSmxLxcB7GfEpPGDLTGuwwMVdZU',
            'accept' => 'application/json',
        ];
        $get = \Http::withHeaders($headers)->get($url);
        return $get->body();
    }

    public function getVerificacao($idPedido, $itemCode, $codigo, $lineNum) {
        $url = 'http://10.70.0.121:2501/api/Separacao/VerificaAlocacao';
        $headers = [
            'Authorization' => 'Bearer VybrXPzUrlmxWV8ss513VtHZSmxLxcB7GfEpPGDLTGuwwMVdZU',
            'accept' => 'application/json',
        ];
        $body = [
            'idPedido' => (string)$idPedido,
            'itemCode' => (string)$itemCode,
            'codigo' => (string)$codigo,
            'lineNum' => (string)$lineNum
        ];

        $post = Http::withHeaders($headers)->post($url, $body);
        return $post->json();


    }

    public function sendReserva($item) {
        $url = 'http://10.70.0.121:2501/api/Separacao/Reserva';
        $headers = [
            'Authorization' => 'Bearer VybrXPzUrlmxWV8ss513VtHZSmxLxcB7GfEpPGDLTGuwwMVdZU',
            'accept' => 'application/json',
        ];
        $body = [
            "DocEntry" => $item['raw']['docentry'],
            "LineNum" => $item['raw']['linenum'],
            'SapUser' => \Auth::user()->sap,
            'Objtype' => $item['raw']['objtype'],
            'CardCode' => $item['raw']['cardcode'],
        ];
        $post = \Http::withHeaders($headers)->post($url, $body);

        return $post->json();
    }

    public function headerAlocarItems()
    {
        return [
            ['key' => 'Lote', 'label' => 'Lote', 'class' => 'w-20'],
            ['key' => 'Qtd', 'label' => 'Qtd', 'class' => 'w-20'],
            ['key' => 'Alocado', 'label' => 'Alocado', 'class' => 'w-20'],
            ['key' => 'Alocar', 'label' => 'Alocar', 'class' => 'w-20'],
            ['key' => 'DistNumber', 'label' => 'DistNumber', 'class' => 'w-20'],
        ];
    }

    public function SendAlocacao($item) {

        $this->validate([
            'barcode' => 'required'
        ], [
            'barcode.required' => 'Código do lote é Obrigatório'
        ]);

        $verificacao = $this->getVerificacao($item['id'], $item['raw']['itemcode'], $this->barcode, $item['raw']['linenum']);

        if (isset($verificacao['Message']) && $verificacao['Message'] == 'O código de barras informado não foi localizado na lista de lotes disponíveis para este item.') {
            $this->error($verificacao['Message'], position: 'toast-bottom', timeout: 6000);
            return;
        }

        $this->lotesAlocar = $verificacao;
        $this->qtd = [];
    }

    public function alocarMaisItens($item) {



        if (empty($this->qtd)) {
            $this->error('A quantidade para o lote '. $item['Lote'] .' é obrigatória', position: 'toast-bottom', timeout: 6000);
            return;
        }

        if (!isset($this->qtd['(text)'.$item['Lote']])) {
            $this->error('A quantidade para o lote '. $item['Lote'] .' deve ser pelo menos 1', position: 'toast-bottom', timeout: 6000);
            return;
        }


        $url = 'http://10.70.0.121:2501/api/Separacao/Alocacao';
        $headers = [
            'Authorization' => 'Bearer VybrXPzUrlmxWV8ss513VtHZSmxLxcB7GfEpPGDLTGuwwMVdZU',
            'accept' => 'application/json',
        ];
        $body = [
            "DocEntry" => $this->lotesAlocar['dpItens']['Pedido'],
            "ItemCode" => $this->lotesAlocar['dpItens']['ItemLote'],
            "Lote" => $item['DistNumber'],
            "Quantidade" => $this->qtd['(text)'.$item['Lote']],
            "QuantidadeTotal" => $this->qtd['(text)'.$item['Lote']],
            "LineNum" => $this->lotesAlocar['dpItens']['LinhaItem'],
            'SapUser' => \Auth::user()->sap,
        ];


        $post = \Http::withHeaders($headers)->post($url, $body);
        $resp = $post->json();
        $this->qtd = [];
        $this->lotesAlocar = $this->getVerificacao($this->lotesAlocar['dpItens']['Pedido'], $this->lotesAlocar['dpItens']['ItemLote'], $this->lotesAlocar['lotes'][0]['Lote'], $this->lotesAlocar['dpItens']['LinhaItem']);
        if (isset($resp['Message']) && $resp['Message'] == "Erro ao alocar Lote :-4014 - Cannot add row without complete selection of batch/serial numbers") {
            $this->error($resp['Message'], position: 'toast-bottom', timeout: 6000);
            return;
        }

        $save = [
            'idPedido' => $this->lotesAlocar['dpItens']['Pedido'],
            'itemCode' => $this->lotesAlocar['dpItens']['ItemLote'],
            'lote' => $item['DistNumber'],
            'lineNum' => $this->lotesAlocar['dpItens']['LinhaItem'],
            'codeBar' => $item['Lote'],
        ];

        impressao::create($save);

        $this->barcode = null;

        if (isset($resp['Mensagem']) && $resp['Mensagem'] == 'Nenhuma ação executada, favor revisar o item ou o lote.') {
            $this->error($resp['Mensagem'], position: 'toast-bottom', timeout: 6000);
        } else {
            $this->success($resp['Mensagem'], position: 'toast-bottom', timeout: 6000);
        }
    }

    public function render()
    {
        return view('livewire.pedido-item', [
            'items' => $this->dataTable(),
            'headers' => $this->tableHeader,
            'headersAlocar' => $this->headerAlocarItems()
        ]);
    }
}
