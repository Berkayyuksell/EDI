<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
         Schema::create('zt_test_item_list', function (Blueprint $table) {
            $table->id();
            $table->string('type', 10);                 // INIT / DETAIL / FINE
            $table->string('record_type', 10)->nullable(); 
            $table->string('procedure_name', 50)->nullable();
            $table->string('transaction_date', 20)->nullable();
            $table->string('composition_code_old', 20)->nullable();
            $table->string('composition_desc', 255)->nullable();
            $table->string('composition_code', 50)->nullable();
            $table->string('trail_record_type', 20)->nullable();
            $table->string('number_of_records', 20)->nullable();
            $table->timestamps(); // created_at, updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('zt_test_item_list');
    }
};