<?php

/**
* Plugin Name: DB Connection Plugin
* Description: Plugin for connecting to a databse.
* Version: 1.0 
* Author: TBR
* Author URI: Author's website
* License: A "Slug" license name e.g. GPL12
*/
add_shortcode('mw_list_products', 'mediweight_list_products' );
add_shortcode('mw_create_product', 'mediweight_create_product' );
add_shortcode('mw_product', 'mediweight_product' );
add_shortcode('mw_admin', 'mediweight_admin' );
add_shortcode('mw_create_admin', 'mediweight_create_admin' );
add_shortcode('mw_patient', 'mediweight_patient' );
add_shortcode('mw_find_patient', 'mediweight_find_patient' );
add_shortcode('mw_find_patient_2', 'mediweight_find_patient_2' );
add_shortcode('mw_create_patient', 'mediweight_create_patient' );
add_shortcode('mw_add_product_to_patient', 'mediweight_add_product_to_patient' );



function db_connect(){

	require("mediweight_setting.php");

	// Create connection
	$db = new mysqli($servername, $username, $password, $database);

	// Check connection
	if ($db->connect_error) {
	    die("Connection failed: " . $db->connect_error);
	} 

	return $db;
}

function mediweight_add_product_to_patient(){
	ob_start();
	 ?>
		<form action="action_page.php">
		CPR-nr:
		<input name="CPR-nr" type="text" value="" />
		<br><br>
		Navn:
		<input name="name" type="text" value="" />
		<br><br>
		Adresse:
		<input name="address" type="text" value="" />
		<br><br>
		Produkter:
		<input name="products" type="text" value="" />
		<br><br>
		<form action="demo_form.asp" method="get">
		<br><br>
		<input type="checkbox" name="fluid" value="Væske"> Væske<br>
		<input type="checkbox" name="steril" value="Sterilvare"> Sterilvare<br>
		<input type="checkbox" name="medicin" value="C-vitamin"> C-vitamin<br>
		<br><br>
		<input type="submit" value="Submit">
		</form>
	<?php
	
	$listStr = ob_get_contents();
	 ob_end_clean();
	
	return $listStr;
}

function mediweight_create_patient(){
	ob_start();
?>
		Opret patient
		<form method="post" action="">
		<br><br>
		CPR-nr:
		<input id="CPR-nr" name="CPR-nr" type="text" value="" />
		<br><br>
		Navn:
		<input id="fullname" name="fullname" type="text" value="" />
		<br><br>
		Adresse:
		<input id="address" name="address" type="text" value="" />
		<br><br>
		Postnummer:
		<input id="zip" name="zip" type="text" value="" />
		<br><br>
		Telefon:
		<input id="telephone" name="telephone" type="text" value="" />
		<br><br>
		<button type="submit">Tilføj</button>
		</form>
	<?php
	$cpr=$_REQUEST['CPR-nr'];
	$personName=$_REQUEST['fullname'];
	$personAdd=$_REQUEST['address'];
	$personZip=$_REQUEST['zip'];
	$persontlf=$_REQUEST['telephone'];

	if(strlen($cpr) > 0 && strlen($personName) > 0 && strlen($personAdd) > 0 && strlen($personZip) > 0 && strlen($persontlf) > 0){
		$db = db_connect();
		$query = "INSERT INTO patient (cpr, name, address, zipcode, phone) VALUES('$cpr','$personName','$personAdd','$personZip','$persontlf')";
       

		if ($db->query($query) === TRUE) {
			echo "New record created successfully";
		} else {
			echo "Error: " . $query . "<br>" . $db->error;
		}
	
                unset($_REQUEST['CPR-nr']);
                unset($_REQUEST['fullname']);
                unset($_REQUEST['address']);
                unset($_REQUEST['zip']);
                unset($_REQUEST['telephone']);
                unset($cpr);
                unset($personName);
                unset($personAdd);
                unset($personZip);
                unset($persontlf);
                $db->close(); 
	}	
	
$listStr = ob_get_contents();
ob_end_clean();
	
return $listStr;
}

