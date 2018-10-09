<?php
  require 'vendor/autoload.php';
  use Intervention\Image\ImageManager;

  // phpinfo();
  // die();
  $errors = array();
  if (isset($_FILES["image"])) {
    $fileSize = $_FILES["image"]["size"];
    $fileTmp = $_FILES["image"]["tmp_name"];
    $fileType = $_FILES["image"]["type"];
    $errors = array();

    if ($fileSize > 5000000) { // if the file size is larger than 5mb
      array_push($errors, "The file is to large, must be under 5MB");
    }

    $validExtensions = array("jpeg", "jpg", "png");
    $fileNameArray = explode(".", $_FILES["image"]["name"]);

    $fileExt = strtolower(end($fileNameArray));

    if (in_array($fileExt, $validExtensions) === false) {
      array_push($errors, 'File type not allowed, can only be a jpg or png');
    }

    if (empty($errors)) {
      $destination = "images/uploads";
      if (! is_dir($destination)) {
        mkdir("images/uploads/", 0777, true);
      }

      $newFileName = uniqid() .".". $fileExt;

      $manager = new ImageManager();
      $mainImage = $manager->make($fileTmp);
      $mainImage->save($destination."/".$newFileName, 100);

      $thumbDestination = "images/uploads/thumbnails";
      if (! is_dir($thumbDestination)) {
        mkdir("images/uploads/thumbnails/", 0777, true);
      }

      $thumbnailImage = $manager->make($fileTmp);
      $thumbnailImage->resize(300, null, function($constraint) {
        $constraint->aspectRatio();
        $constraint->upsize();
      });
      $thumbnailImage->save($thumbDestination."/".$newFileName, 100);

    }

  } else {
    array_push($errors, "File not found, please upload an image");
  }

  $page = "image";
  $desc = "This is the description of the Contact Page";

  require("templates/header.php");
?>

<main role="main" class="inner cover">
    <h1 class="cover-heading">Image Upload Page</h1>

    <?php if($_FILES && !empty($errors)): ?>
        <div class="alert alert-danger" role="alert">
            <ul>
                <?php foreach($errors as $singleError): ?>
                    <li><?= $singleError; ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="post" action="image.php" enctype="multipart/form-data">
        <div class="form-group">
            <label for="image">Up Load An Image</label><br>
            <input type="file" name="image" >
        </div>
        <button type="submit" class="btn btn-outline-light btn-block">Submit</button>
    </form>

</main>

<?php require("templates/footer.php"); ?>
