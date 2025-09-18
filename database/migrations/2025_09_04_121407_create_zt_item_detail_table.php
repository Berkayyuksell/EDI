<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('zt_item_detail', function (Blueprint $table) {
            $table->id();
            $table->string('procedure_name', 50)->nullable();       // 0-8
            $table->string('composition_code_old', 20)->nullable(); // 8-12
            $table->string('composition_desc', 255)->nullable();    // 13-90
            $table->string('composition_code', 50)->nullable();     // 90-99
            $table->timestamps(); // created_at & updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('zt_item_detail');
    }
};