function mediweight_find_patient(){
	$USER_CRUD_DELETE = 1;
	$USER_CRUD_UPDATE = 2;
	$USER_CRUD_ATTACH_PROD = 3;

	ob_start();

	$patientcpr=$_REQUEST['searchcpr'];

	if(strlen($patientcpr) > 0){
		$db = db_connect();

		$shouldupdate=$_REQUEST['shouldupdate'];

		if($shouldupdate == $USER_CRUD_UPDATE){
			$cpr=$_REQUEST['CPR-nr'];
			$personName=$_REQUEST['fullname'];
			$personAdd=$_REQUEST['address'];
			$personZip=$_REQUEST['zip'];
			$persontlf=$_REQUEST['phone'];

			$udpquery = "UPDATE patient SET name='$personName', address='$personAdd', cpr='$cpr', zipcode='$personZip', phone='$persontlf' WHERE cpr='$patientcpr'";
			$db->query($udpquery);
		}elseif($shouldupdate == $USER_CRUD_DELETE){	
			$delquery = "DELETE FROM patient WHERE cpr='$patientcpr'";
			$db->query($delquery);
		}			

		$query = "SELECT * FROM patient WHERE cpr LIKE '%$patientcpr%'";
	
		if($result=$db->query($query)){
			$row = $result->fetch_assoc();

		}
		$result->free();
		$db->close();
	}

	 ?>

		<form action="" method="post">
		CPR-nr:<br><br>
		<input type="text" id="searchcpr" name="searchcpr" value=""><br>
		<br><br>
		<button type="submit">Find patient</button>
		<br><br>
		</form>
	
		<form action="" method="post">
		CPR-nr:
		<input name="CPR-nr" type="text" id="CPR-nr" value="<?php echo $row['cpr']; ?> ">
		<br><br>
		Navn:
		<input name="fullname" type="text" id="fullname" value="<?php echo $row['name']; ?> "/>
		<br><br>
		Adresse:
		<input name="address" type="text" id="address" value="<?php echo $row['address']; ?>" />
		<br><br>
		Postnummer:
		<input name="zip" type="text" id="zip" value="<?php echo $row['zipcode']; ?>" />
		<br><br>
		Telefonnummer:
		<input name="phone" type="text" id="phone" value="<?php echo $row['phone']; ?>" />
		<br><br>
		Produkter:
		<input name="products" type="text" id="products" value="<?php echo $row['product']; ?>" />
		<input name="searchcpr" type="hidden" id="searchcpr" value="<?php echo $row['cpr']; ?>" />
		<input name="shouldupdate" type="hidden" id="shouldupdate" value="<?php echo $USER_CRUD_UPDATE;?>" />	
		<br><br>
		<br>
		<button type="submit">Opdater</button>
		</form>

		<form method="post" action="">
		<input name="shouldupdate" type="hidden" id="shouldupdate" value="<?php echo $USER_CRUD_DELETE;?>" />	
		<input name="searchcpr" type="hidden" id="searchcpr" value="<?php echo $row['cpr']; ?>" />
		<button type="submit">Slet</button>
		</form>

		<form method="post" action="http://mediweight.gnusys.dk/tilfoej-produkt-til-patient/">
		<button type="submit">Tilføj produkt til patient</button>
		</form>	
	<?php
	?>
	<?php
		
				
	$listStr = ob_get_contents();
	ob_end_clean();
	
	return $listStr;
}


function mediweight_patient(){
	ob_start();
	 ?>
		<form method="get" action="http://mediweight.gnusys.dk/opret-patient/">
		<button type="submit">Opret patient</button>
		</form>
		<br>
		<form method="get" action="http://mediweight.gnusys.dk/find-patient/">
		<button type="submit">Find patient</button>
		</form>
	<?php
	
	$listStr = ob_get_contents();
	ob_end_clean();

	return $listStr;
}

function mediweight_admin(){
	ob_start();
$db=db_connect();
	 ?>
		<form method="post" action="">
		Name:<br>
		<input type="text" id="searchadmin" name="searchadmin" value="" />
		<br>
		<button type="submit">Find admin</button>
		</form>
	
	
		<?php
		$adminname=$_REQUEST['searchadmin'];
		//echo $adminname;			
		?>

		<?php		
			
		if(strlen($adminname) > 0 ){
			$db = db_connect();
			$query = "SELECT * FROM admin Where name LIKE '%$adminname%'";
 

			if ($result=$db->query($query)){
				while ($row = $result->fetch_assoc()){
					echo $row['name'] . "<br />" . $row['phone'];
				}	
			}

			$result->free();
			$db->close();	
		}
		?>
	<?php
	
	$listStr = ob_get_contents();
	ob_end_clean();

	return $listStr;
}

