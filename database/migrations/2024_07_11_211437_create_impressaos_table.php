<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('impressoes', function (Blueprint $table) {
            $table->id();
            $table->string('idPedido');
            $table->string('itemCode');
            $table->string('lote');
            $table->string('lineNum');
            $table->string('codeBar');
            $table->boolean('printed')->default(false);
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('impressoes');
    }
};
