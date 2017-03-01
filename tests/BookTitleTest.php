<?php

/**
* @backupGlobals disabled
* @backupStaticAttributes disabled
*/

require_once "src/BookTitle.php";

$server = 'mysql:host=localhost:8889;dbname=library_test';
$username = 'root';
$password = 'root';
$DB = new PDO($server, $username, $password);

class BookTitleTest extends PHPUnit_Framework_TestCase
{
    protected function tearDown()
    {
        BookTitle::deleteAll();
    }
    function test_save_getAll()
    {
        // Arrange
        $first_name = 'Dave';
        $last_name = 'g';
        $test_book_title = new BookTitle($first_name, $last_name);
        $test_book_title->save();

        $first_name2 = 'doug';
        $last_name2 = 'p';
        $test_book_title2 = new BookTitle($first_name, $last_name);
        $test_book_title2->save();
        // Act
        $result = BookTitle::getAll();
        $expected_result = array($test_book_title, $test_book_title2);
        // Assert
        $this->assertEquals($result, $expected_result);
    }
}
?>
