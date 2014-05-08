<?php
header('Content-type: text/html; charset=UTF-8');

$titulo = $_GET['mytitle'];
$name = $_GET['myname'];
$pages = $_GET['mypages'];
$email = $_GET['myemail'];
$desc = $_GET['mydesc'];

require 'connection.php';
$titulo = mysqli_real_escape_string($con, $titulo);
$name = mysqli_real_escape_string($con, $name);
$pages = mysqli_real_escape_string($con, $pages);
$email = mysqli_real_escape_string($con, $email);
$desc = mysqli_real_escape_string($con, $desc);

$IDresult = mysqli_query($con, "SELECT storyID FROM user_sub ORDER BY storyID DESC LIMIT 1");
$lastID = mysqli_fetch_array ($IDresult);
$newID = $lastID[0] + 1;

$sql="INSERT INTO user_sub (storyID, titulo, nombre, descripcion, email) VALUES ('".$newID."', '".$titulo."', '".$name."', '".$desc."', '".$email."')";
mysqli_query($con,$sql);

$totalPages = count($pages);

for ($i = 0; $i < $totalPages; $i++){
	$panels = $pages[$i];
	$totalPanels = count($panels);
	for ($j = 0; $j < $totalPanels; $j++){
		$panelInfo = $panels[$j];
		$panelName = $panelInfo[0];
		$panelX = $panelInfo[1];
		$panelY = $panelInfo[2];
		$panelZ = $panelInfo[3];
		$sql2="INSERT INTO user_stories (storyID, panel, page, panel_order, x_pos, y_pos, z_pos) VALUES (".$newID.", '".$panelName."', ".$i.", ".$j.", ".$panelX.", ".$panelY.", ".$panelZ.")";
		mysqli_query($con,$sql2);
	}
}
echo json_encode( array( "id"=>$newID ) );
mysqli_close($con);
?>