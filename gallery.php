<!DOCTYPE html>
<html lang="es">
<meta charset="UTF-8">
<title>Alfredo: Una pseudo-biografía</title>
<meta name="description" content="Galería">
<link rel="icon" href="">
<script type="text/javascript" src="//use.typekit.net/qjz6zfi.js"></script>
<script type="text/javascript">try{Typekit.load();}catch(e){}</script>
<link rel="stylesheet" type="text/css" media="all" href="style.css">
<body>
<div id="fb-root"></div>
<script>
	window.fbAsyncInit = function() {
		FB.init({
			appId      : '672579049464159',
			xfbml      : false,
			version    : 'v2.0'
		});
	};
	(function(d, s, id) {
		var js, fjs = d.getElementsByTagName(s)[0];
		if (d.getElementById(id)) return;
		js = d.createElement(s); js.id = id;
		js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&appId=672579049464159&version=v2.0";
		fjs.parentNode.insertBefore(js, fjs);
	}(document, 'script', 'facebook-jssdk'));
</script>
<div id="sidebar">
	<div id="tags-container">
		<div id="nav-container">
			<h1><a href="index.php">Alfredo:</br><span>Una pseudo-biografía</span></a></h1>
			<nav id="primary">
				<li><a href="index.php">¡Crea tu cómic!</a></li>
				<li><a href="gallery.php" class="active">Galería</a></li>
			</nav>
			<div id="search">
				<h4>Búsqueda</h4>
				<form id="search" action="gallery.php" method="get">
					<input type="text" name="query" id="query" val=""></input>
					<button id="search" type="submit">Buscar</button>
				</form>
			</div>
		</div>
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
$query;
$direccion='';

function cPageCounter($id, $con) {
	$comicPageQuery = "SELECT COUNT(DISTINCT page) FROM user_stories WHERE storyID=".$id;
	$comicPageResult = mysqli_query($con, $comicPageQuery);
	$comicPageCount = mysqli_fetch_array($comicPageResult);
	mysqli_free_result($comicPageResult);
	return($comicPageCount[0]);
}

