<?php
  session_start();
  require("db.php");

  // ================== Clean Request Handler =================== //
  if(isset($_POST['allotSubmit']) && isset($_SESSION['username'])){
    $reqId = mysqli_real_escape_string($db, $_POST['allotId']);
    $workerId = mysqli_real_escape_string($db, $_POST['workerId']);

    $allot_query = "Update cleanrequest set worker_id = '$workerId', req_status=1 where request_id = '$reqId'";
    $allot_result = mysqli_query($db,$allot_query);
    if ($allot_result) {
      $_SESSION['worker_alloted'] = "Housekeeper successfully alloted";
    }else {
      $_SESSION['allotment_failed'] = "Failed to allot worker, contact site management.";
    }
    header("Location: allot.php");
  }

  // Student Registration
  if (isset($_POST['regSubmit']) && isset($_SESSION['username'])) {
    $rollnumber = mysqli_real_escape_string($db, $_POST['regRoll']);
    $roomnumber = mysqli_real_escape_string($db, $_POST['regRoom']);
    $floornumber = mysqli_real_escape_string($db, $_POST['regFloor']);
    $password = md5(12345);
    $roomnumber = strtolower($roomnumber);

    $hostel_name = substr($_SESSION['username'], -1);
    $reg_query = "INSERT INTO student VALUES ('$rollnumber', '$password', '$roomnumber', '$floornumber', '$hostel_name')";
    try {
      $reg_result = mysqli_query($db, $reg_query);
      if ($reg_result) {
        $_SESSION['student_registered'] = 'Student with Rollnumber ' . $rollnumber . ' is Registered.';
      } else {
        throw new Exception('Duplicate entry');
      }
    } catch (Exception $e) {
      $_SESSION['student_registered'] = 'Student is already Registered! ';
    }
    header("Location: registerstudent.php");
    exit();
  }

 // Worker Registration
if (isset($_POST['regKeeperSubmit']) && isset($_SESSION['username'])) {
  $name = mysqli_real_escape_string($db, $_POST['regName']);
  $floornumber = mysqli_real_escape_string($db, $_POST['regFloor']);
  $hostel_name = substr($_SESSION['username'], -1);

  $name = strtolower($name);

  // Check if the housekeeper already exists
  $check_query = "SELECT * FROM housekeeper WHERE name = '$name' AND floor = '$floornumber'";
  $check_result = mysqli_query($db, $check_query);
  if (mysqli_num_rows($check_result) > 0) {
    $_SESSION['worker_registered'] = 'Housekeeper is already registered.';
    header("Location: registerworker.php");
    exit();
  }

  // Register the new housekeeper
  $reg_query = "INSERT INTO housekeeper (name, hostel, floor) VALUES ('$name', '$hostel_name', '$floornumber')";
  $reg_result = mysqli_query($db, $reg_query);
  if ($reg_result) {
    $_SESSION['worker_registered'] = 'New Housekeeper Registered.';
  } else {
    $_SESSION['worker_registered'] = 'Registration Failed!';
  }
  header("Location: registerworker.php");
  exit();
}


?>
