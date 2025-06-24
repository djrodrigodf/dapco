<?php

namespace App\Livewire;

use App\Models\impressao;
use Livewire\Component;
use Mary\Traits\Toast;

class Impression extends Component
{
    use Toast;

    public string $search = '';
    public bool $drawer = false;
    public $selected = [];
    public $headerEtiquetas = [];
    public $listEtiquetas = [];

    protected $queryString = ['idpedido'];
    public $itemSelect = [];
    public $qtdEmb = 0;
public string $searchCode = '';
    public $createEtiqueta = [
        "DocEntry" => '',
        "LineNum" => '',
        "ItemCode" => '',
        "QuantidadeImp" => '',
        "QuantityEtqGeradas" => '',
        "Peso" => '',
        "ImprimirTodas" => true,
        "SapUser" => '',
        "QuantidadeAlocar" => '',
        "QuantidadeTotal" => ''
    ];
    public bool $confirmImpression = false;
    public array $sortBy = ['column' => 'name', 'direction' => 'asc'];
    public $getItens;
    public $idpedido;
    public $items = [];

    public function clear(): void
    {
        $this->reset();
        $this->success('Filters cleared.', position: 'toast-bottom');
    }

    public function impressionItem() {
        $impression = impressao::find($this->itemSelect['id']);
        $impression->printed = 1;
        $impression->save();
        $this->success('Enviado para impressão com sucesso!',timeout: 6000, position: 'toast-bottom');
        $this->itemSelect = [
            'idPedido' => '',
            'itemCode' => '',
            'lote' => '',
            'codeBar' => ''
        ];
        $this->confirmImpression = false;
    }

    public function excludeSelect($idPedido)
    {


        if (count($this->selected) == 0) {
            $this->error('Selecione ao menos uma etiqueta.', position: 'toast-bottom', timeout: 6000);
            return false;
        }

        $array = collect($this->listEtiquetas);
        $select = $array->whereIn('u_dpetique', $this->selected)->values();


        $url = 'http://10.70.0.121:2501/api/Separacao/ExcluirSelecionadas';
        $headers = [
            'Authorization' => 'Bearer VybrXPzUrlmxWV8ss513VtHZSmxLxcB7GfEpPGDLTGuwwMVdZU',
            'accept' => 'application/json',
        ];
        $post = \Http::withHeaders($headers)->post($url, $array);
        $resp = $post->json();

        if (isset($resp['Message']) && $resp['Message'] == 'Ocorreu um erro.') {
            $this->error($resp['Message'], position: 'toast-bottom', timeout: 6000);
            return false;
        }
        $this->success($resp['Mensagem'],timeout: 6000, position: 'toast-bottom');
        $this->getEtiquetas($select[0]['docentry']);

    }

    public function addSelect($idPedido) {

        if (count($this->selected) == 0) {
            $this->error('Selecione ao menos uma etiqueta.', position: 'toast-bottom', timeout: 6000);
            return false;
        }

$array = [];
        foreach ($this->selected as $item) {
            $array[] = [
                'docentry' => $idPedido,
                'uDpEtique' => $item
            ];
        }

        $url = 'http://10.70.0.121:2501/api/Separacao/ImprimirSelecionadas';
        $headers = [
            'Authorization' => 'Bearer VybrXPzUrlmxWV8ss513VtHZSmxLxcB7GfEpPGDLTGuwwMVdZU',
            'accept' => 'application/json',
        ];
        $post = \Http::withHeaders($headers)->post($url, $array);
        $resp = $post->json();

        if (isset($resp['Message']) && $resp['Message'] == 'Ocorreu um erro.') {
            $this->error($resp['Message'], position: 'toast-bottom', timeout: 6000);
            return false;
        }
        $this->success($resp['Mensagem'],timeout: 6000, position: 'toast-bottom');
    }

