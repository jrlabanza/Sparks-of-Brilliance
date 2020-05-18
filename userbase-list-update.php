<?php session_start();?>
<?php

$id = isset($_GET['id']) ? $_GET['id'] : "";

if (isset($_SESSION['username'])){

    if ($_SESSION['userType'] != 1 && $_SESSION['userType'] != 3){
        $_SESSION['message'] = 1;
        header('Location: logout_page.php');
        exit();
        }
        include "includes/header.php"; 
        
        update_userdata_list($id);

        $query = "SELECT * FROM employeeinfos WHERE id =". $id;
        $sql = $userconnect->query($query);
        $result = get_data_array($sql); ?>

        <div class="card mb-3" style="margin-left:120px; margin-right:120px; margin-top:60px;">
            <div class="card-header"><i class="fas fa-table mr-1"></i>USERBASE LIST [HUMAN RESOURCES]</div>
            <div class="card-body">
                <h3>UPDATE USER</h3>
                <form action="userbase-list-update.php?id=<?php echo $id ?>" method="post">
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
                        <input type="text" class="form-control form-control-sm" id="master_lastName"  name="lastName" style="font-size: 10px;" value="<?php echo $result['lastName'] ?>" autocomplete="off" required>
                    </div>
                    <div class="col-2">         
                        <input type="text" class="form-control form-control-sm" id="master_firstName" name="firstName" style="font-size: 10px;" value="<?php echo $result['firstName'] ?>" autocomplete="off" required>
                    </div>
                    <div class="col-2">         
                        <input type="text" maxlength="8" pattern= "[0-9]+" class="form-control form-control-sm" id="" name="cid" style="font-size: 10px;" value="<?php echo $result['cidNum'] ?>" autocomplete="off" required>
                    </div>
                    <div class="col-2">         
                        <input type="email" class="form-control form-control-sm"  id="master_email" name="email" style="font-size: 10px;" value="<?php echo $result['email'] ?>" autocomplete="off" required>
                    </div>
                    <div class="col-2">         
                        <input type="text" class="form-control form-control-sm"  id="master_deptcode" name="deptcode" style="font-size: 10px;" value="<?php echo $result['deptCode'] ?>" autocomplete="off" required>
                    </div>
                    <div class="col-2">         
                        <select type="text" class="form-control form-control-sm" id="master_sob_pos" name="sob_pos" value="<?php echo $result['isSuperior'] ?>">
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
                        <input type="text" class="form-control form-control-sm" id="master_ffID"  name="ffID" style="font-size: 10px;" autocomplete="off" value="<?php echo $result['ffId'] ?>" required>
                    </div>
                    <div class="col-2">         
                        <input type="text" class="form-control form-control-sm" id="master_jobDescription" name="jobDescription" style="font-size: 10px;" autocomplete="off" value="<?php echo $result['jobName'] ?>" required>
                    </div>
                    <div class="col-2">         
                        <input type="text" class="form-control form-control-sm" id="master_immediateSupervisor" name="immediateSupervisor" style="font-size: 10px;" autocomplete="off" value="<?php echo $result['immediateSuperior'] ?>" required>
                    </div>
                    <div class="col-2">         
                        <input type="text" maxlength="8" pattern= "[0-9]+" class="form-control form-control-sm"  id="master_supervisorCID" name="supervisorCID" style="font-size: 10px;" autocomplete="off" value="<?php echo $result['supvCID'] ?>" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <button type="submit" class="btn btn-sm btn-danger mt-3" name="delete_userbase" style="float:left;"><i class="fas fa-user-times mr-1"></i>DELETE USER</button>
                        <button type="submit" class="btn btn-sm btn-primary mt-3" name="update_userbase" style="float:right;"><i class="fas fa-plus mr-1"></i>UPDATE USER</button>
                    </div>
                </div>
                </form>
                <hr>
  
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
