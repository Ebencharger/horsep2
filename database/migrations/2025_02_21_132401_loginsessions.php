<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Loginsessions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loginsessions', function (Blueprint $table) {
            $table->id();
            $table->string('userId');
            $table->longText('session');
            $table->string('ipaddress');
            $table->string('device');
            $table->string('browser');
            $table->string('city')->nullable();
            $table->string('country')->nullable();
            $table->timestamp('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('loginsessions');
    }
}
