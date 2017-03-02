<?php
    date_default_timezone_set('America/Los_Angeles');

    require_once __DIR__."/../vendor/autoload.php";
    require_once __DIR__."/../src/Author.php";
    require_once __DIR__."/../src/BookTitle.php";
    require_once __DIR__."/../src/Patron.php";


    $app = new Silex\Application();

    $app['debug'] = true;


    $server = 'mysql:host=localhost:8889;dbname=library';
    $username = 'root';
    $password = 'root';
    $DB = new PDO($server, $username, $password);

    $app->register(new Silex\Provider\TwigServiceProvider(), array(
        'twig.path' => __DIR__.'/../views'
    ));

    use Symfony\Component\HttpFoundation\Request;
    Request::enableHttpMethodParameterOverride();

    $app->get("/", function() use ($app) {
        return $app['twig']->render("index.html.twig");
    });

    $app->get("/librarians", function() use ($app) {
        return $app['twig']->render("librarians.html.twig");
    });


    $app->get("/authors", function() use ($app) {
        return $app['twig']->render("authors.html.twig", array('books' => BookTitle::getAll(), 'authors' => Author::getAll()));
    });

    $app->get("/author/{id}", function($id) use ($app) {

        return $app['twig']->render("edit_author.html.twig", array('books' => BookTitle::getAll(), 'author' => Author::find($id)));
    });

    $app->patch("/patch/edit_author/{id}", function($id) use ($app) {
        $author = Author::find($id);
        $first_name = $_POST['first_name'];
        $last_name = $_POST['last_name'];
        $author->updateAuthor($first_name, $last_name);
        return $app['twig']->render("edit_author.html.twig", array('books' => BookTitle::getAll(), 'author' => Author::find($id)));
    });

    $app->delete("/delete/author/{id}", function($id) use ($app) {
        $author = Author::find($id);
        $author->delete();
        return $app['twig']->render("authors.html.twig", array('books' => BookTitle::getAll(), 'authors' => Author::getAll()));
    });

    $app->post("/post/add_author", function() use ($app) {
        $first_name = $_POST['first_name'];
        $last_name = $_POST['last_name'];
        $author = new Author($first_name, $last_name);
        $author->save();
        return $app['twig']->render("authors.html.twig", array('books' => BookTitle::getAll(), 'authors' => Author::getAll()));
    });

    $app->post("/post/add_author_to_book/{book_id}", function($book_id) use ($app) {
        $book = BookTitle::find($book_id);
        $book->addAuthor($_POST['author']);
        return $app['twig']->render("books.html.twig", array('books' => BookTitle::getAll(), 'authors' => Author::getAll()));
    });

    $app->post("/post/add_book_to_author/{author_id}", function($author_id) use ($app) {
        $author = Author::find($author_id);
        $author->addBook($_POST['book']);
        return $app['twig']->render("authors.html.twig", array('books' => BookTitle::getAll(), 'authors' => Author::getAll()));
    });

    $app->get("/books", function() use ($app) {
        return $app['twig']->render("books.html.twig", array('books' => BookTitle::getAll(), 'authors' => Author::getAll()));
    });


    $app->post("/post/add_book", function() use ($app) {
        $new_book = new BookTitle($_POST['title']);
        $new_book->save();

        $copies = $_POST['number_of_copies'];
        for ($i=0; $i < $copies; $i++) {
            $GLOBALS['DB']->exec("INSERT INTO book_copies (book_title_id) VALUES ({$new_book->getId()});");
        }

        return $app['twig']->render("books.html.twig", array('books' => BookTitle::getAll(), 'authors' => Author::getAll()));
    });

    $app->get("/book/{id}", function($id) use ($app) {
        //USE availableCopies FUNCTION WHEN REFACTORING
        $current_book = BookTitle::find($id);
        $number_of_copies = $GLOBALS['DB']->query("SELECT COUNT(*) FROM book_copies WHERE book_title_id = {$id} AND patron_id IS NULL;");
        $number_of_copies = $number_of_copies->fetchAll(PDO::FETCH_ASSOC);
        $copies = $number_of_copies[0];
        $something = ($copies['COUNT(*)']);
        $checkOutBooks = $current_book->getCheckedOutBooks();
        return $app['twig']->render("edit_book.html.twig", array('copies'=>$something ,'book' => BookTitle::find($id), 'authors' => Author::getAll(), 'checkout_books' => $checkOutBooks));
    });

    $app->post("/return_book/{id}", function($id) use ($app) {
        BookTitle::returnBook($_POST['returned_book']);
        return $app->redirect('/book/' . $id);
    });

    $app->patch("/patch/edit_book/{id}", function($id) use ($app) {
        $book = BookTitle::find($id);
        $title = $_POST['title'];
        $book->updateTitle($title);
        return $app['twig']->render("edit_book.html.twig", array('books' => BookTitle::getAll(), 'book' => BookTitle::find($id), 'authors' => Author::getAll()));
    });

    $app->delete("/delete/book/{id}", function($id) use ($app) {
        $book = BookTitle::find($id);
        $book->delete();
        return $app['twig']->render("books.html.twig", array('books' => BookTitle::getAll(), 'authors' => Author::getAll()));
    });

    $app->get("/patrons", function() use ($app) {
        return $app['twig']->render("patrons.html.twig", array('patrons' => Patron::getAll()));
    });

    $app->get("/patron/{id}", function($id) use ($app) {
        $patron = Patron::find($id);
        return $app['twig']->render("patron_login.html.twig", array('patron' => $patron));
    });

    $app->post("/add_patron", function() use ($app) {
        $new_patron = new Patron($_POST['first_name'], $_POST['last_name']);
        $new_patron->save();
        return $app['twig']->render("patrons.html.twig", array('patrons' => Patron::getAll(), 'books' => BookTitle::getAll(), 'authors' => Author::getAll()));
    });

    $app->get("/patron_view/{id}", function($id) use ($app) {
        $patron = Patron::find($id);
        $borrowed_books = $patron->getBorrowedBooks();
        $history = $patron->history();
        return $app['twig']->render("patron_view.html.twig", array('books' => BookTitle::getAll(),'my_books' => $borrowed_books,'patron' => $patron, 'history' => $history));
    });

    $app->post("/patron/checkout/{id}", function($id) use ($app) {
        $patron = Patron::find($id);
        $checkout_date = date("Y-m-d");
        $due_date = date('Y-m-d', strtotime(' +1 week'));
        $patron->checkOutBook($_POST['checkout_book'], $checkout_date, $due_date);
        $borrowed_books = $patron->getBorrowedBooks();
        return $app->redirect('/patron_view/' . $id);
    });

    return $app;
 ?>
