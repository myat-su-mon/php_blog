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
    if(empty($_POST['name']) || empty($_POST['email'])){
      if(empty($_POST['name'])){
        $nameError = 'Name cannot be empty';
      }
      if(empty($_POST['email'])){
        $emailError = 'Email is required';
      }
    }elseif(!empty($_POST['password']) && strlen($_POST['password']) < 4 ){
      $passwordError = 'Password should be 4 characters at least';
    }else {
      $id = $_POST['id'];
      $name = $_POST['name'];
      $email = $_POST['email'];
      $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
      if(empty($_POST['role'])){
          $role = 0;
      }else {
          $role = 1;
      }

      $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email AND id != :id");
      $stmt->execute(array(':email' => $email, ':id' => $id));
      $user = $stmt->fetch(PDO::FETCH_ASSOC);

      if($user) {
          echo "<script>alert('User Email already exists');</script>";
      }else {
          if($password != null){
            $stmt = $pdo->prepare("UPDATE users SET name='$name', email='$email', password='$password', role='$role' WHERE id='$id'");
          }else {
            $stmt = $pdo->prepare("UPDATE users SET name='$name', email='$email', role='$role' WHERE id='$id'");
          }
          $result = $stmt->execute();            
          if($result) {
              echo "<script>alert('Successfully Updated');window.location.href='user_list.php';</script>";
          }
      }
    }
  }

  $stmt = $pdo->prepare("SELECT * FROM users WHERE id=".$_GET['id']);
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
                    <form action="user_edit.php" method="post">
                    <input type="hidden" name="_token" value="<?php echo $_SESSION['_token']; ?>">
                    <div class="form-group">
                        <label for="name">Name</label><p class="text-danger"><?php echo empty($nameError)? '': '*'.$nameError; ?></p>
                        <input class="form-control" type="text" name="name" value="<?php echo escape($result[0]['name']) ?>">
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label><p class="text-danger"><?php echo empty($emailError)? '': '*'.$emailError; ?></p>
                        <input class="form-control" type="email" name="email" value="<?php echo escape($result[0]['email']) ?>">
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label><p class="text-danger"><?php echo empty($passwordError)? '': '*'.$passwordError; ?></p>
                        <span>This user already has a password</span>
                        <input class="form-control" type="password" name="password">
                    </div>
                    <div class="form-group">
                        <label for="role">Admin</label>
                        <input class="ml-3" type="checkbox" name="role" <?php echo $result[0]['role']? 'checked': '' ?>>
                    </div>
                    <div class="form-group">
                        <a href="user_list.php"  class="btn btn-default">Back</a>
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