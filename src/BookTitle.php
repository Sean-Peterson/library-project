<?php
require_once __DIR__."/../src/Author.php";

    class BookTitle
    {
        private $title;
        private $id;

        function __construct($title, $id=null)
        {
            $this->title = $title;
            $this->id = $id;
        }
        public function getTitle(){
    		return $this->title;
    	}

    	public function setTitle($title){
    		$this->title = $title;
    	}

    	public function getId(){
    		return $this->id;
    	}

    	public function setId($id){
    		$this->id = $id;
    	}

        function save()
        {
            $GLOBALS['DB']->exec("INSERT INTO book_titles (title) VALUES ('{$this->getTitle()}');");
            $this->id = $GLOBALS['DB']->lastInsertId();
        }

        static function getAll()
        {
            $returned_books = $GLOBALS['DB']->query("SELECT * FROM book_titles;");
            $books = array();
            foreach($returned_books as $book)
            {
                $new_book = new BookTitle ($book['title'], $book['id']);
                array_push($books, $new_book);
            }
            return $books;
        }
        static function find($id)
        {
            $find_book = $GLOBALS['DB']->query("SELECT * FROM book_titles WHERE id = {$id};");
            $found_book = null;
            foreach($find_book as $book)
            {
                $found_book = new BookTitle ($book['title'], $book['id']);
            }
            return $found_book;
        }

        static function deleteAll()
        {
            $GLOBALS['DB']->exec("DELETE FROM book_titles;");
        }

        function delete()
        {
            $GLOBALS['DB']->exec("DELETE FROM book_titles WHERE id = {$this->getId()};");
        }

        function updateTitle($new_title)
        {
            $GLOBALS['DB']->exec("UPDATE book_titles SET title = '{$new_title}' WHERE id = {$this->getId()};");
            $this->title = $new_title;
        }

        function addAuthor($author_id)
        {
        $GLOBALS['DB']->exec("INSERT INTO books_authors (book_title_id, author_id) VALUES ({$this->getId()}, {$author_id});");
        }

        function getAuthors()
        {
            $returned_authors = $GLOBALS['DB']->query("SELECT authors.* FROM book_titles
                JOIN books_authors ON (books_authors.book_title_id = book_titles.id)
                JOIN authors ON (authors.id = books_authors.author_id)
                WHERE book_titles.id = {$this->getId()};");
            $authors = [];
            if ($returned_authors == null)
            {
                return null;
            }
            foreach($returned_authors as $author)
            {
                $first_name = $author['first_name'];
                $last_name = $author['last_name'];
                $id = $author['id'];
                $new_author = new Author($first_name, $last_name, $id);
                array_push($authors, $new_author);
            }
            return $authors;
        }
        function availableCopies()
        {
            $number_of_copies = $GLOBALS['DB']->query("SELECT COUNT(*) FROM book_copies WHERE book_title_id = {$this->getId()} AND patron_id IS NULL;");
            $number_of_copies = $number_of_copies->fetchAll(PDO::FETCH_ASSOC);
            $copies = $number_of_copies[0];
            $available_copies = ($copies['COUNT(*)']);
            return $available_copies;
        }

        function getCheckedOutBooks()
        {
            $checked_out_copies = $GLOBALS['DB']->query("SELECT * FROM book_copies WHERE patron_id IS NOT NULL AND book_title_id = {$this->getId()};");
            $copies = $checked_out_copies->fetchAll(PDO::FETCH_ASSOC);
            $output_array = [];
            foreach ($copies as $copy)
            {
                $book_title_id = $copy['book_title_id'];
                $new_book = BookTitle::find($book_title_id);
                $title = $new_book->getTitle();

                $id = $copy['id'];
                $patron_id = $copy['patron_id'];
                $patron = Patron::find($patron_id);
                $new_array = array('title' => $title,'patron_id' =>$patron_id, 'patron'=> $patron, 'id' => $id);
                array_push($output_array, $new_array);
            }
            return $output_array;
        }

        static function returnBook($book_copies_id)
        {
            $GLOBALS['DB']->exec("UPDATE book_copies SET patron_id = NULL WHERE id = {$book_copies_id};");
        }



    }
 ?>
