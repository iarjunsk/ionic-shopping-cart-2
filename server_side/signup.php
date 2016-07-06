<?php

	if (isset($_SERVER['HTTP_ORIGIN'])) {
        header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Max-Age: 86400');    // cache for 1 day
    }

 

    // Access-Control headers are received during OPTIONS requests
    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
            header("Access-Control-Allow-Methods: GET, POST, OPTIONS");         

        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
            header("Access-Control-Allow-Headers:        {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
        exit(0);
    }



 

   $postdata = file_get_contents("php://input");

  if (isset($postdata)) {
		$request = json_decode($postdata);
		$name=$request->n;  
		$username=$request->un; 		
		$password=$request->ps; 
		$password=md5($password);
		$phone=$request->ph; 
		$address=$request->add; 
		$pincode=$request->pin; 
		
		$conn = new mysqli('localhost', 'user', 'pass', 'dbname');

		// To protect MySQL injection for Security purpose
		$name = stripslashes($name);
		$username = stripslashes($username);
		$password = stripslashes($password);
		$phone = stripslashes($phone);
		$address = stripslashes($address);
	        $pincode = stripslashes($pincode);
		// the above stripslashes is not required unless if you are using javascript, which im still new to Ionic but i guess it uses javascript for posting ? So i will just keep them.

		$stment = $conn->prepare("SELECT count(*) FROM `users` WHERE `u_id` = ?");
		$stment->bind_param('s', $username);
		$stment->execute();

		$return = $stment->fetch();
		if ($return) {
			$outp='{"result":{"created": "0" , "exists": "1" } }';
		} else {
			$stment = $conn->prepare("INSERT INTO users (u_name, u_id, u_password, u_phone, u_address, u_pincode, u_verified) VALUES (?, ?, ?, ?, ?, ?, ?)");
			$stment->bind_param("ssssssi", $name, $username, $password, $phone, &address, $pincode, 1);
			$stment->execute();
			$outp='{"result":{"created": "1", "exists": "0" } }';
		}


		echo $outp;
		
		$stment-close();
		$conn->close();

}

?> 
