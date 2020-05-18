<?php session_start();?>
<?php

if (isset($_SESSION['username'])){

    if ($_SESSION['department_head'] != "Department Head"){
        $_SESSION['message'] = 1;
        header('Location: logout_page.php');
        exit();
        } ?>
        <?php include "includes/header.php"; ?>
       
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
            <div class="card-header"><i class="fas fa-table mr-1"></i>REPORTING [DEPARTMENT HEAD]
               <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#myModal" style="float:right;">Filter Record</button>
            </div>
            <div class="card-body">
                <table class="download table table-bordered table-hover" id="" width="100%" cellspacing="0" style="font-size:10px;">
                    <thead>
                        <tr>
                            <th>Ticket No.</th>
                            <th>Recommended Date</th>
                            <th>Recommended By</th>
                            <th>Name of Associate</th>
                            <th>CID No.</th>
                            <th>Amount</th>


                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    
                        ///////////////////////////////////////////////////////////////////////////
                        if (isset($_POST['startDate']) == "" && isset($_POST['endDate']) == "" ){     
                            
                            $showall_query ="SELECT * FROM formfill_infov2 INNER JOIN associate_info ON formfill_infov2.id = associate_info.sobFormID WHERE dept_num ='". $_SESSION['deptCode'] ."' AND isDeleted = 0 ORDER BY subticket_no ASC";
                            $allshow = $connection->query($showall_query);

                        }

                        else{

                            $startDate = $_POST['startDate'];
                            $endDate = $_POST['endDate'];
                            $showall_query ="SELECT * FROM formfill_infov2 INNER JOIN associate_info ON formfill_infov2.id = associate_info.sobFormID WHERE (DATE(rec_date) BETWEEN '$startDate' and '$endDate') AND dept_num ='". $_SESSION['deptCode'] ."' AND isDeleted = 0 ORDER BY subticket_no ASC";
                            $allshow = $connection->query($showall_query); 

                        }
                        ///////////////////////////////////////////////////////////////////////////



                        while ($row_filed = mysqli_fetch_array($allshow)){

                            $associate_filed = "SELECT * FROM associate_info WHERE sobFormID=". $row_filed['id'];
                            $result_assoc = $connection->query($associate_filed);
                            $asoc = get_assocArray($result_assoc);
                            $asocLen = sizeof($asoc);
                                
                            echo "<tr>";
                            echo "<td>". $row_filed["subticket_no"] ."</td>";
                            echo "<td>". $row_filed["rec_date"] ."</td>";
                            echo "<td>". $row_filed["rec_by"] ."</td>";
                            echo "<td>". $row_filed['associate_name'] ."</td>";
                            echo "<td>". $row_filed['user_CID_num'] ."</td>";
                            echo "<td>". $row_filed['rec_amount']."</td>";

                        }

                        ?>
                    </tbody>
                </table>
                
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
