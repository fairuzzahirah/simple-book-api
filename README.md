<div class="container">
    <h1>Firebase Realtime Database API for Book Management 📚</h1>
</div>

This repository contains a Laravel-based API for managing books using Firebase Realtime Database. It supports CRUD operations (Create, Read, Update, Delete) and is designed to demonstrate efficient integration of Firebase services in a PHP application.

## Features ✨
- 🔍 Retrieve all books: Fetches a list of all books stored in the Firebase Realtime Database.
- ➕ Add new books: Adds a book with details like title, author, and published date.
- 🖋️ Update book details: Modify information about a specific book.
- ❌ Delete books: Remove a book entry from the database.
- 🔢 Auto-increment IDs: Maintains unique, sequential IDs for each book.

## Tech Stack 🛠️
- Backend: Laravel (PHP Framework)
- Database: Firebase Realtime Database
- Validation: Laravel Request Validation
- Environment: .env for Firebase credentials

<body>
    <div class="container">
        <h1>API Endpoints 🔗</h1>
        <table>
            <thead>
                <tr>
                    <th>HTTP Method</th>
                    <th>Endpoint</th>
                    <th>Description</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>GET</td>
                    <td><code>/api/books</code></td>
                    <td>Retrieve all books</td>
                </tr>
                <tr>
                    <td>POST</td>
                    <td><code>/api/books</code></td>
                    <td>Add a new book</td>
                </tr>
                <tr>
                    <td>GET</td>
                    <td><code>/api/books/{id}</code></td>
                    <td>Get a specific book by ID</td>
                </tr>
                <tr>
                    <td>PUT</td>
                    <td><code>/api/books/{id}</code></td>
                    <td>Update a book's details</td>
                </tr>
                <tr>
                    <td>DELETE</td>
                    <td><code>/api/books/{id}</code></td>
                    <td>Delete a book by ID</td>
                </tr>
            </tbody>
        </table>
    </div>
</body>

## Prerequisites ⚙️
- PHP: Version 8.0 or higher.
- Laravel: Version 9 or higher.
- Add your Firebase service account JSON file path and database URL to the .env file:

```env
FIREBASE_CREDENTIALS=/path/to/firebase_credentials.json
FIREBASE_DATABASE_URL=https://your-database-url.firebaseio.com
```

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Contact 
Feel free to reach out with questions or suggestions:
- Name: Fairuz Zahirah Abhista
- Email: fairuzza.fza@gmail.com
- GitHub: https://github.com/fairuzzahirah/
