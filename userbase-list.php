<?php session_start();?>
<?php

if (isset($_SESSION['username'])){

    if ($_SESSION['userType'] != 1 && $_SESSION['userType'] != 3){
        $_SESSION['message'] = 1;
        header('Location: logout_page.php');
        exit();
    }
    include "includes/header.php";
    
    $query = "SELECT * FROM userlookup.employeeinfos";
    $sql = $userconnect->query($query);
    $result = get_assocArray($sql);
    $size = sizeof($result); ?>
    <div class="alert alert-warning text-center" role="alert" style="margin-left:120px; margin-right:120px; margin-top:60px;" >
    WARNING: SENSITIVE DATA. HR ACCESS ONLY
    </div>
    <div class="card mb-3" style="margin-left:120px; margin-right:120px; margin-top:20px;">
        <div class="card-header"><i class="fas fa-table mr-1"></i>USERDATA LIST [HUMAN RESOURCES]</div>
        <div class="card-body">
            <h3>ADD USER</h3>
            <form action="userbase-list.php" method="post">
            <?php add_userdata_list() ?>
                <div class="row">
                    <div class="col-2">
                        <h6>Last Name:</h6>
                    </div>
                    <div class="col-2">         
                        <h6>First Name:</h6>
                    </div>
                    <div class="col-2">         
                        <h6>CID Number:</h6>
                    </div>
                    <div class="col-2">         
                        <h6>Email:</h6>
                    </div>
                    <div class="col-2">         
                        <h6>Department Code:</h6>
                    </div>
                    <div class="col-2">         
                        <h6>Position:</h6>
                    </div>
                </div>
                <div class="row">
                    <div class="col-2">
                        <input type="text" class="form-control form-control-sm" id="master_lastName"  name="lastName" style="font-size: 10px;" autocomplete="off" required>
                    </div>
                    <div class="col-2">         
                        <input type="text" class="form-control form-control-sm" id="master_firstName" name="firstName" style="font-size: 10px;" autocomplete="off" required>
                    </div>
                    <div class="col-2">         
                        <input type="text" maxlength="8" pattern= "[0-9]+" class="form-control form-control-sm" id="" name="cid" style="font-size: 10px;" autocomplete="off" required>
                        
                    </div>
                    <div class="col-2">         
                        <input type="email" class="form-control form-control-sm"  id="master_email" name="email" style="font-size: 10px;" autocomplete="off" required>
                    </div>
                    <div class="col-2">         
                        <input type="text" class="form-control form-control-sm"  id="master_deptcode" name="deptcode" style="font-size: 10px;" autocomplete="off" required>
                    </div>
                    <div class="col-2">         
                        <select type="text" class="form-control form-control-sm" id="master_sob_pos" name="sob_pos">
                            <option value="0">NORMAL USER</option>
                            <option value="1">DEPARTMENT HEAD/ SUPERIOR ETC.</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-2">
                        <h6>FFID:</h6>
                    </div>
                    <div class="col-2">         
                        <h6>JOB DESCRIPTION:</h6>
                    </div>
                    <div class="col-2">         
                        <h6>IMMEDIATE SUPERVISOR:</h6>
                    </div>
                    <div class="col-2">         
                        <h6>SUPERVISOR CID:</h6>
                    </div>
                </div>
                <div class="row">
                    <div class="col-2">
                        <input type="text" class="form-control form-control-sm" id="master_ffID"  name="ffID" style="font-size: 10px;" autocomplete="off" required>
                    </div>
                    <div class="col-2">         
                        <input type="text" class="form-control form-control-sm" id="master_jobDescription" name="jobDescription" style="font-size: 10px;" autocomplete="off" required>
                    </div>
                    <div class="col-2">         
                        <input type="text" class="form-control form-control-sm" id="master_immediateSupervisor" name="immediateSupervisor" style="font-size: 10px;" autocomplete="off" required>
                    </div>
                    <div class="col-2">         
                        <input type="text" maxlength="8" pattern= "[0-9]+" class="form-control form-control-sm"  id="master_supervisorCID" name="supervisorCID" style="font-size: 10px;" autocomplete="off" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <button type="submit" class="btn btn-sm btn-primary mt-3" name="create_userdata" style="float:right;"><i class="fas fa-plus mr-1"></i>ADD USER</button>
                    </div>
                </div>

            </form>
            <hr>
            <h3>USER TABLE</h3>
            <table class="download table table-bordered table-hover" id="" width="100%" cellspacing="0" style="font-size:10px;"> 
                <thead>
                    <tr>
                        <th>Last Name</th>
                        <th>First Name</th>
                        <th>CID No.</th>
                        <th>Email</th>
                        <th>Department Code</th>
                        <th>Edit</th>
                    </tr>
                </thead>
                <tbody><?php
                    for ($i = 0 ; $i < $size ; $i++){ ?>
                        <tr class='show-master'>
                            <td class='firstName'><?php echo $result[$i]['lastName'];?></td>
                            <td class='lastName'><?php echo $result[$i]['firstName'];?></td>
                            <td class='userCID'><?php echo $result[$i]['cidNum'];?></td>
                            <td class='email'><?php echo $result[$i]['email'];?></td>
                            <td class='deptCode'><?php echo $result[$i]['deptCode'];?></td>
                            <td> <a href="userbase-list-update.php?id=<?php echo $result[$i]['id'];?>" class='btn btn-info btn-sm edit-button' data-machine-id="<?php echo $results[$i]['id'];?>">VIEW</a></td>
                        </tr><?php
                    } ?>
                </tbody>
            </table>
        </div>
    </div><?php 
    include "includes/footer.php";
}

else{

    $_SESSION['message'] = 1;
    header('Location: logout_page.php');
    exit();
    
}


?>
