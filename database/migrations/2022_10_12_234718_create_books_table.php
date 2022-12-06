<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBooksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('isbn', 15)->unique();
            $table->string('title'); //maxlength default 255
            $table->string('description', 500)->nullable();
            $table->date('publish_date')->nullable();
            //Relaciones
            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('editorial_id');
        });

        Schema::table('books', function (Blueprint $table) {
            //references->atributo para forenea on-> tabla donde está el atributo
            $table->foreign('category_id')->references('id')->on('categories');
            $table->foreign('editorial_id')->references('id')->on('editorials');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('books');
    }
}