require 'connection.php';
if (!empty($_GET['thank']) && $_GET['thank']=="you" && !empty($_GET['id'])){
	$id = $_GET["id"];
	$direccion = 'http://alfredounapseudobiografia.com/gallery.php?id='.$id;
	echo '<div id="thank-container"><p>¡Gracias por completar mi historia! Ahora es el momento de compartirla con el mundo. ';
	echo 'La dirección directa de tu cómic es <a href="'.$direccion.'">AlfredoUnaPseudoBiografia.com/gallery.php?id='.$id.'</a></p>';
	echo '<div id="social">';
	echo '<div class="fb-like" data-href="'.$direccion.'" data-layout="button" data-action="like" data-show-faces="false" data-share="true"></div>';
	echo '<div><a href="'.$direccion.'" class="twitter-share-button" data-lang="en" data-count="none">Tweet</a></div></div></div>';
}
if (!empty($_COOKIE['max'])) {//----------------------------------------------------------------------- Max Pages Set in Cookies
	$max = $_COOKIE['max'];
}
if (!empty($_GET['max']) && preg_match("/[0-9]{1,2}/",$_GET['max'])) {//------------------------------- Max Pages Changed
	$max = $_GET['max'];
	setcookie("max", $max, time()+60*60*24*7);
}
if (!empty($_GET['id']) && preg_match("/[0-9]{1,4}/",$_GET['id'])) {	//----------------------------- User asks for specific comic
	$storyID = $_GET['id'];
	$sql="SELECT COUNT(*) FROM user_sub WHERE storyID=".$storyID;  
	$panelResult = mysqli_query($con,$sql);
	$panelCount = mysqli_fetch_array($panelResult);
	if ( $panelCount[0] == 1 ) {	//----------------------------------------------------------------------------------------- Comic exists
		$sql="SELECT * FROM user_sub WHERE storyID=".$storyID;
		if (!empty($_GET['cp']) && preg_match("/[0-9]{1,4}/",$_GET['cp']) ) {//------------------------------------------------------------------ User asks for specific comic page
			$comicPageCount = cPageCounter($storyID, $con);
			if ($_GET['cp'] <= $comicPageCount) {
				$comicPage = $_GET['cp'] - 1;
				$activeComicPage = $_GET['cp'];
			}
		}
	} 
	else {	//----------------------------------------------------------------------------------------------------------------- Comic doesn't exists
		$sql="SELECT * FROM user_sub ORDER BY storyID DESC LIMIT ".$offset.",".$max;	
		echo <<<HTML
	<div class="error">
		<p>No encontramos el cómic que está buscando.</p>
		<p>Aquí están los mas recientes.</p>
	</div>
HTML;
/*-----------DO NOT INDENT PREVIOUS LINE--------------*/
}
	mysqli_free_result($panelResult);
	$result = mysqli_query($con,$sql);
} 
elseif (!empty($_GET['query'])) {//-------------------------------------------------------------------- Start Search
	$query = $_GET['query']; 
	$min_length = 3;
	// you can set minimum length of the query if you want 
	if(strlen($query) >= $min_length){ // if query length is more or equal minimum length then	
		//Parte de este codigo es gracias a Hub pages http://csk157.hubpages.com/hub/Simple-search-PHP-MySQL
		$query = htmlspecialchars($query);
		$query = mysqli_real_escape_string($con, $query);
		$search_count = mysqli_query($con, "SELECT COUNT(*) FROM user_sub
			WHERE (`titulo` LIKE '%".$query."%') OR (`nombre` LIKE '%".$query."%') OR (`descripcion` LIKE '%".$query."%')") or die(mysqli_error($con));
		
		$stories = mysqli_fetch_array($search_count);
		if($stories[0] > 0){
			$pageCount = ceil( $stories[0] / $max );
			if ( !empty($_GET['page']) && preg_match("/[0-9]/",$_GET['page']) && ($_GET['page'] <= $pageCount)) {
				$activePage = $_GET['page'];
				$offset = ($activePage - 1) * $max;
			}
			$result = mysqli_query($con, "SELECT * FROM user_sub
				WHERE (`titulo` LIKE '%".$query."%') OR (`nombre` LIKE '%".$query."%') OR (`descripcion` LIKE '%".$query."%') LIMIT ".$offset.",".$max) or die(mysqli_error($con));
		} 
		else {
			$sql="SELECT * FROM user_sub ORDER BY storyID DESC LIMIT ".$offset.",".$max;	
		echo <<<HTML
	<div class="error">
		<p>No encontramos ningún cómic con el término que está buscando.</p>
		<p>Aquí están los mas recientes.</p>
	</div>
HTML;
/*-----------DO NOT INDENT PREVIOUS LINE--------------*/
		$result = mysqli_query($con,$sql);
		}
	} else {
	
	}
	mysqli_free_result($search_count);
} 
else {	//--------------------------------------------------------------------------------------------- Default Gallery
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
	$result = mysqli_query($con,$sql);
}
//----------------------------------------------------------------------------------------------------- Search for wide panels
$panelInfoQuery = "SELECT * FROM paneles";
$panelInfoResult = mysqli_query($con, $panelInfoQuery);
$isWide = array();
$i = 1;
while ( $panelInfo = mysqli_fetch_array($panelInfoResult) ) {
	$isWide[$i] = $panelInfo['wide'];
	$i++;
}
mysqli_free_result($panelInfoResult);
//----------------------------------------------------------------------------------------------------- Page Counter
if ($pageCount > 1){
	echo <<<HTML
	<div id="selector">
		<h4>Cómics por página</h4>
		<ul>
			<li class="active" value="5">5</li>
			<li value="10">10</li>
			<li value="25">25</li>
		</ul>
	</div>
HTML;
	
	echo '<div class="gallery-pages">';
	for ($i = 1; $i <= $pageCount; $i++) {
		echo '<a href="gallery.php?page='.$i;
		if (!empty($query)){
			echo '&query='.$query;
		}
		echo '" class="pages';
		if ($i == $activePage) {
			echo ' active-page';
		}
		echo '">'.$i.'</a>';
	}
	echo '</div>';
}
//----------------------------------------------------------------------------------------------------- Start Displaying Comics
while ($fieldinfo = mysqli_fetch_array($result)){
		$storyID = $fieldinfo['storyID'];
		$panelQuery = "SELECT * FROM user_stories WHERE storyID=".$storyID." && page=".$comicPage." ORDER BY panel_order ASC";
		$direccion = 'http://alfredounapseudonbiografia.com/gallery.php?id='.$storyID;
		$panels = mysqli_query($con, $panelQuery);
		if ($comicPageCount == 0) {
			$comicPageCount = cPageCounter($storyID, $con);
		}
		echo '<div name="'.$storyID.'" class="story">';
		echo '<h2>'.$fieldinfo['titulo'].'</h2>';
		echo '<div class="more-info"><h3>'.$fieldinfo['nombre'].'</h3>';
		echo '<p>'.$fieldinfo['descripcion'].'</p></div>';
		echo '<div class="canvas">';
		while ($panelArray = mysqli_fetch_array($panels)) { //---------------------------------------------------------------------------- Start Displaying Panels
			echo '<div class="paneles';
			if ( $isWide[$panelArray['panel']] == 1 ){
				echo ' wide';
			}
			echo '" name="'.$panelArray['panel'].'" style="background-image:url(paneles/'.$panelArray['panel'].'.jpg); ';
			echo 'left:'.$panelArray['x_pos'].'px; top:'.$panelArray['y_pos'].'px; z-index: '.$panelArray['z_pos'].';"></div>';
		}
		echo '</div>';
		if ( $comicPageCount > 1) { //----------------------------------------------------------------------------------------------------- Comic Page Counter
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
		echo '<div id="social">';
		echo '<div class="fb-like" data-href="'.$direccion.'" data-layout="button" data-action="like" data-show-faces="false" data-share="true"></div>';
		echo '<div><a href="'.$direccion.'" class="twitter-share-button" data-lang="en" data-count="none">Tweet</a></div></div>';
		echo '</div>';
		mysqli_free_result($panels);
		$comicPageCount = 0;
}
//----------------------------------------------------------------------------------------------------- Page Counter
if ($pageCount > 1){
	echo '<div class="gallery-pages">';
	for ($i = 1; $i <= $pageCount; $i++) {
		echo '<a href="gallery.php?page='.$i;
		if (!empty($query)){
			echo '&query='.$query;
		}
		echo '" class="pages';
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
<script>//-------------------- Twitter
!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="https://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");
</script>
<script type="text/javascript" language="javascript" src="js/jquery-2.1.0.min.js"></script>
<script type="text/javascript" language="javascript" src="js/jquery-migrate-1.2.1.min.js"></script>
<script type="text/javascript" language="javascript" src="js/jquery-ui-1.10.3.custom.js"></script>
<script type="text/javascript" language="javascript" src="js/jquery.hoverIntent.minified.js"></script>
<script type="text/javascript" language="javascript" src="js/jquery.ui.touch-punch.min.js"></script>
<script type="text/javascript" language="javascript" src="js/scripts.js"></script>
</body>