    public function printAll($idPedido) {


        $array = [];
        foreach ($this->listEtiquetas as $item) {

            $array[] = [
                'docentry' => $idPedido,
                'uDpEtique' => $item['u_dpetique']
            ];
        }

        $url = 'http://10.70.0.121:2501/api/Separacao/ImprimirSelecionadas';
        $headers = [
            'Authorization' => 'Bearer VybrXPzUrlmxWV8ss513VtHZSmxLxcB7GfEpPGDLTGuwwMVdZU',
            'accept' => 'application/json',
        ];
        $post = \Http::withHeaders($headers)->post($url, $array);
        $resp = $post->json();

        if (isset($resp['Message']) && $resp['Message'] == 'Ocorreu um erro.') {
            $this->error($resp['Message'], position: 'toast-bottom', timeout: 6000);
            return false;
        }
        $this->success($resp['Mensagem'],timeout: 6000, position: 'toast-bottom');
    }

    public function getItemPedido($pedido) {

        $line = $pedido['linenum'] - 1;
        $url = 'http://10.70.0.121:2501/api/Separacao/Pedido/'. $pedido['docentry'].'/Item/'.$line;
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
        return $data;
    }

    public function criarEtiqueta($pedido, $all = null) {

        $item = $this->getItemPedido($pedido);

        $this->createEtiqueta = [
            "DocEntry" => $pedido['docentry'],
            "LineNum" => $item['linenum'],
            "ItemCode" => $pedido['itemcode'],
            "QuantidadeEmb" => $this->qtdEmb > 0 ? $this->qtdEmb : $item['U_DPQtdEmb'],
            "QuantityEtqGeradas" => $item['u_dpqtdade'],
            "Peso" => $item['weight1'],
            "ImprimirTodas" => $all,
            "SapUser" => \Auth::user()->sap,
            "QuantidadeAlocar" => $item['alocado'],
            "QuantidadeTotal" => $item['quantity']
        ];


        $url = 'http://10.70.0.121:2501/api/Separacao/CriaEtiquetas';
        $headers = [
            'Authorization' => 'Bearer VybrXPzUrlmxWV8ss513VtHZSmxLxcB7GfEpPGDLTGuwwMVdZU',
            'accept' => 'application/json',
        ];
        $post = \Http::withHeaders($headers)->post($url, [$this->createEtiqueta]);
        $resp = $post->json();

        if (isset($resp['Status']) && !$resp['Status']) {
            $this->error($resp['Mensagem'], position: 'toast-bottom', timeout: 6000);
            $this->createEtiqueta = [
                "DocEntry" => '',
                "LineNum" => '',
                "ItemCode" => '',
                "QuantidadeImp" => '',
                "QuantityEtqGeradas" => '',
                "Peso" => '',
                "ImprimirTodas" => true,
                "SapUser" => \Auth::user()->sap,
                "QuantidadeAlocar" => '',
                "QuantidadeTotal" => ''
            ];
            return false;
        }
        $pedido['lineNum'] = $item['linenum'];

        $this->getEtiquetas($pedido);

        $this->success($resp[0]['Mensagem'],timeout: 6000, position: 'toast-bottom');
        $this->createEtiqueta = [
            "DocEntry" => '',
            "LineNum" => '',
            "ItemCode" => '',
            "QuantidadeImp" => '',
            "QuantityEtqGeradas" => '',
            "Peso" => '',
            "ImprimirTodas" => true,
            "SapUser" => \Auth::user()->sap,
            "QuantidadeAlocar" => '',
            "QuantidadeTotal" => ''
        ];

    }



    public function updatedConfirmImpression($value)
    {
        if (!$value) {
            $this->itemSelect = [
                'idPedido' => '',
                'itemCode' => '',
                'lote' => '',
                'codeBar' => ''
            ];
            $this->createEtiqueta = [
                "DocEntry" => '',
                "LineNum" => '',
                "ItemCode" => '',
                "QuantidadeImp" => '',
                "QuantityEtqGeradas" => '',
                "Peso" => '',
                "ImprimirTodas" => true,
                "SapUser" => \Auth::user()->sap,
                "QuantidadeAlocar" => '',
                "QuantidadeTotal" => ''
            ];
            $this->qtdEmb = 0;
        }
    }

