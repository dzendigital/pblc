<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        # таблица содержит список настроек, например "email" - почта, куда отправлять письма с сайта
        Schema::create('settings', function (Blueprint $table) {
            $table->id();

            $table->string('title', 255); # название
            $table->string('slug', 255); # slug
            $table->string('value', 255)->nullable(); # значение

            $table->integer('is_visible')->nullable(); # показатель видимости
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('settings');
    }
}