function mediweight_create_admin(){
	ob_start();
?>
		<br><br>
		<form method="post" action="">
		Name:<br>
		<input type="text" id="newadminname" name="newadminname" value="" /><br>
		Telefonnummer:<br>
		<input type="text" id="newadminphone" name="newadminphone" value="" /><br>
		<br>
		<button type="submit">Opret admin</button>
		</form>
	
		<?php
		$newAdmin=$_REQUEST['newadminname'];
		$newPhone=$_REQUEST['newadminphone'];

		if(strlen($newAdmin) > 0 && strlen($newPhone) > 0){
			$db=db_connect();
			$query = "INSERT INTO admin (name, phone) VALUES('$newAdmin','$newPhone')";


			if ($db->query($query) === TRUE) {
				echo "New record created successfully";
			} else {
				echo "Error: " . $query . "<br>" . $db->error;
			}

			unset($_REQUEST['$newadminname']);
			unset($_REQUEST['newadminphone']);
			unset($newAdmin);
			unset($newPhone);
			$db->close();
		}
		?>

	<?php
	$listStr = ob_get_contents();
	ob_end_clean();

	return $listStr;
}

function mediweight_product(){
	ob_start();
	 ?>
		<form method="get" action="http://mediweight.gnusys.dk/opret-produkt/">
	   	<button type="submit">Opret produkt</button>
		</form>
		<br><br>
		<form method="get" action="http://mediweight.gnusys.dk/se-produkter/">
		<button type="submit">Se produkter</button>
		</form>
	<?php
	
	$listStr = ob_get_contents();
	ob_end_clean();

	return $listStr;
}

function mediweight_list_products(){
    ob_start();
 
$db=db_connect(); 

	?>
		<form action="action_page.php">Alle produkter:<br>
		<?php			
	
		$query = "SELECT * FROM product";
			
			if ($result=$db->query($query)){
				while ($row = $result->fetch_assoc()){
					echo $row['name'] . "<br />";
				}	
			$result->free();
				
			}
		$db->close();
		?>
	
		<br><br>
		<input type="submit" value="Rediger" /><br><br>
		 <input type="submit" value="Slet" /><br><br>		
		</form>	
	<?php
 
	$listStr = ob_get_contents();
	ob_end_clean();
	
	return $listStr;
}

function mediweight_create_product(){
	ob_start();

	?>
		<form method="post" action="">
		Type: 
		<input name="prodType" id="prodType" type="text" value="" />
		<br><br>
		Navn:
		<input name="prodName" id="prodName" type="text" value="" />
		<br><br>
		Vægt:
		<input name="prodWeight" id="prodWeight" type="text" value="" />		
		<br><br>
		Beskrivelse:
		<input name="proddes" id="proddes" type="text" value="" />
		<br><br>
		<button type="submit">Tilføj</button>
		</form>
	<?php
	$newPtype=$_REQUEST['prodType'];
	$newPname=$_REQUEST['prodName'];
	$newPweight=$_REQUEST['prodWeight'];
	$newPdesc=$_REQUEST['proddes'];

	if(strlen($newPtype) > 0 && strlen($newPname) > 0 && strlen($newPweight) > 0 && strlen($newPdesc) > 0){

		$db=db_connect();
		$query = "INSERT INTO product (type, name, weight, description) VALUES ('$newPtype', '$newPname' , '$newPweight' , '$newPdesc')";
		
	
		if ($db->query($query) === TRUE) {
			echo "New record created successfully";
		} else {
			echo "Error: " . $query . "<br>" . $db->error;
		}

		unset($_REQUEST['$prodType']);
		unset($_REQUEST['$prodName']);
		unset($_REQUEST['$prodWeight']);
		unset($_REQUEST['$proddes']);
		unset($newPtype);
		unset($newPname);
		unset($newPweight);
		unset($newPdesc);
		$db->close();
	}
	?>

<?php
$listStr = ob_get_contents();
ob_end_clean();

return $listStr;
}
