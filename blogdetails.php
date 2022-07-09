<?php
  session_start();
  require 'config/config.php';
  require 'config/common.php';

  if(empty($_SESSION['user_id']) && empty($_SESSION['logged_in'])) {
    header('Location: login.php');
  }

  $stmt = $pdo->prepare("SELECT * FROM posts WHERE id=".$_GET['id']);
  $stmt->execute();
  $result = $stmt->fetchAll();

  $postId = $_GET['id'];

  $cmtstmt = $pdo->prepare("SELECT * FROM comments WHERE post_id=".$postId);
  $cmtstmt->execute();
  $cmtresult = $cmtstmt->fetchAll();
  
  $auresult = [];
  if($cmtresult){
    foreach ($cmtresult as $key => $value) {
      $authorId = $cmtresult[$key]['author_id'];
      $austmt = $pdo->prepare("SELECT * FROM users WHERE id=".$authorId);
      $austmt->execute();
      $auresult[] = $austmt->fetchAll();
    }
  }
  
  if (!empty($_POST)){
    if(empty($_POST['comment'])){
      if(empty($_POST['comment'])){
        $commentError = 'Comment cannot be empty';
      }else {
        $comment = $_POST['comment'];
        $stmt = $pdo->prepare("INSERT INTO comments(content, author_id, post_id) VALUES (:content, :author_id, :post_id)");
        $result = $stmt->execute(
          array(':content' => $comment, ':author_id' => $_SESSION['user_id'], ':post_id' => $postId)
        );

        if($result) {
            header('Location: blogdetails.php?id='.$postId);
        }
      }
    }
  }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Blog Details</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="dist/css/adminlte.min.css">
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
  
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper ml-0">
    <!-- Main content -->
    <section class="content">
    <div class="row">
          <div class="col-md-12">
            <!-- Box Comment -->
            <div class="card card-widget">
            <div class="card-header">
                <div class="card-title float-none">
                <h5 class="text-center"><?php echo escape($result[0]['title']); ?></h5>
                </div>
                <!-- /.user-block -->
                
              </div>
              <!-- /.card-header -->
              <div class="card-body">
              <img class="img-fluid pad" src="admin/images/<?php echo $result[0]['image']; ?>">

                <p><?php echo escape($result[0]['content']); ?></p>
                <span class="float-right text-muted">127 likes - 3 comments</span>
                <span><h4>Comments</h4></span><hr>
                <a href="index.php" type="button" class="btn btn-default">Go Back</a>
              </div>
              <!-- /.card-body -->
              <div class="card-footer card-comments">
                <?php if($cmtresult) { ?>
                  <?php foreach($cmtresult as $key=>$value){ ?>
                    <div class="card-comment">
                  <!-- User image -->
                  <img class="img-circle img-sm" src="dist/img/user3-128x128.jpg" alt="User Image">

                  <div class="comment-text">
                    <span class="username">
                      <?php echo escape($auresult[$key][0]['name']); ?>
                      <span class="text-muted float-right"><?php echo $value['created_at']; ?></span>
                    </span><!-- /.username -->
                    <?php echo escape($value['content']); ?><br>
                  </div>
                  <!-- /.comment-text -->
                </div>
                  <?php }?>
                <?php }?>
                <!-- /.card-comment -->
              </div>
              <!-- /.card-footer -->
              <div class="card-footer">
                <form action="" method="post">
                <input type="hidden" name="_token" value="<?php $_SESSION['_token']; ?>">
                  <img class="img-fluid img-circle img-sm" src="dist/img/user4-128x128.jpg" alt="Alt Text">
                  <!-- .img-push is used to add margin to elements next to floating images -->
                  <div class="img-push">
                  <p class="text-danger"><?php echo empty($commentError)? '': '*'.$commentError; ?></p>
                    <input type="text" name="comment" class="form-control form-control-sm" placeholder="Press enter to post comment">
                  </div>
                </form>
              </div>
              <!-- /.card-footer -->
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->
        </div>
    </section>
    <!-- /.content -->

    <a id="back-to-top" href="#" class="btn btn-primary back-to-top" role="button" aria-label="Scroll to top">
      <i class="fas fa-chevron-up"></i>
    </a>
  </div>
  <!-- /.content-wrapper -->

  <footer class="main-footer ml-0">
        <!-- To the right -->
        <div class="float-right d-none d-sm-inline mb-3 mr-5"><a href="logout.php" type="button" class="btn btn-default">Logout</a></div>
        <!-- Default to the left -->
        <strong>Copyright &copy; 2022
          <a href="#">Su Mon</a></strong
        >
        All rights reserved.
      </footer>

  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
  </aside>
  <!-- /.control-sidebar -->
</div>
<!-- ./wrapper -->

<!-- jQuery -->
<script src="plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/adminlte.min.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="dist/js/demo.js"></script>
</body>
</html>