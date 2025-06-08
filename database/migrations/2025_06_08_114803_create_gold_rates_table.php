<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gold_rates', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('type')->default(1); // 1 = 24K Gold, 2 = 22K Gold, 3 = Silver, etc.
            $table->decimal('price', 10, 2);
            $table->date('date');
            $table->bigInteger('created_by'); // removed invalid length argument
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('gold_rates');
    }
};
