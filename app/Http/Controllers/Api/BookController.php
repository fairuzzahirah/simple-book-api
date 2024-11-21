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
        // Initialize Firebase Database reference
        $this->database = (new Factory)
            ->withServiceAccount(env('FIREBASE_CREDENTIALS'))
            ->withDatabaseUri(env('FIREBASE_DATABASE_URL'))
            ->createDatabase();
    }
    public function index()
    {
        // Mengambil data buku dari Firebase Realtime Database
        $books = $this->database->getReference('books')->getValue();
    
        // Jika data ada, format ke dalam array yang sesuai
        $formattedBooks = [];
        foreach ($books as $key => $book) {
            $formattedBooks[] = [
                'id' => $key,  // ID Firebase adalah key unik
                'title' => $book['title'],
                'author' => $book['author'],
                'published_at' => $book['published_at'],
                'created_at' => $book['created_at'],
                'updated_at' => $book['updated_at'],
            ];
        }
    
        // Mengembalikan respons JSON dengan format yang diinginkan
        return response()->json([
            'data' => $formattedBooks
        ]);
    }    
    public function store(Request $request)
    {
        // Validasi input data
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'published_at' => 'required|date',
        ]);
    
        // Mengambil nilai counter ID terakhir dari Firebase
        $lastBookId = $this->database->getReference('book_counter')->getValue();
        
        // Jika counter belum ada (misalnya belum ada buku yang ditambahkan), mulai dari 1
        if (!$lastBookId) {
            $lastBookId = 0;
        }
    
        // Increment counter ID
        $newBookId = $lastBookId + 1;
    
        // Data buku yang akan disimpan
        $bookData = [
            'title' => $validatedData['title'],
            'author' => $validatedData['author'],
            'published_at' => $validatedData['published_at'],
            'created_at' => now(),
            'updated_at' => now(),
            'id' => $newBookId
        ];
    
        // Menyimpan data buku dengan ID yang baru di Firebase
        $this->database->getReference('books/' . $newBookId)->set($bookData);
    
        // Update nilai counter di Firebase agar ID berikutnya bertambah
        $this->database->getReference('book_counter')->set($newBookId);
    
        // Mengembalikan respons dengan ID buku dan data lengkap
        return response()->json([
            'message' => 'Book created successfully',
            'id' => $newBookId,
            'data' => $bookData
        ], 201); // HTTP Status Code 201 - Created
    }

    public function show($id)
    {
        // Fetch the book from Firebase Realtime Database by its ID
        $book = $this->database->getReference('books/' . $id)->getValue();

        // Cek apakah buku ditemukan
        if ($book) {
            return response()->json([
                'message' => 'Book found',
                'data' => $book,
            ], 200);
        }

        // Jika buku tidak ditemukan, kembalikan respons 404
        return response()->json([
            'message' => 'Book not found',
        ], 404);
    }
    public function update(Request $request, $id)
    {
        // Validasi input data
        $validatedData = $request->validate([
            'title' => 'nullable|string|max:255', // Title adalah nullable, artinya bisa dikirim atau tidak
            'author' => 'nullable|string|max:255', // Author juga nullable
            'published_at' => 'nullable|date', // Published_at juga nullable
        ]);
    
        // Cek apakah buku ada di Firebase
        $book = $this->database->getReference('books/' . $id)->getValue();
    
        if (!$book) {
            return response()->json([
                'message' => 'Book not found',
            ], 404);
        }
    
        // Persiapkan data yang akan diupdate
        $updatedData = [];
    
        // Update hanya jika nilai tersebut ada dalam permintaan
        if (isset($validatedData['title'])) {
            $updatedData['title'] = $validatedData['title'];
        }
    
        if (isset($validatedData['author'])) {
            $updatedData['author'] = $validatedData['author'];
        }
    
        if (isset($validatedData['published_at'])) {
            $updatedData['published_at'] = $validatedData['published_at'];
        }
    
        // Tambahkan timestamp untuk updated_at
        $updatedData['updated_at'] = now();
    
        // Jika tidak ada data yang diubah, kembalikan respons yang sesuai
        if (empty($updatedData)) {
            return response()->json([
                'message' => 'No changes detected',
            ], 400);
        }
    
        // Menyimpan pembaruan ke Firebase
        $this->database->getReference('books/' . $id)->update($updatedData);
    
        // Mengembalikan respons sukses
        return response()->json([
            'message' => 'Book updated successfully',
            'data' => $updatedData,
        ], 200);
    }
    public function destroy($id)
    {
        // Cek apakah buku dengan ID yang diberikan ada
        $book = $this->database->getReference('books/' . $id)->getValue();

        if (!$book) {
            // Jika buku tidak ada, kembalikan respons 404
            return response()->json([
                'message' => 'Book not found',
            ], 404);
        }

        // Menghapus data buku dari Firebase Realtime Database berdasarkan ID
        $this->database->getReference('books/' . $id)->remove();

        // Mengembalikan respons sukses
        return response()->json([
            'message' => 'Book deleted successfully',
        ], 200);
    }
}
