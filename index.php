<?php
  session_start();
  require 'config/config.php';
  require 'config/common.php';

  if(empty($_SESSION['user_id']) && empty($_SESSION['logged_in'])) {
    header('Location: login.php');
  }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>User | Blog</title>

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
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <h1 class="text-center">Blog Site</h1>
      </div><!-- /.container-fluid -->
    </section>
    <?php
                    if(!empty($_GET['pageno'])){
                      $pageno = $_GET['pageno'];
                    }else {
                      $pageno = 1;
                    }

                    $numOfrecs = 6;
                    $offset = ($pageno -1) * $numOfrecs;

                    if(empty($_POST['search'])) {
                      $stmt = $pdo->prepare("SELECT * FROM posts ORDER BY id DESC");
                      $stmt->execute();
                      $rawResult = $stmt->fetchAll();
                      $total_pages = ceil(count($rawResult)/$numOfrecs);

                      $stmt = $pdo->prepare("SELECT * FROM posts ORDER BY id DESC LIMIT $offset, $numOfrecs");
                      $stmt->execute();
                      $result = $stmt->fetchAll();
                    }else {
                      $searchKey = $_POST['search'];
                      $stmt = $pdo->prepare("SELECT * FROM posts WHERE title LIKE '%$searchKey%' ORDER BY id DESC");
                      $stmt->execute();
                      $rawResult = $stmt->fetchAll();
                      $total_pages = ceil(count($rawResult)/$numOfrecs);
                      print_r($rawResult);
  
                      $stmt = $pdo->prepare("SELECT * FROM posts WHERE title LIKE '%$searchKey%' ORDER BY id DESC LIMIT $offset, $numOfrecs");
                      $stmt->execute();
                      $result = $stmt->fetchAll();
                    }
                  ?>
    <!-- Main content -->
    <section class="content">
    <div class="row">
    <?php
                        if($result){
                          $i = 1;
                          foreach($result as $value){ ?>
                          <div class="col-md-4">
            <!-- Box Comment -->
            <div class="card card-widget">
              <div class="card-header">
                <div class="card-title float-none">
                <h5 class="text-center"><?php echo $value['title']; ?></h5>
                </div>
                <!-- /.user-block -->
              </div>
              <!-- /.card-header -->
              <div class="card-body">
              <a href="blogdetails.php?id=<?php echo $value['id']; ?>">
                <img class="img-fluid pad" src="admin/images/<?php echo $value['image'] ?>" style="height: 200px !important">
              </a>

                <p>I took this photo this morning. What do you guys think?</p>
                <button type="button" class="btn btn-default btn-sm"><i class="fas fa-share"></i> Share</button>
                <button type="button" class="btn btn-default btn-sm"><i class="far fa-thumbs-up"></i> Like</button>
                <span class="float-right text-muted">127 likes - 3 comments</span>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
                          <?php 
                          $i++;
                          }
                        }
                        ?>
        </div>
        <div class="row float-right">
                    <ul class="pagination">
                      <li class="page-item"><a class="page-link" href="?pageno=1">First</a></li>
                      <li class="page-item <?php if($pageno <= 1) echo 'disabled'; ?>">
                        <a class="page-link" href="<?php echo $pageno <=1 ? '#' : '?pageno='.($pageno-1); ?>">Previous</a>
                      </li>
                      <li class="page-item disabled">
                        <a class="page-link" href="#"><?php echo $pageno; ?></a>
                      </li>
                      <li class="page-item <?php if($pageno >= $total_pages) echo 'disabled'; ?>">
                        <a class="page-link" href="<?php echo $pageno >= $total_pages ? '#' : '?pageno='.($pageno+1); ?>">Next</a>
                      </li>
                      <li class="page-item"><a class="page-link" href="?pageno=<?php echo $total_pages; ?>">Last</a></li>
                    </ul>
                  </div> <br><br>
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