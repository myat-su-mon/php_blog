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
    if(empty($_POST['title']) || empty($_POST['content'])){
      if(empty($_POST['title'])){
        $titleError = 'Title cannot be null';
      }
      if(empty($_POST['content'])){
        $contentError = 'Content is required';
      }
    } else {
      $id = $_POST['id'];
      $title = $_POST['title'];
      $content = $_POST['content'];
  
      if($_FILES['image']['name'] != null) {
          $file = 'images/'.($_FILES['image']['name']);
          $imageType = pathinfo($file, PATHINFO_EXTENSION);
  
          if($imageType != 'png' && $imageType != 'jpg' && $imageType != 'jpeg'){
              echo "<script>alert('Image must be png, jpg, jpeg')</script>";
          }else {
              $image = $_FILES['image']['name'];
              move_uploaded_file($_FILES['image']['tmp_name'], $file);
  
              $stmt = $pdo->prepare("UPDATE posts SET title='$title', content='$content', image='$image' WHERE id='$id'");
              $result = $stmt->execute();            
              if($result) {
                  echo "<script>alert('Successfully Updated');window.location.href='index.php';</script>";
              }
          }
      }else {
          $stmt = $pdo->prepare("UPDATE posts SET title='$title', content='$content' WHERE id='$id'");
          $result = $stmt->execute();            
          if($result) {
              echo "<script>alert('Successfully Updated');window.location.href='index.php';</script>";
              header('Location: index.php');
          }
      }
    }
  }

  $stmt = $pdo->prepare("SELECT * FROM posts WHERE id=".$_GET['id']);
  $stmt->execute();
  $result = $stmt->fetchAll();
?>

<?php include('header.php') ?>
  <!-- Main content -->
        <div class="content">
          <div class="container-fluid">
            <div class="row">
              <div class="col-12">
                <div class="card">
                    <div class="card-body">
                    <form action="" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="_token" value="<?php echo $_SESSION['_token']; ?>">
                    <div class="form-group">
                        <label for="title">Title</label><?php echo empty($titleError)? '': '*'.$titleError; ?></p>
                        <input class="form-control" type="text" name="title" value="<?php echo escape($result[0]['title']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="content">Content</label><?php echo empty($contentError)? '': '*'.$contentError; ?></p>
                        <textarea class="form-control" name="content" cols="30" rows="10"> value="<?php echo escape($result[0]['content']) ?>" </textarea>
                    </div>
                    <div class="form-group">
                        <label for="image">Image</label><br>
                        <img src="images/<?php echo $result[0]['image'] ?>" width="150" height="150"><br><br>
                        <input type="file" name="image">
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