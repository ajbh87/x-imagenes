<?php
header('Content-type: text/html; charset=UTF-8');
$name = strval($_POST['myid']);

require 'connection.php';

//second query
if ($name == 'todos') {
	$name = 'panelID';
	$selection = 'Todos';
} else {
	$sql2="SELECT * FROM tag_info WHERE stud='".$name."'";
	$result2 = mysqli_query($con,$sql2);
	$taginfo = mysqli_fetch_array($result2);
	$selection = $taginfo['Name'];
}

//first query
$sql="SELECT ".$name." FROM tags ORDER BY RAND()";
$result = mysqli_query($con,$sql);
//title
echo '<h3>'.$selection.'</h3>';
//is wide? preparation
$panelInfoQuery = "SELECT * FROM paneles";
	$panelInfoResult = mysqli_query($con, $panelInfoQuery);
	$isWide = array();
	$i = 1;
	while ( $panelInfo = mysqli_fetch_array($panelInfoResult) ) {
		$isWide[$i] = $panelInfo['wide'];
		$i++;
	}
	mysqli_free_result($panelInfoResult);

while($fieldinfo = mysqli_fetch_array($result)) {
	$id =  $fieldinfo[0];
	if ($id > 0) {
		echo '<div class="drag paneles';
		if ( $isWide[$fieldinfo[0]] == 1 ){
					echo ' wide';
				}
		echo '" name="'.$fieldinfo[0].'" style="background-image:url(paneles/'.$fieldinfo[0].'.jpg);"></div>';
		}
}
mysqli_close($con);
?>