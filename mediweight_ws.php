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

	if ($decodedJson->{'function'} == 'update'){
		$updateSql = "UPDATE patient SET inStock = '" . $decodedJson->{'instock'}  . "'  WHERE cpr = '" . $decodedJson->{'patientId'} ."';";	
		$db->query($updateSql);
		echo "OK";
	}else if ($decodedJson->{'function'} == 'order'){
                $orderSql = "INSERT INTO quote (patientId, productId, amount) VALUES  ('" .  $decodedJson->{'patientId'} . "', '" .  $decodedJson->{'prodId'} . "', '" . $decodedJson->{'amount'}."');";
        	$db->query($orderSql);	
		echo "OK";
        }

	$db->close();
?>
