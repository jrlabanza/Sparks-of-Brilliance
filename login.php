<?php session_start();?>
<?php 
if (isset($_SESSION['username'])){
    
    header ('Location: index.php');
    exit();
}
?>
<!--?php include "database/db.php";?>-->
<?php include "includes/header.php"; ?>
 <div class="mt-5" id="login">
        <div class="container">
            <div id="login-row" class="row justify-content-center align-items-center">
                <div id="login-column" class="col-md-6">
                    <div id="login-box" class="col-md-12">
                        <form id="login-form" class="form" action="ldap_connect.php" method="post">
                            <h3 class="text-center text-info">Login</h3>
                            <div class="form-group">
                                <label for="username" class="text-info">Username:</label><br>
                                <input type="text" name="username" id="username" class="form-control" autocomplete="off">
                            </div>
                            <div class="form-group">
                                <label for="password" class="text-info">Password:</label><br>
                                <input type="password" name="password" id="password" class="form-control">
                            </div>
                            <div class="form-group">
                                <input type="submit" name="submit" class="btn btn-info btn-md" value="Login">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    <?php
    
    if (isset($_SESSION['message']) == 1){ ?>
          
           <div class="card text-center" style="width:500px; margin:auto; height:70px; margin-top:20px;">
                 <div class="card-body">
                        <label class="text-center"><font color="red">Username or Password Incorrect! / You are not authorized. Please contact Cherry Candelario for more details Please Try Again!</font></label><br>
                 </div>
           </div>
     <?php
         
         $_SESSION['message'] = null;                           
   } ?>     
   
   
    </div>





<?php include "includes/footer.php" ?>
