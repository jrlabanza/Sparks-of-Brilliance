<?php session_start();?>
<?php

if (isset($_SESSION['username'])){

    if ($_SESSION['userType'] != 2 && $_SESSION['userType'] != 3){
        $_SESSION['message'] = 1;
        header('Location: logout_page.php');
        exit();
        }
    ?>
        <?php include "includes/header.php"; ?>
        <?php

    $showstat = null;
        
    $showallrec_query ="SELECT * from formfill_infov2 INNER JOIN associate_info ON formfill_infov2.id = associate_info.sobFormID WHERE (status = '3') AND isDeleted = 0 ORDER BY app_date DESC";
    $result = mysqli_query($connection, $showallrec_query);
    finance_update();  ?>
    
        <!-- ID CARDS -->
    
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

                         while ($row = mysqli_fetch_array($result)){
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
    
}

?>
