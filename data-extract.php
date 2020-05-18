<?php session_start();?>
<?php

if (isset($_SESSION['username'])){

    if ($_SESSION['userType'] != 1 && $_SESSION['userType'] != 3 && $_SESSION['userType'] != 4 && $_SESSION['userType'] != 5 )
    {
        $_SESSION['message'] = 1;
        header('Location: logout_page.php');
        exit();
    } 
    include "includes/header.php"; ?>
       
    <div class="card mb-3" style="margin-left:80px; margin-right:80px; margin-top:60px;">
        <div class="card-header"><i class="fas fa-table mr-1"></i>DATA EXTRACTION
            <!-- <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#myModal" style="float:right; font-size: 8px;">Filter Record</button> -->
        </div>
        <div class="card-body"><?php 
            if($_SESSION['userType'] != 5)
            {?>
                <form method="post">
                    <div class="row">
                        <div class="col-2">
                            <h6>From:</h6>
                        </div>
                        <div class="col-2">         
                            <h6>To:</h6>
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
                </form><?php
            }?>  
            <hr>
            <div class="container-fluid">
                <div class='col-12 col-lg-12 col-sm-12'>
                    <table class="download table table-bordered table-hover"  cellspacing="0" style="font-size:10px;">
                        <thead>
                            <tr>
                                <th>WW</th>
                                <th>Ticket No.</th>
                                <th>Sub-Ticket No.</th>
                                <th>Recommended Date</th>
                                <th>Recommended By</th>
                                <th>Charging Department</th>
                                <th>Name of Associate</th>
                                <th>CID No.</th>
                                <th>Key Strategy</th>
                                <th>Status</th>
                                <th>Amount</th>
                                <th>View</th>  

                            </tr>
                        </thead>
                        <tbody>
                        <?php

                            $deptLists = file_get_contents("depts.json");
                            $deptcount = json_decode($deptLists, true);
                            $department = $deptcount[0][$_SESSION['deptCode']];
                            $key = array_keys($deptcount[0], $department);
                            // print_r($key);
                            $keylen = sizeof($key);
                            $keystr = "";
                            // echo $keylen;
                            for ($k=0;$k<$keylen;$k++)
                            {
                                if ($k == ($keylen - 1)){
                                    $keystr .= "dept_num = '$key[$k]'";
                                }
                                else{
                                    $keystr .= "dept_num = '$key[$k]' OR ";
                                }
                                
                                // echo $key[$k];
                            }
                            
                            ///////////////////////////////////////////////////////////////////////////
                            if (empty($_POST['startDate']) && empty($_POST['endDate']) && empty($_POST['stat']) && $_SESSION['userType'] != 5){     

                                $showall_query ="SELECT * FROM formfill_infov2 INNER JOIN associate_info ON formfill_infov2.id = associate_info.sobFormID WHERE isDeleted = 0 ORDER BY subticket_no ASC";
                                $allshow = $connection->query($showall_query);

                            }
                            
                            else if (empty($_POST['startDate']) && empty($_POST['endDate']) && isset($_POST['stat']) && $_SESSION['userType'] != 5){

                                $stat = $_POST['stat'];
                                $showall_query ="SELECT * FROM formfill_infov2 INNER JOIN associate_info ON formfill_infov2.id = associate_info.sobFormID WHERE status = $stat AND isDeleted = 0 ORDER BY subticket_no ASC";
                                $allshow = $connection->query($showall_query); 
                            }

                            else if (isset($_POST['startDate']) && isset($_POST['endDate']) && empty($_POST['stat']) && $_SESSION['userType'] != 5){

                                $startDate = $_POST['startDate'];
                                $endDate = $_POST['endDate'];
                                $showall_query ="SELECT * FROM formfill_infov2 INNER JOIN associate_info ON formfill_infov2.id = associate_info.sobFormID WHERE (DATE(rec_date) BETWEEN '$startDate' AND '$endDate') AND isDeleted = 0 ORDER BY subticket_no ASC";
                                $allshow = $connection->query($showall_query);
                            }

                            else if ($_SESSION['userType'] != 5){

                                $startDate = $_POST['startDate'];
                                $endDate = $_POST['endDate'];
                                $stat = $_POST['stat'];
                                $showall_query ="SELECT * FROM formfill_infov2 INNER JOIN associate_info ON formfill_infov2.id = associate_info.sobFormID WHERE (DATE(rec_date) BETWEEN '$startDate' AND '$endDate') AND status = $stat  AND isDeleted = 0 ORDER BY subticket_no ASC";
                                $allshow = $connection->query($showall_query); 

                            }
                            else
                            {
                                $startDate = $_POST['startDate'];
                                $endDate = $_POST['endDate'];
                                $stat = $_POST['stat'];
                                $deptCode = $_SESSION['deptCode'];
                                $showall_query ="SELECT * FROM formfill_infov2 INNER JOIN associate_info ON formfill_infov2.id = associate_info.sobFormID WHERE isDeleted = 0 AND (".$keystr.") ORDER BY subticket_no ASC";
                                $allshow = $connection->query($showall_query); 
                            }

                            while ($row_filed = mysqli_fetch_array($allshow)){

                                $deptList = file_get_contents("depts.json");
                                $dept = json_decode($deptList, true);
                                $wwyear = "";

                                $ddate = $row_filed['rec_date'];
                                $date = new DateTime($ddate);
                                $month = $date->format("n");
                                $day = $date->format("j");
                                $year = $date->format("y");
                                
                                if($year == 18){
                                    $wwyear = '2018';
                                    $yeardesc = "'18";
                                }

                                else if($year == 19){
                                    $wwyear = '2019';
                                    $yeardesc = "'19";
                                }
                                
                                else if($year == 20){
                                    $wwyear = '2020';
                                    $yeardesc = "'20";
                                }
                                
                                $CalendarList = file_get_contents($wwyear."_cal.json");
                                $cal = json_decode($CalendarList, true);
            
                                $findww = $cal[$month]; //week search 
                                
                                $workWeek = 0;
            
                                foreach($findww as $key => $value){ //display week of all id
                                    
                                    
                                    $tempDays = explode(",", $value); // segregate week to array
                                    
                                    $tempDaysLen = sizeof($tempDays);
                                    
                                    for($j=0; $j<$tempDaysLen; $j++){

                                        if ($tempDays[$j] == $day){ //if array matches day data = true

                                        $workWeek = $key;

                                        break;
                                        }
                                    }
            
                                }


                                $associate_filed = "SELECT * FROM associate_info WHERE sobFormID=". $row_filed['id'];
                                $result_assoc = $connection->query($associate_filed);
                                $asoc = get_assocArray($result_assoc);
                                $asocLen = sizeof($asoc);
                                    
                                echo "<tr>";
                                echo "<td>". "WW".$workWeek.$yeardesc."</td>";
                                echo "<td>". $row_filed["ticket_no"] ."</td>";
                                echo "<td>". $row_filed["subticket_no"] ."</td>";
                                echo "<td>". $row_filed["rec_date"] ."</td>";
                                echo "<td>". $row_filed["rec_by"] ."</td>";
                                echo "<td>". $dept[0][$row_filed["dept_num"]] ."</td>";
                                echo "<td>". $row_filed['associate_name'] ."</td>";
                                echo "<td>". $row_filed['user_CID_num'] ."</td>";
                                echo "<td>". $row_filed['key_strat'] ."</td>";
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
                                echo "<td>". $row_filed['rec_amount'] ."</td>";
                                echo "<td> <a href='view-form.php?id=". $row_filed['sobFormID'] ."' class='btn btn-info btn-sm' style='height:20px; font-size:10px;' role='button' name='edit'>View</a></td>";
                            }

                            ?>
                        </tbody>
                    </table>
                </div>
                
            </div>      
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
