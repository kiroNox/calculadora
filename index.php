<?php 
	session_start();


	$pagina = "calculadora-back-dos"; 
	 if (!empty($_GET['p'])){
	   $pagina = $_GET['p'];
	 }
	if(is_file("controlador/$pagina.php")){
		$excepciones_p=[
			"principal",
			"login",
			"reset-pass",
			"out"
		];

		require_once("assets/config/config.php");
		require_once ("modelo/loader.php");
		require_once("controlador/$pagina.php");
	}
	else{
		echo "pagina en construcción I <br>";
	}
	// Para preguntar:
	/*
		Nivel de precicion de los calculos (el redondeo)
		para las deducciones quincenales como se hace en febrero
	*/
 ?>