<?php session_start();

if (isset($_SESSION['username']))
{
    include "includes/header.php"; 
    include "database/db.php";
    
    $no_of_assoc = $_SESSION['no_of_assoc'];
    $current_amount = $_SESSION['current_amount'];
    $custom_amount = $_SESSION['custom_amount'];


    if($current_amount == "eq")
    {
       $new_amount = $custom_amount;
    }
    else if($current_amount == "da")
    {
        $new_amount = $custom_amount / $no_of_assoc;
    }
    else if($current_amount == "mi")
    {
        $new_amount = $custom_amount;
    }
   
    if ($_SESSION['userType'] == 1 || $_SESSION['userType'] == 2 || $_SESSION['userType'] == 3 || $_SESSION['userType'] == 4 || $_SESSION['userType'] == 5 || $_SESSION['isSuperior'] == 1)
    {   
        createForm();

        // query list    
        $sql ="SELECT * from employeeinfos WHERE isDeleted=0 ORDER BY lastName ASC";
        $result = $userconnect-> query($sql);
        $all = get_assocArray($result);
        $empsLen = sizeof($all);

        $sql ="SELECT * from employeeinfos WHERE isDeleted=0 AND ffId ='". $_SESSION['username'] ."'";
        $result = $userconnect-> query($sql);
        $d = get_data_array($result);

        $sql = "SELECT * FROM employeeinfos WHERE isDeleted=0 AND isSuperior = '1' ORDER BY lastName ASC";
        $result = $userconnect->query($sql);
        $s = get_assocArray($result);
        $supLen = sizeof($s);

        $sql ="SELECT COUNT(*) AS man_count FROM masterlist WHERE deptCode = '". $_SESSION['deptCode'] ."'AND department_head = 'Department Head'" ;
        $result = $connection->query($sql);
        $count = get_data_array($result);

        $sql = "SELECT * FROM masterlist WHERE deptCode = '". $_SESSION['deptCode'] ."' AND department_head = 'Department Head'";
        $result = $connection->query($sql);
        $new_approver = get_assocArray($result);
        $new_approver_size = sizeof($new_approver);
        
        $sql = "SELECT * FROM masterlist WHERE userCID = '". $_SESSION['cidNum'] ."'";
        $result = $connection->query($sql);
        $multi_approver_check = get_data_array($result);?>
    
        <div class="alert alert-warning" role="alert" style="margin-left:180px; margin-right:180px; margin-top:70px;" >
        WARNING: BE WARY OF YOUR INPUT, ONCE IT IS SUBMITTED, AN EMAIL NOTIFICATION WILL BE SENT TO THE APPROVER.
        </div>
        <form action="create-form.php" method = "post" enctype="multipart/form-data">
            <input type="text" name="no_of_associates" hidden value="<?php echo $no_of_assoc; ?>">
            <input type="text" name="amount" hidden value="<?php echo $amount; ?>">
            <input type="text" name="custom_amount" hidden value="<?php echo $custom_amount; ?>">
            <div class="card text" style="margin-left:10%; margin-right:10%; margin-top:10px;">
                <div class="card-header">
                    <div class="row">
                        <div class="col-4">
                            <i class="fas fa-star mr-1"></i>SPARKS OF BRILLIANCE
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
                    <div class="row">
                        <div class="col-12">
                            <div style="padding-bottom:10px;"><i class="fas fa-info-circle mr-1"></i>User Information</div>
                            <div style="font-size:10px;"><?php 
                            
                            for ($form = 0 ; $form < $no_of_assoc ; $form++)
                            { 
                                $rowKeyStrgyID = "keyStrgy_" . $form;
                                $rowCIDid = "cid_" . $form; ?>
                                
                                <div class="appendform">
                                    <div class="row">
                                        <div class=col-3>
                                            <label for="">Name of Associate (Last,First)</label>   
                                            <input list="listall"type="text"  name="associate_name_<?php echo $form; ?>" data-add-cid="<?php echo $rowCIDid ?>"class="form-control form-control-sm input mr-auto empName" required="true">     
                                            <datalist id="listall"><?php
                                                for($i=0; $i<$empsLen; $i++){
                                                    echo "<option data-empID='". $all[$i]['id'] ."' value='". $all[$i]['lastName'] ." ". $all[$i]['firstName'] ."'>";
                                                }?>
                                            </datalist>     
                                        </div>
                                        <div class="col-3">
                                            <label for="">CID#</label>   
                                            <input type="number" name="userCIDnum_<?php echo $form; ?>" id="<?php echo $rowCIDid; ?>" class="form-control form-control-sm mr-auto cid" pattern="[0-9]{8}" required="true" autocomplete="off" >
                                        </div>
                                        <div class="col-3">          
                                            <label for="">Key Strategy</label>
                                            <select name="key_strat_<?php echo $form; ?>"class="form-control form-control-sm key_strat" data-other-key-strgy="<?php echo $rowKeyStrgyID ?>" required="true">
                                                <option></option>
                                                <option>Capacity Increase</option>
                                                <option>Cost Reduction</option>
                                                <option>Cost Avoidance</option>
                                                <option>Zero Defect</option>
                                                <option>Yield Improvement</option>
                                                <option>Test COE</option>
                                                <option>Human Capital</option>
                                                <option>TPM</option>
                                                <!-- <option>E-SAVINGS</option> -->
                                                <option>Others</option>
                                            </select>
                                        </div>
                                        <div class="col-2 other" id="<?php echo $rowKeyStrgyID; ?>">
                                            <label for="" style="color: red;">PLEASE SPECIFY*</label>   
                                            <input type="text" name="others_specify_<?php echo $form; ?>" class="form-control form-control-sm mr-auto others-specify"  autocomplete="off" >
                                        </div>
                                        <div class="col-2">
                                            <label for="">Amount</label> 
                                            <input type="number" name="rec_amount_<?php echo $form; ?>" class="form-control form-control-sm " min="200" max="2000" required="true" autocomplete="off" value="<?php echo $new_amount;?>">
                                        </div>
                                    </div>
                                </div><?php 
                            } ?>
                            <hr>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <div>
                                <div style="padding-bottom:10px;"><i class="fas fa-exclamation-circle mr-1"></i>Situation or Task</div>
                                <textarea type='text' name="sit_task" class='form-control form-control-sm' rows="5" required="true"></textarea>
                            </div>

                        </div>
                    </div>                                    
                    <div class="row">
                        <div class="col-6">
                            <div>
                                <div style="padding-bottom:10px;"><i class="fas fa-people-carry mr-1"></i>Action Done</div>
                                <textarea type='text' name="act_done" class='form-control form-control-sm' rows="5"  required="true"></textarea>
                            </div>
                        </div>
                        <div class="col-6"> 
                            <div>
                                <div style="padding-bottom:10px;"><i class="fas fa-clipboard-check mr-1"></i>Remarkable Results</div>
                                <textarea type='text' name="rem_res" class='form-control form-control-sm' rows="5" required="true"></textarea>
                            </div>         
                        </div>
                    </div> <?php
            
                    if (isset($multi_approver_check['department_head']) && $multi_approver_check['department_head'] == "Department Head" )
                    {

                    }
                    else
                    {
                        if ($count['man_count'] > 1)
                        { ?>
                            <div class="row">
                                <div class="col-3" id="<?php echo $rowKeyStrgyID; ?>">
                                    <label for="" style="color: red;">PLEASE SELECT AN APPROVER*</label>   
                                    <select class='form-control form-control-sm' name='approver' required='true'>
                                        <option value=""></option><?php 
                                        for ($j = 0 ; $j < $new_approver_size ; $j++)
                                        { ?>
                                            
                                            <option value="<?php echo $new_approver[$j]['userCID']?>"><?php echo $new_approver[$j]['firstName']. " " . $new_approver[$j]['lastName']?></option><?php 
                                        
                                        } ?>
                                    </select>
                                </div>
                            </div> <?php 
                        }
                    } ?>

                    <div>
                        <div>
                            <div class="row" style="padding-top:30px;">
                                <div class="col-2">
                                    <input class="btn btn-primary submit_sob" type="submit" name="submit" value="SUBMIT">
                                </div>
                            </div>                                    
                        </div>                                                    
                    </div>     
                </div>
            </div>
        </form>                                                                     
        <?php 
        include "includes/footer.php";    
    }
  
     else
     {
         header('Location: logout_page.php');
         exit();
     }
}
else
{
    header('Location: logout_page.php');
    exit();
}
?>
     
 