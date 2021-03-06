<?php
include "../lib/php/functions.php";


$empty_product = (object) [
  "name"=>"",
  "price"=>"",
  "description"=>"",
  "thumbnail"=>"",
  "images"=>"",
  "quantity"=>"",
  "category"=>"",
];


// CRUD LOGIC
try{
$conn = makePDOConn();
switch(@$_GET['action']) {
  case "update":
    $statement = $conn->prepare("UPDATE
    `products`
    SET 
        `name`=?,
        `price`=?,
        `category`=?,
        `description`=?,
        `thumbnail`=?,
        `images`=?,
        `date_modified`=NOW()
    WHERE `id`=?
      ");
    $statement->execute([
        $_POST['product-name'],
        $_POST['product-price'],
        $_POST['product-category'],
        $_POST['product-description'],
        $_POST['product-thumbnail'],
        $_POST['product-images'],
        $_GET['id']
    ]);

    header("location:{$_SERVER['PHP_SELF']}?id={$_GET['id']}");
    break;
  case "create":
    $statement = $conn->prepare("INSERT INTO
    `products`
    (
        `name`,
        `price`,
        `category`,
        `description`,
        `thumbnail`,
        `images`,
        `date_create`,
        `date_modified`
    )
    VALUES
    (?,?,?,?,?,?,NOW(),NOW())
    ");

    $statement->execute([
        $_POST['product-name'],
        $_POST['product-price'],
        $_POST['product-category'],
        $_POST['product-description'],
        $_POST['product-thumbnail'],
        $_POST['product-images'], 
    ]);

    $id = $conn->lastInsertId();

    header("location:{$_SERVER['PHP_SELF']}?id=$id");
    break;
  case "delete":
    $statement = $conn->prepare("DELETE FROM `products` WHERE id=?");

    $statement->execute([$_GET['id']]);
    $id = $conn->lastInsertId();

    header("location:{$_SERVER['PHP_SELF']}");
    break;
}
} catch(PDOException $e) {
  die($e->getMessage());
}

//TEMPLATES
function makeListItemTemplate($r,$o) {
return $r.<<<HTML
<div class="col-xs-12 col-md-12 col-xl-4">
  <div class="itemlist-item display-flex" style="flex-direction: column; align-items: center;">
      <div class="flex-none">
        <div class="image-square">
          <img src="../$o->thumbnail">
        </div>
      </div>
      <div class="flex-stretch">
        <div><strong>$o->name</strong></div>
        <div><strong>$o->category</strong></div>
      </div>
      <div class="flex-stretch display-flex" style="margin-top: 8px; width: 100%; flex-direction: row; align-items: center; justify-content: center;">
          <div><a class="form-button"  href="./?id=$o->id">edit</a></div>
          <div><a class="form-button"  href="../product_item.php?id=$o->id" style="margin-left: 20px;">visit</a></div>
      </div>
  </div>
</div>
HTML;
}

function makeProductForm($o) {

$id = $_GET['id'];

$addoredit = $id == "new" ? 'Add' : 'Edit';
$createorupdate = $id == "new" ? 'create' : 'update';
$deletebutton = $id == "new" ? '' : <<<HTML
<button class="form-button"><a href="{$_SERVER['PHP_SELF']}?id=$id&action=delete">Delete</a></button>
HTML;

$images = array_reduce(explode(",",$o->images), function($r,$p) {
  return $r."'$p'; ";
});

$data_show = $id == "new" ? "" : <<<HTML
<div class="card soft">

<div class="product-main">
  <img src="../$o->thumbnail">
</div>

<h2>$o->name</h2>
<div class="form-control">
  <strong>Price</strong>
  <span>$o->price</span>
</div>
<div class="form-control">
  <strong>Category</strong>
  <span>$o->category</span>
</div>
<div class="form-control">
  <strong>Description</strong>
  <span>$o->description</span>
</div>

<div class="form-control">
  <strong>Thumbnail</strong>
  <span>$o->thumbnail</span>
</div>

<div class="form-control">
  <strong>Other images</strong>
  <span>$images</span>
</div>

</div>
HTML;


echo <<<HTML
<div class="container" style="margin-left: 0; margin-right: 0">
  <nav class="nav-pills">
    <div class="card flat">
    <nav class="nav-crumbs">
      <ul>
        <li><a href="index.php"><i class="fas fa-arrow-left" style="color:black;"></i> Back</a></li>    
      </ul>
    </nav>
  </div>
  </nav>
  <form method="post" action="{$_SERVER['PHP_SELF']}?id=$id&action=$createorupdate">
    <div class="grid gap" style="padding-top:0px;">
HTML;

if ($data_show) {
echo <<<HTML
<div class="col-xs-12 col-md-12">
  $data_show
  <br>
  $deletebutton
</div>
HTML;
}

echo <<<HTML
      <div class="col-xs-12 col-md-12 col-xl-12">
        <div class="card soft">
        <h2>$addoredit Product</h2>
        <div class="form-control">
          <label class="form-label" for="product-name">Name</label>
          <input class="form-input" id="product-name" name="product-name" value="$o->name">
        </div>

        <div class="form-control">
          <label class="form-label" for="product-price">Price</label>
          <input class="form-input" id="product-price" name="product-price" value="$o->price">
        </div>

        <div class="form-control">
          <label class="form-label" for="product-category">Category</label>
          <input class="form-input" id="product-categorye" name="product-category" value="$o->category">
        </div>

        <div class="form-control">
          <label class="form-label" for="product-description">Description</label>
          <textarea class="form-input" id="product-descriptione" name="product-description">$o->description</textarea>
        </div>

        <div class="form-control">
          <label class="form-label" for="product-thumbnail">Thumbnail</label>
          <input class="form-input" id="product-thumbnail" name="product-thumbnail" value="$o->thumbnail">
        </div>
       
       <div class="form-control">
          <label class="form-label" for="product-images">Other Images</label>
          <input class="form-input" id="product-images" name="product-images" value="$o->images">
        </div>

        <div class="form-control">
          <input type="submit" class="form-button" value="Submit">
        </div>
        </div>
      </div>
    </div>
  </form>
</div>
HTML;

}

// file_put_contents, json_encode, explode, $_POST
// CRUD - Create Read Update Delete

$USER_DATA_FILE = "../data/users.json";

$users = getData($USER_DATA_FILE);

function showUserPage($user, $id) {
  $classes = implode(", ", $user->classes);

echo <<<HTML
<nav class='nav-crumbs'>
  <ul>
    <li><a href='users.php'>Back</a></li>
  </ul>
</nav>
<div>
  <h2>$user->name</h2>
  <div>
    <strong>Type</strong>
    <span>$user->type</span>
    <br>
  </div>
  <div>
    <strong>Email</strong>
    <span>$user->email</span>
  </div>
  <div>
    <strong>Classes</strong>
    <span>$classes</span>
  </div>
</div>

<br>
<h2>Update user information</h2>

<form method="post" action="users.php?id=$id">
  <label for="name">Type</label>
  <input type="text" id="name" name="name">
  </br>
  <label for="email">Email</label>
  <input type="text" id="email" name="email">
  </br>
  <label for="classes">Classes</label>
  <input type="text" id="classes" name="classes">
  </br>
  <input type="submit" value="Submit">
</form>
HTML;
}

include_once "../lib/php/functions.php";

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>Learning Data</title>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width">
  <script src="https://kit.fontawesome.com/041ded284b.js" crossorigin="anonymous"></script>
  <link rel="stylesheet" href="../lib/css/gridsystem.css" type="text/css">
  <link rel="stylesheet" href="../lib/css/styleguide.css" type="text/css">
  <link rel="stylesheet" href="../css/storetheme.css" type="text/css">
</head>
<body>

<?php include "admin_navbar.php" ?>

<main>
<?php
  if (isset($_GET['id'])) {
    if($_GET['id'] == "new") {
      makeProductForm($empty_product);
    } else {
      $rows = getRows($conn, "SELECT * FROM`products` WHERE `id`='{$_GET['id']}'");
      makeProductForm($rows[0]);
    }
  } else {
echo "
<div class='container'>
  <h2>Product List</h2>
  <div class='grid'>
    ";
    $rows = getRows ($conn,"SELECT * FROM `products`");
    echo array_reduce($rows,'makeListItemTemplate');
echo "
    </ul>
  </div>
</div>
";
  }
?>
</main>

<?php include "../parts/footer.php" ?>
  
</body>
</html>