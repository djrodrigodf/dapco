<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class impressao extends Model
{
    use HasFactory;

    protected $table = 'impressoes';

    protected $fillable = [
        'idPedido',
        'itemCode',
        'lote',
        'lineNum',
        'codeBar',
        'is_etiqueta',
    ];
}
