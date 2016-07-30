<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

$conn = new mysqli("localhost", "db_user", "db_pass", "db_name");

	$till=10;
	
	if(isset($_GET["till"]) && !empty($_GET["till"]) ){
		$till = $_GET["till"];
		$till = $conn->real_escape_string($till);
	}

	$query="SELECT p_id,p_name,p_description,p_image_id,p_price FROM products where p_id<=".$till." and p_available=1 ";


	if(isset($_GET["category"]) && !empty($_GET["category"]) ){
		
		$cat = $_GET["category"];
		$cat = stripslashes($cat);
		$cat = $conn->real_escape_string($cat);
		$query=$query."and p_category like ".$cat." ";
		
	}
	
	if( isset($_GET["sort"]) && !empty($_GET["sort"]) ){
		
		$s = $_GET["sort"];
		if($s=="n"){	$query.="order by p_name";}
		else if($s=="plh"){	$query.="order by p_price";}
		else if($s=="phl"){	$query.="order by p_price desc";}
	}

	
	

$result = $conn->query($query);
$outp = "";
while($rs = $result->fetch_array(MYSQLI_ASSOC)) {
    if ($outp != "") {$outp .= ",";}
    $outp .= '{"p_id":"'  . $rs["p_id"] . '",';
    $outp .= '"p_name":"'   . $rs["p_name"]        . '",';
	$outp .= '"p_description":"'   . $rs["p_description"]        . '",';
	$outp .= '"p_image_id":"'   . $rs["p_image_id"]        . '",';
	$outp .= '"p_price":"'. $rs["p_price"]     . '"}';
}


// Adding has more
$result=$conn->query("SELECT count(*) as total from products");
$data=$result->fetch_array(MYSQLI_ASSOC);
$total = $data['total'];

if(($total-$till)>0){$has_more=$total-$till;}
			    else{$has_more=0;}
			
				
	$outp ='{"has_more":'.$has_more.',"records":['.$outp.']}';

	
$conn->close();

echo($outp);
?> 