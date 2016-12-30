<?php


	require("mediweight_setting.php");

	// Create connection
	$db = new mysqli($servername, $username, $password, $database);

	// Check connection
	if ($db->connect_error) {
	    die("Connection failed: " . $db->connect_error);
	} 

	$json=$_REQUEST['json'];
	$decodedJson=json_decode($json);
	echo "<pre>";
	echo $decodedJson->{'function'};
	echo $decodedJson->{'patientId'};
	echo $decodedJson->{'prodId'};
	echo $decodedJson->{'instock'};
	echo "<pre>";

	if ($decodedJson->{'function'} == 'update'){
		$updateSql = "UPDATE patient SET product = " . $decodedJson->{'instock'}  . "  WHERE id = " . $decodedJson->{'patientId'};	
		$db->query($updateSql);
	}else if ($decodedJson->{'function'} == 'order'){
                $orderSql = ""};
                $db->query($orderSql);
        }

	$db->close();





?>
