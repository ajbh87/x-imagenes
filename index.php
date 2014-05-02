<!DOCTYPE html>
<html lang="es">
<meta charset="UTF-8">
<title>Alfredo: Una pseudo-biografía</title>
<meta name="description" content="Aqui escribo el subtitulo">
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
<?php
if (empty($_COOKIE['tut'])){
echo <<<HTML
	<div id="tut-container">
		<div id="tut-welcome">
			<h3>Bienvenido al cómic</h3>
			<h1>Alfredo:</br><span>Una pseudo-biografía</span></h1>
			
		</div>
	</div>
HTML;
}
?>
<div id="container">
	<div id="menu">
		<a id="gallery" href="gallery.php">Galería</a>
	</div>
	<div id="submission">
		<a id="sub_close" class="close">X</a>
		<div id="sub-content">
			<form id="submit" >  
				<fieldset> 
					<label for="title" id="title_label">Título</label>  
					<input type="text" name="title" id="title" size="30" value="" class="text-input" />  
					<label for="name" id="name_label">Nombre</label>  
					<input type="text" name="name" id="name" size="30" value="" class="text-input" />
					<label for="email" id="email_label">E-Mail</label>  
					<input type="text" name="email" id="email" size="30" value="" class="text-input" />
					<label for="desc" id="desc_label">Descripción</label>
					<textarea id="desc" rows="4" cols="50" placeholder="Si quiere describa su obra..."></textarea>
					<br />  
					<input type="submit" class="button" id="submit_btn" label="Enviar"></input>
				</fieldset>  
			</form>  
		</div>
	</div>
	<div id="sidebar">
		<div id="tags">
			<h1>Alfredo:</br><span>Una pseudo-biografía</span></h1>
			<h2>¡Tags!</h2>
			<ul id="tags">
				<li><a name="todos">Todos</a></li>
				<?php
				require 'connection.php';
				$sql="SELECT * FROM tag_info ";
				$result = mysqli_query($con,$sql);

				while($fieldinfo = mysqli_fetch_array($result))
					{
					echo '<li><a name="',$fieldinfo['Stud'],'">',$fieldinfo['Name'],'</a></li>';
					}
				mysqli_close($con);
				?>
			</ul>
		</div>
		<div id="tools">
			<h3>Herramientas</h3>
			<a id="add-page">+ Añadir Página</a>
			<a id="remove-page">- Eliminar Página</a>
			<a id="submit">Finalizar</a>
		</div>
	</div>
	<div id="canvas-out-cont">
		<div id="canvas-cont">
			<div class="canvas canvas-active" name="1">
				<div class="handle">
				</div>
			</div>
		</div>	
		<div id="selection-wrap">
			<div id="selection">
			</div>
		</div>
	</div>
	
</div>
</body>
