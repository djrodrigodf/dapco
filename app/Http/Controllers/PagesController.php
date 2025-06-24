<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PagesController extends Controller
{
    public function pedidoItens($id) {
        return view('livewire.pedido-item', ['id' => $id]);
    }

}
