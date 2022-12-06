<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Author extends Model
{
    use HasFactory;

    protected $table = "authors";

    protected $fillable =[
        "id",
        "name",
        "first_surname",
        "second_surname"
    ];

    public $timestamp = false;

    public function books(){
        //Relación de 1  muchos
        return $this->belongsToMany(
            Book::class, //Tabla con la que existe la relación
            'authors_books', //Tabla intersección
            'authors_id', //Tabla en donde me encuentro
            'books_id' //Tabla hacía dónde estableceré la relación
        );
    }
}
