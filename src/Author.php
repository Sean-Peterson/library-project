<?php
    class Author
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
            $GLOBALS['DB']->exec("INSERT INTO authors (first_name, last_name) VALUES ('{$this->getFirstName()}','{$this->getLastName()}');");
            $this->id = $GLOBALS['DB']->lastInsertId();
        }

        static function getAll()
        {
            $returned_authors = $GLOBALS['DB']->query("SELECT * FROM authors;");
            $authors = array();
            foreach($returned_authors as $author)
            {
                $new_author = new Author($author['first_name'], $author['last_name'], $author['id']);
                array_push($authors, $new_author);
            }
            return $authors;
        }
        static function find($id)
        {
            $find_author = $GLOBALS['DB']->query("SELECT * FROM authors WHERE id = {$id};");
            $found_author = null;
            foreach($find_author as $author)
            {
                $found_author = new Author($author['first_name'], $author['last_name'], $author['id']);
            }
            return $found_author;
        }

        static function deleteAll()
        {
            $GLOBALS['DB']->exec("DELETE FROM authors;");
        }

        function delete()
        {
            $GLOBALS['DB']->exec("DELETE FROM authors WHERE id = {$this->getId()};");
        }

        function updateAuthor($first_name, $last_name)
        {
            $GLOBALS['DB']->exec("UPDATE authors SET first_name = '{$first_name}' WHERE id = {$this->getId()};");
            $GLOBALS['DB']->exec("UPDATE authors SET last_name = '{$last_name}' WHERE id = {$this->getId()};");
            $this->$first_name = $first_name;
            $this->$last_name = $last_name;
        }



    }



 ?>
