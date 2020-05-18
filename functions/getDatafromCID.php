<?php
    include "../database/db.php";
   
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

$empID = isset($_POST['empID']) ? $_POST['empID'] : 0;

$query ="SELECT * FROM employeeinfos WHERE cidNum = $empID limit 1";
$result = mysqli_query ($userconnect, $query); 
$d = get_data_array($result);


// $deptHead = isset($_POST['deptHead']) ? $_POST['deptHead'] : 0;
        

echo json_encode($d);
?>    
    
