<?php
//connects php to mysql//
$connection = mysqli_connect('PHSM01WS012', 'automation','automation_APPs2017!','sob_db');
// $connection = mysqli_connect('localhost', 'root','','sob_db');

if(!$connection)
{
    die ('DATABASE NOT CONNECTED');
}
// $userconnect = mysqli_connect('localhost','root','','sob_db');
$userconnect = mysqli_connect('phsm01ws012','usercheecker','usercheecker01','userlookup');

if(!$userconnect)
{
    die ('DATABASE NOT CONNECTED');
}

// $equipment = mysqli_connect('phsm01ws012','readonly','readonly01','cents');
// if(!$equipment){
// die ('DATABASE NOT CONNECTED');
// }




?>
