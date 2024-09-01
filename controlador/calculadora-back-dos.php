<?php
	if(is_file("vista/calculadora-back.php")){

		$cl = new Clase_prueba2;

		if(!empty($_POST)){// si hay alguna consulta tipo POST
			$accion = $_POST["accion"];// siempre se pasa un parametro con la accion que se va a realizar
			if($accion == "load_bitacora"){
				
			}

			exit;
		}

		$formula = "253+3+a+b+c+[cinco(-10+20)]-3";
		$formula = "2+3+(6*3 + 6*8) - 5";
		$formula = "GALLETAS 2";

		$cl->add_var("x","5");





		// $cl->add_var("a","10");
		// $cl->add_var("b","11");
		// $cl->add_var("c","12");
		// $cl->add_var("d","13");
		// $cl->add_var("cinco","5");

		

		$respuesta_calculadora = $cl->leer_formula($formula);


		require_once("vista/calculadora-back.php");
	}
	else{
		echo "pagina en construccion ($pagina)";
	}
?>