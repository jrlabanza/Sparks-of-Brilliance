<?php session_start();
include "database/db.php";
/**
 * Created by Joe of ExchangeCore.com
 */

if(isset($_POST['username']) && isset($_POST['password'])){
    
        
    
    if (!empty($_POST['username']) && !empty($_POST['password'])){
                $adServer = "ldap://ad.onsemi.com";

        $ldap = ldap_connect($adServer);

        $username = $_POST['username']; // FF ID
        $password = $_POST['password'];
        
        $ldaprdn = 'onsemi' . "\\" . $username;
        ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3);
        ldap_set_option($ldap, LDAP_OPT_REFERRALS, 0);
        $bind = @ldap_bind($ldap, $ldaprdn, $password);

        if ($bind) {

            $filter="(sAMAccountName=$username)";
            $result = ldap_search($ldap,"dc=MYDOMAIN,dc=COM",$filter);
            ldap_sort($ldap,$result,"sn");
            $info = ldap_get_entries($ldap, $result);
            $_SESSION['username'] = $username;           
            
                        
            $sql ="SELECT * FROM employeeinfos WHERE ffId='$username'";
            $user_res = mysqli_query($userconnect, $sql);
            if(!$user_res) {
		          die('Query Failed' . mysqli_error());
				}
            while($row = mysqli_fetch_assoc($user_res)){
            $userId = $row['id'];
			$deptCode = $row['deptCode'];
            $jobName = $row['jobName'];
            $cidNum =$row['cidNum'];
                
			}
            
            $priv = "SELECT * FROM masterlist WHERE userCID =". $cidNum;
            $privresult = $connection->query($priv);
            
            while ($p = mysqli_fetch_array($privresult)){
                $userPriv = $p['userCID'];    
                $department_head = $p['department_head'];
            }
            
            if (isset($userPriv)){
                $_SESSION['isSuperior'] = 1;
                if ($department_head == "Department Head"){
                    $_SESSION['department_head'] = "Department Head";
                }
                else{
                    $_SESSION['department_head'] = "";
                }
                
            }
            
            else
            {
               $_SESSION['isSuperior'] = 0; 
                
            }
            
          
            $_SESSION['id'] = $userId;
            $_SESSION['cidNum'] = $cidNum;
            $_SESSION['jobName'] = $jobName;
			$_SESSION['deptCode'] = $deptCode;
            $_SESSION['message'] = null;
            
		    $usersql = "SELECT * FROM usertype WHERE ffId='$username'";
            $userresult = $connection->query($usersql);
            
            while ($user = mysqli_fetch_array($userresult)){
                
                $userType = $user['type'];

            }
            
            $_SESSION['userType'] = $userType;

            @ldap_close($ldap);
            

            header("Location: index.php");
            exit();
        }
    }
}



//echo "FALSE";
$_SESSION['message'] = 1;
header("Location: login.php");
exit();
?>
