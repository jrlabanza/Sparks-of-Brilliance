<!DOCTYPE html>
<?php 
include "database/db.php";
include "php/link.php";
include "functions/functions.php";

$showall_query ="SELECT * from employeeinfos WHERE isDeleted=0";
$fullresult = $connection-> query($showall_query);
$all = get_assocArray($fullresult);
$empsLen = sizeof($all);

if (isset($_SESSION['username']))
{
    $sql ="SELECT * from employeeinfos WHERE isDeleted=0 AND ffId ='". $_SESSION['username'] ."'";
    $result = $userconnect-> query($sql);
    $s = get_data_array($result);
}?>

<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="x-ua-compatible" content="IE=edge"> 
        <meta http-equiv="x-ua-compatible" content="IE=11">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <meta name="author" content="">
        <link rel="icon" href="assets/star.png">
        <link href="css-ext.css" rel="stylesheet">
        <title>ON-SEMI SPARKS OF BRILLIANCE TARLAC</title>
    </head>
    <body style="background-color: #dcdfe5; font-size:14px;">
        <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Instructions:</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <hr>
                        <p><b>How to Log-In</b></p>
                        <hr>
                        <p>1. Enter Your AD Account when you are prompt to the Log-in Screen</p>
                        <p>2. Press the "Log-in Button</p>
                        <img src="assets/help1.png" alt="" width='400px' height='200px'>
                        <hr>
                        <p><b>How to Submit a SOB Form</b></p>
                        <hr>
                        <p>1. Select the "WELCOME" tab and Select "CREATE" button to open the confirmation pop up page</p>
                        <img src="assets/help2.png" alt="" width='800px' height='200px'>
                        <p>2. Inside the Form Creation, Follow what has been instructed Below.</p>
                        <img src="assets/help8.png" alt="" width='800px' height='400px'>
                        <p>3. Inside the Form Creation, Follow what has been instructed Below.</p>
                        <img src="assets/help3.png" alt="" width='100%' height='600px'>
                        <hr>
                        <p><b>How to Check Status/View the form</b></p>
                        <hr>
                        <img src="assets/help5.png" alt="" width='100%' height='200px'>
                        <br>
                        <br>
                        <img src="assets/help6.png" alt="" width='100%' height='400px'>
                        <hr>
                        <p><b>How to Update the Form</b></p>
                        <hr>
                        <p><b>NOTE: </b>You can only update the form when the status of the form is at "FOR MANAGER APPROVAL"</p>
                        <img src="assets/help5.png" alt="" width='100%' height='200px'>
                        <br>
                        <br>
                        <img src="assets/help7.png" alt="" width='100%' height='400px'>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
        <form action="create-form.php" method = "post">
            <?php formproperty() ?>
            <div class="modal fade" id="formcount">
                <div class="modal-dialog" style="margin-top:10%;">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Confirmation</h4>
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                        </div>
                        <div class="modal-body m-2">
                            <div class="row">
                                <div class="col-6">No. Associates:<input type="number" class="form-control form-control-sm no_of_associates" name="no_of_associates"></div>
                                <div class="col-6 amount_modifier">Amount Distributed:
                                    <br>
                                    <div class="form-check-inline">
                                        <label class="form-check-label">
                                            <input type="radio" class="form-check-input amount" name="amount" value="eq">Equal Amount
                                        </label>
                                    </div>
                                    <div class="form-check-inline">
                                        <label class="form-check-label">
                                            <input type="radio" class="form-check-input amount" name="amount" value="da">Divided Amount
                                        </label>
                                    </div>
                                    <div class="form-check-inline">
                                        <label class="form-check-label">
                                            <input type="radio" id= "default_amount" class="form-check-input amount" name="amount" value="mi">Manual Input
                                        </label>
                                    </div>
                                    <div class="form-check-inline">
                                        <label class="form-check-label">
                                            <input type="number" class="form-control form-control-sm custom_amount" name="custom_amount" >
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <p style="font-size: 12px; float:left;">*Only on Manager Pending is Updating Available, Once it has been approved by the Manager, It cannot be changed.</p>
                            <input class="btn btn-primary" type="submit" name="form_property" value="UPDATE FORM" style="">
                            <button type="button" class="btn btn-danger" data-dismiss="modal">CLOSE</button>
                        </div>
                    </div>
                </div>
            </div>
        </form> 

        <?php
        if (isset($_SESSION['username']) == 'username') 
        { ?>

            <nav class="navbar navbar-expand-lg navbar-dark fixed-top" style="font-size: 13px; background-color: #133455;">     
                <img src="assets/star.png" alt="Logo" style="width:40px; margin-right: 10px;">
                <a class="navbar-brand" style="color: white;">SPARKS OF BRILLIANCE</a>      
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
                    <div class="navbar-nav">
                        <a class="nav-item nav-link" href="http://10.153.239.120">PROMIS</a>
                        <a class="nav-item nav-link how-to" data-toggle="modal" data-target="#exampleModal">HOW TO</a>
                    </div>
                </div>
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="color:white; margin-right: 30px;" ><i class="fas fa-globe-asia mr-1"></i>WELCOME <?php echo $s['firstName'] ?>
                    </a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <a class="dropdown-item" href="index.php"><i class="fas fa-home mr-1"></i>Home</a>
                        <a class="dropdown-item create-form" data-toggle="modal" data-target="#formcount"><i class="fas fa-file-signature mr-1 create"></i>Create Form</a>
                        <?php 

                        if ($_SESSION['userType'] == 1 || $_SESSION['userType'] == 3)
                        { ?>
                            <a class="dropdown-item" href="master-list.php"><i class="fas fa-clipboard-list mr-1"></i>Master List</a> <?php    
                        }

                        if ($_SESSION['userType'] == 1 || $_SESSION['userType'] == 3)
                        { ?>
                            <a class="dropdown-item" href="userbase-list.php"><i class="fas fa-clipboard-list mr-1"></i>Userbase List</a><?php  
                        }

                        if ($_SESSION['userType'] == 5 || $_SESSION['userType'] == 4 || $_SESSION['userType'] == 3 || $_SESSION['userType'] == 1)
                        { ?>
                            <a class="dropdown-item" href="data-extract.php"><i class="fas fa-history mr-1"></i>Data Extraction</a> <?php 
                         }

                        if ($_SESSION['userType'] == 1 || $_SESSION['userType'] == 3)
                        { ?>
                            <a class="dropdown-item" href="hr-log.php"><i class="fas fa-history mr-1"></i>File Log [HR]</a> <?php   
                        }

                        if ($_SESSION['userType'] == 2 || $_SESSION['userType'] == 3)
                        { ?>
                            <a class="dropdown-item" href="finance-log.php"><i class="fas fa-history mr-1"></i>File Log [FN]</a> <?php     
                        }

                        if (isset($_SESSION['department_head']) && ($_SESSION['department_head']) == "Department Head")
                        { ?>
                            <a class="dropdown-item" href="dh-reporting.php"><i class="fas fa-file mr-1"></i>Reporting [DH]</a><?php 
                        }

                        if ($_SESSION['userType'] == 2 || $_SESSION['userType'] == 3)
                        { ?>
                            <a class="dropdown-item" href="finance-list.php"><i class="fas fa-thumbs-up mr-1"></i>Approval [FN]</a> <?php    
                        } ?>
                        
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="logout_page.php" ><i class="fas fa-sign-out-alt mr-1"></i>Log-Out</a>
                    </div>
                </div>
            </nav> <?php 

        }
        else{ ?>    
        <nav class="navbar navbar-expand-lg navbar-dark fixed-top" style="font-size: 13px; background-color: #133455;">     
            <img src="assets/star.png" alt="Logo" style="width:40px; margin-right: 10px;">
            <a class="navbar-brand" style="color: white;">SPARKS OF BRILLIANCE</a>     
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
                <div class="navbar-nav">
                    <a class="nav-item nav-link" href="http://10.153.239.120">PROMIS</a>
                    <a class="nav-item nav-link how-to" data-toggle="modal" data-target="#exampleModal">HOW TO</a>
                </div>
            </div>
            <a class="nav-item nav-link" href="login.php" style="float:right; color:white; margin-right: 30px; font-size:15px;"><i class="fas fa-sign-in-alt mr-1"></i>Log-In</a>
        </nav> <?php 
        }
    ?>
    
    
    
  