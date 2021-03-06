<?php
require_once "classes/autoload.php";
use com\soloproyectos\common\dom\node\DomNode;
header("Content-type: text/plain; charset=UTF-8");

$root = DomNode::createFromString(file_get_contents("test.xml"));

// Example 1
echo "*** Example 1: Traversing an XML document ***\n\n";
$books = $root->query("books item");
foreach ($books as $book) {
    echo "Title: " . $book->attr("title") . "\n";
    
    // gets genres
    echo "Genres: ";
    $genresIds = explode(" ", $book->attr("class"));
    foreach ($genresIds as $id) {
        $item = $root->query("genres item[id = '$id']");
        echo $item->text() . " ";
    }
    echo "\n";
    
    // prints author
    $item = $root->query("authors item[id = '" . $book->attr("author_id") . "'] name");
    echo "Author: " . $item->text() . "\n";
    
    // prints aditional info
    echo "ISBN: " . $book->query("isbn")->text() . "\n";
    echo "Available: " . $book->query("available")->text() . "\n";
    echo "Description: " . trim($book->query("description")->text()) . "\n";
    
    echo "---\n";
}

// Example 2
echo "\n*** Example 2: printing HTML contents ***\n\n";
$authors = $root->query("authors item[id = arthur-cclarke]");
echo $authors . "\n";

// Example 3
echo "\n*** Example 3: storing and retrieving data ***\n\n";
$item = $root->query("books item[id = 3]");
$item->data("myVar", array("This is", "an", "arbitrary", "data", "structure"));
print_r($item->data("myVar"));

// Example 4
echo "\n*** Example 4: changing attributes and inner texts ***\n\n";
$item = $root->query("authors item[id = isaac-asimov]");
// changes the biography
$item->query("bio")->text("Isaac Asimov is AWESOME.");
// changes or adds new attributes
$item->attr("id", "isaac-awesome");
$item->attr("title", "The Awesome man");
// prints the modified node
echo $item . "\n";

// Example 5
echo "\n*** Example 5: adding new nodes ***\n\n";
$authors = $root->query("authors");
// adds a new science fiction author
$authors->append(new DomNode("item", array("id" => "ray-bradbury", "title" => "Ray Bradbury"), function ($target) {
    $target->append(new DomNode("name", "Ray Bradbury"));
    $target->append(new DomNode("born", "1920-08-22"));
    $target->append(new DomNode("died", "2012-06-05"));
    $target->append(new DomNode("bio", "Ray Douglas Bradbury was an American fantasy, science fiction, horror and mystery fiction writer."));
}));
// prepends raw content
$authors->prepend(
    '<item id="mary-shelley">' .
        '<name>Mary Shelley</name>' .
        '<born>1797-08-30</born>' .
        '<died>1851-02-01</died>' .
        '<bio>Mary Shelley was an English novelist, short story writer, dramatist, essayist, biographer, and travel writer, best known for her Gothic novel Frankenstein: or, The Modern Prometheus.</bio>' .
    '</item>'
);
echo $authors . "\n";

// Example 6
echo "\n*** Example 6: removing and clearing nodes ***\n\n";
echo "Removing the 'The Songs of Distant Earth'...\n";
$book = $root->query("books item[id = 3]");
$book->remove();
echo "Removing all genres...\n";
$genres = $root->query("genres");
$genres->clear();
echo $genres . "\n";

// Example 7
echo "\n*** Example 7: counting the number of elements ***\n\n";
// selects all even books
$items = $root->query("books item:even");
echo "There are " . count($items) . " 'even' books.\n";
