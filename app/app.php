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
    $app->get("/patrons", function() use ($app) {
        return $app['twig']->render("patrons.html.twig", array('patrons' => Patron::getAll()));
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
        $number_of_copies = $GLOBALS['DB']->query("SELECT COUNT(*) FROM book_copies WHERE book_title_id = {$id} AND patron_id IS NULL;");
        $number_of_copies = $number_of_copies->fetchAll(PDO::FETCH_ASSOC);
        $copies = $number_of_copies[0];
        $something = ($copies['COUNT(*)']);

        return $app['twig']->render("edit_book.html.twig", array('copies'=>$something ,'book' => BookTitle::find($id), 'authors' => Author::getAll()));
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

    return $app;
 ?>
