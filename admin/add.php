<?php
  session_start();
  require '../config/config.php';
  require '../config/common.php';

  if(empty($_SESSION['user_id']) && empty($_SESSION['logged_in'])) {
    header('Location: login.php');
  }

  if($_SESSION['role'] != 1){
    header('Location: login.php');
  }

  if($_POST){
    if(!hash_equals($_SESSION['_token'], $_POST['_token'])) die();
    if(empty($_POST['title']) || empty($_POST['content']) || empty($_FILES['image'])){
      if(empty($_POST['title'])){
        $titleError = 'Title cannot be null';
      }
      if(empty($_POST['content'])){
        $contentError = 'Content is required';
      }
      if(empty($_FILES['image'])){
        $imageError = 'Image cannot be null';
      }

    }else{
      $file = 'images/'.($_FILES['image']['name']);
      $imageType = pathinfo($file, PATHINFO_EXTENSION);

      if($imageType != 'png' && $imageType != 'jpg' && $imageType != 'jpeg'){
          echo "<script>alert('Image must be png, jpg, jpeg')</script>";
      }else {
        $title = $_POST['title'];
        $content = $_POST['content'];
        $image = $_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'], $file);

        $stmt = $pdo->prepare("INSERT INTO posts(title, content, image, author_id) VALUES (:title, :content, :image, :author_id)");
        $result = $stmt->execute(
            array(':title' => $title, ':content' => $content, ':image' => $image, ':author_id' => $_SESSION['user_id'])
        );

        if($result) {
            echo "<script>alert('Successfully added');window.location.href='index.php';</script>";
        }

    }
    }
  }
?>

<?php include('header.php') ?>
  <!-- Main content -->
        <div class="content">
          <div class="container-fluid">
            <div class="row">
              <div class="col-12">
                <div class="card">
                    <div class="card-body">
                    <form action="add.php" method="post" enctype="multipart/form-data">
                      <input type="hidden" name="_token" value="<?php echo $_SESSION['_token']; ?>">
                    <div class="form-group">
                        <label for="title">Title</label><p class="text-danger"><?php echo empty($titleError)? '': '*'.$titleError; ?></p>
                        <input class="form-control" type="text" name="title" >
                    </div>
                    <div class="form-group">
                        <label for="content">Content</label><p class="text-danger"><?php echo empty($contentError)? '': '*'.$contentError; ?></p>
                        <textarea class="form-control" name="content" cols="30" rows="10"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="image">Image</label><p class="text-danger"><?php echo empty($imageError)? '': '*'.$imageError; ?></p>
                        <input type="file" name="image" required>
                    </div>
                    <div class="form-group">
                        <a href="index.php"  class="btn btn-default">Back</a>
                        <input type="submit" value="Submit" class="btn btn-success">
                    </div>
                  </form>
                    </div>
                </div>
              </div>
            </div>
            <!-- /.row -->
          </div>
          <!-- /.container-fluid -->
        </div>
        <!-- /.content -->
    <?php include('footer.html'); ?>