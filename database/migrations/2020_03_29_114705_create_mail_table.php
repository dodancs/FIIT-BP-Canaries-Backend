<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMailTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('mail', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('uuid')->unique()->notNullable()->index();
            $table->uuid('canary')->notNullable()->index();
            $table->foreign('canary')->references('uuid')->on('canaries');
            $table->timestamp('received_on', 0);
            $table->string('from')->notNullable();
            $table->string('subject')->notNullable();
            $table->longText('body')->notNullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('mail');
    }
}
