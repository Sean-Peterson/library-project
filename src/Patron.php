<?php
    class Patron
    {
        private $first_name;
        private $last_name;
        private $id;

        function __construct($first_name, $last_name, $id=null)
        {
            $this->first_name = $first_name;
            $this->last_name = $last_name;
            $this->id = $id;
        }

        function getFirstName(){
    		return $this->first_name;
    	}

    	function setFirstName($first_name){
    		$this->first_name = $first_name;
    	}

    	function getLastName(){
    		return $this->last_name;
    	}

    	function setLastName($last_name){
    		$this->last_name = $last_name;
    	}
        function getFullName(){
            return $this->getFirstName() . " " . $this->getLastName();
        }

    	function getId(){
    		return $this->id;
    	}

        function save()
        {
            $GLOBALS['DB']->exec("INSERT INTO patrons (first_name, last_name) VALUES ('{$this->getFirstName()}','{$this->getLastName()}');");
            $this->id = $GLOBALS['DB']->lastInsertId();
        }

        static function getAll()
        {
            $returned_patrons = $GLOBALS['DB']->query("SELECT * FROM patrons;");
            $patrons = array();
            foreach($returned_patrons as $patron)
            {
                $new_patron = new Patron($patron['first_name'], $patron['last_name'], $patron['id']);
                array_push($patrons, $new_patron);
            }
            return $patrons;
        }
        static function findPatron($id)
        {
            $find_patron = $GLOBALS['DB']->query("SELECT * FROM patrons WHERE id = {$id};");
            $found_patron = null;
            foreach($find_patron as $patron)
            {
                $found_patron = new Patron($patron['first_name'], $patron['last_name'], $patron['id']);
            }
            return $found_patron;
        }

        static function deleteAll()
        {
            $GLOBALS['DB']->exec("DELETE FROM patrons;");
        }

        function delete()
        {
            $GLOBALS['DB']->exec("DELETE FROM patrons WHERE id = {$this->getId()};");
        }

        function updatePatron($first_name, $last_name)
        {
            $GLOBALS['DB']->exec("UPDATE patrons SET {$first_name} = '{$last_name}' WHERE id = {$this->getId()};");
            $this->$first_name = $first_name;
            $this->$last_name = $last_name;
        }
        function getBorrowedBooks()
        {
            $borrowed_books = $GLOBALS['DB']->query("SELECT .* FROM
                JOIN  ON ( = )
                JOIN  ON (.id = )
                WHERE  = {$this->getId()};");
            $books = [];
            if ($borrowed_books == null)
            {
                return null;
            }
            foreach($borrowed_books as $book)
            {

                $id = $book['book_title_id'];
                $new_book = BookTitle($id);
                array_push($books, $new_book);
            }
            return $books;
        }

    }



 ?>
