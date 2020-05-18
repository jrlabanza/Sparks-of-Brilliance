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



  

    $deptCode = isset($_POST['deptCode']) ? $_POST['deptCode'] : 0;

    $query ="SELECT * FROM employeeinfos WHERE isSuperior = 1 AND deptCode = '$deptCode'" ;
    $result = mysqli_query ($connection, $query); 
    $d = get_assocArray($result);

    
            
//    print_r($d);

    echo json_encode($d);
?>    
    
