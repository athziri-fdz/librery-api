<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class BookReviews extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('book_reviews', function (Blueprint $table) {
            $table->id();
            $table->text('comment');
            $table->boolean('edited');
            //creando foreneas
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('book_id');
        });

        Schema::table('book_reviews', function (Blueprint $table) {
            //references->atributo para forenea on-> tabla donde está el atributo
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('book_id')->references('id')->on('books');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::drop('book_reviews');
    }
}
