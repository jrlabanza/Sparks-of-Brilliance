<?php session_start();?>
<?php

if (isset($_SESSION['username'])){

    if ($_SESSION['userType'] == 1 || $_SESSION['userType'] == 2 || $_SESSION['userType'] == 3 || $_SESSION['userType'] == 4 || $_SESSION['isSuperior'] == 1){
       
        include "includes/header.php"; 
        include "database/db.php";
        

        
        //query
        if (is_numeric($_GET['id']))
        {
            $dr_id = (isset($_GET['id'])) ? $_GET['id'] : 0;
        }
        manager_update($dr_id);
        update_form($dr_id);
        hr_update($dr_id);
        hr_pending_update($dr_id);
        man_pending_update($dr_id);
        rec_update_hr($dr_id);
        rec_update_man($dr_id);
        form_del($dr_id);
        
        $showall_query ="SELECT * from employeeinfos WHERE isDeleted=0 ORDER BY lastName ASC";
        $fullresult = $userconnect-> query($showall_query);
        $all = get_assocArray($fullresult);
        $empsLen = sizeof($all);
        
        $supsql = "SELECT * FROM employeeinfos WHERE isDeleted=0 AND isSuperior = '1' ORDER BY lastName ASC";
        $results = $userconnect->query($supsql);
        $s = get_assocArray($results);
        $supLen = sizeof($s);

        $sql ="SELECT * from formfill_infov2 WHERE id=". $dr_id . " AND isDeleted = 0" ;
        $result = $connection-> query($sql);
        $d = get_data_array($result);

        $sql_assoc = "SELECT * FROM associate_info WHERE sobFormID=". $dr_id;
        $result_assoc = $connection->query ($sql_assoc);
        $assoc = get_assocArray($result_assoc);
        $assocLen = sizeof($assoc);

        $deptsql ="SELECT * from employeeinfos WHERE cidNum=". $d['deptmanId'];
        $mresult = $userconnect->query($deptsql);
        $m = get_data_array($mresult);
       
        $selectitem = "SELECT * FROM uploads WHERE itemId=". $dr_id ; 
        $itemresult = $connection->query($selectitem);
        $item = get_assocArray($itemresult);

        $itemLen = sizeof($item);

        $quantity_sql = "SELECT COUNT(*) as countitem FROM uploads WHERE itemId=". $dr_id ." AND item_name != '' " ; 
        $quantity_result = $connection->query($quantity_sql);
        $quan = get_data_array($quantity_result);

        $totalitem = $quan['countitem'];
        
       //status description
        
        if($d['status'] == 0){
            $statcolor = "red";
            $statmessage = "DECLINED";
        }
        else if($d['status'] == 1){
            $statcolor = "black";
            $statmessage = "FOR MANAGER APPROVAL";
        }
        else if($d['status'] == 2){
            $statcolor = "black";
            $statmessage = "FOR HR APPROVAL";
            
        }
        else if($d['status'] == 3){
            $statcolor = "green";
            $statmessage = "HR APPROVED, FINANCE FINALIZATION";
            
        }
        else if($d['status'] == 4){
            $statcolor = "blue";
            $statmessage = "FINANCE CLOSED";
            
        }
        else if($d['status'] == 5 || $d['status'] == 6){
            $statcolor = "red";
            $statmessage = "FORM FOR REVIEW, SEE NEW ACTION UNDER REMARKS";
            
        }
                
        ?>
           
        <form action="view-form.php?id=<?php echo $dr_id; ?>" method = "post" enctype="multipart/form-data">
        <!-- Upload Modal -->                                                                                        
            <div class="modal fade" id="fileModal">
                <div class="modal-dialog">
                    <div class="modal-content">

             
                        <div class="modal-header">
                            <h4 class="modal-title">UPLOADED FILES</h4>
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                        </div>

                        <div class="modal-body">
                
                            <?php if ($totalitem === 0){ ?>
                                <p> 0 FILE/S HAVE BEEN UPLOADED</p>

                            <?php }
                            else{ ?>
                                <p><?php echo $totalitem ?> FILE/S HAVE BEEN UPLOADED</p>
                            <?php } 
                        
                            for ($i=0 ; $i < $itemLen ; $i++)
                            {   
                                 
                                echo "<div> <a href='uploads/". $item[$i]['item_name'] ."'  target='_blank'>" . $item[$i]['item_name'] ."</a></div>";
                                
                            } ?>
                        </div>

            
                        <div class="modal-footer">
                            <p style="font-size: 12px; float:left;">*Documents such as Excel and Word can only be downloaded not previewed</p>
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        
            <!-- update modal-->                                                                            
            <div class="modal fade" id="updateModal">
                <div class="modal-dialog">
                    <div class="modal-content">
             
                        <div class="modal-header">
                            <h4 class="modal-title">Confirmation</h4>
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                        </div>

                        <div class="modal-body">
                            <div>Confrim Update?</div>
                        </div>

                        <div class="modal-footer">
                            <p style="font-size: 12px; float:left;">*Only on Manager Pending is Updating Available, Once it has been approved by the Manager, It cannot be changed.</p>
                            <input class="btn btn-primary" type="submit" name="update_form" value="UPDATE FORM" style="">
                            <button type="button" class="btn btn-danger" data-dismiss="modal">CLOSE</button>
                        </div>

                    </div>
                </div>
            </div>
            <!--Delete Modal-->                                                                            
            <div class="modal fade" id="deleteModal">
                <div class="modal-dialog">
                    <div class="modal-content">
             
                        <div class="modal-header">
                            <h4 class="modal-title">Confirmation</h4>
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                        </div>
            
                        <div class="modal-body">
                            <div>Confrim Delete?</div>
                        </div>

                        <div class="modal-footer">
                           <p style="font-size: 12px; float:left;">*Only on Manager Pending is Deleting Available, Once it has been approved by the Manager, It cannot be changed.</p>
                           <input class="btn btn-danger" type="submit" name="deleteform" value="DELETE FORM" style="">
                           <button type="button" class="btn btn-primary" data-dismiss="modal">CLOSE</button>
                        </div>
                    </div>
                </div>
            </div>
            
        
            <!-- ID CARDS -->
            <div class="alert alert-light text-center" role="alert" style="margin-left:8%; margin-right:8%; margin-top:70px; color:<?php echo $statcolor ?>; font-size:20px;" >
                <?php echo $statmessage; ?>
            </div>

            <!-- pending action from hr-->        
            <?php if (($_SESSION['userType'] == 1 || $_SESSION['userType'] == 3) && $d['status'] == 5){ ?>
                
                <div class="card" style="margin-left:8%; margin-right:8%; padding-bottom:10px; margin-bottom:10px; margin-top: 10px;">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-4">
                                <i class="fas fa-check mr-1"></i>NEW ACTION [RECOMMENDER]
                            </div>
                            <div class="col-5">
                                <div style="float: right;">ATTACH DOCUMENTS:</div>     
                            </div>
                            <div class="col-3">
                                <input type="file" name="fileToUpload[]" id="fileToUpload"   multiple style="float:right;">
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <p style="color:red;">PLEASE SEE HR REMARKS FOR YOUR NEW ACTION:</p>
                        <textarea type="text" class="form-control" name="hr_remarks" rows="5" readonly><?php echo $d['hr_pen_rem'];?></textarea>
                    </div>     

                    <div class="row">
                        <div class="col-12">
                        <input class="btn btn-primary" type="submit" name="rec_submit_hr" value="SUBMIT" style="float:right; margin-right: 20px;    ">
                        </div>
                    </div>
                </div>
            <?php } ?>
            <!-- pending action from manager-->
            <?php if (($_SESSION['userType'] == 1 || $_SESSION['userType'] == 3) && $d['status'] == 6){ ?>
                
                <div class="card" style="margin-left:8%; margin-right:8%; padding-bottom:10px; margin-bottom:10px; margin-top: 10px;">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-4">
                                <i class="fas fa-check mr-1"></i>NEW ACTION [RECOMMENDER]
                            </div>
                            <div class="col-5">
                                <div style="float: right;">ATTACH DOCUMENTS:</div>     
                            </div>
                            <div class="col-3">
                                <input type="file" name="fileToUpload[]" id="fileToUpload"   multiple style="float:right;">
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <p style="color:red;">PLEASE SEE MANAGER REMARKS FOR YOUR NEW ACTION:</p>
                        <textarea type="text" class="form-control" name="man_remarks" rows="5" readonly><?php echo $d['man_pen_rem'];?></textarea>
                    </div>     

                    <div class="row">
                        <div class="col-12">
                        <input class="btn btn-primary" type="submit" name="rec_submit_man" value="SUBMIT" style="float:right; margin-right: 20px;    ">
                        </div>
                    </div>
                </div>
            <?php } ?>
            <!-- view Form -->
            <div class="card text" style="margin-left:8%; margin-right:8%; margin-top:10px;">
                <div class="card-header">
                    <div class="row">
                        <div class="col-4">
                            <i class="fas fa-star"></i>SPARKS OF BRILLIANCE <?php if ($d['status'] == 1 && (isset($_SESSION['cidNum']) && $_SESSION['cidNum'] == $d['rec_CID'])){ ?> [UPDATING AVAILABLE] <?php } ?>
                        </div>

                        <div class="col-8">
                            <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#fileModal" style="font-size:10px; float: right;">
                              SHOW UPLOADED DOCUMENTS
                            </button>
                        </div>
                    </div>
                </div>
                <?php 
                $isreadable;

                if ($d['status'] == 1 && (isset($_SESSION['cidNum']) && $_SESSION['cidNum'] == $d['rec_CID'])){ 
                    $isreadable = "";
                }
                else {
                    $isreadable = 'readonly';
                } ?>

                <div class ="card-body">
                    <div class="row m-2">
                        <div class="col-12">

                            <div><i class="fas fa-info-circle mr-1"></i>User Information</div>
                                <div style='font-size:10px;'>  
                                   <?php for ($associate = 0 ; $associate < $assocLen ; $associate++){
                                    $rowKeyStrgyID = "keyStrgy_" . $associate;
                                    $rowCIDid = "cid_" . $associate;  ?>
                                    <div class="row">
                                        <div class=col-3>
                                            <label for="">Name of Associate</label>   
                                            <input list="listall" type="text"  name="associate_name_<?php echo $associate; ?>" data-add-cid="<?php echo $rowCIDid ?>" class="form-control form-control-sm input mr-auto empName" value="<?php echo $assoc[$associate]['associate_name'];?>" <?php echo $isreadable ?> >
                                            <datalist id="listall"> <?php
                                                for($i=0; $i<$empsLen; $i++){
                                                    echo "<option data-empID='". $all[$i]['id'] ."' value='". $all[$i]['lastName'] ." ". $all[$i]['firstName'] ."'>";
                                                } ?>
                                            </datalist>     
                                        </div>
                                        <div class="col-3">

                                            <label for="">CID#</label>   
                                            <input type="text" name="userCIDnum_<?php echo $associate; ?>" id="<?php echo $rowCIDid; ?>" class="form-control form-control-sm mr-auto cid" pattern="[0-9]{8}" value="<?php echo $assoc[$associate]['user_CID_num'];?>" <?php echo $isreadable ?> >

                                        </div>
                                        <div class="col-3">          

                                            <label for="">Key Strategy</label>
                                                <select type="text" id="dept" name="key_strat_<?php echo $associate; ?>" class="form-control form-control-sm mr-auto key_strat" data-other-key-strgy="<?php echo $rowKeyStrgyID ?>"  value="" <?php echo $isreadable; ?> >
                                                    <option><?php echo $assoc[$associate]['key_strat'];?></option>
                                                    <option>Capacity Increase</option>
                                                    <option>Cost Reduction</option>
                                                    <option>Zero Defect</option>
                                                    <option>Yield Improvement</option>
                                                    <option>Test COE</option>
                                                    <option>Human Capital</option>
                                                    <option>TPM</option>
                                                    <option>Others</option>
                                                </select>

                                        </div>
                                        <div class="col-2 other" id="<?php echo $rowKeyStrgyID; ?>">

                                                <label for="" style="color: red;">PLEASE SPECIFY*</label>   
                                                <input type="text" name="others_specify_<?php echo $associate; ?>" class="form-control form-control-sm mr-auto others-specify"  autocomplete="off" >

                                        </div>
                                        <?php if (($_SESSION['userType'] == 1 || $_SESSION['userType'] == 2 || $_SESSION['userType'] == 3 || $_SESSION['isSuperior'] == 1) && ($d['status'] == 1 || $d['status'] == 2)){ ?> 

                                            <div class="col-3">

                                                <label for="">Amount (P200-2000 ONLY)</label> 
                                                <input type="text" name="rec_amount_<?php echo $associate; ?>" class="form-control form-control-sm " min="200" max="2000" value="<?php echo $assoc[$associate]['rec_amount'];?>">

                                            </div> <?php }

                                            else if ($_SESSION['isSuperior'] == 0 || $d['status'] == 3 || $d['status'] == 4){ ?>

                                            <div class="col-3">

                                            <label for="">Amount (P200-2000 ONLY)</label> 
                                            <input type="text" name="rec_amount_<?php echo $associate; ?>" class="form-control form-control-sm " min="200" max="2000" value="<?php echo $assoc[$associate]['rec_amount'];?>" <?php echo $isreadable ?> >

                                            </div>   
                                        <?php } ?>

                                        
                                    </div>
                                    <?php } ?>

                                    <hr>
                                        
                                    
                                    <div class="row">
                                    <div class="col-4"> 

                                        <label for="">Department Number</label> 
                                        <input type="text" id="dept" name="dept_num" class="form-control form-control-sm mr-auto"  value="<?php echo $d['dept_num'];?>" readonly >

                                    </div>
                                        
                                        <div class="col-4">          

                                            <label for="">Approver</label>
                                            <input name="deptmanId" class="form-control form-control-sm"  value="<?php echo $m['firstName']." ".$m['lastName']; ?>" readonly>

                                        </div>
                                               
                                    </div>
                                    <div class="row other">
                                            <div class="col-4">

                                                <label for="" style="color: red;">PLEASE SPECIFY*</label>   
                                                <input type="text" name="others_specify" class="form-control form-control-sm mr-auto others-specify"  autocomplete="off" >

                                            </div>
                                    </div>  
                                </div>                                 
                            </div>    

                        <div class="col-6">
                            <div><i class="fas fa-exclamation-circle mr-1"></i>Situation or Task</div>
                            <textarea type='text' name="sit_task" class='form-control form-control-sm' rows="5" <?php echo $isreadable ?> ><?php echo $d['sit_task'];?></textarea>
                        </div> 
                    </div>   

                    <div class="row m-2">
                        <div class="col-6">

                            <div><i class="fas fa-people-carry mr-1"></i>Action Done</div>
                            <textarea type='text' name="act_done" class='form-control form-control-sm' rows="5"  <?php echo $isreadable ?> ><?php echo $d['act_done'];?></textarea>

                        </div>

                    <div class="col-6"> 

                        <div><i class="fas fa-clipboard-check mr-1"></i>Remarkable Results</div>
                        <textarea type='text' name="rem_res" class='form-control form-control-sm' rows="5" <?php echo $isreadable ?> ><?php echo $d['rem_res'];?></textarea>

                    </div>  
                </div>

                    <!-- ///////////////CHANGES WILL APPLY HERE////////////////////////-->

                <div class="row ml-1">

                    <div class="col-3" >
                        <label for="">Recommended By:</label>   
                        <input type="text"  name="rec_by" class="form-control form-control-sm input mr-auto" value="<?php echo $d['rec_by'];?>" readonly>
                            
                        <label for="">CID:</label>   
                        <input type="text"  name="rec_by" class="form-control form-control-sm input mr-auto" value="<?php echo $d['rec_CID'];?>" readonly>     
                    </div>

                    <div class="col-3">
                        <label for="">Approved By:</label>   
                        <input type="text"  name="rec_by" class="form-control form-control-sm input mr-auto" value="<?php echo $d['app_by'];?>" readonly>     
                             
                        <label for="">CID:</label>   
                        <input type="text"  name="rec_by" class="form-control form-control-sm input mr-auto" value="<?php echo $d['app_CID'];?>" readonly>     
                    </div>

                    <div class="col-3">
                        <label for="">Validated By:</label>   
                        <input type="text"  name="valid_by" class="form-control form-control-sm input mr-auto" value="<?php echo $d['valid_by'];?>" readonly>     
                            
                        <label for="">CID:</label>   
                        <input type="text"  name="rec_by" class="form-control form-control-sm input mr-auto" value="<?php echo $d['val_CID'];?>" readonly>     
                    </div>

                    <?php if ($d['status'] == 3 || $d['status'] == 4) { ?>
                        <div class="col-3"></div>
                    <?php }
                    //Update on Status 1
                    if ($d['status'] == 1 && (isset($_SESSION['cidNum']) && $_SESSION['cidNum'] == $d['rec_CID'])){ ?>
                        <div class="col-3">
                            <div class="row">
                                <div class="card" style="width:80%;">
                                    <div class="card-header">
                                        <i class="fas fa-check mr-1"></i>NEW ACTION
                                    </div>
                                    <div class="card-body" >
                                        <div class="row">
                                            <input type="button" class="btn btn-primary" data-toggle="modal" data-target="#updateModal" name="update_form" value="UPDATE FORM" style="margin: auto;">
                                        </div>
                                        <br>
                                        <div class="row">
                                            <input type="button" class="btn btn-danger" data-toggle="modal" data-target="#deleteModal" name="delete_form" value="DELETE FORM" style="margin: auto;">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                <?php  } ?>
                </div>
                <?php 
                                     

                if ($d['status'] != 3 && $d['status'] != 0 && $d['status'] != 4){
                    //Manger Remarks
                    if ($d['status'] == 2 || $d['status'] == 5){  ?>
                        <div class="row">
                            <div class="col-12">
                                <div class="card" style="margin-top:10px; margin-left:8%; margin-right:8%; margin-bottom:10px;">
                                    <div class="card-header"><i class="fas fa-check"></i>REMARKS [Manager]
                                    </div>
                                    <div class="card-body">
                                        <div class="col-12">
                                            <textarea type="text" class="form-control" rows="5" name="manager-remarks" required="true" value="" readonly><?php echo $d['app_rem'];?></textarea>
                                        </div>            
                                    </div>     
                                </div>
                            </div> 
                        </div> 
                    <?php }
                    else{                
                    }
                    //HR Next Action
                    if (($_SESSION['userType'] == 1 || $_SESSION['userType'] == 3) && $d['status'] == 2){ ?>
                        <div class="row">
                            <div class="col-12">
                                <div class="card" style="margin-top:10px; margin-left:8%; margin-right:8%;padding-bottom:10px; margin-bottom:10px;">
                                    <div class="card-header"><i class="fas fa-check mr-1"></i>NEW ACTION: REMARKS [Human Resources]</div>
                                    <div class="card-body">
                                        <textarea type="text" class="form-control" required="true" name="hr_remarks" rows="5"></textarea>
                                    </div>     
                                    <div class="row">
                                        <div class="col-5">
                                            <input class="btn btn-primary" type="submit" name="approved_hr" value="APPROVE" style="float:right;">
                                        </div>
                                        <div class="col-2">
                                            <input class="btn btn-warning" type="submit" name="pending_hr" value="FOR PENDING" style="margin-left: 10px;">
                                        </div>
                                        <div class="col-5">
                                            <input class="btn btn-danger" type="submit" name="declined_hr" value="DECLINE">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php }
                    //////////////////////////STATUS PENDING FORM NEEDED ACTION /////////////////////////////////////////////// 
                    if (($_SESSION['userType'] == 1 || $_SESSION['userType'] == 3) && $d['status'] == 5){ ?>
<!--
                        <div class="card" style="margin-top:10px; margin-left:120px; margin-right:120px;padding-bottom:10px; margin-bottom:10px;">
                            <div class="card-header"><i class="fas fa-check mr-1"></i>NEW ACTION: REMARKS [Human Resources]</div>
                                <div class="card-body">
                                    <textarea type="text" class="form-control" name="hr_remarks" rows="5"></textarea>
                                </div>     

                            <div class="row">
                                <div class="col-6">
                                    <input class="btn btn-primary" type="submit" name="approved_hr" value="APPROVE" style="float:right;">
                                </div>

                                <div class="col-6">
                                    <input class="btn btn-danger" type="submit" name="declined_hr" value="DECLINE">
                                </div>
                            </div>
                        </div>
-->
                    <?php }

                            //////////////////////////////STATUS FOR MANAGER APPOVAL/////////////////////////////////////////////////
                    if ((($_SESSION['isSuperior'] == 1 && $d['status'] == 1) && $_SESSION['cidNum'] == $d['deptmanId']) || ($_SESSION['userType'] == 3 && $d['status'] == 1)){ ?>
                        <div class="card" style="margin-top:10px; margin-left:8%; margin-right:8%;padding-bottom:10px; margin-bottom:10px;">
                            <div class="card-header"><i class="fas fa-check mr-1"></i>NEW ACTION: REMARKS [Manager]</div>
                                <div class="card-body">
                                    <textarea type="text" class="form-control" required="true" rows="5" name="manager-remarks"></textarea>
                                </div>            

                            <div class="row">
                                <div class="col-5">
                                    <input class="btn btn-primary" type="submit" name="approved_man" value="APPROVE" style="float:right;">
                                </div>
                                <div class="col-2">
                                            <input class="btn btn-warning" type="submit" name="pending_man" value="FOR PENDING" style="margin-left: 10px;">
                                        </div>
                                <div class="col-5">
                                    <input class="btn btn-danger" type="submit" name="declined_man" value="DECLINE">
                                </div>

                            </div>
                        </div>  
                               <?php                                   
                    }

                }


                else{
                    ?>
                    <div class="row m-2">
                        <div class="col-6">
                            <div><i class="fas fa-check mr-1"></i>REMARKS [Manager]</div>
                            <textarea type="text" class="form-control" rows="5" name="manager-remarks" value="" readonly><?php echo $d['app_rem'];?></textarea>
                        </div>

                        <div class="col-6">
                            <div><i class="fas fa-check mr-1"></i>REMARKS [Human Resources]</div>
                            <textarea type="text" class="form-control" name="hr_remarks" rows="5" readonly><?php echo $d['valid_rem'];?></textarea>
                        </div>
                    </div>        

                </div>

                <?php } ?>
               
            </div>

            </form>
            <?php  
    }
    include "includes/footer.php";   
}


 else
     {
        $_SESSION['message'] = 1;
        header('Location: logout_page.php');
        exit();
       
     }


?>
     
 