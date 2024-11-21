<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Database;

class BookController extends Controller
{
    protected $database;

    public function __construct()
    {
        $this->database = (new Factory)
            ->withServiceAccount(env('FIREBASE_CREDENTIALS'))
            ->withDatabaseUri(env('FIREBASE_DATABASE_URL'))
            ->createDatabase();
    }
    public function index()
    {
        $books = $this->database->getReference('books')->getValue();
        if (empty($books)) {
            return response()->json([
                'message' => 'No books found',
                'data' => []
            ], 200);
        }
    
        $formattedBooks = array_map(function($book, $key) {
            return [
                'id' => $key,
                'title' => $book['title'] ?? null, // Ensures key exists, or null if not
                'author' => $book['author'] ?? null,
                'published_at' => $book['published_at'] ?? null,
                'created_at' => $book['created_at'] ?? null,
                'updated_at' => $book['updated_at'] ?? null,
            ];
        }, $books, array_keys($books));
        
    
        return response()->json([
            'data' => $formattedBooks
        ]);
    }    
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'published_at' => 'required|date',
        ]);
    
        $lastBookId = $this->database->getReference('book_counter')->getValue();
        
        if (!$lastBookId) {
            $lastBookId = 0;
        }
    
        $newBookId = $lastBookId + 1;
    
        $bookData = [
            'title' => $validatedData['title'],
            'author' => $validatedData['author'],
            'published_at' => $validatedData['published_at'],
            'created_at' => now(),
            'updated_at' => now(),
            'id' => $newBookId
        ];
    
        $this->database->getReference('books/' . $newBookId)->set($bookData);
    
        $this->database->getReference('book_counter')->set($newBookId);
    
        return response()->json([
            'message' => 'Book created successfully',
            'id' => $newBookId,
            'data' => $bookData
        ], 201);
    }

    public function show($id)
    {
        $book = $this->database->getReference('books/' . $id)->getValue();
        if ($book) {
            return response()->json([
                'message' => 'Book found',
                'data' => $book,
            ], 200);
        }
        return response()->json([
            'message' => 'Book not found',
        ], 404);
    }
    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'title' => 'nullable|string|max:255', 
            'author' => 'nullable|string|max:255', 
            'published_at' => 'nullable|date', 
        ]);
    
        $book = $this->database->getReference('books/' . $id)->getValue();
    
        if (!$book) {
            return response()->json([
                'message' => 'Book not found',
            ], 404);
        }
    
        $updatedData = [];

        if (isset($validatedData['title'])) {
            $updatedData['title'] = $validatedData['title'];
        }
    
        if (isset($validatedData['author'])) {
            $updatedData['author'] = $validatedData['author'];
        }
    
        if (isset($validatedData['published_at'])) {
            $updatedData['published_at'] = $validatedData['published_at'];
        }
    
        $updatedData['updated_at'] = now();
    
        if (empty($updatedData)) {
            return response()->json([
                'message' => 'No changes detected',
            ], 400);
        }
    
        $this->database->getReference('books/' . $id)->update($updatedData);
    
        return response()->json([
            'message' => 'Book updated successfully',
            'data' => $updatedData,
        ], 200);
    }
    public function destroy($id)
    {
        $book = $this->database->getReference('books/' . $id)->getValue();

        if (!$book) {
            return response()->json([
                'message' => 'Book not found',
            ], 404);
        }

        $this->database->getReference('books/' . $id)->remove();

        return response()->json([
            'message' => 'Book deleted successfully',
        ], 200);
    }
}
