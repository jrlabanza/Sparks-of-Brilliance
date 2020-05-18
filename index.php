<?php session_start();

$deptList = file_get_contents ("depts.json");
$department = json_decode($deptList, true);

if (isset($_SESSION['username'])){

    if ($_SESSION['userType'] == 1 || $_SESSION['userType'] == 2 || $_SESSION['userType'] == 3 || $_SESSION['userType'] == 4 || $_SESSION['userType'] == 5 || $_SESSION['isSuperior'] == 1)
    {

        include "includes/header.php";

        clear_data(); 

        $showstat = "";
        /////////////////QUERY for PENDING FORMS///

        //////////////////HR PENDING///////////////
        if ($_SESSION['userType'] == 1 || $_SESSION['userType'] == 3)
        {
            $showallrec_query = "SELECT * from formfill_infov2 WHERE ((status = 2 OR (status = 1 AND deptmanId ='". $_SESSION['cidNum'] ."')) OR ((status = 5 OR status = 6) AND rec_ffId = '". $_SESSION['username'] ."')) AND isDeleted = 0 ORDER BY rec_date DESC";
            $result = mysqli_query($connection, $showallrec_query);
        }

        //////////////////SUPERIOR PENDING//////////////////////////////////////
        else if (isset($_SESSION['isSuperior']) && $_SESSION['isSuperior'] == "1")
        {
        $showallrec_query ="SELECT * from formfill_infov2 WHERE ((status = 1 AND deptmanId ='". $_SESSION['cidNum'] ."') OR (status = 5 AND rec_ffId = '". $_SESSION['cidNum'] ."')) AND isDeleted = 0 ORDER BY rec_date DESC" ;
        $result = mysqli_query($connection, $showallrec_query);
        
        } ?>

        <br>
        <br>
        <br>
        <div class="card mb-1" style="margin-left:120px; margin-right:120px;">
            <div class="card-header"><i class="fas fa-home mr-1"></i>Home</div>
        </div>
        <?php   
        //All isSuperior Pending Forms
        /*DEBUG CODE--->*/

        if ((isset($_SESSION['isSuperior']) && $_SESSION['isSuperior'] == "1") || ($_SESSION['userType'] == 1 || $_SESSION['userType'] == 3))
        { ?>
        
            <div class="card mb-3" style="margin-left:120px; margin-right:120px; margin-top:10px;">
                <div class="card-header"><i class="fas fa-table mr-1"></i>PENDING FORMS</div>
                <div class="card-body">
                    
                    <table class="display table table-bordered" id="" width="100%" cellspacing="0" style="font-size:10px;">
                        <thead>
                            <tr>
                                <th>Recommended Date</th>
                                <th>Recommended By</th>
                                <th>Department</th>
                                <th>Name of Associate</th>
                                <th>CID No.</th>
                                <th>Approved By</th>
                                <th>Approved Date</th>
                                <th>Status</th>
                                <th> Actions</th>
                            </tr>
                        </thead>
                        <tbody><?php

                        while ($row = mysqli_fetch_array($result)){

                            $associate_filed = "SELECT * FROM associate_info WHERE sobFormID=". $row['id'];
                            $result_assoc = $connection->query($associate_filed);
                            $asoc = get_assocArray($result_assoc);
                            $asocLen = sizeof($asoc);

                            if($row['status'] == "3")
                            {
                                $color = "table-success";
                            }
                            else if($row['status'] == "2")
                            {
                                $color = "table-warning";
                            }
                            else if($row['status'] == "1")
                            {
                                $color = "table-active";
                            }
                            else if($row['status'] == "0")
                            {
                                $color = "table-danger";
                            }
                            else if($row['status'] == "4")
                            {
                                $color = "table-default";
                            }
                            else if($row['status'] == "5" || $row['status'] == "6")
                            {
                                $color = "table-danger";
                            } 

                            echo "<tr class='$color'>";
                            echo "<td>". $row["rec_date"] ."</td>";
                            echo "<td>". $row["rec_by"] ."</td>";
                            echo "<td>". $row["dept_num"] ."</td>";
                            echo "<td>";
                            for ($cid = 0 ; $cid < $asocLen ;$cid++){

                                echo $asoc[$cid]["associate_name"]. "<br>";
                                
                            }
                            echo "</td>";
                            
                            echo "<td>";
                            for ($cid = 0 ; $cid < $asocLen ;$cid++){
                                
                                echo $asoc[$cid]["user_CID_num"]. "<br>";
                            }
                            echo "</td>";
                            echo "<td>". $row["app_by"]."</td>";
                            echo "<td>". $row["app_date"] ."</td>";
                            if ($row["status"] == "1"){
                                $showstat = "FOR MANAGER PENDING";
                            }
                            else if ($row["status"] == "2"){
                                $showstat = "FOR HR PENDING";
                            }
                            else if ($row["status"] == "3"){
                                $showstat = "APPROVED";
                            }
                            else if ($row["status"] == "0"){
                                $showstat = "DECLINED";
                            }
                            else if ($row["status"] == "4"){
                                $showstat = "FINANCE CLOSED";
                            }
                            else if ($row['status'] == "5" || $row['status'] == "6"){
                                $showstat = "ACTION NEEDED SEE FORM";
                            } 
                            else{
                                $showstat = "-";
                            }

                            echo "<td>". $showstat ."</td>";

                            ?>
                            <td> <a href="view-form.php?id=<?php echo $row['id'];?>" class="btn btn-info btn-sm" style="height:20px; font-size:10px;" role="button" name="edit" >View</a></td>  <?php

                        } ?>
                        </tbody>
                    </table>
                </div>
            </div><?php
        } ?>

        <div class="card mb-3" style="margin-left:120px; margin-right:120px;">
            <div class="card-header"><i class="fas fa-table mr-1"></i>FILED FORMS</div>
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
                <table class="download table table-bordered" width="100%" cellspacing="0" style="font-size:10px;">
                    <thead>
                        <tr>
                            <th>Recommended Date</th>
                            <th>Recommended By</th>
                            <th>Department</th>
                            <th>Name of Associate</th>
                            <th>CID No.</th>
                            <th>Approved By</th>
                            <th>Approved Date</th>
                            <th>Status</th>
                            <th> Actions</th>
                        </tr>
                    </thead>
                    <tbody> <?php

                    if (empty($_POST['startDate']) && empty($_POST['endDate']) && empty($_POST['stat']) && empty($_POST['cd']))
                    {     
                        $showall_query ="SELECT * FROM formfill_infov2 WHERE isDeleted = 0 AND (rec_ffId ='". $_SESSION['username'] ."') ORDER BY rec_date DESC";
                        $filedforms = $connection->query($showall_query);

                    }
                    
                    else if (empty($_POST['startDate']) && empty($_POST['endDate']) && isset($_POST['stat']) && empty($_POST['cd']))
                    {
                        $stat = $_POST['stat'];
                        $showall_query ="SELECT * FROM formfill_infov2  WHERE status = $stat AND (rec_ffId ='". $_SESSION['username'] ."') AND isDeleted = 0 ORDER BY rec_date DESC";
                        $filedforms = $connection->query($showall_query); 
                    }

                    else if (empty($_POST['startDate']) && empty($_POST['endDate']) && empty($_POST['stat']) && isset($_POST['cd']))
                    {
                        $cd = $_POST['cd'];
                        $showall_query ="SELECT * FROM formfill_infov2  WHERE dept_num = '$cd' AND (rec_ffId ='". $_SESSION['username'] ."') AND  isDeleted = 0 ORDER BY rec_date DES";
                        $filedforms = $connection->query($showall_query);
                    }

                    else if (isset($_POST['startDate']) && isset($_POST['endDate']) && empty($_POST['stat']) && empty($_POST['cd']))
                    {
                        $startDate = $_POST['startDate'];
                        $endDate = $_POST['endDate'];
                        $showall_query ="SELECT * FROM formfill_infov2  WHERE (DATE(rec_date) BETWEEN '$startDate' AND '$endDate') AND (rec_ffId ='". $_SESSION['username'] ."') AND isDeleted = 0 ORDER BY rec_date DESC";
                        $filedforms = $connection->query($showall_query); 
                    }

                    else if (isset($_POST['startDate']) && isset($_POST['endDate']) && isset($_POST['stat']) && empty($_POST['cd']))
                    {
                        $startDate = $_POST['startDate'];
                        $endDate = $_POST['endDate'];
                        $stat = $_POST['stat'];
                        $showall_query ="SELECT * FROM formfill_infov2 WHERE (DATE(rec_date) BETWEEN '$startDate' AND '$endDate') AND status = $stat AND (rec_ffId ='". $_SESSION['username'] ."') AND isDeleted = 0 ORDER BY rec_date DESC";
                        $filedforms = $connection->query($showall_query); 
                    }

                    else if (isset($_POST['startDate']) && isset($_POST['endDate']) && empty($_POST['stat']) && isset($_POST['cd']))
                    {
                        $startDate = $_POST['startDate'];
                        $endDate = $_POST['endDate'];
                        $cd = $_POST['cd'];
                        $showall_query ="SELECT * FROM formfill_infov2 WHERE (DATE(rec_date) BETWEEN '$startDate' AND '$endDate') AND dept_num = '$cd' AND (rec_ffId ='". $_SESSION['username'] ."') AND isDeleted = 0 ORDER BY rec_date DESC";
                        $filedforms = $connection->query($showall_query);                                
                    }

                    else 
                    {
                        $startDate = $_POST['startDate'];
                        $endDate = $_POST['endDate'];
                        $stat = $_POST['stat'];
                        $cd = $_POST['cd'];
                        $showall_query ="SELECT * FROM formfill_infov2  WHERE (DATE(rec_date) BETWEEN '$startDate' AND '$endDate') AND status = $stat AND dept_num = '$cd' AND (rec_ffId ='". $_SESSION['username'] ."') AND isDeleted = 0 ORDER BY rec_date DESC";
                        $filedforms = $connection->query($showall_query);        
                    }

                    while ($row_filed = mysqli_fetch_array($filedforms))
                    {
                    
                        $associate_filed = "SELECT * FROM associate_info WHERE sobFormID=". $row_filed['id'];
                        $result_assoc = $connection->query($associate_filed);
                        $asoc = get_assocArray($result_assoc);
                        $asocLen = sizeof($asoc);

                        if($row_filed['status'] == "3")
                        {
                            $color = "table-success";
                        }
                        else if($row_filed['status'] == "2")
                        {
                            $color = "table-warning";
                        }
                        else if($row_filed['status'] == "1")
                        {
                            $color = "table-active";
                        }
                        else if($row_filed['status'] == "0")
                        {
                            $color = "table-danger";
                        }
                        else if($row_filed['status'] == "4")
                        {
                            $color = "table-default";
                        }
                        else if($row_filed['status'] == "5" || $row_filed['status'] == "6")
                        {
                            $color = "table-danger";
                        } 

                        echo "<tr class='$color'>";
                        echo "<td>". $row_filed["rec_date"] ."</td>";
                        echo "<td>". $row_filed["rec_by"] ."</td>";
                        echo "<td>". $row_filed["dept_num"] ."</td>";                    
                        echo "<td>";
                        for ($cid = 0 ; $cid < $asocLen ;$cid++)
                        {
                            echo $asoc[$cid]["associate_name"]. "<br>";
                        }
                        echo "</td>";
                        echo "<td>";
                        for ($cid = 0 ; $cid < $asocLen ;$cid++)
                        {
                            echo $asoc[$cid]["user_CID_num"]. "<br>";
                        }
                        echo "</td>";
                        echo "<td>". $row_filed["app_by"]."</td>";
                        echo "<td>". $row_filed["app_date"] ."</td>";
                        if ($row_filed["status"] == "1")
                        {
                            $showstat = "FOR MANAGER PENDING";
                        }
                        else if ($row_filed["status"] == "2")
                        {
                            $showstat = "FOR HR PENDING";
                        }
                        else if ($row_filed["status"] == "3")
                        {
                            $showstat = "APPROVED";
                        }
                        else if ($row_filed["status"] == "0")
                        {
                            $showstat = "DECLINED";
                        }
                        else if ($row_filed["status"] == "4")
                        {
                            $showstat = "FINANCE CLOSED";
                        }
                        else if ($row_filed['status'] == "5" || $row_filed['status'] == "6")
                        {
                            $showstat = "ACTION NEEDED SEE FORM";
                        } 
                        else{
                            $showstat = "-";
                        }
                        echo "<td>". $showstat ."</td>"; ?>
                      
                        <td> <a href="view-form.php?id=<?php echo $row_filed['id'];?>" class="btn btn-primary btn-sm" style="height:20px; font-size:10px;" role="button" name="edit" >View</a></td> <?php
                    } ?>
                    </tbody>
                </table>
            </div>
        </div> <?php
                       
        //HR Whole File Log zbffgh
        if ($_SESSION['userType'] == 1 || $_SESSION['userType'] == 3)
        { ?>
            <div class="card mb-3" style="margin-left:120px; margin-right:120px;">
                <div class="card-header"><i class="fas fa-table mr-1"></i>REPORTING [HR]</div>
                <div class="card-body">
                    <table class="hr table table-bordered table-hover" id="" width="100%" cellspacing="0" style="font-size:10px;">
                        <thead>
                            <tr>
                            <th>No.</th>
                            <th>CID</th>
                            <th>Name of Employee</th>
                            <th>Amount</th>
                            <th>No. of SOB</th>
                            <th>Charge to Department</th>
                            <th>Department Name</th>
                            <th>Key Strategy</th>
                            <th>Recommended By</th>
                            </tr>
                        </thead>
                        <tbody><?php
                            $showall_query ="SELECT dept_num , rec_by, rec_amount , associate_name, user_CID_num, key_strat FROM formfill_infov2 INNER JOIN associate_info ON formfill_infov2.id = associate_info.sobFormID WHERE ((status = 4 AND isCleared = 0) OR (status = 3 AND isCleared = 0)) AND isDeleted = 0";
                            $allshow = $connection->query($showall_query);
                            $number = 0;

                            while ($row_all = mysqli_fetch_array($allshow))
                            {
                                // $count = "SELECT SUM(rec_amount) as amount FROM associate_info INNER JOIN formfill_infov2 ON formfill_infov2.id = associate_info.sobFormID WHERE user_CID_num =" . $row_all['user_CID_num'] ." AND ((status = 4 AND isCleared = 0) OR (status = 3 AND isCleared = 0)) AND isDeleted = 0";
                                // $countres = $connection->query($count);
                                // $c = get_data_array($countres);
                                
                                $sobquery = "SELECT COUNT(*) as sob FROM associate_info INNER JOIN formfill_infov2 ON formfill_infov2.id = associate_info.sobFormID WHERE user_CID_num =" . $row_all['user_CID_num'] ." AND ((status = 4 AND isCleared = 0) OR (status = 3 AND isCleared = 0)) AND isDeleted = 0";
                                $sobres = $connection->query($sobquery);
                                $s = get_data_array($sobres);  

                                $number =  $number + 1;
                                
                                    echo "<tr>";
                                    echo "<td>". $number ."</td>";
                                    echo "<td>". $row_all["user_CID_num"] ."</td>";
                                    echo "<td>". $row_all["associate_name"] ."</td>";
                                    echo "<td>". $row_all['rec_amount'] ."</td>";
                                    echo "<td>". $s["sob"] ."</td>";
                                    echo "<td>". $row_all["dept_num"]."</td>";
                                    echo "<td>". $department[0][$row_all["dept_num"]] ."</td>"; 
                                    echo "<td>". $row_all["key_strat"] ."</td>";
                                    echo "<td>". $row_all['rec_by'] ."</td>";
                            } ?>
                        </tbody>
                    </table>
                    <form action="index.php" method="post">
                        <input type="submit" name="submit" class="btn btn-danger btn-sm" value="CLEAR DATA" style="float:right;">
                    </form>
                </div>
            </div><?php
        }
        ?> 
        <?php include "includes/footer.php";


    }
    else
    {
        $_SESSION['message'] = 1;
        header('Location: logout_page.php');
        exit();
    }
}

else{

    $_SESSION['message'] = 1;
    header('Location: logout_page.php');
    exit();
    
}?>
