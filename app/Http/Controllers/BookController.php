<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Traits\ApiResponse;
use Illuminate\Http\Response;
use Illuminate\Http\Request;

class BookController extends Controller
{
    use ApiResponse;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }
    public function index()
    {
        $book = Book::all();
        //Send $author for ApiResponse
        return $this->successResponse($book);
    }

    /**
     * Create an instance of book
     * @return Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validateRequest($request, 'store');

        $book = Book::create($request->all());

        return $this->successResponse($book, Response::HTTP_CREATED);
    }

    /**
     * Return an specific book
     * @return Illuminate\Http\Response
     */
    public function show($book)
    {
        $book = Book::findOrFail($book);

        return $this->successResponse($book);
    }

    public function update($book, Request $request)
    {

        $this->validateRequest($request, 'update');

        $book = Book::findOrFail($book);

        $book->fill($request->all());

        //Verify if exists $author Request
        if ($book->isClean()) {
            return $this->errorResponse('At least one value must change', Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $book->save();

        return $this->successResponse($book);
    }

    /**
     * Delete an existing author
     * @return Illuminate\Http\Response
     */
    public function destroy($book)
    {
        $book = Book::findOrFail($book);

        $book->delete();

        return $this->successResponse($book);
    }

    //

    public function validateRequest(Request $request, $typeRequest)
    {
        if ($typeRequest == 'store') {
            $rules = [
                'title' => 'required|max:100',
                'description' => 'required|max:100',
                'price' => 'required',
            ];
        } elseif ($typeRequest == 'update')
        $rules = [
            'title' => 'max:100',
            'description' => 'max:100',
            'price' => 'max:100',
        ];

        $messages = [
            'title.max' => 'The title have more then 10 caracters',
            'title.required' => 'The title is required',
            'description.required' => 'The description has required',
            'description.max' => 'The description have more then 100 caracters',
            'price.required' => 'The price has required',
        ];
        return $this->validate($request, $rules, $messages);
    }
}
