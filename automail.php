<?php
//connects php to mysql//
$connection = mysqli_connect('10.153.239.121', 'automation','automation_APPs2017!','sob_db');
//$connection = mysqli_connect('localhost', 'root','','sob_db');
if(!$connection)
{
    die ('DATABASE NOT CONNECTED');
}

//$userconnect = mysqli_connect('localhost','root','','sob_db');
$userconnect = mysqli_connect('phsm01ws012','usercheecker','usercheecker01','userlookup');

if(!$userconnect)
{
    die ('DATABASE NOT CONNECTED');
}

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

$sql = "SELECT * FROM formfill_infov2 WHERE status= 1 AND isDeleted = 0";
$result = $connection->query($sql);
$statone = get_assocArray($result);

$status1Len = sizeof($statone);

$sql = "SELECT * FROM formfill_infov2 WHERE status= 2 AND isDeleted = 0";
$result = $connection->query($sql);
$stattwo = get_assocArray($result);

$status2Len = sizeof($stattwo);?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Document</title>
    </head>
    <body><?php
        if ($status1Len > 0)
        { ?> 
            <table border="1">
                <thead>
                    <tr>
                        <th>TicketNo Status 1</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                    for ($one = 0 ; $one < $status1Len ; $one++){
                        $sql = "SELECT * FROM employeeinfos WHERE cidNum =" . $statone[$one]['rec_CID'];
                        $result = $userconnect->query($sql);
                        $recman = get_data_array($result);
                        $combimail = $recman['email'].", Joe.Labanza@onsemi.com";

                        $sql = "SELECT * FROM employeeinfos WHERE cidNum =" . $statone[$one]['deptmanId'];
                        $result = $userconnect->query($sql);
                        $deptman = get_data_array($result);

                        echo "<tr>";
                        echo "<td>". $statone[$one]['ticket_no'] ."</td>";
                        echo "</tr>";
                        sendmail_utf8($deptman['email'], "SPARKS OF BRILLIANCE", "apps.donotreply@onsemi.com", "E-SOB : MANAGER PENDING", "<p><b>TICKET #:". $statone[$one]['ticket_no'] ."</b> You have a current Sparks of Brilliance Form to be reviewed. Please see your pending forms</p> <p>Please use Google Chrome or Mozilla FireFox <a href='http://phsm01ws014.ad.onsemi.com/sob/view-form.php?id=".  $statone[$one]['id'] ."'>VIEW FORM</a></p> <br> <br> <br/><br/><b style='color:red'>Please do not reply.</b> <br/><br/>Applications Engineering <br/> SPARKS OF BRILLIANCE", $combimail);
                    }

                
                ?>    
                </tbody><?
        }
        if ($status2Len > 0)
        { ?>        
            </table>
            <table border="1">
                <thead>
                    <tr>
                        <th>TicketNo Status 2</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                    for ($two = 0 ; $two < $status2Len ; $two++)
                    {
                        $sql = "SELECT * FROM employeeinfos WHERE cidNum =" . $stattwo[$two]['rec_CID'];
                        $result = $userconnect->query($sql);
                        $recman = get_data_array($result);

                        $combimail = $recman['email'].", Joe.Labanza@onsemi.com";

                        echo "<tr>";
                        echo "<td>". $stattwo[$two]['ticket_no'] ."</td>";
                        echo "</tr>";
                        sendmail_utf8("Cherry.Candelario@onsemi.com", "SPARKS OF BRILLIANCE", "apps.donotreply@onsemi.com", "E-SOB : HR PENDING", "<p><b>TICKET #:". $stattwo[$two]['ticket_no'] ."</b> You have a current Sparks of Brilliance Form to be reviewed. Please see your pending forms</p> <p>Please use Google Chrome or Mozilla FireFox <a href='http://phsm01ws014.ad.onsemi.com/sob/view-form.php?id=".  $stattwo[$two]['id'] ."'>VIEW FORM</a></p> <br> <br> <br/><br/><b style='color:red'>Please do not reply.</b> <br/><br/>Applications Engineering <br/> SPARKS OF BRILLIANCE", $combimail);
                    }
                ?>    
                </tbody>
            </table>
        <?php } ?>    
    </body>
</html>