    public function mount()
    {
        if ($this->idpedido) {
            $this->getPedidoid($this->idpedido);
        }

    }

    public function getItem($item): void
    {



        $this->itemSelect = $item;

        $this->getEtiquetas($item);
        $this->confirmImpression = true;

    }

    public function updatedSearch($value)
    {

    }

    public function headers(): array
    {

        return [
            ['key' => 'docentry', 'label' => 'Pedido', 'class' => 'w-20'],
            ['key' => 'itemcode', 'label' => 'Codigo', 'class' => 'w-20'],
        ];
    }

    public function HttpGetItems($filter = null): void
    {
        $allImpression = impressao::where('printed', 0)
            ->when($filter, function ($query) use ($filter) {
                return $query->whereAny(
                    [
                        'idPedido',
                        'itemCode',
                    ],
                    'LIKE',
                    "%$filter%"
                );
            })->groupBy('idPedido', 'itemCode')
            ->get();
        $this->getItens = $allImpression;
    }

    public function getEtiquetas($pedido) {
        $this->listEtiquetas = [];
        $lineNum = $pedido['linenum'] - 1;
        $url = 'http://10.70.0.121:2501/api/Separacao/Etiqueta/'.$pedido['docentry'].'/item/'.$lineNum;
        $headers = [
            'Authorization' => 'Bearer VybrXPzUrlmxWV8ss513VtHZSmxLxcB7GfEpPGDLTGuwwMVdZU',
            'accept' => 'application/json',
        ];
        $get = \Http::withHeaders($headers)->get($url);
        $this->listEtiquetas = $get->json();
        $this->headerEtiquetas = [
            ['key' => 'u_dpqtdade', 'label' => 'Qtd', 'class' => 'w-20'],
            ['key' => 'u_dpetique', 'label' => 'Etiqueta', 'class' => 'w-20'],
        ];

    }

    public function print($item) {

        $uDpEtique = $item['u_dpetique'];
        $url = 'http://10.70.0.121:2501/api/Separacao/Imprimir/'.$item['docentry'].'/item/'.$uDpEtique;
        $headers = [
            'Authorization' => 'Bearer VybrXPzUrlmxWV8ss513VtHZSmxLxcB7GfEpPGDLTGuwwMVdZU',
            'accept' => 'application/json',
        ];
        $get = \Http::withHeaders($headers)->get($url);
        $get = $get->json();
        $this->success($get['Mensagem'],timeout: 6000, position: 'toast-bottom');
    }


public function updatedIdpedido($id) {
        if (!empty($id)) {
            $this->getPedidoid($id);
        } else {
            $this->items = [];
	    $this->searchCode = '';
        }

    }

public function updatedSearchCode($v) {

        if ($this->idpedido) {

            $this->getPedidoid($this->idpedido);
        }
    }

    public function getPedidoid($id) {
        $this->itemSelect = [];
        if (!empty($id)) {
            $url = 'http://10.70.0.121:2501/api/Separacao/Etiqueta/'.$id;
            $headers = [
                'Authorization' => 'Bearer VybrXPzUrlmxWV8ss513VtHZSmxLxcB7GfEpPGDLTGuwwMVdZU',
                'accept' => 'application/json',
            ];
            $get = \Http::withHeaders($headers)->get($url);
            $get = $get->json();

if (isset($get['Message'])) {
                $this->items = [];
            } else {
                $this->items = collect($get)
                    ->filter(fn($item) => empty($this->searchCode) || $item['itemcode'] === $this->searchCode)
                    ->values()
                    ->toArray();
            }

        }
    }



    public function render()
    {
        return view('livewire.impression', [
            'headers' => $this->headers(),
            'headerEtiquetas' => $this->headerEtiquetas,
            'listEtiquetas' => $this->listEtiquetas,
        ]);
    }
}
