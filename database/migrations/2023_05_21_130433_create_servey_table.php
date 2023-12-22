<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServeyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('servey', function (Blueprint $table) {
            $table->id();
            $table->string('orgname');
            $table->string('orgcomname');
            $table->string('orgadress');
            $table->string('orgperson');
            $table->string('inn');
            $table->string('kpp')->nullable();
            $table->string('ogrnip');
            $table->string('bankname');
            $table->string('bankbik');
            $table->string('bankcor');
            $table->string('bankcheck');
            $table->string('firstname');
            $table->string('lastname');
            $table->string('secondname')->nullable();
            $table->string('firstname_genetive');
            $table->string('lastname_genetive');
            $table->string('secondname_genetive')->nullable();
            $table->string('signatoryposition');
            $table->string('signatoryreason');
            $table->string('email');
            $table->string('phone');
            $table->string('product');
            $table->string('adresmfc');
            $table->string('policy');
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create("document", function (Blueprint $table) {
            $table->id();
            $table->string('title', 255)->nullable(); # наименование 
            $table->string('slug', 255)->nullable(); # slug

            $table->string('src', 255)->nullable(); # путь до файал для View
            $table->string('url', 255)->nullable(); # путь до файла для Storage

            $table->integer('is_visible')->nullable();
            $table->integer('sort')->nullable(); # сортировка

            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('document_servey', function (Blueprint $table) {
            $table->unsignedBigInteger('document_id');
            $table->unsignedBigInteger('servey_id');
        
            $table->foreign('document_id')->references('id')->on('document')->onDelete('cascade');
            $table->foreign('servey_id')->references('id')->on('servey')->onDelete('cascade');
        
            $table->primary(['document_id', 'servey_id']);

        }); 
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('servey');
        Schema::dropIfExists('servey_document');
        Schema::dropIfExists('document_servey');
    }
}
