<?php session_start();?>
<?php

if (isset($_SESSION['username'])){

    if ($_SESSION['userType'] != 1 && $_SESSION['userType'] != 3){
        $_SESSION['message'] = 1;
        header('Location: logout_page.php');
        exit();
        } ?>
        <?php include "includes/header.php"; ?>
       
        <div class="card mb-3" style="margin-left:120px; margin-right:120px; margin-top:60px;">
            <div class="card-header"><i class="fas fa-table mr-1"></i>HISTORY LOG [HUMAN RESOURCES]</div>
            <div class="card-body">
                <form method="post">
                    <div class="row">
                        <div class="col-2">
                            <h6>From:</h6>
                        </div>
                        <div class="col-2">         
                            <h6>To:</h6>
                        </div>
                        <div class="col-2">         
                            <h6>Department Code:</h6>
                        </div>
                        <div class="col-2">         
                            <h6>Status:</h6>
                        </div>
                        <div class="col-4">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-2">
                            <input type="date" class="form-control form-control-sm"  name="startDate" style="font-size: 10px;">
                        </div>
                        <div class="col-2">         
                            <input type="date" class="form-control form-control-sm"  name="endDate" style="font-size: 10px;">
                        </div>
                        <div class="col-2">         
                            <input type="text" class="form-control form-control-sm"  name="cd" style="font-size: 10px;" autocomplete="off">
                        </div>
                        <div class="col-2">         
                            <select type="text" class="form-control form-control-sm" name="stat">
                                <option value=""></option>
                                <option value="0">DECLINED</option>
                                <option value="1">FOR MANAGER PENDING</option>
                                <option value="2">FOR HR PENDING</option>
                                <option value="3">APPROVED</option>
                                <option value="4">FINANCE CLOSED</option>
                            </select>
                        </div>
                        <div class="col-4">
                            <div class="row">
                                <button type="submit" class="btn btn-sm btn-primary mt-auto mr-2" name="search"><i class="fas fa-search mr-1"></i></button>
                                
                            </div>
                        </div>
                    </div>  
                </form>
                <hr>  
                <table class="download table table-bordered table-hover" id="" width="100%" cellspacing="0" style="font-size:10px;">
                    <thead>
                    <tr>
                        <th>Recommended Date</th>
                        <th>Recommended By</th>
                        <th>Name of Associate</th>
                        <th>Department</th>
                        <th>CID No.</th>
                        <th>Key Strategy</th>
                        <th>Approved By</th>
                        <th>Approved Date</th>
                        <th>Status</th>
                        <th> Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    

                    if (empty($_POST['startDate']) && empty($_POST['endDate']) && empty($_POST['stat']) && empty($_POST['cd'])){     

                        $showall_query ="SELECT * FROM formfill_infov2 WHERE isDeleted = 0  ORDER BY rec_date ASC";
                        $filedforms = $connection->query($showall_query);

                    }
                    
                    else if (empty($_POST['startDate']) && empty($_POST['endDate']) && isset($_POST['stat']) && empty($_POST['cd'])){

                        $stat = $_POST['stat'];
                        $showall_query ="SELECT * FROM formfill_infov2  WHERE status = $stat AND isDeleted = 0 ORDER BY rec_date ASC";
                        $filedforms = $connection->query($showall_query); 
                    }

                    else if (empty($_POST['startDate']) && empty($_POST['endDate']) && empty($_POST['stat']) && isset($_POST['cd'])){

                        $cd = $_POST['cd'];
                        $showall_query ="SELECT * FROM formfill_infov2  WHERE dept_num = '$cd'  AND  isDeleted = 0 ORDER BY rec_date ASC";
                        $filedforms = $connection->query($showall_query);                                }

                    else if (isset($_POST['startDate']) && isset($_POST['endDate']) && empty($_POST['stat']) && empty($_POST['cd'])){

                        $startDate = $_POST['startDate'];
                        $endDate = $_POST['endDate'];
                        $showall_query ="SELECT * FROM formfill_infov2  WHERE (DATE(rec_date) BETWEEN '$startDate' AND '$endDate') AND isDeleted = 0 ORDER BY rec_date ASC";
                        $filedforms = $connection->query($showall_query);                                }

                    else if (isset($_POST['startDate']) && isset($_POST['endDate']) && isset($_POST['stat']) && empty($_POST['cd'])){

                        $startDate = $_POST['startDate'];
                        $endDate = $_POST['endDate'];
                        $stat = $_POST['stat'];
                        $showall_query ="SELECT * FROM formfill_infov2 WHERE (DATE(rec_date) BETWEEN '$startDate' AND '$endDate') AND status = $stat  AND isDeleted = 0 ORDER BY rec_date ASC";
                        $filedforms = $connection->query($showall_query);                                }

                    else if (isset($_POST['startDate']) && isset($_POST['endDate']) && empty($_POST['stat']) && isset($_POST['cd'])){

                        $startDate = $_POST['startDate'];
                        $endDate = $_POST['endDate'];
                        $cd = $_POST['cd'];
                        $showall_query ="SELECT * FROM formfill_infov2 WHERE (DATE(rec_date) BETWEEN '$startDate' AND '$endDate') AND dept_num = '$cd'  AND isDeleted = 0 ORDER BY rec_date ASC";
                        $filedforms = $connection->query($showall_query);                                }

                    else {

                        $startDate = $_POST['startDate'];
                        $endDate = $_POST['endDate'];
                        $stat = $_POST['stat'];
                        $cd = $_POST['cd'];
                        $showall_query ="SELECT * FROM formfill_infov2  WHERE (DATE(rec_date) BETWEEN '$startDate' AND '$endDate') AND status = $stat AND dept_num = '$cd' AND isDeleted = 0 ORDER BY rec_date ASC";
                        $filedforms = $connection->query($showall_query);        
                    }


                        while ($row_filed = mysqli_fetch_array($filedforms)){
                        $associate_filed = "SELECT * FROM associate_info WHERE sobFormID=". $row_filed['id'];
                        $result_assoc = $connection->query($associate_filed);
                        $asoc = get_assocArray($result_assoc);
                        $asocLen = sizeof($asoc);
                            $color = "";
                            
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
                                        $color = "table-defaut";
                                        }
                        else if($row_filed['status'] == "5"){
                                        $color = "table-primary";
                                        }
                            
                        echo "<tr class='$color'>";
                        echo "<td>". $row_filed["rec_date"] ."</td>";
                        echo "<td>". $row_filed["rec_by"] ."</td>";
                        
                        echo "<td>";
                            for ($cid = 0 ; $cid < $asocLen ;$cid++){

                                echo $asoc[$cid]["associate_name"]. "<br>";
                                
                            }
                        echo "</td>";
                        echo "<td>". $row_filed["dept_num"] ."</td>";    
                        echo "<td>";
                            for ($cid = 0 ; $cid < $asocLen ;$cid++){
                                
                                echo $asoc[$cid]["user_CID_num"]. "<br>";
                            }
                        echo "</td>";
                        echo "<td>";
                            for ($cid = 0 ; $cid < $asocLen ;$cid++){
                                
                                echo $asoc[$cid]["key_strat"]. "<br>";
                            }
                        echo "</td>";
                        echo "<td>". $row_filed["app_by"]."</td>";
                        echo "<td>". $row_filed["app_date"] ."</td>";
                        if ($row_filed["status"] == "1"){
                            $showstat = "FOR MANAGER PENDING";
                        }
                        else if ($row_filed["status"] == "2"){
                            $showstat = "FOR HR PENDING";
                        }
                        else if ($row_filed["status"] == "3"){
                            $showstat = "APPROVED";
                        }
                        else if ($row_filed["status"] == "0"){
                            $showstat = "DECLINED";
                        }
                        else if ($row_filed["status"] == "4"){
                                    $showstat = "FINANCE CLOSED";
                                }
                        else if ($row_filed["status"] == "5"){
                                    $showstat = "ACTION NEEDED SEE FORM";
                                } 
                        else{
                            $showstat = "-";
                        }

                        echo "<td>". $showstat ."</td>";
                        ?>
                        <?php  



                        ?>
                        <td> <a href="view-form.php?id=<?php echo $row_filed['id'];?>" class="btn btn-info btn-sm" role="button" name="edit" style="height:20px; font-size:10px;" >View</a></td>

                        <?php

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
