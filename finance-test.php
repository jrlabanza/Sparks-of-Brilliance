<?php session_start();

if (isset($_SESSION['username'])){

  if ($_SESSION['userType'] != 2 && $_SESSION['userType'] != 3)
    {
      $_SESSION['message'] = 1;
      header('Location: logout_page.php');
      exit();
    } 
    include "includes/header.php"; 
      
    $user = "SELECT * FROM employeeinfos WHERE ffId='". $_SESSION['username'] ."'"; 
    $userres = $userconnect->query($user);
    $u = get_data_array($userres);


    $showallrec_query ="SELECT * from formfill_infov2 WHERE status = '3' ORDER BY app_date DESC";
    $result = $connection->query($showallrec_query);
    $f = get_assocArray($result);
    
    $sql = "SELECT * FROM formfill_infov2 ORDER BY rec_date DESC LIMIT 1";
    $result = $connection-> query($sql);
    $user = get_data_array($result);
    $batch_no = $user['batch_no'] + 1;
    
    $total = sizeof($f);
    
    for ($i = 0 ; $i < $total; $i++){

        $showasscoiates = "SELECT * FROM associate_info WHERE sobFormID =". $f[$i]['id'];
        $showresult = $connection->query($showasscoiates);
        $showassoc = get_assocArray($showresult);
        $showassocSize = sizeof($showassoc);
        $emailquery = "SELECT * FROM employeeinfos WHERE cidNum ='". $f[$i]['rec_CID']. "'";
        $resultmail = $userconnect->query($emailquery);
        $m = get_data_array($resultmail);

        // $finalstatus = "UPDATE formfill_infov2 SET status = 4, fin_by ='". $u['lastName'] . " " . $u['firstName'] ."', fin_date = CURRENT_TIMESTAMP, batch_no = '$batch_no' WHERE id =". $f[$i]['id'] ."";
        // $finalstat = $connection->query($finalstatus);
        
        $receiver = $m['email'];
        $title = "SPARKS OF BRILLIANCE";
        $sender = "apps.donotreply@onsemi.com";
        $subject = "E-SOB : FINANCE CLOSED";
        $msg .= "<p><b>TICKET #:". $f[$i]['ticket_no'] ."</b>";

        $msg .= "Your current Sparks of Brilliance Form has been finalized by the HR and finance and has completed transaction. Please see status on your form.</p>
        <p>Please use Google Chrome or Mozilla FireFox <a href='http://phsm01ws014.ad.onsemi.com/sob/view-form.php?id=". $f[$i]['id'] ."'>VIEW FORM</a></p><br>
        <table style='border: 1px solid black ; padding: 5px;'><thead><tr><th style='border: 1px solid black ; padding: 5px;'>ASSOCIATE NAME</th><th style='border: 1px solid black ; padding: 5px;'>CID NUMBER</th></tr></thead><tbody>"; 
        for($s = 0 ; $s < $showassocSize ; $s++)
        {
          $msg .= "<tr>";
          $msg .= "<td style='border: 1px solid black ; padding: 5px;'>". $showassoc[$s]['associate_name'] ."</td>";
          $msg .= "<td style='border: 1px solid black ; padding: 5px;'>". $showassoc[$s]['user_CID_num'] ."</td>";
          $msg .= "<tr>";
        }
        $msg .= "</tbody><table>
        <br> <br/><br/><b style='color:red'>Please do not reply.</b> <br/><br/>Applications Engineering <br/> SPARKS OF BRILLIANCE";

        sendmail_utf8($receiver,$title,$sender,$subject,$msg);
    }    

    $showstat = null;
        
    $showallrec_query ="SELECT * from formfill_infov2 INNER JOIN associate_info ON formfill_infov2.id = associate_info.sobFormID WHERE (status = '3') AND isDeleted = 0 ORDER BY app_date DESC";
    $result = mysqli_query($connection, $showallrec_query);
    finance_update();  ?>

    <br>
    <br>
    <br>  
    <!-- DataTables Example -->
    <div class="card mb-3" style="margin-left:120px; margin-right:120px; margin-top:10px;">
      <div class="card-header">
        <i class="fas fa-table"></i>
        PENDING FORMS [FINANCE]
      </div>
      <div class="card-body">
          <table class="download table table-bordered table-hover" width="100%" cellspacing="0">
            <thead>
              <tr>
                <th>CID No.</th>
                <th>Name</th>
                <th>Amount</th>
                <th>Charging Department</th>
                <th>Validation Date</th>
              </tr>
            </thead>
            <tbody>
              <?php

                while ($row = mysqli_fetch_array($result))
                {
                  $associate_filed = "SELECT * FROM associate_info WHERE sobFormID=". $row['id'];
                  $result_assoc = $connection->query($associate_filed);
                  $asoc = get_assocArray($result_assoc);
                  $asocLen = sizeof($asoc);
                  echo "<tr>";
                  echo "<td>". $row["user_CID_num"] ."</td>";
                  echo "<td>". $row["associate_name"] ."</td>";
                  echo "<td>". $row["rec_amount"] ."</td>";
                  echo "<td>". $row["dept_num"] ."</td>";
                  echo "<td>". $row["valid_date"] ."</td>";
                }

                ?>
            </tbody>
          </table>
        <form action="finance-list.php" method="post">
            <input class="btn btn-success "type="submit" name="submit" value="Approved" style="float:right; margin-top:10px;">
        </form>
      </div>
    </div>
  <?php include "includes/footer.php";
}

else{

  $_SESSION['message'] = 1;
  header('Location: logout_page.php');
  exit();

}?>
