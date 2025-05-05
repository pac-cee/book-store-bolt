<?php
// Check if there are any categories
$stmt = $conn->prepare("SELECT COUNT(*) FROM categories");
$stmt->execute();
$categoryCount = $stmt->fetchColumn();

// Only insert sample data if there are no categories
if ($categoryCount === 0) {
    // Insert sample categories
    $categories = [
        ['name' => 'Fiction', 'slug' => 'fiction', 'description' => 'Novels, short stories, and other fictional works'],
        ['name' => 'Non-Fiction', 'slug' => 'non-fiction', 'description' => 'Biographies, memoirs, self-help, and educational books'],
        ['name' => 'Mystery', 'slug' => 'mystery', 'description' => 'Suspense, detective, and crime novels'],
        ['name' => 'Science Fiction', 'slug' => 'science-fiction', 'description' => 'Science fiction, fantasy, and speculative fiction'],
        ['name' => 'Biography', 'slug' => 'biography', 'description' => 'Biographies and autobiographies of notable individuals'],
        ['name' => 'History', 'slug' => 'history', 'description' => 'Books about historical events, periods, and figures']
    ];
    
    $stmt = $conn->prepare("INSERT INTO categories (name, slug, description) VALUES (:name, :slug, :description)");
    
    foreach ($categories as $category) {
        $stmt->bindParam(':name', $category['name']);
        $stmt->bindParam(':slug', $category['slug']);
        $stmt->bindParam(':description', $category['description']);
        $stmt->execute();
    }
    
    // Insert sample books
    $books = [
        [
            'title' => 'To Kill a Mockingbird',
            'author' => 'Harper Lee',
            'description' => 'Set in the American South during the 1930s, the novel tells the story of a lawyer who defends a Black man falsely accused of rape, as seen through the eyes of the lawyer\'s young daughter.',
            'price' => 12.99,
            'original_price' => 15.99,
            'cover_image' => 'https://images.pexels.com/photos/5834/nature-grass-leaf-green.jpg?auto=compress&cs=tinysrgb&w=1600',
            'category_id' => 1, // Fiction
            'isbn' => '9780061120084',
            'publisher' => 'Harper Perennial Modern Classics',
            'publication_date' => '1960-07-11',
            'pages' => 336,
            'language' => 'English',
            'stock_quantity' => 25,
            'featured' => true
        ],
        [
            'title' => '1984',
            'author' => 'George Orwell',
            'description' => 'A dystopian novel set in a totalitarian society ruled by the Party, which has total control over every aspect of people\'s lives.',
            'price' => 10.99,
            'original_price' => 14.99,
            'cover_image' => 'https://images.pexels.com/photos/590493/pexels-photo-590493.jpeg?auto=compress&cs=tinysrgb&w=1600',
            'category_id' => 4, // Science Fiction
            'isbn' => '9780451524935',
            'publisher' => 'Signet Classic',
            'publication_date' => '1949-06-08',
            'pages' => 328,
            'language' => 'English',
            'stock_quantity' => 18,
            'featured' => true
        ],
        [
            'title' => 'The Great Gatsby',
            'author' => 'F. Scott Fitzgerald',
            'description' => 'Set in the Jazz Age, the novel tells the tragic story of Jay Gatsby and his pursuit of Daisy Buchanan.',
            'price' => 11.99,
            'original_price' => 13.99,
            'cover_image' => 'https://images.pexels.com/photos/46274/pexels-photo-46274.jpeg?auto=compress&cs=tinysrgb&w=1600',
            'category_id' => 1, // Fiction
            'isbn' => '9780743273565',
            'publisher' => 'Scribner',
            'publication_date' => '1925-04-10',
            'pages' => 180,
            'language' => 'English',
            'stock_quantity' => 0,
            'featured' => false
        ],
        [
            'title' => 'Pride and Prejudice',
            'author' => 'Jane Austen',
            'description' => 'A romantic novel following the character development of Elizabeth Bennet, who learns about the repercussions of hasty judgments.',
            'price' => 9.99,
            'original_price' => 12.99,
            'cover_image' => 'https://images.pexels.com/photos/1130980/pexels-photo-1130980.jpeg?auto=compress&cs=tinysrgb&w=1600',
            'category_id' => 1, // Fiction
            'isbn' => '9780141439518',
            'publisher' => 'Penguin Classics',
            'publication_date' => '1813-01-28',
            'pages' => 432,
            'language' => 'English',
            'stock_quantity' => 32,
            'featured' => true
        ],
        [
            'title' => 'The Hobbit',
            'author' => 'J.R.R. Tolkien',
            'description' => 'A fantasy novel about the adventures of hobbit Bilbo Baggins, who is hired as a "burglar" by a group of dwarves seeking to reclaim their treasure from a dragon.',
            'price' => 14.99,
            'original_price' => 17.99,
            'cover_image' => 'https://images.pexels.com/photos/1563075/pexels-photo-1563075.jpeg?auto=compress&cs=tinysrgb&w=1600',
            'category_id' => 4, // Science Fiction
            'isbn' => '9780547928227',
            'publisher' => 'Houghton Mifflin Harcourt',
            'publication_date' => '1937-09-21',
            'pages' => 304,
            'language' => 'English',
            'stock_quantity' => 28,
            'featured' => true
        ],
        [
            'title' => 'The Da Vinci Code',
            'author' => 'Dan Brown',
            'description' => 'A mystery thriller novel following symbologist Robert Langdon as he investigates a murder in the Louvre Museum.',
            'price' => 11.99,
            'original_price' => 14.99,
            'cover_image' => 'https://images.pexels.com/photos/46792/the-ball-stadion-football-the-pitch-46792.jpeg?auto=compress&cs=tinysrgb&w=1600',
            'category_id' => 3, // Mystery
            'isbn' => '9780307474278',
            'publisher' => 'Anchor',
            'publication_date' => '2003-03-18',
            'pages' => 597,
            'language' => 'English',
            'stock_quantity' => 42,
            'featured' => false
        ],
        [
            'title' => 'Steve Jobs',
            'author' => 'Walter Isaacson',
            'description' => 'The exclusive biography of Steve Jobs, based on more than forty interviews with Jobs conducted over two years.',
            'price' => 19.99,
            'original_price' => 24.99,
            'cover_image' => 'https://images.pexels.com/photos/256262/pexels-photo-256262.jpeg?auto=compress&cs=tinysrgb&w=1600',
            'category_id' => 5, // Biography
            'isbn' => '9781451648539',
            'publisher' => 'Simon & Schuster',
            'publication_date' => '2011-10-24',
            'pages' => 656,
            'language' => 'English',
            'stock_quantity' => 15,
            'featured' => false
        ],
        [
            'title' => 'Sapiens: A Brief History of Humankind',
            'author' => 'Yuval Noah Harari',
            'description' => 'A book that explores the history of the human species, from the evolution of archaic human species to the present.',
            'price' => 16.99,
            'original_price' => 19.99,
            'cover_image' => 'https://images.pexels.com/photos/4666751/pexels-photo-4666751.jpeg?auto=compress&cs=tinysrgb&w=1600',
            'category_id' => 2, // Non-Fiction
            'isbn' => '9780062316097',
            'publisher' => 'Harper',
            'publication_date' => '2015-02-10',
            'pages' => 464,
            'language' => 'English',
            'stock_quantity' => 23,
            'featured' => true
        ],
        [
            'title' => 'Gone Girl',
            'author' => 'Gillian Flynn',
            'description' => 'A thriller novel about the disappearance of a woman on her fifth wedding anniversary.',
            'price' => 12.99,
            'original_price' => 15.99,
            'cover_image' => 'https://images.pexels.com/photos/256450/pexels-photo-256450.jpeg?auto=compress&cs=tinysrgb&w=1600',
            'category_id' => 3, // Mystery
            'isbn' => '9780307588371',
            'publisher' => 'Crown Publishing Group',
            'publication_date' => '2012-06-05',
            'pages' => 422,
            'language' => 'English',
            'stock_quantity' => 19,
            'featured' => false
        ],
        [
            'title' => 'The Catcher in the Rye',
            'author' => 'J.D. Salinger',
            'description' => 'A novel about a teenage boy\'s experiences in New York City after being expelled from his prep school.',
            'price' => 10.99,
            'original_price' => 13.99,
            'cover_image' => 'https://images.pexels.com/photos/358532/pexels-photo-358532.jpeg?auto=compress&cs=tinysrgb&w=1600',
            'category_id' => 1, // Fiction
            'isbn' => '9780316769488',
            'publisher' => 'Little, Brown and Company',
            'publication_date' => '1951-07-16',
            'pages' => 240,
            'language' => 'English',
            'stock_quantity' => 27,
            'featured' => false
        ],
        [
            'title' => 'The Lord of the Rings',
            'author' => 'J.R.R. Tolkien',
            'description' => 'An epic high-fantasy novel set in Middle-earth, following the hobbit Frodo Baggins and the Fellowship of the Ring on a quest to destroy the One Ring.',
            'price' => 29.99,
            'original_price' => 34.99,
            'cover_image' => 'https://images.pexels.com/photos/2437299/pexels-photo-2437299.jpeg?auto=compress&cs=tinysrgb&w=1600',
            'category_id' => 4, // Science Fiction
            'isbn' => '9780618640157',
            'publisher' => 'Houghton Mifflin Harcourt',
            'publication_date' => '1954-07-29',
            'pages' => 1178,
            'language' => 'English',
            'stock_quantity' => 22,
            'featured' => false
        ],
        [
            'title' => 'A Brief History of Time',
            'author' => 'Stephen Hawking',
            'description' => 'A book that explores the nature of time, space, and the universe.',
            'price' => 15.99,
            'original_price' => 18.99,
            'cover_image' => 'https://images.pexels.com/photos/2150/sky-space-dark-galaxy.jpg?auto=compress&cs=tinysrgb&w=1600',
            'category_id' => 2, // Non-Fiction
            'isbn' => '9780553380163',
            'publisher' => 'Bantam',
            'publication_date' => '1988-04-01',
            'pages' => 212,
            'language' => 'English',
            'stock_quantity' => 3,
            'featured' => false
        ]
    ];
    
    $stmt = $conn->prepare("
        INSERT INTO books (
            title, author, description, price, original_price, cover_image, 
            category_id, isbn, publisher, publication_date, pages, 
            language, stock_quantity, featured
        ) VALUES (
            :title, :author, :description, :price, :original_price, :cover_image, 
            :category_id, :isbn, :publisher, :publication_date, :pages, 
            :language, :stock_quantity, :featured
        )
    ");
    
    foreach ($books as $book) {
        $stmt->bindParam(':title', $book['title']);
        $stmt->bindParam(':author', $book['author']);
        $stmt->bindParam(':description', $book['description']);
        $stmt->bindParam(':price', $book['price']);
        $stmt->bindParam(':original_price', $book['original_price']);
        $stmt->bindParam(':cover_image', $book['cover_image']);
        $stmt->bindParam(':category_id', $book['category_id']);
        $stmt->bindParam(':isbn', $book['isbn']);
        $stmt->bindParam(':publisher', $book['publisher']);
        $stmt->bindParam(':publication_date', $book['publication_date']);
        $stmt->bindParam(':pages', $book['pages']);
        $stmt->bindParam(':language', $book['language']);
        $stmt->bindParam(':stock_quantity', $book['stock_quantity']);
        $stmt->bindParam(':featured', $book['featured']);
        $stmt->execute();
    }
    
    // Insert sample admin user (email: admin@example.com, password: admin123)
    $adminPassword = password_hash('admin123', PASSWORD_DEFAULT);
    
    $stmt = $conn->prepare("
        INSERT INTO users (name, email, password, is_admin) 
        VALUES ('Admin User', 'admin@example.com', :password, 1)
    ");
    $stmt->bindParam(':password', $adminPassword);
    $stmt->execute();
    
    // Insert sample regular user (email: user@example.com, password: user123)
    $userPassword = password_hash('user123', PASSWORD_DEFAULT);
    
    $stmt = $conn->prepare("
        INSERT INTO users (name, email, password, is_admin) 
        VALUES ('Regular User', 'user@example.com', :password, 0)
    ");
    $stmt->bindParam(':password', $userPassword);
    $stmt->execute();
}