<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Convertir de timestamp a time
        DB::statement('ALTER TABLE servicios ALTER COLUMN hora_inicio TYPE time USING hora_inicio::time');
        DB::statement('ALTER TABLE servicios ALTER COLUMN hora_fin TYPE time USING hora_fin::time');
    }

    public function down()
    {
        DB::statement('ALTER TABLE servicios ALTER COLUMN hora_inicio TYPE timestamp USING hora_inicio::timestamp');
        DB::statement('ALTER TABLE servicios ALTER COLUMN hora_fin TYPE timestamp USING hora_fin::timestamp');
    }
};