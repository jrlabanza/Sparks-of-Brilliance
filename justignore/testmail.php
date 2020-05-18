<?php


//Mail FUnction
function sendmail_utf8($to, $from_user, $from_email, $subject = '(No subject)', $message = '', $cc=""){
            $from_user = "=?UTF-8?B?".base64_encode($from_user)."?=";
            $subject = "=?UTF-8?B?".base64_encode($subject)."?=";

            $headers = "From: $from_user <$from_email>\r\n".
                       "MIME-Version: 1.0" . "\r\n" .
                       "Content-type: text/html; charset=UTF-8" . "\r\n";

            if ($cc != ""){
                $headers .= "CC: ". $cc ." \r\n";
            }

            return mail($to, $subject, $message, $headers);
        }
//Get data row in SQL
function get_data_array($result){

$data = array();

if (is_object($result) && !empty($result->num_rows)) {
    while ($row = $result->fetch_assoc()) {
        foreach($row as $col => $value){
            $data[$col] = $value;
        }
    }
    $result->free();
}

return $data;

}
//Get all continuous rows
function get_assocArray($result){ 

$data = array(); 

if (is_object($result) && !empty($result->num_rows)) { 

    while ($row = $result->fetch_assoc()) { 
        $tempColumns = array(); 
        foreach($row as $col => $value){ 
            $tempColumns[$col] = $value; 
        } 
        array_push($data, $tempColumns); 
    } 
    $result->free(); 
}
return $data; 
}
$combimail = ("HaroldCarlo.Rebuldela@onsemi.com". "," . "Joe.Labanza@onsemi.com");

sendmail_utf8("Joshua.Buenaventura@onsemi.com", "SPARKS OF BRILLIANCE", "apps.donotreply@onsemi.com", "Pending Form from HAROLD REBULDELA", "<p><b>TICKET #:SOB-181206091124 </b> You have a current Sparks of Brilliance Form to be reviewed. Please see your pending forms</p> <p>Please use Google Chrome or Mozilla FireFox <a href='http://phsm01ws013/sob/view-form.php?id=80'>VIEW FORM</a></p> <br> <br> <br/><br/><b style='color:red'>Please do not reply.</b> <br/><br/>Applications Engineering <br/> SPARKS OF BRILLIANCE", $combimail);
//sendmail_utf8("Joe.Labanza@onsemi.com", "SPARKS OF BRILLIANCE", "apps.donotreply@onsemi.com", "Pending Form from HAROLD REBULDELA", "<p><b>TICKET #:SOB-181206091124 </b> You have a current Sparks of Brilliance Form to be reviewed. Please see your pending forms</p> <p>Please use Google Chrome or Mozilla FireFox <a href='http://phsm01ws013/sob/view-form.php?id=80'>VIEW FORM</a></p> <br> <br> <br/><br/><b style='color:red'>Please do not reply.</b> <br/><br/>Applications Engineering <br/> SPARKS OF BRILLIANCE", "Joe.Labanza@onsemi.com");
?>



