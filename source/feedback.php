<?php 
  session_start();
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

  // Error message variable
  $outTimeError = "";

  // Check if the form is submitted
  if (isset($_POST['feedSubmit'])) {
    $outTime = $_POST['feedTimeout'];
    $inTime = $_POST['feedTimein'];

    // Validate out time
    if ($outTime < $inTime) {
      $outTimeError = "Please enter a valid out time.";
    } else {
      // Process the form and handle feedback submission
      require("db.php");
      $rollnumber = $_SESSION['rollnumber'];
      $feedreqid = mysqli_real_escape_string($db, $_POST['feedReqid']);
      $feedrating = mysqli_real_escape_string($db, $_POST['feedRating']);
      $feedtimein = mysqli_real_escape_string($db, $_POST['feedTimein']);
      $feedtimeout = mysqli_real_escape_string($db, $_POST['feedTimeout']);
      $feedsuggestion = mysqli_real_escape_string($db, $_POST['feedSuggestion']);
      $feedcomplaints = mysqli_real_escape_string($db, $_POST['feedComplaints']);

      $feed_query = "INSERT into feedback(rollnumber,request_id,rating,timein,timeout) values ('$rollnumber','$feedreqid','$feedrating','$feedtimein','$feedtimeout')";

      // Submit Feedback
      $feed_result = mysqli_query($db, $feed_query);

      // Increment Rooms Cleaned and req status
      $workerid = mysqli_query($db, "SELECT worker_id from cleanrequest where request_id='$feedreqid'");
      $workerid2 = mysqli_fetch_assoc($workerid);
      $workerid3 = $workerid2['worker_id'];
      mysqli_query($db, "Update housekeeper set rooms_cleaned = rooms_cleaned + 1 where worker_id = '$workerid3'");
      mysqli_query($db, "Update cleanrequest set req_status = 2 where request_id = '$feedreqid'");

      if ($feed_result) {
        $_SESSION['feed_sent'] = "Feedback is sent for request id - ".$feedreqid;
      }

      $feedid = mysqli_query($db, "SELECT feedback_id from feedback where request_id='$feedreqid'");
      $feedid2 = mysqli_fetch_assoc($feedid);
      $feedid3 = $feedid2['feedback_id'];

      if($feedsuggestion != ""){
        $suggest_query = "INSERT into suggestions(feedback_id,rollnumber,suggestion) values ('$feedid3','$rollnumber','$feedsuggestion')";
        $suggest_result = mysqli_query($db, $suggest_query);
      }

      if($feedcomplaints != ""){
        $complaint_query = "INSERT into complaints(feedback_id,rollnumber,complaint) values ('$feedid3','$rollnumber','$feedcomplaints')";
        $complaint_result = mysqli_query($db, $complaint_query);
        
        mysqli_query($db, "Update housekeeper set complaints = complaints + 1 where worker_id = '$workerid3'");
      }
      header("Location: feedback.php");
      exit();
    }
  }
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>Feedback Form - Housekeeper Student Dashboard</title>
  <?php require("meta.php"); ?>
</head>
<body>
  <!-- Side Navigation -->
  <?php require("sidenav.php"); ?>
  <!-- Main content -->
  <div class="main-content">
    <!-- Header -->
    <div class="header bg-background pb-6 pt-5 pt-md-6">
      <div class="container-fluid">
        <!-- Notification message -->
        <?php if (isset($_SESSION['feed_sent'])) : ?>
          <div class="alert alert-success alert-dismissible fade show" role="alert">
            <span class="alert-inner--icon"><i class="ni ni-like-2"></i></span>
            <?php echo $_SESSION['feed_sent']; unset($_SESSION['feed_sent']); ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
        <?php endif ?>
        <?php require("headerstats.php"); ?>
      </div>
    </div>
    <!-- Page content -->
    <div class="container-fluid mt--5 pb-6">
      <div class="row mt-2">
        <div class="col-xl-12 order-xl-1">
          <div class="card bg-secondary shadow">
            <div class="card-header bg-white border-0">
              <h3 class="mb-0">Housekeeping Feedback</h3>
            </div>
            <div class="card-body pb-5">
              <form method="POST" autocomplete="off" action="feedback.php">
                <div class="pl-lg-4">
                  <div class="row">
                    <div class="col-md-6">
                      <div class="form-group">
                        <label class="form-control-label" for="input-requestid">Request Id <span class="text-danger">*</span></label>
                        <select name="feedReqid" class="form-control" id="input-requestid" required>
                          <option selected="true" value="" disabled="disabled">Select Option</option>
                          <?php
                            require("db.php");
                            $rollnumber = $_SESSION['rollnumber'];
                            $reqids_query = "SELECT request_id, name FROM cleanrequest cr 
                                              INNER JOIN housekeeper hk ON cr.worker_id = hk.worker_id 
                                              INNER JOIN student s ON s.rollnumber = cr.rollnumber 
                                              WHERE cr.req_status = 1 AND s.rollnumber = '$rollnumber'";
                            $reqids_result = mysqli_query($db, $reqids_query);
                            while ($row = mysqli_fetch_assoc($reqids_result)) {
                              echo '<option value="' . $row['request_id'] . '">' . $row['request_id'] . ' - ' . $row['name'] . '</option>';
                            }
                          ?>
                        </select>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-group">
                        <label class="form-control-label" for="input-rating">Rate Service <span class="text-danger">*</span></label>
                        <select name="feedRating" class="form-control" id="input-rating" required>
                          <option selected="true" value="" disabled="disabled">Select Option</option>
                          <option value="1">1 Poor Cleaning</option>
                          <option value="2">2 Not Satisfied</option>
                          <option value="3">3 Satisfactory</option>
                          <option value="4">4 Good Cleaning</option>
                          <option value="5">5 Excellent Work</option>
                        </select>
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-lg-6">
                      <div class="form-group">
                        <label class="form-control-label" for="input-timein">Time In <span class="text-danger">*</span></label>
                        <input name="feedTimein" type="time" id="input-timein" class="form-control form-control-alternative" required>
                      </div>
                    </div>
                    <div class="col-lg-6">
                      <div class="form-group">
                        <label class="form-control-label" for="input-timeout">Time Out <span class="text-danger">*</span></label>
                        <input name="feedTimeout" type="time" id="input-timeout" class="form-control form-control-alternative" required>
                        <?php if ($outTimeError) : ?>
                          <small class="text-danger"><?php echo $outTimeError; ?></small>
                        <?php endif; ?>
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-lg-6">
                      <div class="form-group">
                        <label class="form-control-label" for="input-suggestions">Suggestions</label>
                        <textarea name="feedSuggestion" class="form-control form-control-alternative" id="input-suggestions" rows="3" placeholder="We'd love to hear some suggestions.."></textarea>
                      </div>
                    </div>
                    <div class="col-lg-6">
                      <div class="form-group">
                        <label class="form-control-label" for="input-complaints">Complaints</label>
                        <textarea name="feedComplaints" class="form-control form-control-alternative" id="input-complaints" rows="3" placeholder="Got complaints for housekeeping service?"></textarea>
                      </div>
                    </div>
                  </div>
                  <button name="feedSubmit" class="btn btn-icon btn-3 btn-primary" type="submit">
                    <span class="btn-inner--text">Submit</span>
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="assets/vendor/jquery/dist/jquery.min.js"></script>
  <script src="assets/vendor/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/bootstrap/dist/js/bootstrap-datepicker.min.js"></script>
  <script src="assets/js/argon.min.js"></script>
</body>
</html>
