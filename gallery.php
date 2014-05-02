<!DOCTYPE html>
<html lang="es">
<meta charset="UTF-8">
<title>Alfredo: Una pseudo-biografía</title>
<meta name="description" content="Galería">
<link rel="icon" href="">
<script type="text/javascript" src="//use.typekit.net/qjz6zfi.js"></script>
<script type="text/javascript">try{Typekit.load();}catch(e){}</script>
<script type="text/javascript" language="javascript" src="js/jquery-2.1.0.min.js"></script>
<script type="text/javascript" language="javascript" src="js/jquery-migrate-1.2.1.min.js"></script>
<script type="text/javascript" language="javascript" src="js/jquery-ui-1.10.3.custom.js"></script>
<script type="text/javascript" language="javascript" src="js/jquery.hoverIntent.minified.js"></script>
<script type="text/javascript" language="javascript" src="js/jquery.ui.touch-punch.min.js"></script>
<script type="text/javascript" language="javascript" src="js/scripts.js"></script>
<link rel="stylesheet" type="text/css" media="all" href="style.css">
<body>
<div id="sidebar">
	<div id="tags">
		<a href="/uni"><h1>Alfredo:</br><span>Una pseudo-biografía</span></h1></a>
		<h2>Galería</h2>
		<ul>
			<li>Búsqueda</li>
		</ul>
	</div>
</div>

<div id="gallery">
<?php
$max = 5;
$counter =0;
$offset = 0;
$sql = "";
$result;
$pageCount = 0;
$activePage = 1;
$comicPage = 0;
$activeComicPage = 1;
$comicPageCount = 0;

function cPageCounter($id, $con) {
	$comicPageQuery = "SELECT COUNT(DISTINCT page) FROM user_stories WHERE storyID=".$id;
	$comicPageResult = mysqli_query($con, $comicPageQuery);
	$comicPageCount = mysqli_fetch_array($comicPageResult);
	mysqli_free_result($comicPageResult);
	return($comicPageCount[0]);
}

require 'connection.php';

if ( !empty($_GET['max']) && preg_match("/[0-9]{1,2}/",$_GET['max'])) {
	$max = $_GET['max'];
}
if (!empty($_GET['id']) && preg_match("/[0-9]{1,4}/",$_GET['id'])) {	//----------------------------- User asks for specific comic
	$storyID = $_GET['id'];
	$sql="SELECT COUNT(*) FROM user_sub WHERE storyID=".$storyID;  
	$panelResult = mysqli_query($con,$sql);
	$panelCount = mysqli_fetch_array($panelResult);
	if ( $panelCount[0] == 1 ) {	//--------------------------------------------------------------------- Comic exists
		$sql="SELECT * FROM user_sub WHERE storyID=".$storyID;
		if (!empty($_GET['cp']) && preg_match("/[0-9]{1,4}/",$_GET['cp']) ) {//----------------------------- User asks for specific comic page
			$comicPageCount = cPageCounter($storyID, $con);
			if ($_GET['cp'] <= $comicPageCount) {
				$comicPage = $_GET['cp'] - 1;
				$activeComicPage = $_GET['cp'];
			}
		}
	} else {	//------------------------------------------------------------------------------------- Comic doesn't exists
		$sql="SELECT * FROM user_sub ORDER BY storyID DESC LIMIT ".$offset.",".$max;	
		echo <<<HTML
	<div class="error">
		<p>No encontramos el cómic que está buscando. Aquí están los mas recientes.</p>
	</div>
HTML;
/*-----------DO NOT IDENT PREVIOUS LINE--------------*/
}
	mysqli_free_result($panelResult);
} else {	//----------------------------------------------------------------------------------------- Gallery page check & count
	$sql="SELECT COUNT(*) FROM user_sub";
	$result = mysqli_query($con,$sql);
	$stories = mysqli_fetch_array($result);
	$pageCount = ceil( $stories[0] / $max );
	if ( !empty($_GET['page']) && preg_match("/[0-9]/",$_GET['page']) && ($_GET['page'] <= $pageCount) ) {
		$activePage = $_GET['page'];
		$offset = ($activePage - 1) * $max;
	}
	mysqli_free_result($result);
	$sql="SELECT * FROM user_sub ORDER BY storyID DESC LIMIT ".$offset.",".$max;
}
$result = mysqli_query($con,$sql);

$panelInfoQuery = "SELECT * FROM paneles";
$panelInfoResult = mysqli_query($con, $panelInfoQuery);
$isWide = array();
$i = 1;
while ( $panelInfo = mysqli_fetch_array($panelInfoResult) ) {
	$isWide[$i] = $panelInfo['wide'];
	$i++;
}
mysqli_free_result($panelInfoResult);
if ($pageCount > 1){
	echo '<div id="gallery-pages">';
	for ($i = 1; $i <= $pageCount; $i++) {
		echo '<a href="gallery.php?page='.$i.'" class="pages';
		if ($i == $activePage) {
			echo ' active-page';
		}
		echo '">'.$i.'</a>';
	}
	echo '</div>';
}

while ($fieldinfo = mysqli_fetch_array($result)){
		$storyID = $fieldinfo['storyID'];
		$panelQuery = "SELECT * FROM user_stories WHERE storyID=".$storyID." && page=".$comicPage." ORDER BY panel_order ASC";
		$panels = mysqli_query($con, $panelQuery);
		if ($comicPageCount == 0) {
			$comicPageCount = cPageCounter($storyID, $con);
		}
		echo '<div name="'.$storyID.'" class="story">';
		echo '<h2>'.$fieldinfo['titulo'].'</h2>';
		echo '<div class="more-info"><h3>'.$fieldinfo['nombre'].'</h3>';
		echo '<p>'.$fieldinfo['descripcion'].'</p></div>';
		echo '<div class="canvas">';
		while ($panelArray = mysqli_fetch_array($panels)) {
			echo '<div class="paneles';
			if ( $isWide[$panelArray['panel']] == 1 ){
				echo ' wide';
			}
			echo '" name="'.$panelArray['panel'].'" style="background-image:url(paneles/'.$panelArray['panel'].'.jpg); ';
			echo 'left:'.$panelArray['x_pos'].'px; top:'.$panelArray['y_pos'].'px; z-index: '.$panelArray['z_pos'].';"></div>';
		}
		echo '</div>';
		if ( $comicPageCount > 1) {
			echo '<div class="comic-pages">';
			for ($i = 1; $i <= $comicPageCount; $i++) {
				echo '<a href="gallery.php?id='.$storyID.'&cp='.$i.'" id="'.$i.'" class="comic-pages';
				if ($i == $activeComicPage) {
					echo ' active-comic-page';
				}
				echo '">'.$i.'</a>';
			}
			echo '</div>';
		}
		echo '</div>';
		mysqli_free_result($panels);
		$comicPageCount = 0;
}
if ($pageCount > 1){
	echo '<div id="gallery-pages">';
	for ($i = 1; $i <= $pageCount; $i++) {
		echo '<a href="gallery.php?page='.$i.'" class="pages';
		if ($i == $activePage) {
			echo ' active-page';
		}
		echo '">'.$i.'</a>';
	}
	echo '</div>';
}
mysqli_close($con);

?>
</div>
</body>
