<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\BookReviews;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;

class BookController extends Controller
{

    public function BookController(){

    }

    public function index()
    {
        $books = Book::with('authors', 'category', 'editorial')->get();
        return [
            "error" => false,
            "message" => "Successfull",
            "data" => $books
        ];
    }


    public function store (Request $request){
        DB::beginTransaction();
        try{
            $existIbsn = Book::where('isbn', trim($request->isbn))->exists();
            if(!$existIbsn){
                $book = new Book();
                $book->isbn = trim($request->isbn);
                $book->title = $request->title;
                $book->description = $request->description;
                $book->category_id = $request->category["id"];
                $book->editorial_id = $request->editorial["id"];
                $book->publish_date = Carbon::now();
                $book->save();
                foreach ($request->authors as $item) {
                    $book->authors()->attach($item);
                }
                $bookId=$book->id;
                return [
                    "status" => true,
                    "message" => "your book has been created",
                    "data" => [
                        "book_id"=>$bookId,
                        "book" => $book
                    ]
                ];
            }else{
                return [
                    "status" => false,
                    "message" => "the ISBN already exist",
                    "data" => []
                ];
            }
            DB::commit();
        }catch(Exception $e){
            DB::rollBack();
            return [
                "status" => false,
                "message" => "Wrong operation",
                "data" => []
            ];
        }

    }

    public function update(Request $request, $id){
        DB::beginTransaction();
        $response = $this->getResponse();
        $book = Book::find($id);
        try{
            if($book){
                $isbnOwner = Book::where("isbn", $request->isbn)->first();
                if(!$isbnOwner || $isbnOwner->id == $book->id){
                    $book->isbn = trim($request->isbn);
                    $book->title = $request->title;
                    $book->description = $request->description;
                    $book->category_id = $request->category["id"];
                    $book->editorial_id = $request->editorial["id"];
                    $book->publish_date = Carbon::now();
                    $book->update();
                    //Delete relations
                    foreach ($book->authors as $item) {
                        $book->authors()->detach($item->id);
                    }
                    //Add new authors
                    foreach ($request->authors as $item) {
                        $book->authors()->attach($item);
                    }
                    $book = Book::with('category','editorial', 'authors')->where('id', $id)->get();
                    $response["message"] = "Your book has been updated";
                    $response["error"] = false;
                    $response["data"] = $book;
                }else{
                    $response["message"] = "ISBN duplicated";
                }
            }else{
                $response["message"] = "404 not found";
            }
            DB::commit();
        }catch(Exception $e){
            DB::rollBack();
            return [
                "status" => false,
                "message" => "Wrong operation",
                "data" => []
            ];
        }
        return $response;
    }

    public function show($id){
        $response = $this->getResponse();
        $book = Book::with(['authors', 'category', 'editorial'])->where('id',$id)->get();
        if($book){
            $response["error"] = false;
            $response["message"] = "Your book";
            $response["data"] = $book;
        }else{
            $response["message"] = "404 not found";
        }
        return $response;
    }

    public function destroy($id){
        $response = $this->getResponse();
        $book = Book::where('id', $id)->first();
        if($book){
            foreach ($book->authors as $item) {
                $book->authors()->detach($item->id);
            }
            $book->delete();
            $response["error"] = false;
            $response["message"] = "Your book has been deleted";
                $response["data"] = [];
        }else{
            $response["message"] = "404 not found";
        }
        return $response;
    }

    public function addBookReview(Request $request, $id){
        $userAuth = auth()->user();
        if (isset($userAuth->id)) {
            $book_review = new BookReviews();
            $book_review->comment = $request->comment;
            $book_review->edited = false;
            $book_review->user_id = $userAuth->id;
            $book_review->book_id = $id;
            $book_review->save();
            return $this->getResponse201('review', 'created', $book_review);
        }else{
            return $this->getResponse401();
        }
    }

    public function updateBookReview(Request $request, $idComment){
        $userAuth = auth()->user();
        if (isset($userAuth->id)) {
            $book_review = BookReviews::find($idComment);
            if($book_review){
                if($book_review->user_id == $userAuth->id){
                    $book_review->comment = $request->comment;
                    $book_review->edited = true;
                    $book_review->update();
                    return $this->getResponse201('review', 'updated', $book_review);
                }else{
                    return $this->getResponse403();
                }
            }
        }else{
            return $this->getResponse401();
        }
    }
}
