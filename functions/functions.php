<?php include "database/db.php";

//Mail FUnction
function sendmail_utf8($to, $from_user, $from_email, $subject = '(No subject)', $message = '', $cc="")
{
    $from_user = "=?UTF-8?B?".base64_encode($from_user)."?=";
    $subject = "=?UTF-8?B?".base64_encode($subject)."?=";

    $headers = "From: $from_user <$from_email>\r\n".
                "MIME-Version: 1.0" . "\r\n" .
                "Content-type: text/html; charset=UTF-8" . "\r\n";

    if ($cc != "")
    {
        $headers .= "CC: ". $cc ." \r\n";
    }

    return mail($to, $subject, $message, $headers);
}

//Get data row in SQL
function get_data_array($result)
{
    $data = array();

    if (is_object($result) && !empty($result->num_rows))
    {
        while ($row = $result->fetch_assoc())
        {
            foreach($row as $col => $value)
            {
                $data[$col] = $value;
            }
        }
        $result->free();
    }

    return $data;
}

//Get all continuous rows
function get_assocArray($result)
{ 
    $data = array(); 

    if (is_object($result) && !empty($result->num_rows))
    { 
        while ($row = $result->fetch_assoc())
        { 
            $tempColumns = array(); 
            foreach($row as $col => $value)
            { 
                $tempColumns[$col] = $value; 
            } 
            array_push($data, $tempColumns); 
        } 
        $result->free(); 
    }
    return $data; 
}

//Sanitize Input Protection		
function sanitizeInput($input)
{ 
    global $connection;
    $inputTemp = ""; 
    $inputTemp = htmlspecialchars($input, ENT_QUOTES); 
    $inputTemp = filter_var($inputTemp, FILTER_SANITIZE_STRING); 
    $inputTemp = $connection->real_escape_string($inputTemp); 
    return $inputTemp; 
}

// Form Properties
function formproperty()
{
    if (isset($_POST['form_property'])){    
        $_SESSION['no_of_assoc'] = $_POST['no_of_associates'];
        $_SESSION['current_amount'] = $_POST['amount'];
        $_SESSION['custom_amount'] = $_POST['custom_amount'];
    }
}
//Create Form Function

