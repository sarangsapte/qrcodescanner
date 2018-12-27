<?php
ob_start();
error_reporting(0);
$con=mysqli_connect("localhost","db","username","password");
//$con=mysqli_connect("localhost","root","","dbname");
// Check connection
if (mysqli_connect_errno())
{
  		echo "Failed to connect to mysqli_connect: " . mysqli_connect_error();
}

//echo json_encode(print_r($_POST));

function clean_mysqli_data($value){
  $value = trim($value);
  return $value;
}

function mysql_date(){
  return date("Y-m-d H:i:s"); 
}

function get_client_ip() {
    $ipaddress = '';
    if (getenv('HTTP_CLIENT_IP'))
        $ipaddress = getenv('HTTP_CLIENT_IP');
    else if(getenv('HTTP_X_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
    else if(getenv('HTTP_X_FORWARDED'))
        $ipaddress = getenv('HTTP_X_FORWARDED');
    else if(getenv('HTTP_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_FORWARDED_FOR');
    else if(getenv('HTTP_FORWARDED'))
       $ipaddress = getenv('HTTP_FORWARDED');
    else if(getenv('REMOTE_ADDR'))
        $ipaddress = getenv('REMOTE_ADDR');
    else
        $ipaddress = 'UNKNOWN';
    return $ipaddress;
}

$user_agent = $_SERVER['HTTP_USER_AGENT'];

function getOS() { 

    global $user_agent;

    $os_platform  = "Unknown OS Platform";

    $os_array     = array(
                          '/windows nt 10/i'      =>  'Windows 10',
                          '/windows nt 6.3/i'     =>  'Windows 8.1',
                          '/windows nt 6.2/i'     =>  'Windows 8',
                          '/windows nt 6.1/i'     =>  'Windows 7',
                          '/windows nt 6.0/i'     =>  'Windows Vista',
                          '/windows nt 5.2/i'     =>  'Windows Server 2003/XP x64',
                          '/windows nt 5.1/i'     =>  'Windows XP',
                          '/windows xp/i'         =>  'Windows XP',
                          '/windows nt 5.0/i'     =>  'Windows 2000',
                          '/windows me/i'         =>  'Windows ME',
                          '/win98/i'              =>  'Windows 98',
                          '/win95/i'              =>  'Windows 95',
                          '/win16/i'              =>  'Windows 3.11',
                          '/macintosh|mac os x/i' =>  'Mac OS X',
                          '/mac_powerpc/i'        =>  'Mac OS 9',
                          '/linux/i'              =>  'Linux',
                          '/ubuntu/i'             =>  'Ubuntu',
                          '/iphone/i'             =>  'iPhone',
                          '/ipod/i'               =>  'iPod',
                          '/ipad/i'               =>  'iPad',
                          '/android/i'            =>  'Android',
                          '/blackberry/i'         =>  'BlackBerry',
                          '/webos/i'              =>  'Mobile'
                    );

    foreach ($os_array as $regex => $value)
        if (preg_match($regex, $user_agent))
            $os_platform = $value;

    return $os_platform;
}

function getBrowser() {

    global $user_agent;

    $browser        = "Unknown Browser";

    $browser_array = array(
                            '/msie/i'      => 'Internet Explorer',
                            '/firefox/i'   => 'Firefox',
                            '/safari/i'    => 'Safari',
                            '/chrome/i'    => 'Chrome',
                            '/edge/i'      => 'Edge',
                            '/opera/i'     => 'Opera',
                            '/netscape/i'  => 'Netscape',
                            '/maxthon/i'   => 'Maxthon',
                            '/konqueror/i' => 'Konqueror',
                            '/mobile/i'    => 'Handheld Browser'
                     );

    foreach ($browser_array as $regex => $value)
        if (preg_match($regex, $user_agent))
            $browser = $value;

    return $browser;
}

if(count($_POST) > 0){

	$action = '';
	$action = $_POST['action']; 

	if($action == 'qrcoderegi'){

				$result = mysqli_query($con, 'SELECT * FROM exibition_submissions WHERE mobile= "'.$_POST['mobile'].'"');
				if( mysqli_num_rows($result) > 0 ){
					echo "Already Exist";
				}else{


							$ok_to_call = 0;
							$ok_to_sms = 0;
							$ok_to_whatsapp = 0;
							$ok_to_email = 0;
							$get_notifications_of_events = 0;

							if($_POST['yes_no'] == 'Yes'){

								if( $_POST['ok_to_call'] == 'true'){
									$ok_to_call = 1;
								}

								if( $_POST['ok_to_sms'] == 'true'){
									$ok_to_sms = 1;
								}

								if( $_POST['ok_to_whatsapp'] == 'true'){
									$ok_to_whatsapp = 1;
								}

								if( $_POST['ok_to_email'] == 'true'){
									$ok_to_email = 1;
								}
							}

							if( $_POST['get_notifications_of_events'] == 'Yes'){
									$get_notifications_of_events = 1;
							}

							mysqli_query($con, "INSERT INTO `exibition_submissions`(fullname,mobile,email,ok_to_call,ok_to_sms,ok_to_whatsapp,ok_to_email,get_notifications_of_events, created_date, updated_date) VALUES 
								('".clean_mysqli_data($_POST['fullname'])."',
								'".clean_mysqli_data($_POST['mobile'])."', '".clean_mysqli_data($_POST['email'])."', '".clean_mysqli_data($ok_to_call)."','".clean_mysqli_data($ok_to_sms)."','".clean_mysqli_data($ok_to_whatsapp)."','".clean_mysqli_data($ok_to_email)."','".clean_mysqli_data($get_notifications_of_events)."',
									'".mysql_date()."',
									'".mysql_date()."'
							)");


							echo 'Success';

				}

	}elseif($action == 'qrcoderegiLogin'){

				$result = mysqli_query($con, 'SELECT * FROM exibition_submissions WHERE mobile= "'.$_POST['mobile'].'"');
				if( mysqli_num_rows($result) > 0 )
			    {

			    	$login_data = array();
			    	$login_data  = array(
			    		'login_mobile' => $_POST['mobile'],
			    		'login_details' => get_client_ip(),
			    		'login_date' =>	mysql_date(),
			    		'user_os' => getOS(),
						'user_browser' => getBrowser()	    		
			    	);

			    	$login_data = json_encode($login_data);
			    	mysqli_query($con, "INSERT INTO exibition_login(login_data) VALUES 
								('".clean_mysqli_data($login_data)."')");

			        echo "Auth";
			    }else{
			    	echo "Unauthorized Access";
			    }

	}elseif( $action == 'savedata' ){

			$result = mysqli_query($con, 'SELECT * FROM exibition_submissions WHERE mobile= "'.$_POST['mobileid'].'"');
			if( mysqli_num_rows($result) > 0 )
			{
				  
					$array_val = array();
					$json_encode = array();
					$results = mysqli_fetch_assoc($result);
					if($results['scan_url_record'] == ''){ // First Time
						$array_val[] = $_POST['content'];
						$json_encode = json_encode($array_val);
						mysqli_query($con, "
								UPDATE exibition_submissions 
								SET scan_url_record = '".clean_mysqli_data($json_encode)."',
								updated_date = '".mysql_date()."' 
								WHERE mobile = '".clean_mysqli_data($_POST['mobileid'])."' 
						");
					}else{ // Second , Third ..... Times

						$json_decode = array();
						$json_decode = json_decode( $results['scan_url_record'] );

						if (in_array($_POST['content'], $json_decode, true)) { // Yes Available

						}else{ // Not Available
							array_push($json_decode, $_POST['content']);
						}				

						$json_encode = json_encode($json_decode);
						mysqli_query($con, "
								UPDATE exibition_submissions 
								SET scan_url_record = '".clean_mysqli_data($json_encode)."',
								updated_date = '".mysql_date()."'
								WHERE mobile = '".clean_mysqli_data($_POST['mobileid'])."' 
						");

					}

			}else{
				    	echo "Unauthorized Access";
			}

	}elseif($action == 'showuserdata'){

			//print_r($_POST);
			$result = mysqli_query($con, 'SELECT * FROM exibition_submissions WHERE mobile= "'.$_POST['mobileid'].'"');
			
			if( mysqli_num_rows($result) > 0 )
			{

				$data = mysqli_fetch_assoc($result); 
				$json_decode = array(); 
				//$json_decode = json_decode($data['scan_url_record']); 
				echo $data['scan_url_record'];

			}else{

					echo "Unauthorized Access";
			}

	}elseif($action == 'whatsappmessage'){

		if( count($_POST['mobile']) != '' ){

			$result = mysqli_query($con, 'SELECT * FROM exibition_submissions WHERE mobile= "'.$_POST['mobile'].'"');
			
			if( mysqli_num_rows($result) > 0 )
			{

			}else{
					/*mysqli_query($con, "INSERT INTO `exibition_submissions`(mobile, created_date, updated_date) VALUES 
								(
								'".clean_mysqli_data($_POST['mobile'])."',
									'".mysql_date()."',
									'".mysql_date()."'
							)");*/
			}
		}

	}else{ 
		echo "Access Denied";
		//header("Location: http://example.com/myOtherPage.php");
		die();
	}

}else{ 
	echo "Access Denied";	
	die();
}

//mysqli_connect_close($con);
?>