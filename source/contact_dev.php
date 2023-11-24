<?php
session_start();
require("db.php");

if (!isset($_SESSION['rollnumber'])) {
  header("Location: login.php");
  exit();
}

if (isset($_GET['logout'])) {
  unset($_SESSION['rollnumber']);
  session_destroy();
  mysqli_close($db);
  header("Location: login.php");
  exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>Contact Developer - Housekeeper</title>
  <?php require("meta.php"); ?>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
  <style>
 .container {
  display: flex;
  justify-content: center;
  align-items: center;
  height: 100vh;
}

.box {
  text-align: center;
  padding: 20px;
  background-color: #7F82C8;
  border-radius: 10px;
  margin-top: -350px;
  width: 300px;
  height: 300px;
}


.profile-photo {
  width: 200px;
  height: 250px;
  border-radius: 50%;
  margin-bottom: 25px;
}

.social-icons a {
  color: #000;
  font-size: 34px;
  margin: 0 7px;
}




  </style>
</head>
<body>
  <!-- Side Navigation -->
  <?php require("sidenav.php"); ?>
  
  <!-- Main content -->
  <div class="main-content">
    <!-- Header -->
    <div class="header bg-background pb-6 pt-5 pt-md-6">
      <div class="container-fluid">
        <!-- notification message -->
        <?php if (isset($_SESSION['student_logged'])) : ?>
          <div class="alert alert-success alert-dismissible fade show" role="alert">
            <span class="alert-inner--icon"><i class="ni ni-like-2"></i></span>
            <span class="alert-inner--text"><strong>Welcome to the online Housekeeping service.</strong>
            <?php echo $_SESSION['student_logged']; unset($_SESSION['student_logged']); ?>
          </span>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
        <?php endif ?>

        <?php require("headerstats.php"); ?>
      </div>
    </div>
    
    <!-- Page content -->

        <div class="container">
            <div class="box">
        <img class="profile-photo" src="pratik_profile.jpeg" alt="Your Photo">
        <div class="social-icons">
          <a href="https://instagram.com/pratik___841?igshid=NGExMmI2YTkyZg==" target="_blank"><i class="fab fa-instagram" style="color: #E1306C;"></i></a>
          <a href="https://github.com/pratikmpp22" target="_blank"><i class="fab fa-github" style="color: #333;"></i></a>
          <a href="https://www.linkedin.com/in/pratik-patil-ba8b36193/" target="_blank"><i class="fab fa-linkedin" style="color: #0A66C2;"></i></a>
          <a href="mailto:patilmpratik2018@gmail.com"><i class="fas fa-envelope" style="color: #D44638;"></i></a>
        </div>
      </div>
      </div>
   

    <script src="assets/vendor/jquery/dist/jquery.min.js"></script>
    <script src="assets/vendor/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/argon.min.js"></script>
  </body>
</html>