function createForm()
{
	global $connection;
    global $userconnect;
    if (isset($_POST['submit']))
    {
        $sql ="SELECT * from employeeinfos WHERE isDeleted=0 AND ffId ='". $_SESSION['username'] ."'";
        $result = $userconnect-> query($sql);
        $d = get_data_array($result);
        
        $sql_approver = "SELECT * FROM masterlist WHERE deptCode ='". $_SESSION['deptCode'] ."' AND department_head = 'Department Head'";
        $result_approver = $connection-> query($sql_approver);
        $approver = get_data_array($result_approver);

        $sql ="SELECT COUNT(*) AS man_count FROM masterlist WHERE deptCode = '". $_SESSION['deptCode'] ."'AND department_head = 'Department Head'" ;
        $result = $connection->query($sql);
        $count = get_data_array($result);

        $sql = "SELECT * FROM masterlist WHERE userCID = '". $_SESSION['cidNum'] ."'";
        $result = $connection->query($sql);
        $multi_approver_check = get_data_array($result);

        if (isset($multi_approver_check['department_head']) && $multi_approver_check['department_head'] == "Department Head")
        {
            $deptmanId = $d['supvCID'];
        }

        else
        {
            if ($count['man_count'] > 1)
            {
                $deptmanId = $_POST['approver'];
            }

            else
            {
                if ($_SESSION['cidNum'] == $approver['userCID'])
                {
                    $deptmanId = $d['supvCID'];          
                }

                else
                {
                    if (isset($approver['userCID']))
                    {
                        $deptmanId = $approver['userCID'];
                    }

                    else
                    {
                        echo ("<script LANGUAGE='JavaScript'>
                                window.alert('Your Department has no Department Head in the Server. Please Contact HR.');
                                window.location.href='create-form.php';
                                </script>");
                        stop();
                    }    
                }
            }
        }

        $no_of_associates = $_POST['no_of_associates'];

        $ticket_no = "SOB-". date("ymdHis");
        
        $sit_task = sanitizeInput($_POST['sit_task']);
        $act_done = sanitizeInput($_POST['act_done']);
        $rem_res = sanitizeInput($_POST['rem_res']);
       
        $rec_by = $d['lastName'].' '.$d['firstName'];

        $rec_ffId = $_SESSION['username'];
        $jobName = $_SESSION['jobName'];
        $cidNum = $_SESSION['cidNum'];
        $rec_dept = $_SESSION['deptCode'];
        $status = "1";

        for ($check = 0 ; $check < $no_of_associates ; $check++){

            $tempUserCIDnum = "userCIDnum_" . $check;
            $userCIDnum = sanitizeInput($_POST[$tempUserCIDnum]);

            if ($userCIDnum == $_SESSION['cidNum'])
            {
                echo ("<script LANGUAGE='JavaScript'>
                        window.alert('An invalid input has been detected. Reverting Changes');
                        window.location.href='create-form.php';
                        </script>");
                stop();
            }

        }

        $query = "INSERT INTO formfill_infov2 (dept_num,sit_task,act_done,rem_res,rec_by,rec_ffId,status,rec_CID,deptmanId,no_of_assoc,ticket_no) VALUES ('$rec_dept', '$sit_task', '$act_done' ,'$rem_res' ,'$rec_by','$rec_ffId', '$status','$cidNum','$deptmanId','$no_of_associates','$ticket_no')";
	    $result3 = $connection-> query($query);

        for ($assoc = 0 ; $assoc < $no_of_associates ; $assoc++)
        {
            $tempAssociateName = "associate_name_". $assoc;
            $tempUserCIDnum = "userCIDnum_" . $assoc;
            $tempRec_amount = "rec_amount_" . $assoc;
            $tempOthers_specify = "others_specify_" . $assoc;
            $tempKeyStrat = "key_strat_" . $assoc;
            $associateName = sanitizeInput(strtoupper($_POST[$tempAssociateName]));
            $userCIDnum = sanitizeInput($_POST[$tempUserCIDnum]);
            $rec_amount = sanitizeInput($_POST[$tempRec_amount]);

            if ($_POST[$tempKeyStrat] == "Others")
            {
                $others_specify = sanitizeInput($_POST[$tempOthers_specify]);
                $key_strat = "";    
            }

            else 
            {
                $key_strat = sanitizeInput($_POST[$tempKeyStrat]);
                $others_specify = "";
            }

            $new_key_strat = $key_strat. "" .$others_specify; 
            
            $sql = "SELECT * FROM formfill_infov2 ORDER BY rec_date DESC LIMIT 1 ";
            $result = $connection->query($sql);
            $date = get_data_array($result);

            $formId = $date['id'];

            if ($no_of_associates > 1)
            {
                $subticket_no = $date['ticket_no'] . '-' . ($assoc + 1);
            }

            else
            {
                $subticket_no = $date['ticket_no'];
            }
            
            $sql = "INSERT INTO associate_info (associate_name,user_CID_num,rec_amount,key_strat,sobFormID,subticket_no) VALUES ('$associateName','$userCIDnum','$rec_amount','$new_key_strat','$formId','$subticket_no')";
            $result = $connection->query($sql);
        }
 
        $itemquery = "SELECT * FROM formfill_infov2 WHERE rec_ffId ='". $_SESSION['username'] . "' ORDER BY rec_date DESC LIMIT 1";
        $itemresult = $connection-> query($itemquery);
        $ir = get_data_array($itemresult);
        
                 
        $total = count($_FILES["fileToUpload"]["name"]);
        
        
        if ($_FILES["fileToUpload"]["name"][0] != "")
        {

            for ($i=0; $i<$total; $i++)
            {
                
                $itemName = ($_FILES["fileToUpload"]["name"][$i]);
                $itemNo = $ir['id'];
                $newfilename = date('dmYHis')."_".str_replace("", "", basename($_FILES["fileToUpload"]["name"][$i]));
                $target_dir = "uploads/";
                $target_file = $target_dir . $newfilename;
                $uploadOk = 1;
                $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

                if ($_FILES["fileToUpload"]["size"][$i] > 50000000)
                {
                    echo ("<script LANGUAGE='JavaScript'>
                            window.alert('File is too large, Returning to Create Form');
                            window.location.href='create-form.php';
                            </script>");
                    $uploadOk = 0;
                }

                $validExtensions = array('jpg' , 'png' , 'jpeg' , 'gif' , 'xlsx', 'docx', 'pdf', 'pptx',
                "xls", "xlt", "xlm", "xlsm", "xltx",
                "xltm", "xlsb", "xla", "xlam", "xll", "xlw",
                "ppt", "pot", "pps", "pptx", "pptm", "potx",
                "potm", "ppam", "ppsx", "ppsm", "sldx", "sldm",
                "doc", "docm", "dot", "dotm", "dotx",
                "msg", "pdf");

                
                if(!in_array($imageFileType."", $validExtensions))
                {
                    echo ("<script LANGUAGE='JavaScript'>
                            window.alert('File Upload Failed / Invalid File, Returning to Create Form');
                            window.location.href='create-form.php';
                            </script>");
                    $uploadOk = 0;
                }
                
                if ($uploadOk == 0)
                {
                    echo "Sorry, your file was not uploaded.";
            
                } 
                
                else 
                {
                    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"][$i], $target_file)) 
                    {

                    } 
                    else
                    {
                        echo $_FILES['fileToUpload']['error'][$i];
                        echo "Sorry, there was an error uploading your file.";
                    }
                }
                $iteminsert = "INSERT INTO uploads (item_name,itemId) VALUES ('$newfilename',$itemNo)";
                $itemeun = $connection->query($iteminsert);
            }
        }
        else
        {
            $itemNo = $ir['id'];
            $iteminsert = "INSERT INTO uploads (item_name,itemId) VALUES ('$newfilename',$itemNo)";
            $itemeun = $connection->query($iteminsert);
        }
        
        $mailquery = "SELECT * FROM employeeinfos WHERE cidNum = $deptmanId";
        $mailres = $userconnect->query($mailquery);
        $s = get_data_array($mailres);
        
        $combimail = $d['email'];

        sendmail_utf8($s['email'], "SPARKS OF BRILLIANCE", "apps.donotreply@onsemi.com", "E-SOB : MANAGER PENDING", "<p><b>TICKET #:". $ir['ticket_no'] ."</b> You have a current Sparks of Brilliance Form to be reviewed. Please see your pending forms</p> <p>Please use Google Chrome or Mozilla FireFox <a href='http://phsm01ws014.ad.onsemi.com/sob/view-form.php?id=". $ir['id'] ."'>VIEW FORM</a></p> <br> <br> <br/><br/><b style='color:red'>Please do not reply.</b> <br/><br/>Applications Engineering <br/> SPARKS OF BRILLIANCE", $combimail);
     
	    echo ("<script LANGUAGE='JavaScript'>
        window.alert('Record Succesfully Created, An email notification has been sent to the Approver');
        window.location.href='index.php';
        </script>");
   
	}
}

//Show all Data
function showAllData()
{
    global $connection;
    global $userconnect;
    //Get all data function 
    $showall_query ="SELECT * from employeeinfos WHERE isDeleted=0";
    $fullresult = $userconnect-> query($showall_query);
    $all = get_assocArray($fullresult);
    $empsLen = sizeof($all);
}


//get only data in ffid
function getFFID()
{
    global $connection;
    global $userconnect;
    $sql ="SELECT * from employeeinfos WHERE isDeleted=0 AND ffId ='". $_SESSION['username'] ."'";
    $result = $userconnect-> query($sql);
    $d = get_data_array($result);
}

//UPDATE FORM
function update_form($dr_id)
{
    global $connection;
    global $userconnect;

    if (isset($_POST['update_form']))
    {
        $sql ="SELECT * from employeeinfos WHERE isDeleted=0 AND ffId ='". $_SESSION['username'] ."'";
        $result = $userconnect-> query($sql);
        $d = get_data_array($result); 
        
        $sql = "SELECT * FROM associate_info WHERE sobFormID= $dr_id";
        $result = $connection->query($sql);
        $no_of_associates = get_assocArray($result);

        $sql = "SELECT * FROM formfill_infov2 WHERE id =". $dr_id;
        $result = $connection->query($sql);
        $checkassoc = get_data_array($result);

        $assocLen = sizeof($no_of_associates);
        $sit_task = sanitizeInput($_POST['sit_task']);
        $act_done = sanitizeInput($_POST['act_done']);
        $rem_res = sanitizeInput($_POST['rem_res']);

        //CHECKING
        for ($check = 0 ; $check < $checkassoc['no_of_assoc'] ; $check++){

            $tempUserCIDnum = "userCIDnum_" . $check;
            $userCIDnum = sanitizeInput($_POST[$tempUserCIDnum]);

            if ($userCIDnum == $_SESSION['cidNum'])
            {
                echo ("<script LANGUAGE='JavaScript'>
                        window.alert('An invalid input has been detected. Reverting Changes');
                        window.location.href='view-form.php?id=". $dr_id ."';
                        </script>");
                stop();
            }

        }
        
        $update_SQL = "UPDATE formfill_infov2 SET  sit_task = '$sit_task', act_done = '$act_done',rem_res = '$rem_res' WHERE id= $dr_id" ;
        $update_result =mysqli_query($connection, $update_SQL);
        if(!$update_result)
        {
            die("QUERY FAILED" . mysqli_error($connection));
        }

        for ($assoc = 0 ; $assoc < $assocLen ; $assoc++)
        {

            $tempAssociateName = "associate_name_". $assoc;
            $tempUserCIDnum = "userCIDnum_" . $assoc;
            $tempRec_amount = "rec_amount_" . $assoc;
            $tempOthers_specify = "others_specify_" . $assoc;
            $tempKeyStrat = "key_strat_" . $assoc;

            $associateName = sanitizeInput(strtoupper($_POST[$tempAssociateName]));
            $userCIDnum = sanitizeInput($_POST[$tempUserCIDnum]);
            $rec_amount = sanitizeInput($_POST[$tempRec_amount]);

            if ($_POST[$tempKeyStrat] == "Others")
            {
                $others_specify = sanitizeInput($_POST[$tempOthers_specify]);
                $key_strat = "";
            }

            else 
            {
                $key_strat = sanitizeInput($_POST[$tempKeyStrat]);
                $others_specify = "";
            }
            $new_key_strat = $key_strat. "" .$others_specify; 
            $sql = "UPDATE associate_info SET associate_name = '$associateName' , user_CID_num = '$userCIDnum', rec_amount = '$rec_amount', key_strat = '$new_key_strat' WHERE id =". $no_of_associates[$assoc]['id'];
            $result = $connection->query($sql);
        }
	    echo ("<script LANGUAGE='JavaScript'>
        window.alert('Record Succesfully Updated');
        </script>");
   
     }
}




//Manager Updating
function manager_update($dr_id)
{
    
    if (isset($_POST['approved_man']))
    {
        
        global $connection;
        global $userconnect;
        
        $manager_remarks = sanitizeInput($_POST['manager-remarks']);
        $sql ="SELECT * from employeeinfos WHERE isDeleted=0 AND ffId ='". $_SESSION['username'] ."'";
        $result = $userconnect-> query($sql);
        $d = get_data_array($result);
        
        $app_by = $d['firstName'].' '.$d['lastName'];
        $man_stat = "2";

        $sql = "SELECT * FROM associate_info WHERE sobFormID= $dr_id";
        $result = $connection->query($sql);
        $no_of_associates = get_assocArray($result);
        $assocLen = sizeof($no_of_associates);

        for ($assoc = 0 ; $assoc < $assocLen ; $assoc++)
        {
            $tempRec_amount = "rec_amount_" . $assoc;
           
            $rec_amount = sanitizeInput($_POST[$tempRec_amount]);

            $sql = "UPDATE associate_info SET  rec_amount = '$rec_amount' WHERE id =". $no_of_associates[$assoc]['id'];
            $result = $connection->query($sql);
        }

        $jobName = $_SESSION['jobName'];
        $cidNum = $_SESSION['cidNum'];
        
        $hr_updateSQL = "UPDATE formfill_infov2 SET app_by = '$app_by', app_date = CURRENT_TIMESTAMP,  status = '$man_stat', app_rem = '$manager_remarks', app_CID = '$cidNum' WHERE id= $dr_id" ;
        $update_result =mysqli_query($connection, $hr_updateSQL);

        if(!$update_result)
        {
              die("QUERY FAILED" . mysqli_error($connection));
        }

        $sender_notif = "SELECT * FROM formfill_infov2 WHERE id= $dr_id";
        $sender_result = mysqli_query($connection, $sender_notif);
        $n = get_data_array($sender_result);
        
        $sender_ffid = $n['rec_ffId'];
        $email_sender_query = "SELECT * FROM employeeinfos WHERE ffId = '$sender_ffid'";
        $email_sender_result = mysqli_query($userconnect, $email_sender_query);
        $o = get_data_array($email_sender_result);
        
		$combimail = $o['email'];
        
        sendmail_utf8("Cherry.Candelario@onsemi.com", "SPARKS OF BRILLIANCE", "apps.donotreply@onsemi.com", "E-SOB : HR PENDING", "<p><b>TICKET #:". $n['ticket_no'] ."</b> You have a current Sparks of Brilliance Form to be reviewed. Please see your pending forms</p> <p>Please use Google Chrome or Mozilla FireFox <a href='http://phsm01ws014.ad.onsemi.com/sob/view-form.php?id=". $n['id'] ."'>VIEW FORM</a></p> <br> <br> <br/><br/><b style='color:red'>Please do not reply.</b> <br/><br/>Applications Engineering <br/> SPARKS OF BRILLIANCE", $combimail);
        echo ("<script LANGUAGE='JavaScript'>
        window.alert('Record Succesfully Approved, Status forwarded to HR');
        </script>");        
    }
		
    else if (isset($_POST['declined_man']))
    {
        global $connection;
        global $userconnect;
        
        $manager_remarks = sanitizeInput($_POST['manager-remarks']);
        $sql ="SELECT * from employeeinfos WHERE isDeleted=0 AND ffId ='". $_SESSION['username'] ."'";
        $result = $userconnect-> query($sql);
        $d = get_data_array($result);
        
        $man_stat = "0";
        
        $hr_updateSQL = "UPDATE formfill_infov2 SET status = '$man_stat', app_rem = '$manager_remarks' WHERE id= $dr_id" ;
        $update_result =mysqli_query($connection, $hr_updateSQL);
        if(!$update_result)
        {
            die("QUERY FAILED" . mysqli_error($connection));
        }
        
        $sender_notif = "SELECT * FROM formfill_infov2 WHERE id= $dr_id";
        $sender_result = mysqli_query($connection, $sender_notif);
        $n = get_data_array($sender_result);
        
        $sender_ffid = $n['rec_ffId'];
        $email_sender_query = "SELECT * FROM employeeinfos WHERE ffId = '$sender_ffid'";
        $email_sender_result = mysqli_query($userconnect, $email_sender_query);
        $o = get_data_array($email_sender_result);
		
		$combimail = $o['email'];
        
        sendmail_utf8($o['email'], "SPARKS OF BRILLIANCE", "apps.donotreply@onsemi.com", "E-SOB : DECLINED", "<p><b>TICKET #:". $n['ticket_no'] ."</b> Your current Sparks of Brilliance Form has been declined. Please see your form</p> <p>Please use Google Chrome or Mozilla FireFox <a href='http://phsm01ws014.ad.onsemi.com/sob/view-form.php?id=". $n['id'] ."'>VIEW FORM</a></p><br> <br> <br/><br/><b style='color:red'>Please do not reply.</b> <br/><br/>Applications Engineering <br/> SPARKS OF BRILLIANCE", $combimail);
        
        echo ("<script LANGUAGE='JavaScript'>
        window.alert('Record Successfully Declined');
        </script>");  
    }
    
}


function rec_update_hr($dr_id)
{//Update HR Pending form Recommender
    
    if (isset($_POST['rec_submit_hr']))
    {
        global $connection;
        global $userconnect;
       
        $sql = "UPDATE formfill_infov2 SET status = 2 WHERE id =". $dr_id;
        $result = $connection->query($sql);
            
        $itemquery = "SELECT * FROM formfill_infov2 WHERE id=". $dr_id;
        $itemresult = $connection-> query($itemquery);
        $ir = get_data_array($itemresult);
                        
        $total = count($_FILES["fileToUpload"]["name"]);
        
        if ($_FILES["fileToUpload"]["name"][0] != "")
        {
            for ($i=0; $i<$total; $i++)
            {
                $itemName = ($_FILES["fileToUpload"]["name"][$i]);
                $itemNo = $ir['id'];
                $newfilename = date('dmYHis')."_".str_replace("", "", basename($_FILES["fileToUpload"]["name"][$i]));
                $target_dir = "uploads/";

                $target_file = $target_dir . $newfilename;
                
                $uploadOk = 1;
                $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

                if ($_FILES["fileToUpload"]["size"][$i] > 50000000) 
                {
                    echo ("<script LANGUAGE='JavaScript'>
                            window.alert('File is too large, Returning to Create Form');
                            window.location.href='view-form.php?id=". $dr_id ."';
                            </script>");
                    $uploadOk = 0;
                }

                $validExtensions = array('jpg' , 'png' , 'jpeg' , 'gif' , 'xlsx', 'docx', 'pdf', 'pptx', "xls", "xlt", "xlm", "xlsm", "xltx",
                                            "xltm", "xlsb", "xla", "xlam", "xll", "xlw",
                                            "ppt", "pot", "pps", "pptx", "pptm", "potx",
                                            "potm", "ppam", "ppsx", "ppsm", "sldx", "sldm",
                                            "doc", "docm", "dot", "dotm", "dotx",
                                            "msg", "pdf");

                if(!in_array($imageFileType."", $validExtensions))
                {

                    echo ("<script LANGUAGE='JavaScript'>
                            window.alert('File Upload Failed / Invalid File, Returning to Create Form');
                            window.location.href='view-form.php?id=". $dr_id ."';
                            </script>");
                    $uploadOk = 0;
                }

                if ($uploadOk == 0) 
                {
                    echo "Sorry, your file was not uploaded.";
                } 

                else 
                {
                    
                    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"][$i], $target_file)) 
                    {

                    } 
                    else 
                    {
                        echo $_FILES['fileToUpload']['error'][$i];
                        echo "Sorry, there was an error uploading your file.";

                    }
                }
                $iteminsert = "INSERT INTO uploads (item_name,itemId) VALUES ('$newfilename','$itemNo')";
                $itemeun = $connection->query($iteminsert);
            }
        }

        $sender_notif = "SELECT * FROM formfill_infov2 WHERE id= $dr_id";
        $sender_result = mysqli_query($connection, $sender_notif);
        $n = get_data_array($sender_result);
        
        $sender_ffid = $n['rec_ffId'];
        $email_sender_query = "SELECT * FROM employeeinfos WHERE ffId = '$sender_ffid'";
        $email_sender_result = mysqli_query($userconnect, $email_sender_query);
        $o = get_data_array($email_sender_result);
		
		$combimail = $o['email'];
        
        sendmail_utf8("Cherry.Candelario@onsemi.com", "SPARKS OF BRILLIANCE", "apps.donotreply@onsemi.com", "E-SOB : HR PENDING", "<p><b>TICKET #:". $n['ticket_no'] ."</b> You have a current Sparks of Brilliance Form to be reviewed. Please see your pending forms</p> <p>Please use Google Chrome or Mozilla FireFox <a href='http://phsm01ws014.ad.onsemi.com/sob/view-form.php?id=". $ir['id'] ."'>VIEW FORM</a></p> <br> <br> <br/><br/><b style='color:red'>Please do not reply.</b> <br/><br/>Applications Engineering <br/> SPARKS OF BRILLIANCE", $combimail);
        
        echo ("<script LANGUAGE='JavaScript'>
        window.alert('Record Succesfully Updated');
        </script>");  
    }
}

function rec_update_man($dr_id)
{//Update Manager from Recommender
    
    if (isset($_POST['rec_submit_man']))
    {
        
        global $connection;
        global $userconnect;
       
        $sql = "UPDATE formfill_infov2 SET status = 1 WHERE id =". $dr_id;
        $result = $connection->query($sql);
            
        //Upload Fucntion            
        $itemquery = "SELECT * FROM formfill_infov2 WHERE id=". $dr_id;
        $itemresult = $connection-> query($itemquery);
        $ir = get_data_array($itemresult);
                        
        $total = count($_FILES["fileToUpload"]["name"]);
        
        
        if ($_FILES["fileToUpload"]["name"][0] != "")
        {
            for ($i=0; $i<$total; $i++)
            {
                
                $itemName = ($_FILES["fileToUpload"]["name"][$i]);
                $itemNo = $ir['id'];
                
                
                $newfilename = date('dmYHis')."_".str_replace("", "", basename($_FILES["fileToUpload"]["name"][$i]));

                            
                $target_dir = "uploads/";
                

                $target_file = $target_dir . $newfilename;
                
                $uploadOk = 1;
                $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

                if ($_FILES["fileToUpload"]["size"][$i] > 50000000)
                {
                    echo ("<script LANGUAGE='JavaScript'>
                            window.alert('File is too large, Returning to Create Form');
                            window.location.href='view-form.php?id=". $dr_id ."';
                            </script>");
                    $uploadOk = 0;
                }

                $validExtensions = array('jpg' , 'png' , 'jpeg' , 'gif' , 'xlsx', 'docx', 'pdf', 'pptx', "xls", "xlt", "xlm", "xlsm", "xltx",
                                            "xltm", "xlsb", "xla", "xlam", "xll", "xlw",
                                            "ppt", "pot", "pps", "pptx", "pptm", "potx",
                                            "potm", "ppam", "ppsx", "ppsm", "sldx", "sldm",
                                            "doc", "docm", "dot", "dotm", "dotx",
                                            "msg", "pdf");

                // Allow certain file formats
                if(!in_array($imageFileType."", $validExtensions)) 
                {
                    echo ("<script LANGUAGE='JavaScript'>
                            window.alert('File Upload Failed / Invalid File, Returning to Create Form');
                            window.location.href='view-form.php?id=". $dr_id ."';
                            </script>");
                    $uploadOk = 0;
                }
                // Check if $uploadOk is set to 0 by an error
                if ($uploadOk == 0) 
                {
                    echo "Sorry, your file was not uploaded.";
                // if everything is ok, try to upload file
                } 
                
                else 
                {
                    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"][$i], $target_file)) 
                    {
            
                    } 
                    
                    else 
                    {
                        echo $_FILES['fileToUpload']['error'][$i];
                        echo "Sorry, there was an error uploading your file.";
                    }
                }

                $iteminsert = "INSERT INTO uploads (item_name,itemId) VALUES ('$newfilename','$itemNo')";
                $itemeun = $connection->query($iteminsert);
            }
        }

        $sender_notif = "SELECT * FROM formfill_infov2 WHERE id= $dr_id";
        $sender_result = mysqli_query($connection, $sender_notif);
        $n = get_data_array($sender_result);
        
        $sender_ffid = $n['rec_ffId'];
        $email_sender_query = "SELECT * FROM employeeinfos WHERE ffId = '$sender_ffid'";
        $email_sender_result = mysqli_query($userconnect, $email_sender_query);
        $o = get_data_array($email_sender_result);

        $mailquery = "SELECT * FROM employeeinfos WHERE cidNum =". $n['deptmanId'];
        $mailres = $userconnect->query($mailquery);
        $s = get_data_array($mailres);
		
	    $combimail = $o['email'];
        
        sendmail_utf8($s['email'], "SPARKS OF BRILLIANCE", "apps.donotreply@onsemi.com", "E-SOB : MANAGER PENDING", "<p><b>TICKET #:". $n['ticket_no'] ."</b> You have a current Sparks of Brilliance Form to be reviewed. Please see your pending forms</p> <p>Please use Google Chrome or Mozilla FireFox <a href='http://phsm01ws014.ad.onsemi.com/sob/view-form.php?id=". $ir['id'] ."'>VIEW FORM</a></p> <br> <br> <br/><br/><b style='color:red'>Please do not reply.</b> <br/><br/>Applications Engineering <br/> SPARKS OF BRILLIANCE", $combimail);
        
        echo ("<script LANGUAGE='JavaScript'>
        window.alert('Record Succesfully Updated');
        </script>");  
    }
}

//HR Update to pend Rec Form
function hr_pending_update($dr_id)
{
    if (isset($_POST['pending_hr']))
    {
        global $connection;
        global $userconnect;
        
        $pen_remarks = sanitizeInput($_POST['hr_remarks']);
        $sql ="SELECT * from employeeinfos WHERE isDeleted=0 AND ffId ='". $_SESSION['username'] ."'";
        $result = $userconnect-> query($sql);
        $d = get_data_array($result);
        
        $valid_by = $d['firstName'].' '.$d['lastName'];
        $pen_stat = "5";
        $sql = "SELECT * FROM associate_info WHERE sobFormID= $dr_id";
        $result = $connection->query($sql);
        $no_of_associates = get_assocArray($result);
        $assocLen = sizeof($no_of_associates);

        for ($assoc = 0 ; $assoc < $assocLen ; $assoc++)
        {

            $tempRec_amount = "rec_amount_" . $assoc;
           
            $rec_amount = sanitizeInput($_POST[$tempRec_amount]);

            $sql = "UPDATE associate_info SET  rec_amount = '$rec_amount' WHERE id =". $no_of_associates[$assoc]['id'];
            $result = $connection->query($sql);
        }

        $jobName = $_SESSION['jobName'];
        $cidNum = $_SESSION['cidNum'];
        
        $hr_updateSQL = "UPDATE formfill_infov2 SET status = '$pen_stat', hr_pen_rem = '$pen_remarks' WHERE id= $dr_id" ;
        $update_result =mysqli_query($connection, $hr_updateSQL);
        if(!$update_result)
        {
            die("QUERY FAILED" . mysqli_error($connection));
        }
        
        $sender_notif = "SELECT * FROM formfill_infov2 WHERE id= $dr_id";
        $sender_result = mysqli_query($connection, $sender_notif);
        $n = get_data_array($sender_result);
        
        $sender_ffid = $n['rec_ffId'];
        
        $email_sender_query = "SELECT * FROM employeeinfos WHERE ffId = '$sender_ffid'";
        $email_sender_result = mysqli_query($userconnect, $email_sender_query);
        $o = get_data_array($email_sender_result);
        
        sendmail_utf8($o['email'], "SPARKS OF BRILLIANCE", "apps.donotreply@onsemi.com", "E-SOB : ACTION REQUIRED", "<p><b>TICKET #:". $n['ticket_no'] ."</b> Your form process has been halted by the HR. Please see your form for your New Action.</p> <p>Please use Google Chrome or Mozilla FireFox <a href='http://phsm01ws014.ad.onsemi.com/sob/view-form.php?id=". $n['id'] ."'>VIEW FORM</a></p> <br> <br> <br/><br/><b style='color:red'>Please do not reply.</b> <br/><br/>Applications Engineering <br/> SPARKS OF BRILLIANCE", "");
        
        echo ("<script LANGUAGE='JavaScript'>
        window.alert('Record Succesfully Halted');
        </script>");  
        }
}
//Manager Update to pend Rec Form
function man_pending_update($dr_id)
{
    
    if (isset($_POST['pending_man']))
    {
        global $connection;
        global $userconnect;
        
        $pen_remarks = sanitizeInput($_POST['manager-remarks']);
        $sql ="SELECT * from employeeinfos WHERE isDeleted=0 AND ffId ='". $_SESSION['username'] ."'";
        $result = $userconnect-> query($sql);
        $d = get_data_array($result);
        
        $valid_by = $d['firstName'].' '.$d['lastName'];
        $pen_stat = "6";
        $sql = "SELECT * FROM associate_info WHERE sobFormID= $dr_id";
        $result = $connection->query($sql);
        $no_of_associates = get_assocArray($result);
        $assocLen = sizeof($no_of_associates);

        for ($assoc = 0 ; $assoc < $assocLen ; $assoc++)
        {
            $tempRec_amount = "rec_amount_" . $assoc;
           
            $rec_amount = sanitizeInput($_POST[$tempRec_amount]);

            $sql = "UPDATE associate_info SET  rec_amount = '$rec_amount' WHERE id =". $no_of_associates[$assoc]['id'];
            $result = $connection->query($sql);
        }
        $jobName = $_SESSION['jobName'];
        $cidNum = $_SESSION['cidNum'];
        
        $hr_updateSQL = "UPDATE formfill_infov2 SET status = '$pen_stat', man_pen_rem = '$pen_remarks' WHERE id= $dr_id" ;
        $update_result =mysqli_query($connection, $hr_updateSQL);

        if(!$update_result)
        {
            die("QUERY FAILED" . mysqli_error($connection));
        }
        
        $sender_notif = "SELECT * FROM formfill_infov2 WHERE id= $dr_id";
        $sender_result = mysqli_query($connection, $sender_notif);
        $n = get_data_array($sender_result);
        
        $sender_ffid = $n['rec_ffId'];
        
        $email_sender_query = "SELECT * FROM employeeinfos WHERE ffId = '$sender_ffid'";
        $email_sender_result = mysqli_query($userconnect, $email_sender_query);
        $o = get_data_array($email_sender_result);
        
        sendmail_utf8($o['email'], "SPARKS OF BRILLIANCE", "apps.donotreply@onsemi.com", "E-SOB : ACTION REQUIRED", "<p><b>TICKET #:". $n['ticket_no'] ."</b> Your form process has been halted by the Approver. Please see your form for your New Action.</p> <p>Please use Google Chrome or Mozilla FireFox <a href='http://phsm01ws014.ad.onsemi.com/sob/view-form.php?id=". $n['id'] ."'>VIEW FORM</a></p> <br> <br> <br/><br/><b style='color:red'>Please do not reply.</b> <br/><br/>Applications Engineering <br/> SPARKS OF BRILLIANCE", "");
        
        echo ("<script LANGUAGE='JavaScript'>
        window.alert('Record Succesfully Halted');
        </script>");  
        }
}



//Human Resources Updating
function hr_update($dr_id)
{
    if (isset($_POST['approved_hr']))
    {
        global $connection;
        global $userconnect;
        
        $hr_remarks = sanitizeInput($_POST['hr_remarks']);
        $sql ="SELECT * from employeeinfos WHERE isDeleted=0 AND ffId ='". $_SESSION['username'] ."'";
        $result = $userconnect-> query($sql);
        $d = get_data_array($result);
        
        $valid_by = $d['firstName'].' '.$d['lastName'];
        $man_stat = "3";

        $sql = "SELECT * FROM associate_info WHERE sobFormID= $dr_id";
        $result = $connection->query($sql);
        $no_of_associates = get_assocArray($result);
        $assocLen = sizeof($no_of_associates);

        for ($assoc = 0 ; $assoc < $assocLen ; $assoc++)
        {
            $tempRec_amount = "rec_amount_" . $assoc;
           
            $rec_amount = sanitizeInput($_POST[$tempRec_amount]);

            $sql = "UPDATE associate_info SET  rec_amount = '$rec_amount' WHERE id =". $no_of_associates[$assoc]['id'];
            $result = $connection->query($sql);
        }

        $jobName = $_SESSION['jobName'];
        $cidNum = $_SESSION['cidNum'];
        
        $hr_updateSQL = "UPDATE formfill_infov2 SET valid_by = '$valid_by', valid_date = CURRENT_TIMESTAMP,  status = '$man_stat', valid_rem = '$hr_remarks', val_CID = '$cidNum' WHERE id= $dr_id" ;
        $update_result =mysqli_query($connection, $hr_updateSQL);

        if(!$update_result)
        {
            die("QUERY FAILED" . mysqli_error($connection));
        }
        
        $sender_notif = "SELECT * FROM formfill_infov2 WHERE id= $dr_id";
        $sender_result = mysqli_query($connection, $sender_notif);
        $n = get_data_array($sender_result);
        
        $sender_ffid = $n['rec_ffId'];
        
        $email_sender_query = "SELECT * FROM employeeinfos WHERE ffId = '$sender_ffid'";
        $email_sender_result = mysqli_query($userconnect, $email_sender_query);
        $o = get_data_array($email_sender_result);
		
		$combimail = $o['email'];
        
        sendmail_utf8("Joylhen.Balboa@onsemi.com", "SPARKS OF BRILLIANCE", "apps.donotreply@onsemi.com", "E-SOB : HR APPROVED/FINANCE PENDING", "<p><b>TICKET #:". $n['ticket_no'] ."</b> An associate has been added to the SOB finance list.</p> <p>Please use Google Chrome or Mozilla FireFox <a href='http://phsm01ws014.ad.onsemi.com/sob/view-form.php?id=". $n['id'] ."'>VIEW FORM</a></p> <br> <br> <br/><br/><b style='color:red'>Please do not reply.</b> <br/><br/>Applications Engineering <br/> SPARKS OF BRILLIANCE",$combimail);
        
        echo ("<script LANGUAGE='JavaScript'>
        window.alert('Record Succesfully Approved, Status forwarded to Finance');
        </script>");
    }

    else if (isset($_POST['declined_hr']))
    {
        global $connection;
        global $userconnect;
        
        $hr_remarks = sanitizeInput($_POST['hr_remarks']);
        $sql ="SELECT * from employeeinfos WHERE isDeleted=0 AND ffId ='". $_SESSION['username'] ."'";
        $result = $userconnect-> query($sql);
        $d = get_data_array($result);
        
        $hr_stat = "0";
    
        $hr_updateSQL = "UPDATE formfill_infov2 SET status = '$hr_stat', valid_rem = '$hr_remarks' WHERE id= $dr_id" ;
        $update_result =mysqli_query($connection, $hr_updateSQL);
        
        if(!$update_result)
        {
            die("QUERY FAILED" . mysqli_error($connection));
        }
        $sender_notif = "SELECT * FROM formfill_infov2 WHERE id= $dr_id";
        $sender_result = mysqli_query($connection, $sender_notif);
        $n = get_data_array($sender_result);
        
        $sender_ffid = $n['rec_ffId'];
        $email_sender_query = "SELECT * FROM employeeinfos WHERE ffId = '$sender_ffid'";
        $email_sender_result = mysqli_query($userconnect, $email_sender_query);
        $o = get_data_array($email_sender_result);
		
		$combimail = $o['email'];
        
       sendmail_utf8($o['email'], "SPARKS OF BRILLIANCE", "apps.donotreply@onsemi.com", "E-SOB : DECLINED", "<p><b>TICKET #:". $n['ticket_no'] ."</b> Your current Sparks of Brilliance Form has been declined. Please see your form</p> <p>Please use Google Chrome or Mozilla FireFox <a href='http://phsm01ws014.ad.onsemi.com/sob/view-form.php?id=". $n['id'] ."'>VIEW FORM</a></p><br> <br> <br/><br/><b style='color:red'>Please do not reply.</b> <br/><br/>Applications Engineering <br/> SPARKS OF BRILLIANCE", $combimail);
       
        echo ("<script LANGUAGE='JavaScript'>
        window.alert('Record Succesfully Declined');
        </script>");
    }
}

//Finace Updating
function finance_update()
{

    global $connection;
    global $userconnect;

    $user = "SELECT * FROM employeeinfos WHERE ffId='". $_SESSION['username'] ."'"; 
    $userres = $userconnect->query($user);
    $u = get_data_array($userres);

    if (isset($_POST['submit']))
    {
        $showallrec_query ="SELECT * from formfill_infov2 WHERE status = '3' ORDER BY app_date DESC";
        $result = $connection->query($showallrec_query);
        $f = get_assocArray($result);
        
        $sql = "SELECT * FROM formfill_infov2 ORDER BY rec_date DESC LIMIT 1";
        $result = $connection-> query($sql);
        $user = get_data_array($result);
        $batch_no = $user['batch_no'] + 1;
        
        $total = sizeof($f);
        
        for ($i = 0 ; $i < $total; $i++)
        {

            $emailquery = "SELECT * FROM employeeinfos WHERE cidNum ='". $f[$i]['rec_CID']. "'";
            $resultmail = $userconnect->query($emailquery);
            $m = get_data_array($resultmail);

                        
            $finalstatus = "UPDATE formfill_infov2 SET status = 4, fin_by ='". $u['lastName'] . " " . $u['firstName'] ."', fin_date = CURRENT_TIMESTAMP, batch_no = '$batch_no' WHERE id =". $f[$i]['id'] ."";
            $finalstat = $connection->query($finalstatus);

            sendmail_utf8($m['email'], "SPARKS OF BRILLIANCE",
                "apps.donotreply@onsemi.com",
                "E-SOB : FINANCE CLOSED",
                "<p><b>TICKET #:". $f[$i]['ticket_no'] ."</b> Your current Sparks of Brilliance Form has been finalized by the HR and finance and has completed transaction. Please see status on your form form.</p> <p>Please use Google Chrome or Mozilla FireFox <a href='http://phsm01ws014.ad.onsemi.com/sob/view-form.php?id=". $f[$i]['id'] ."'>VIEW FORM</a></p><br> <br> <br/><br/><b style='color:red'>Please do not reply.</b> <br/><br/>Applications Engineering <br/> SPARKS OF BRILLIANCE");
        }    

        echo ("<script LANGUAGE='JavaScript'>
        window.alert('Record Succesfully Updated, Emails have been sent to the recipient/s');
        window.location.href='finance-list.php';
        </script>");

    }

}


//User Check;
function check_user()
{
    if (!isset($_SESSION['username']))
    {
        header ("Location: logout_page.php");
        exit();
    }
}

//Clear Data for HR when reporting is Done
function clear_data()
{
    global $connection;
    if (isset($_POST['submit']))
    {
        $sql = "UPDATE formfill_infov2 SET isCleared = 1 WHERE (status = 4 AND isCleared = 0) OR (status = 3 AND isCleared = 0)";
        $result = $connection->query($sql);
    }
}


function form_del($dr_id)
{
    global $connection;

   if (isset($_POST['deleteform']))
   {
       $isDeleted = 1;
       
       $sql = "UPDATE formfill_infov2 SET isDeleted = '$isDeleted' WHERE id=". $dr_id;
       $result = $connection->query($sql);
       
       echo ("<script LANGUAGE='JavaScript'>
            window.alert('Record Succesfully Deleted');
            window.location.href='index.php';
            </script>");
   }
}

function add_master_list()
{
    global $connection;

    if (isset($_POST['create_master_user']))
    {

        $lastName = sanitizeInput($_POST['lastName']);
        $firstName = sanitizeInput($_POST['firstName']);
        $cid = sanitizeInput($_POST['cid']);
        $email = sanitizeInput($_POST['email']);
        $deptcode = sanitizeInput($_POST['deptcode']);
        $sob_pos = sanitizeInput($_POST['sob_pos']);

        print_r($_POST);

        $sql = "INSERT INTO masterlist (userCID,lastName,firstName,email,deptCode,department_head)VALUES ('$cid','$lastName','$firstName','$email','$deptcode','$sob_pos')";
        $result = $connection->query($sql);
        
        echo ("<script LANGUAGE='JavaScript'>
        window.alert('Added a new User in Master List');
        window.location.href='master-list.php';
        </script>");
    }
}

function update_master_list($id)
{
    global $connection;

    if (isset($_POST['update_masterlist']))
    {

        $lastName = sanitizeInput($_POST['lastName']);
        $firstName = sanitizeInput($_POST['firstName']);
        $cid = sanitizeInput($_POST['cid']);
        $email = sanitizeInput($_POST['email']);
        $deptcode = sanitizeInput($_POST['deptcode']);
        $sob_pos = sanitizeInput($_POST['sob_pos']);

        $sql = "UPDATE masterlist SET userCID = '$cid', lastName = '$lastName', firstName = '$firstName', email = '$email', deptCode = '$deptcode', department_head = '$sob_pos' WHERE id =" .$id;
        $result = $connection->query($sql);

        echo ("<script LANGUAGE='JavaScript'>
        window.alert('User Information Updated');
        window.location.href='master-list.php';
        </script>");
    }

    else if (isset($_POST['delete_masterlist']))
    {
        $sql = "DELETE FROM masterlist WHERE id =" .$id;
        $result = $connection->query($sql);

        echo ("<script LANGUAGE='JavaScript'>
        window.alert('User Deleted');
        window.location.href='master-list.php';
        </script>");
    }

}

function add_userdata_list()
{
    global $userconnect;

    if (isset($_POST['create_userdata']))
    {
        $lastName = sanitizeInput($_POST['lastName']);
        $firstName = sanitizeInput($_POST['firstName']);
        $cid = sanitizeInput($_POST['cid']);
        $email = sanitizeInput($_POST['email']);
        $deptcode = sanitizeInput($_POST['deptcode']);
        $sob_pos = sanitizeInput($_POST['sob_pos']);
        $ffID = sanitizeInput($_POST['ffID']);
        $jobDescription = sanitizeInput($_POST['jobDescription']);
        $immediateSupervisor = sanitizeInput($_POST['immediateSupervisor']);
        $supervisorCID = sanitizeInput($_POST['supervisorCID']);
        
        // print_r($_POST);

        $sql = "INSERT INTO employeeinfos (firstName,lastName,email,ffId,jobName,immediateSuperior,isSuperior,deptCode,cidNum,supvCID,isDeleted) VALUES ('$firstName','$lastName','$email','$ffID','$jobDescription','$immediateSupervisor','$sob_pos','$deptcode','$cid','$supervisorCID','0')";
        $result = $userconnect->query($sql);
        
        echo ("<script LANGUAGE='JavaScript'>
        window.alert('Added a new User in Master List');
        window.location.href='userbase-list.php';
        </script>");
    }
}

function update_userdata_list($id)
{
    global $userconnect;

    if (isset($_POST['update_userbase']))
    {
        $lastName = sanitizeInput($_POST['lastName']);
        $firstName = sanitizeInput($_POST['firstName']);
        $cid = sanitizeInput($_POST['cid']);
        $email = sanitizeInput($_POST['email']);
        $deptcode = sanitizeInput($_POST['deptcode']);
        $sob_pos = sanitizeInput($_POST['sob_pos']);
        $ffID = sanitizeInput($_POST['ffID']);
        $jobDescription = sanitizeInput($_POST['jobDescription']);
        $immediateSupervisor = sanitizeInput($_POST['immediateSupervisor']);
        $supervisorCID = sanitizeInput($_POST['supervisorCID']);

        echo $sql = "UPDATE employeeinfos SET firstName = '$firstName', lastName = '$lastName', email = '$email', ffId = '$ffID', jobName = '$jobDescription', immediateSuperior = '$immediateSupervisor', isSuperior = '$sob_pos', deptCode = '$deptcode', cidNum = '$cid', supvCID = '$supervisorCID' WHERE id =" .$id;
        $result = $userconnect->query($sql);

        echo ("<script LANGUAGE='JavaScript'>
        window.alert('User Information Updated');
        window.location.href='userbase-list.php';
        </script>");
    }

    else if (isset($_POST['delete_userbase']))
    {
        $sql = "DELETE FROM employeeinfos WHERE id =" .$id;
        $result = $userconnect->query($sql);

        echo ("<script LANGUAGE='JavaScript'>
        window.alert('User Deleted');
        window.location.href='userbase-list.php';
        </script>");
    }
}

?>










