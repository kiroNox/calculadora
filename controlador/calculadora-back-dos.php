<?php
	if(is_file("vista/calculadora-back.php")){

		$cl = new Clase_prueba2;



		if(!empty($_POST)){// si hay alguna consulta tipo POST
			$accion = $_POST["accion"];// siempre se pasa un parametro con la accion que se va a realizar
			if($accion == "calc_formula"){

				$cl->set_id_trabajador($_POST["trabajador_prueba"]);

				if(isset($_POST["calc_condicional_check"])){

					$variables=null;

					if(isset($_POST["variables"])){
						$temp = json_decode($_POST["variables"]);
						$variables= [];
						foreach ($temp as $elem) {
							$cl->add_var($elem->name,$elem->value,$variables);
						}
					}


					$r = $cl->leer_formula_condicional($_POST["calc_condicional"],$_POST["calc_formula_input"],$variables);
					if($r["resultado"] == "leer_formula_condicional") $r["resultado"] = "leer_formula";

					echo json_encode($r);
				}
				else{

					$variables=null;

					if(isset($_POST["variables"])){
						$temp = json_decode($_POST["variables"]);
						$variables= [];
						foreach ($temp as $elem) {
							$cl->add_var($elem->name,$elem->value,$variables);
						}
					}
					
					echo json_encode($cl->leer_formula($_POST["calc_formula_input"],$variables));
				} 

			}
			else if($accion=="get_calc_reserved_words"){
				echo json_encode($cl->get_calc_reserved_words());
			}
			else if ($accion== "get_lista_trabajadores"){
				echo json_encode($cl->get_lista_trabajadores());
			}
			else if ($accion == "lista_formulas_condicionales"){//testing lista
				$cl->set_id_trabajador($_POST["trabajador_prueba"]);
				$_POST["formulas"] = json_decode($_POST["formulas"],true);
				echo json_encode($cl->leer_formula_condicional($_POST["formulas"]));
			}
			else if ($accion == "guardar_formula"){
				$variables=null;
				$calc_condicional = null;

				if(isset($_POST["calc_condicional"])){
					$calc_condicional = $_POST["calc_condicional"];
				}

				if(isset($_POST["variables"])){
					$temp = json_decode($_POST["variables"]);
					$variables= [];
					foreach ($temp as $elem) {
						$cl->add_var($elem->name,$elem->value,$variables);
					}
					$variables = json_encode($variables);
				}


				$r = $cl->calc_guardar_formula($_POST["calc_formula_input"], $_POST["calc_formula_nombre"], $_POST["calc_descripcion"], $variables, $calc_condicional);
				echo json_encode($r);

			}
			else if($accion = "guardar_lista_formulas_condicionales"){
				$_POST["formulas"] = json_decode($_POST["formulas"],true);
				$r = $cl->calc_guardar_formula_lista($_POST["formulas"], $_POST["calc_formula_nombre"], $_POST["calc_descripcion"]);
				echo json_encode($r);
			}
			else{
				$r=[];
				$r["resultado"] = "error";
				$r["titulo"] = "no programado";
				$r["mensaje"] = "La accion $accion no esta prgramada";

				echo json_encode($r);
			}

			exit;
		}

		require_once("vista/calculadora-back.php");
	}
	else{
		echo "pagina en construccion ($pagina)";
	}
?>