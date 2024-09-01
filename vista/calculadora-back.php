<!DOCTYPE html>
<html lang="es">
<head>
	<?php require_once 'assets/comun/head.php'; ?>
	<title>calculadora - back</title>
</head>
<body>
	<div class="container">
		<h1 class="w-100">probando back</h1>
	</div>

	<div class="container border-info border">
		<?php 

		if(isset($respuesta_calculadora)){

			ob_start();
			
			echo "<pre>\n";
			var_dump($respuesta_calculadora);

			// echo "formula: ";
			// var_dump($respuesta_calculadora[0]);
			// echo "Total: ";
			// var_dump($respuesta_calculadora[2][0]->get_total());
			echo "</pre>";
			
			$valor = ob_get_clean();

			//$valor = preg_replace("/\n+(?=string|float)/", "   ", $valor);
			$valor = preg_replace("/=>\n+/", "=>   ", $valor);
			$valor = preg_replace("/(string\(\d+\))\s+\"(.*)\"/", "$2   $1", $valor);
			$valor = preg_replace("/(float\((\d+)\))/", "$2   $1", $valor);
			echo $valor; exit;
		}


		//var_dump($cl);

		 ?>
	</div>
	
</body>
</html>