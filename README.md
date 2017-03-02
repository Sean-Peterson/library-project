# _Library_

#### _This website is a library management system._

#### By _**Jake Campa and Sean Peterson**_


## Description

_Library allows the user to add one or many books to a library with a unique id. Library also allows is able to record books being checked out and returned. Each new user must create an account before checking out books. Library keeps records of all books checked out by each account._

## Setup/Installation Requirements

* In terminal run the following commands:

1. _Fork and clone this repository onto your desktop from_ [gitHub](https://github.com/Sean-Peterson/library-project).
2. Open chrome and enter localhost:8888/phpmyadmin
3. Click on Import, Choose File, desktop/library-project/data/library.sql.zip
4. Ensure [composer](https://getcomposer.org/) is installed on your computer.
5. Navigate to the root directory of the project in which ever CLI shell you are using and run the command: `composer install`.
6. To run tests enter `composer test` in terminal.
7. Create a local server in the /web folder within the project folder using the command: `php -S localhost:8000` (assuming you are using macOS - commands are different on other operating systems).
8. Open the directory http://localhost:8000/ in any standard web browser.
9. Make sure MAMP is started and change the MySQL number in the src files to your computer's sql port number.

## Specifications

1. homepage prompts user to select a route depending on if the user is a librarian or patron

2. on the librarian route the user can see books listed by author or books

3. on the books route the librarian can select a specific book or input a new book

4. on a specific book page the librarian can update the book's name, delete the book, add an author to the book, and return a book depending on the patron that gave the librarian the book

5. on the patron route the patron must create a user credentials

6. once a user has own credentials the user can then login

9. once logged in the user is shown the books that the user has checked out currently and the entire history of the user's checkouts. The page also allows the user to select a new book to checkout.


## Known Bugs

_None so far._

## Support and contact details

_Please contact seanpeterson11@gmail.com with concerns or comments._

## Technologies Used

* _HTML_
* _CSS_
* _PHP_
* _PHPUnit_
* _Composer_
* _Silex_
* _Twig_
* _MySQL_

### License

*MIT license*

Copyright (c) 2017 **_Jake Campa and Sean Peterson_**
