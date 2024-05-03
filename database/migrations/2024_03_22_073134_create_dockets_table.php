<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dockets', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('docket_num');
            $table->time('time');
            $table->text('summary');
            $table->string('panel');
            $table->string('location');
            $table->tinyInteger('active')->default(1);
            $table->integer('event_id');
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
        Schema::dropIfExists('dockets');
    }
}
