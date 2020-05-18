<?php session_start();?>
<?php

    $_SESSION['username'] = null;
	$_SESSION['isSuperior'] = null;
    $_SESSION['id'] = null;
	$_SESSION['deptCode'] = null;
    $_SESSION['jobName'] = null;
    $_SESSION['userType'] = null;
    $_SESSION['department_head'] = null;


    header("Location: login.php");


?>
