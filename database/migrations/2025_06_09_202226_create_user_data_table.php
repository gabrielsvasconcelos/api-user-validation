<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_data', function (Blueprint $table) {
            $table->id();
            $table->string('cpf', 11)->unique();
            $table->string('cep', 8);
            $table->string('email');
            $table->json('address_data');
            $table->json('name_origin_data');
            $table->enum('cpf_status', ['limpo', 'pendente', 'negativado']);
            $table->enum('risk_level', ['low', 'medium', 'high'])->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_data');
    }
};