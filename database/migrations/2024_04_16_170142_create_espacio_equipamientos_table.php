<?php

use App\Models\EspacioEquipamiento;
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
        Schema::create(EspacioEquipamiento::TABLA, function (Blueprint $table) {
            $table->id();
            $table->foreignId('espacio_id')->constrained()->cascadeOnDelete();
            $table->foreignId('equipamiento_id')->constrained()->cascadeOnDelete();
            $table->integer('cantidad');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(EspacioEquipamiento::TABLA);
    }
};