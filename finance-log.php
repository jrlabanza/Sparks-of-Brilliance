<?php session_start();?>
<?php

if (isset($_SESSION['username'])){

    if ($_SESSION['userType'] != 2 && $_SESSION['userType'] != 3){
        $_SESSION['message'] = 1;
        header('Location: logout_page.php');
        exit();
        } ?>
        <?php include "includes/header.php"; ?>
        <?php

        $showstat = null;

    ?>
       
       <form method="post">
        <!-- Trigger the modal with a button -->

        <!-- Modal -->
        <div id="myModal" class="modal fade" role="dialog">
          <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content text-center">
              <div class="modal-header">
                <h4 class="modal-title text-center">DATE FILTER</h4>
              </div>
              <div class="modal-body">
                <h5><p>From:</p></h5>
                <input type="date" class="form-control" style="width:60%; margin:auto;" name="startDate">
                <h5><p>To:</p></h5>
                <input type="date" class="form-control" style="width:60%; margin:auto;" name="endDate">
              </div>
              <div class="modal-footer">
                <input type="submit" class="btn btn-primary mr-auto" name="search" value="SEARCH DATE">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
              </div>
            </div>
          </div>
        </div>
        </form>
       
        <div class="card mb-3" style="margin-left:120px; margin-right:120px; margin-top:60px;">
            <div class="card-header"><i class="fas fa-table mr-1"></i>HISTORY LOG [FINANCE]
               <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#myModal" style="float:right;">Filter Record</button>
               </div>
                <div class="card-body">
                  
                    <table class="download table table-bordered table-hover " id="" width="100%" cellspacing="0" style="font-size:10px;">
                      <thead>
                        <tr>
                          <th>Recorded Date</th>
                          <th>CID No.</th>
                          <th>Name</th>
                          <th>Amount</th>
                          <th>Charging Department</th>

                        </tr>
                      </thead>
                      <tbody>
                       <?php
                                                
                        ///////////////////////////////////////////////////////////////////////////
                                if (isset($_POST['startDate']) == "" && isset($_POST['endDate']) == "" ){     
         
                                    $query_filed ="SELECT * FROM formfill_infov2 INNER JOIN associate_info ON formfill_infov2.id = associate_info.sobFormID WHERE (status = 4) AND isDeleted = 0 ORDER BY fin_date DESC";
                                    $filedforms = mysqli_query($connection, $query_filed);
                                }

                                else{
                                    $startDate = $_POST['startDate'];
                                    $endDate = $_POST['endDate'];
                                    $query_filed ="SELECT * FROM formfill_infov2 INNER JOIN associate_info ON formfill_infov2.id = associate_info.sobFormID WHERE ((DATE(fin_date) BETWEEN '$startDate' and '$endDate') AND status = 4) AND isDeleted = 0 ORDER BY DATE(fin_date) DESC";
                                    $filedforms = mysqli_query($connection, $query_filed);  
                                }
                        ///////////////////////////////////////////////////////////////////////////
    
//                        $query_filed ="SELECT * from formfill_info WHERE status = 4";
//                        $filedforms = mysqli_query($connection, $query_filed);
//                        if (!$filedforms) {
//                        printf("Error: %s\n", mysqli_error($connection));
//                        exit();
//                         }


                         while ($row_filed = mysqli_fetch_array($filedforms)){
                            
                             if($row_filed['status'] == "3"){
                                 $color = "table-success";
                                 }
                             else if($row_filed['status'] == "2"){
                                 $color = "table-warning";
                                 }
                             else if($row_filed['status'] == "1"){
                                 $color = "table-active";
                                 }
                             else if($row_filed['status'] == "0"){
                                 $color = "table-danger";
                                 }
                             else if($row_filed['status'] == "4"){
                                         $color = "table-primary";
                                         } 
                            echo "<tr class='$color'>";
                            echo "<td>". $row_filed["fin_date"] ."</td>";
                            echo "<td>". $row_filed["user_CID_num"] ."</td>";
                            echo "<td>". $row_filed["associate_name"] ."</td>";
                            echo "<td>". $row_filed["rec_amount"] ."</td>";
                            echo "<td>". $row_filed["dept_num"] ."</td>";



                            }

                          ?>
                      </tbody>
                    </table>
                  
                </div>

              </div>


  <?php include "includes/footer.php"; ?>
  <?php
}


else{
    $_SESSION['message'] = 1;
    header('Location: logout_page.php');
    exit();
    
}
?>