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
        
        update_master_list($id);

        $query = "SELECT * FROM masterlist WHERE id =". $id;
        $sql = $connection->query($query);
        $result = get_data_array($sql); ?>

        <div class="card mb-3" style="margin-left:120px; margin-right:120px; margin-top:60px;">
            <div class="card-header"><i class="fas fa-table mr-1"></i>MASTER LIST [HUMAN RESOURCES]</div>
            <div class="card-body">
                <h3>UPDATE USER</h3>
                <form action="master-list-update.php?id=<?php echo $id ?>" method="post">
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
                            <h6>SOB Position:</h6>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-2">
                            <input type="text" class="form-control form-control-sm"  value='<?php echo $result['lastName'] ?>' name="lastName" style="font-size: 10px;" autocomplete="off">
                        </div>
                        <div class="col-2">         
                            <input type="text" class="form-control form-control-sm" value='<?php echo $result['firstName'];?>' name="firstName" style="font-size: 10px;" autocomplete="off">
                        </div>
                        <div class="col-2">         
                            <input type="text" class="form-control form-control-sm" value='<?php echo $result['userCID'];?>' name="cid" style="font-size: 10px;" autocomplete="off">
                        </div>
                        <div class="col-2">         
                            <input type="text" class="form-control form-control-sm" value='<?php echo $result['email'];?>' name="email" style="font-size: 10px;" autocomplete="off">
                        </div>
                        <div class="col-2">         
                            <input type="text" class="form-control form-control-sm" value='<?php echo $result['deptCode'];?>'  name="deptcode" style="font-size: 10px;" autocomplete="off">
                        </div>
                        <div class="col-2">         
                            <select type="text" class="form-control form-control-sm" name="sob_pos">
                                <option value=''></option>
                                <option value="">People Leader</option>
                                <option value="Department Head">Department Head</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <button type="submit" class="btn btn-sm btn-danger mt-3" name="delete_masterlist" style="float:left;"><i class="fas fa-user-times mr-1"></i>DELETE USER</button>
                            <button type="submit" class="btn btn-sm btn-primary mt-3" name="update_masterlist" style="float:right;"><i class="fas fa-plus mr-1"></i>UPDATE USER</button>
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
