<?php 
trait Calculadora2{


	PRIVATE $calc_status = false;
	PRIVATE $calc_var;
	PRIVATE $calc_f;
	PRIVATE $calc_formula;
	PRIVATE $calc_list_formulas;
	PRIVATE $calc_separadores;
	PRIVATE $calc_posicion;
	PRIVATE $calc_items;
	PRIVATE $counter_loop;
	PRIVATE $calc_error;
	PRIVATE $calc_evaluando;
	PRIVATE $calc_diff_var_formula;



	PUBLIC function calc_init(){
		$this->calc_f = new stdClass();
		$this->calc_var = new stdClass();
		$this->calc_evaluando = new stdClass();
		$this->calc_list_formulas = new stdClass();
		$this->calc_error = null;

		$this->calc_diff_var_formula[] = $this->calc_separadores[] = '\\+';
		$this->calc_diff_var_formula[] = $this->calc_separadores[] = '\\-';
		$this->calc_diff_var_formula[] = $this->calc_separadores[] = '\\/';
		$this->calc_diff_var_formula[] = $this->calc_separadores[] = '\\*';
		$this->calc_diff_var_formula[] = $this->calc_separadores[] = '\\(';
		$this->calc_diff_var_formula[] = $this->calc_separadores[] = '\\)';
		$this->calc_diff_var_formula[] = $this->calc_separadores[] = '\\[';
		$this->calc_diff_var_formula[] = $this->calc_separadores[] = '\\]';
		$this->calc_diff_var_formula[] = $this->calc_separadores[] = '\\{';
		$this->calc_diff_var_formula[] = $this->calc_separadores[] = '\\}';
		$this->calc_separadores[] = '[a-zA-Z]+(?:[_]*[a-zA-Z]*)?';
		//$this->calc_separadores[] = '[0-9]+([\\.][0-9]+)?';
		$this->calc_separadores[] = '__.*__';

		$this->calc_posicion = 0;
		$this->counter_loop = 0;

		$this->calc_status = true;



		$this->add_calc_system_function();

		$this->update_list_formulas();



	}

	PRIVATE function add_calc_system_function(){
		
		$fun = function(){

			$resultado = null;

			if(isset($this->id_trabajador)){
				$consulta = $this->con->prepare("SELECT 1 FROM trabajadores WHERE id_trabajador = ?;");
				$consulta->execute([$this->id_trabajador]);
				if($resp = $consulta->fetch(PDO::FETCH_ASSOC)){// el trabajador existe
					$consulta = null;
					$consulta = $this->con->prepare("SELECT TIMESTAMPDIFF(YEAR, t.creado ,CURRENT_DATE) as tiempo from trabajadores as t WHERE id_trabajador = ?");
					$consulta->execute([$this->id_trabajador]);
					$resp = $consulta->fetch(PDO::FETCH_ASSOC);
					$resultado = intval($resp["tiempo"]);

				}
				else{
					throw new Exception("El trabajador no existe", 1);
					
				}
			}
			else{
				throw new Exception("EL id del trabajador no esta definido", 1);
				
			}
			return $resultado;
		};
		$descrip = "Calcula el tiempo del trabajador en años";
		$this->set_calc_function("TIEMPO_TRABAJADOR",$descrip,$fun,false,true);

		$fun = function(){
			return 0;
		};
		$descrip="galleta XD ";
		$this->set_calc_function("GALLETAS",$descrip,$fun,false);

		// otras funciones....
	}

	PRIVATE function calc_f_clean_cache(){
		foreach ($this->calc_f as $elem) {
			$elem->cl_cache();
		}
	}



	PUBLIC function leer_formula($formula,$variables=null,$cl_cache=true,$var_formu=false){

		try {
			if($variables === null){
				$variables = [];
			}
			$this->calc_check_status(); // check constructor
			$this->set_calc_formula($formula); // limpio la formula de los espacios en blanco
			$formula_array = $this->calc_separador($formula); // separo los elementos


			$this->calc_variables($formula_array,$variables); // remplazo las variables

			
			$formula_array = $this->calc_groups($formula_array); // asigno los grupos





			$r["resultado"] = "leer_formula";
			$r["total"] = $this->resolve_groups($formula_array,$formula);
			$r["formula"] = $this->calc_formula;
			$r["tipo"] = "normal";
			//$this->calc_nodes();
			//$r["total"] = $this->resolve_nodes();

			//$this->calc_resolve();


			

			
			//$r[] = $this->calc_formula;

			$r["variables"] = $this->get_all_var($variables);

			//$r[] = $this->calc_items;
			$r[] = $formula_array;

		} catch (Exception $e) {
			if($this->con instanceof PDO){
				if($this->con->inTransaction()){
					$this->con->rollBack();
				}
			}
			
			$r['resultado'] = 'error';
			$r['titulo'] = "La formula no pudo ser calculada";
			$r["formula"] = $formula;


			if($e->getCode() == 118){
				$r['mensaje'] =  $e->getMessage();
			}
			else{
				$r["mensaje"] = $e->getMessage();
			}
			$r["calc_error"] = $e->getCode();
			// 103 = Las agrupaciones no pueden estar vaciás
			// 118 = La variable/function ... no existe

			if($var_formu!==false){// si no es false significa que estaba evaluando algo y eso seria $var_formu
				throw new Exception("Error al evaluar '$var_formu' :: ".$e->getMessage(), $e->getCode());
			}

			//$r["path"] = $e->getTrace();
			$r["path2"] = $e->getTraceAsString();
			//$r["lista"] = $this->calc_items;
		}

		return $r;

	}

	PUBLIC function leer_formula_condicional($condiciones,$formula= null,$variables=null){
		try {
			if(is_array($condiciones)){// Significa que es una matriz con los datos (asociativo de los argumentos)

				foreach ($condiciones as $lista) {
					if(!isset($lista["variables"])) $lista["variables"] = null;

					$leer_formula_condicional = $this->leer_formula_condicional($lista["condiciones"],$lista["formula"],$lista["variables"]);
					if($leer_formula_condicional["resultado"] == 'error'){
						throw new Exception($leer_formula_condicional["mensaje"], $leer_formula_condicional["calc_error"]);
					}

					if($leer_formula_condicional["total"] == NULL){ // no se cumplió ninguna condicional
						continue;
					}

					return $leer_formula_condicional;
				}

				$r = [];
				$r["resultado"] = "leer_formula_condicional";
				$r["total"] = null;
				$r["tipo"] = "condicional";
				return $r;

			}
			else if(!isset($formula)){
				throw new Exception("Debe introducir una formula valida para la condicional '$condiciones' ", 1);
				
			}

			$condiciones = preg_replace("/\s*/", "", $condiciones);

			if(preg_match("/[<][>]|[<][=]|[>][=]|[<]|[>]|[=]/", $condiciones)){

				$condiciones_separadas = preg_split("/([\<][\>])|([<][=])|([>][=])|([\<])|([\>])|([\=])/", $condiciones,-1,PREG_SPLIT_DELIM_CAPTURE);



				$condiciones_separadas = array_filter($condiciones_separadas,"calc_filter_vacio");
				$condiciones_separadas = array_values($condiciones_separadas);

				if(count($condiciones_separadas) != 3 ){
					throw new Exception("Error en '$condiciones' solo puede usar los siguientes caracteres de comparación (<, >, =, >=, <=)", 1);
				}


				preg_match("/[\<][\>]|[<][=]|[>][=]|[\<]|[\>]|[\=]/", $condiciones,$igualdad);// obtengo la igualdad
				
				$condicion_1 = $this->leer_formula($condiciones_separadas[0]);

				$condicion_2 = $this->leer_formula($condiciones_separadas[2]);

				if($condicion_1["resultado"] == "error"){
					throw new Exception($condicion_1["mensaje"], $condicion_1["calc_error"]);
				}
				
				if($condicion_2["resultado"] == "error"){
					throw new Exception($condicion_2["mensaje"], $condicion_2["calc_error"]);
				}

				$respuesta = false;

				switch ($igualdad[0]) {
					case '<>':
						if($condicion_1 != $condicion_2){
							$respuesta = true;
						}
						break;
					case '=':
						if($condicion_1 == $condicion_2){
							$respuesta = true;
						}
						break;
					case '<':
						if($condicion_1 < $condicion_2){
							$respuesta = true;
						}
						break;
					case '>':
						if($condicion_1 > $condicion_2){
							$respuesta = true;
						}
						break;
					case '>=':
						if($condicion_1 >= $condicion_2){
							$respuesta = true;
						}
						break;
					case '<=':
						if($condicion_1 <= $condicion_2){
							$respuesta = true;
						}
						break;
					default:
						throw new Exception("ERROR al recibir el signo de igualdad de la condición", 1);
				}

				if($respuesta === true){
					$r = $this->leer_formula($formula,$variables);
					if($r["resultado"] == 'error'){
						throw new Exception($r["mensaje"], $r["calc_error"]);
					}
					$r["resultado"] = "leer_formula_condicional";
					$r["tipo"] = "condicional";
					$r["formula"] = $formula;
				}
				else{
					$r =[];
					$r["resultado"] = "leer_formula_condicional";
					$r["total"] = NULL;
					$r["formula"] = $formula;
					$r["variables"] = $variables;
					$r["tipo"] = "condicional";

				}


			}
			else{

				//throw new Exception("Error en la condición '$condiciones' debe tener al menos un signo de comparación (<,>,<>,=)", 1);

				$cond = $this->leer_formula($condiciones);

				if($cond["resultado"] == "leer_formula"){
					if(($cond["total"] > 0)){

						$r = $this->leer_formula($formula,$variables);
						if($r["resultado"] == 'error'){
							throw new Exception($r["mensaje"], $r["calc_error"]);
						}
						$r["resultado"] = "leer_formula_condicional";
						$r["tipo"] = "condicional";
						$r["formula"] = $formula;
					}
					else{
						$r =[];
						$r["resultado"] = "leer_formula_condicional";
						$r["total"] = NULL;
						$r["formula"] = $formula;
						$r["variables"] = $variables;
						$r["tipo"] = "condicional";

					}
				}
				else{
					throw new Exception($cond["mensaje"], $cond["calc_error"]);
				}

				
			}
		
		} catch (Exception $e) {
			if($this->con instanceof PDO){
				if($this->con->inTransaction()){
					$this->con->rollBack();
				}
			}
			
			$r['resultado'] = 'error';
			$r['titulo'] = 'La formula no pudo ser calculada';
			$r["formula"] = $formula;


			if($e->getCode() == 118){
				$r['mensaje'] =  $e->getMessage();
			}
			else{
				$r["mensaje"] = $e->getMessage();
				$r["calc_error"] = $e->getCode();
				// 103 = Las agrupaciones no pueden estar vaciás
				// 118 = La variable/function ... no existe
			}

			//$r["path"] = $e->getTrace();
			$r["path2"] = $e->getTraceAsString();
			//$r["lista"] = $this->calc_items;
		}








		return $r;
	}

	PUBLIC function resolve_groups($formula_array,$formula = null){

		for($i=0;$i<count($formula_array);$i++){
			$token = $formula_array[$i];
			if(is_array($token)){
				$formula_array[$i] = $this->resolve_groups($token);
			}
		}


		$this->calc_nodes($formula_array,$formula);
		return $this->resolve_nodes($formula_array);

	}

	PRIVATE function calc_check_status(){
		if(!$this->calc_status) throw new Exception("Calculadora no inicializada", 1);
		if($this->calc_error) throw new Exception($this->calc_error, 1);
	}

	PRIVATE function calc_separador ($string){
		try {

			//$string = $this->calc_formula;


			if($string ==''){
				throw new Exception("No hay una formula valida", 1);
			}

			$lista_invalido = [
				"script"
			];

			$lista_invalido = implode('|', $lista_invalido);
			$lista_invalido = "/$lista_invalido/";

			if(preg_match($lista_invalido, $string, $found) ){
				throw new Exception("'$found[0]' no es valido en la formula", 1);
			}








			$separadores = $this->calc_separadores;


			$separadores = implode("|", $separadores);

			$separadores = "/($separadores)/";

			$r = preg_split($separadores, $string, -1, PREG_SPLIT_DELIM_CAPTURE);



			$r = array_filter($r,"calc_filter_vacio");

			$operador[] = '^[\\+]$';
			$operador[] = '^[\\-]$';
			$operador[] = '^[\\/]$';
			$operador[] = '^[\\*]$';
			$operador[] = '^[\\(]$';
			$operador[] = '^[\\[]$';
			$operador[] = '^[\\{]$';
			$operador = implode("|", $operador);


			
			







			$r = array_values($r);

			$end = count($r);

			for($i = 0;$i<$end;$i++){

				
				if( isset($r[$i]) and ($r[$i] == '-' or $r[$i] == '+')){
					if($i != 0){
						if(isset($r[($i - 1)]) and preg_match("/$operador/", $r[($i - 1)]) and is_numeric($r[($i + 1)])){ // anterior
							$r[($i+1)] = $r[$i].$r[($i+1)];
							unset($r[$i]);
							continue;
						}
					}
				}

				if (isset($r[$i])){
						if (is_numeric($r[$i])){
							$r[$i] = floatval(number_format($r[$i],2,'.',''));
						}
					}
			}

			$r = array_values($r);

			//$this->calc_items = $r;

			
			
			return $r;





			//$r = $string;
			
		} catch (Exception $e) {

			throw $e;
		}
	}


	PRIVATE function calc_variables(&$formula,$variables){


		$operador = [
			"^[\\+]$",
			"^[\\-]$",
			"^[\\/]$",
			"^[\\*]$",
			"^[\\(]$",
			"^[\\[]$",
			"^[\\{]$"
		];
		$operador = implode("|", $operador);

		foreach ($formula as $key => $value) {
			if(isset($formula[$key]) and preg_match("/[a-zA-Z]+(?:[_]*[a-zA-Z]*)?/", $formula[$key] )){// si encuentra nombres de variables o funciones en la formula
				if( ($temp = $this->get_calc_function($formula[$key])) !== null){// si es una funcion
					$temp = floatval(number_format($temp,2,'.',''));





					$temp_key = intval($key);

					if (isset($formula[$temp_key - 1]) and preg_match("/^\+$ | ^\-$|/", $formula[$temp_key - 1])){
						$anterior = $formula[$temp_key - 1];
						// si el anterior al actual existe y si es un signo de "+" o "-"

						


						if ((isset($formula[$temp_key - 2]) and preg_match("/$operador/", $formula[$temp_key - 2])) or ($temp_key - 2) < 0 ){
							if(($temp < 0 or $temp > 0 ) and $anterior == '-'){

								$temp = -1 * $temp;

								$formula[$key] = $temp;
								unset($formula[$temp_key - 1]);
								$formula = array_values($formula);
								continue;
							}
						}
					}



					$formula[$key] = $temp;
					continue;
				}

				else if(($temp = $this->get_var($formula[$key],$variables) ) !== false){ // si es una variable del usuario

						
						
							$temp = floatval(number_format($temp,2,'.',''));

							$temp_key = intval($key);

							if (isset($formula[$temp_key - 1]) and preg_match("/^\+$ | ^\-$|/", $formula[$temp_key - 1])){
								$anterior = $formula[$temp_key - 1];
								// si el anterior al actual existe y si es un signo de "+" o "-"

								


								if ((isset($formula[$temp_key - 2]) and preg_match("/$operador/", $formula[$temp_key - 2])) or ($temp_key - 2) < 0 ){
									if(($temp < 0 or $temp > 0 ) and $anterior == '-'){

										$temp = -1 * $temp;

										$formula[$key] = $temp;
										unset($formula[$temp_key - 1]);
										$formula = array_values($formula);
										continue;
									}
								}
							}


					$formula[$key] = $temp;
					continue;
				}
				else if(isset($this->calc_list_formulas->{$value})){// si es una formula almacenada con un nombre


					if(isset($this->calc_evaluando->{$value})){
						throw new Exception("Se esta pidiendo evaluar la formula '$value' en ciclo infinito revise la formula", 1);
					}
					else{
						$this->calc_evaluando->{$value} = 1;
					}




					$name_form = $this->calc_list_formulas->{$value}["name"];
					$formula_form = $this->calc_list_formulas->{$value}["formula"];

					if(!is_array($formula_form)){// si no es una lista de formulas con condicionales
						$variables_form = $this->calc_list_formulas->{$value}["variables"];

						if(isset( $this->calc_list_formulas->{$value}["condiciones"] )){

							$condiciones = $this->calc_list_formulas->{$value}["condiciones"];

							$form_resp = $this->leer_formula_condicional($condiciones, $formula_form, $variables_form);

						}
						else{
							$form_resp = $this->leer_formula($formula_form, $variables_form, false);
						}

						if($form_resp["resultado"] != "leer_formula" and $form_resp["resultado"] != "leer_formula_condicional" ){
							throw new Exception($form_resp["mensaje"], $form_resp["calc_error"]);
						}

						$formula[$key] = $form_resp["total"];
					}
					else{
						$form_resp = $this->leer_formula_condicional($formula_form);
						$formula[$key] = $form_resp["total"];
					}

					unset($this->calc_evaluando->{$value});
				}
				else{

					throw new Exception("La variable/function '".$formula[$key]."' no existe", 118);
					
				}
			}
		}
	}

	PUBLIC function add_list_formulas($name,$formula,$descrip,$variables=null,$condiciones = null,$id_formula=null,$replace=false){

		if(!isset($this->calc_list_formulas->{$name}) or ( isset($this->calc_list_formulas->{$name}) and $replace = true ) ){
			if(!isset($this->calc_f->{$name})){// si la funcion no existe
				if(!isset($variables)){
					$variables = [];
				}
				$this->calc_list_formulas->{$name}["formula"] = $formula;
				$this->calc_list_formulas->{$name}["variables"] = $variables;
				$this->calc_list_formulas->{$name}["name"] = $name;
				$this->calc_list_formulas->{$name}["descrip"] = $descrip;
				$this->calc_list_formulas->{$name}["condiciones"] = $condiciones;
				$this->calc_list_formulas->{$name}["id_formula"] = $id_formula;
			}
			else{
				$this->calc_error = "La formula con el nombre '$name' esta tomando una palabra reservada del sistema necesita ser modificada antes de continuar";
			}
		}
		else {
			$this->calc_error = "La formula con el nombre '$name' esta duplicada y debe ser modificada antes de continuar";
		}
	}

	PUBLIC function update_list_formulas(){
		try {


			$this->validar_conexion($this->con);

			$consulta = $this->con->prepare("SELECT f.nombre, f.id_formula, f.descripcion, df.formula, df.variables, df.condicional FROM detalles_formulas AS df LEFT JOIN formulas AS f ON f.id_formula = df.id_formula WHERE 1;");
			$consulta->execute();
			$resp = $consulta->fetchall(PDO::FETCH_GROUP);

			if($resp){


				$this->calc_list_formulas = new stdClass();
				foreach ($resp as $key => $lista) {
					if(count($lista)>1){
						$formula_array = [];
						foreach ($lista as $elem) {
							$temp_array = [];
							$temp_array["condiciones"] = $elem["condicional"];
							$temp_array["formula"] = $elem["formula"];
							$elem["variables"] = ($elem["variables"] !== null)?json_decode($elem["variables"],true):NULL;
							$temp_array["variables"] = $elem["variables"];
							$temp_array["id_formula"] = $elem["id_formula"];
							$formula_array[] = $temp_array;


						}
						$descripcion = $lista[0]["descripcion"];
						$this->add_list_formulas($key, $formula_array, $descripcion);
					}
					else {
						$lista[0]["variables"] = ($lista[0]["variables"] !== NULL)?json_decode($lista[0]["variables"],true):NULL;
						$this->add_list_formulas($key, $lista[0]["formula"], $lista[0]["descripcion"], $lista[0]["variables"], $lista[0]["condicional"], $lista[0]["id_formula"]);
					}
				}


			}
		} catch (Exception $e) {

			$this->calc_error = "Error al actualizar la lista de formulas almacenadas";
		}
		finally{
			//$this->con = null;
			$consulta = null;
		}
	}

	PRIVATE function calc_groups($formula_array){












		if($this->counter_loop>200){
			throw new Exception("Error loop process", 1);
		}
		else{
			$this->counter_loop++;
		}

		$new_formula_array = [];

		$end = count($formula_array);
		$i = 0;
		$ignore = 0;
		$open[0] = false; // se abre un grupo
		$close[0] = false; // se cierra un grupo

		$group_found = [];

	
		for ($i;$i<$end;$i++){



			$token_switch = $token = $formula_array[$i];

			$token_switch = strval($token_switch);
			



			

			if($open[0] === false ){ // si todavia no ha encontrado un grupo
				switch ($token_switch) {
					case '(':
						$open[0] = '('; // abre el grupo
						$open[1] = $i; // guarda donde abrio
						$close[0] = ')'; // guarda como cierra
						
						break;
					case '[':
						$open[0] = '[';
						$open[1] = $i;
						$close[0] = ']';
						break;
					case '{':
						$open[0] = '{';
						$open[1] = $i;
						$close[0] = '}';
						break;
					
					default: // si no esta abierto ningun grupo y no encuentra apertura
						$new_formula_array[] = $token;
				}
				

			}
			else{ // si esta abierto un grupo
				


				if($token == $open[0]){ // si por ejemplo esta abierto un grupo con "(" pero encuentra otro "(" abierto
					$ignore++;
					$group_found[] = $token; // lo guarda y se prepara para ignorar el cierre del mismo
				}
				else if ($token == $close[0]){ // si encuentra el cierre
					if($ignore>0){ // si hay que ignorar algun cierre
						$ignore--;
						$group_found[] = $token;
					}
					else{// si no hay nada que ignorar
						if(count($group_found)<=0){
							throw new Exception("Las agrupaciones no pueden estar vaciás, existe una agrupación de tipo '".$open[0].$close[0]."' ", 103);
							
						}
						$open[0] = false; // elimina la apertura 
						$close[0] = false; // elimina el cierre 
						$close[1] = $i; // guarda donde cerro
					}
				}
				else{
						$group_found[] = $token;
				}
				

				if($open[0] === false){ // si se abrio un grupo pero ahora esta cerrado

					$group_found = $this->calc_groups($group_found); // reviso si hay otro grupo entre el grupo abierto antes


					if($open[1] == 0 and $close[1] == ($end - 1)){ // si el grupo coincide con todo el array
						$new_formula_array =  array_merge($new_formula_array,$group_found);
					}
					else{
						$new_formula_array[] = $group_found;
					}
					
				}
			}

			//echo "<pre>$i < $end and $open[0]</pre>";



		}
		if($open[0] !== false){
			throw new Exception("Se encontro un`'".$open[0]."' sin cerrar", 1);
		}

		return $new_formula_array;
	}


	PUBLIC function get_calc_reserved_words(){
		$lista = [];
		foreach ($this->calc_f as $key => $value) {
			$lista[] = ["name" => $key,"descrip"=> $value->descrip];
		}

		foreach ($this->calc_list_formulas as $key => $value) {
			$temp = ["name" => $key,"descrip" => $value["descrip"]];

			if(isset($value["id_formula"])){
				$temp["id"] = $value["id_formula"];
			}
			$lista[] = $temp;
		 }
		return $lista;
	}










	PUBLIC function calc_nodes(&$formula_array = '' ,$formula=null){ // convierte todos los numero y los operadores en nodos
		$anterior = false;


		for($i=0;$i<count($formula_array);$i++){
			unset($token);
			$token = &$formula_array[$i];




			if(!$anterior){
				$anterior=true;
				$valor = $token;
				$token = new calc_nodo($token,true,$i);
				$token->formula = $formula;
				$token->set_value($valor);

				if(count($formula_array)==1){
					$token->set_unique(true);
				}
			}
			else{
				if( is_numeric( $formula_array[($i-1)]->get_value() ) and is_numeric($formula_array[$i]) ){// si encuentra dos numeros seguidos es resultado de una agrupacion similar a 2(5+3) o algo como 3x
					unset($token);
					array_splice($formula_array, $i,0,"*" );
					$i--;
				}
				else if($formula_array[($i-1)]->is_leaft()){
					$valor = $token;
					$token_anterior = &$formula_array[($i-1)];
					$token = new calc_nodo($token_anterior,$token,null,$i);
					$token->formula = $formula;
					$token_anterior->add_operador($token);
					$token->set_value($valor);

					$token->set_nodo_anterior($token_anterior);

				}
				else{
					$valor = $token;
					$token = new calc_nodo($token,true,$i);
					$token->formula = $formula;
					$token->set_value($valor);
					$formula_array[($i-1)]->add_right($token);
					$token->set_nodo_anterior($formula_array[($i-1)]);
				}


			}
		}
	}

	PUBLIC function resolve_nodes(&$formula_array = ''){// llama a que los nodos se resuelvan
		if($formula_array == ''){
			unset($formula_array);

			$formula_array = &$this->calc_items;
		}
		$formula_array[0]->resolver("*");
		$formula_array[0]->resolver("/");
		$formula_array[0]->resolver("+");
		$formula_array[0]->resolver("-");

		return $formula_array[0]->get_total();
	}






	PUBLIC function set_calc_function($name,$descrip,$func,$arguments,$cache=false){//nombre de la funcion, la funcion misma, si tendra argumentos de la funcion si o no BOOL(no se usa),y si guarda el resultado en cache
		$this->calc_f->{$name} = new calc_functions($name,$descrip,$func,$arguments,$cache);
	}

	PUBLIC function get_calc_function($name){
		if(isset($this->calc_f->{$name})){
			return $this->calc_f->{$name}->execute();
		}
		else{
			return null;
		}
	}





	PUBLIC function set_calc_formula(&$value){
		$value = preg_replace("/\s+/", "", $value);
	}

	PUBLIC function get_var($key,$lista){
		if($lista === null){
			$lista = [];
		}
		if(is_array($lista)){
			if(isset($lista[$key])){
				$resp = $lista[$key];


				if($resp == '__!__'){
					throw new Exception("La variable '$key' no puede ser utilizada al calcular la variable '$key' ", 1);
				}

				$operadores_formula_var = implode("|", $this->calc_diff_var_formula);



				if(preg_match("/$operadores_formula_var/", $resp)){// si tiene operadores es una formula

					$temp_variables=$lista;

					$temp_variables[$key] = "__!__";

					$resp = $this->leer_formula($resp,$temp_variables,false,$key);

					$resp = $resp["total"];

				}






				else if(preg_match("/[a-zA-Z]+(?:[_]*[a-zA-Z]*)?/", $resp )){// si tiene letras es una variable o función
					$temp = $resp;

					if(isset($lista[$temp])){
						$resp = $this->get_var($temp,$lista);
					}
					else if( ($func_resp = $this->get_calc_function($temp) ) !== null){
						$resp = $func_resp;
					}
					else{
						throw new Exception("La variable/function '$temp' no existe", 118);
					}
				}
				return $resp;
			}
			else{
				return false;
			}
		}
		else{
			show_varx($lista);
			throw new Exception("la lista de variables debe ser un arreglo", 1);
			
		}





		// if( isset($this->calc_var->{$key}) ){
		// 	$resp = $this->calc_var->{$key};
		// 	if(preg_match("/[a-zA-Z]+(?:[_]*[a-zA-Z]*)?/", $resp )){
		// 		$temp = $this->calc_var->{$key};

		// 		if(isset($this->calc_var->{$temp})){
		// 			$resp = $this->get_var($temp);
		// 		}
		// 		else if($func_resp = $this->get_calc_function($temp)){
		// 			$resp = $func_resp;
		// 		}
		// 		else{
		// 			throw new Exception("La variable/function '$temp' no existe", 118);
		// 		}




		// 	}

		// 	return $resp;
		// }
		// else{
		// 	return false;
		// }
	}
	PUBLIC function get_all_var($lista){
		if(is_array($lista)){
			return json_encode($lista);
		}
		else{
			return null;
		}
		// $found = false;
		// foreach ($this->calc_var as $elem) {
		// 	$found = true;
		// 	break;
		// }
		// if($found){
		// 	return json_encode($this->calc_var);
		// }
		// else{
		// 	return null;
		// }
	}
	PUBLIC function add_var($key,$value,&$lista=null){
		if(!isset($this->calc_f->{$key}) and !isset($this->calc_list_formulas->{$key})){
			if($lista===null){
				$lista = [];
			}

			$lista[$key] = $value;
			return $lista;
		}
		else{
			$this->calc_error = "El nombre de la variable '$key' no puede ser utilizada, es una variable/función del sistema reservada" ;
			return [];
		}
	}


	PUBLIC function get_lista_trabajadores(){
		try {
			$this->validar_conexion($this->con);
			$this->con->beginTransaction();
			
			$consulta = $this->con->prepare("SELECT id_trabajador as id, CONCAT(nombre,' ',apellido) as nombre FROM trabajadores WHERE estado_actividad = true;");
			$consulta->execute();
			
			$r['resultado'] = 'get_lista_trabajadores';
			$r['mensaje'] =  $consulta->fetchall(PDO::FETCH_ASSOC);
		
		} catch (Exception $e) {
			if($this->con instanceof PDO){
				if($this->con->inTransaction()){
					$this->con->rollBack();
				}
			}
		
			$r['resultado'] = 'error';
			$r['titulo'] = 'Error';
			$r['mensaje'] =  $e->getMessage();
			//$r['mensaje'] =  $e->getMessage().": LINE : ".$e->getLine();
		}
		finally{
			//$this->con = null;
			$consulta = null;
		}
		return $r;
	}

	PUBLIC function calc_guardar_formula($formula, $nombre, $descripcion, $variables=NULL,$condicional=NULL,$orden=0,$commit_on_end=false, $lanzar_error=false){
		$before_transaction = true;
		try {
			$this->validar_conexion($this->con);
			if(!$this->con->inTransaction()){
				$this->con->beginTransaction();
				$before_transaction = false;
			}


			if(isset($this->get_calc_reserved_words()[$nombre])){
				throw new Exception("El nombre de la formulas '$nombre' ya existe o es una palabra reservada del sistema", 1);
			}


			$consulta = $this->con->prepare("SELECT 1 FROM formulas WHERE nombre = ?;"); // reviso el nombre en la bd
			$consulta->execute([$nombre]);

			if($consulta->fetch()){
				throw new Exception("Error Processing Request", 23000);
			}
			$consulta = null;



			$consulta = $this->con->prepare("INSERT INTO formulas  (nombre, descripcion) VALUES (?,?)");
			$consulta->execute([$nombre, $descripcion]);

			$last = $this->con->lastInsertId();

			$consulta = null;

			$consulta = $this->con->prepare("INSERT INTO detalles_formulas (id_formula, formula, variables, condicional, orden) VALUES (:id_formula, :formula, :variables, :condicional, :orden) ");
			$consulta->bindValue(":id_formula",$last);
			$consulta->bindValue(":formula",$formula);
			$consulta->bindValue(":variables",$variables);
			$consulta->bindValue(":condicional",$condicional);
			$consulta->bindValue(":orden",$orden);

			$consulta->execute();






			// code
			
			$r['resultado'] = 'calc_guardar_formula';
			$r['formula'] =  $last;
			if($before_transaction === false or $commit_on_end === true){
				if($this->con->inTransaction()){
					$this->con->commit(); //TODO poner esto
				}
			}
		
		} catch (Exception $e) {
			if($before_transaction !== true){
				if($this->con instanceof PDO){
					if($this->con->inTransaction()){
						$this->con->rollBack();
					}
				}
			}

			$r['resultado'] = 'error';
			$r['titulo'] = 'Error';
			$r['mensaje'] =  $e->getMessage();
			$r['code'] = $e->getCode();

			if($e->getCode()==23000){
				$r['mensaje'] = "La formula con el nombre '$nombre' ya existe";
				if($lanzar_error){
					throw new Exception("La formula con el nombre '$nombre' ya existe", 1);
					
				}
			}

			if($lanzar_error){
				throw $e;
			}

		
		}
		finally{
			$consulta = null;
		}
		return $r;
	}

	PUBLIC function calc_guardar_formula_lista($formulas, $nombre,$descripcion,$lanzar_error = false,$commit_on_end=true){
		$before_transaction = true;
		try {
		 	$this->validar_conexion($this->con);
			if(!$this->con->inTransaction()){
				$this->con->beginTransaction();
				$before_transaction = false;
			}
			if(!is_array($formulas)){
				throw new Exception("La lista de formulas debe de ser un array", 1);
			}
			if(isset($this->get_calc_reserved_words()[$nombre])){
				throw new Exception("El nombre de la formulas '$nombre' ya existe o es una palabra reservada del sistema", 1);
			}

			$consulta = $this->con->prepare("SELECT 1 FROM formulas WHERE nombre = ?;"); // reviso el nombre en la bd
			$consulta->execute([$nombre]);

			if($consulta->fetch()){
				throw new Exception("La formula con el nombre '$nombre' ya existe", 1);
			}
			$consulta = null;

			$consulta = $this->con->prepare("INSERT INTO formulas  (nombre, descripcion) VALUES (?,?)");
			$consulta->execute([$nombre, $descripcion]);

			$last = $this->con->lastInsertId();

			$query = "INSERT INTO detalles_formulas (id_formula, formula, variables, condicional, orden) VALUES ";
			$values_holder = array_fill(0, count($formulas), "(?,?,?,?,?)");
			$values_holder = implode(',', $values_holder);

			$query.=$values_holder;

			$consulta = $this->con->prepare($query);
			$i=1;
			foreach ($formulas as $elem) {
				$consulta->bindValue($i++,$last);
				$consulta->bindValue($i++,$elem["formula"]);
				if(isset($elem["variables"])){
					$elem["variables"] = json_encode($elem["variables"]);
				}
				else{
					$elem["variables"] = NULL;	
				}
				$consulta->bindValue($i++,$elem["variables"]);
				$consulta->bindValue($i++,$elem["condiciones"]);
				$consulta->bindValue($i++,$elem["orden"]);
			}

			$consulta->execute();

		 	// code
		 	
		 	$r['resultado'] = 'calc_guardar_formula_lista';
		 	$r['titulo'] = 'Éxito';
		 	$r['mensaje'] =  "";
		 	//$this->con->commit();

		 	if($before_transaction === false or $commit_on_end === true){
		 		if($this->con->inTransaction()){
		 			$this->con->commit(); //TODO poner esto
		 		}
		 	}
		 
		 } catch (Exception $e) {
		 	if($lanzar_error===false){
		 		if($before_transaction === false or $commit_on_end === true){
				 	if($this->con instanceof PDO){
				 		if($this->con->inTransaction()){
				 			$this->con->rollBack();
				 		}
				 	}
		 		}
		 	}
		 	else{
		 		$consulta = null;
		 		throw new $e;
		 	}
		 
		 	$r['resultado'] = 'error';
		 	$r['titulo'] = 'Error';
		 	$r['mensaje'] =  $e->getMessage();
		 	$r["path"] = $e->getTraceAsString();
		 	//$r['mensaje'] =  $e->getMessage().": LINE : ".$e->getLine();
		 }
		 finally{
		 	//$this->con = null;
		 	$consulta = null;
		 }
		 return $r; 
	}


}



/**
 * 
 */


class calc_nodo{
	PRIVATE $left,$right,$operador,$leaft,$orden,$resolved,$value,$nodo_anterior,$total;
	PRIVATE $unique;
	PUBLIC $formula;
	PUBLIC function __construct($left,$operador,$right=null,$orden=null)
	{

		if(is_numeric($operador)){
			$right = $operador;
			$operador = "*"; 
		}
		$this->resolved = false;

		if((!$left instanceof calc_nodo) and (!is_numeric($left))){throw new Exception("$left no es un numero valido para calcular L", 1);}
		if($right !=null and(!$right instanceof calc_nodo) and (!is_numeric($right))){throw new Exception("$right no es un numero valido para calcular R", 1);}
		$this->leaft = false;// es hoja
		$this->left = $left;
		$this->operador = $operador;
		$this->right = $right;
		$this->orden = $orden;
		$this->total = 0;
		$this->nodo_anterior = null;
		$this->unique = false;

		if($operador ===true){
			$this->leaft=true;
			$this->orden = $right;

			$this->right=null;
			$this->operador=null;
		}
	}

	PUBLIC function is_leaft(){
		return $this->leaft;
	}
	PUBLIC function add_right($token){
		$this->right=$token;
	}
	PUBLIC function add_operador($token){
		$this->operador=$token;
	}

	PUBLIC function get_operador(){
		return $this->operador;
	}

	PUBLIC function get_value(){
		return $this->value;
	}
	PUBLIC function set_value($value){
		$this->value = $value;
	}

	PUBLIC function resolver($control){

		if($this->unique){
			$valor = $this->get_value();
			if(is_numeric($valor)){

				$valor = floatval(number_format($valor,2,".",""));
				$this->set_total($valor);
				$this->set_resolved();
			}
			return true;
		}
		
		if(!$this->is_leaft() and $this->value == $control){


			$numeros_decimales = 2;



			switch ($this->value) {
				case '*':
					$left = ($this->left->resolved)?$this->left->total:$this->left->value;
					$right = ($this->right->resolved)?$this->right->total:$this->right->value;
					$total = floatval(number_format($left, $numeros_decimales, '.', '')) * floatval(number_format($right,$numeros_decimales,'.',''));
					$this->set_total($total);
					$this->set_resolved();
					$this->right->resolver($control);
					break;
				case '/':

					$left = ($this->left->resolved)?$this->left->total:$this->left->value;
					$right = ($this->right->resolved)?$this->right->total:$this->right->value;
					if($right == 0){
						throw new Exception("No se puede dividir entre cero en la posicion (".($this->orden + 1).") de la formula '$this->formula'", 1);
					}
					$total = floatval(number_format($left, $numeros_decimales, '.', '')) / floatval(number_format($right,$numeros_decimales,'.',''));
					$this->set_total($total);
					$this->set_resolved();
					$this->right->resolver($control);
					break;
				case '+':
					$left = ($this->left->resolved)?$this->left->total:$this->left->value;
					$right = ($this->right->resolved)?$this->right->total:$this->right->value;
					$total = floatval(number_format($left,$numeros_decimales,'.','')) + floatval(number_format($right,$numeros_decimales,'.',''));
					$this->set_total($total);
					$this->set_resolved();
					$this->right->resolver($control);
					break;
				case '-':
					$left = ($this->left->resolved)?$this->left->total:$this->left->value;
					$right = ($this->right->resolved)?$this->right->total:$this->right->value;
					$total = floatval(number_format($left,$numeros_decimales,'.','')) - floatval(number_format($right,$numeros_decimales,'.',''));
					$this->set_total($total);
					$this->set_resolved();
					$this->right->resolver($control);
					break;
				
				default:
					throw new Exception("El operador (*,/,+,-) no es valido en la posicion ($this->orden)", 1);
			}
		}
		else if($this->operador instanceof calc_nodo){

			//echo "<pre>\nENTRO AQUI\n</pre>";
			$this->operador->resolver($control);
		}
		else if(!$this->is_leaft()){
			switch ($this->value) {
				case '*':
				case '+':
				case '-':
				case '/':
					$this->right->resolver($control);
					break;
				case ')':
				case ']':
				case '}':
					throw new Exception("hay un '$this->value' de cierre sin una apertura del mismo", 1);
				default:
					throw new Exception("El operador '$this->value' no es valido", 1);
				
			}
		}


		if($this->is_leaft() and $this->resolved !== true and $this->left===null and $this->right===null and $this->operador === null){

			$temp = ($this->is_leaft())?"true":"false";
			throw new Exception("Ocurrio un error con el nodo $this->orden is_leaft =  $temp valor = $this->value", 1);
			
		}
		
	}

	private function set_resolved(){
		if($this->left instanceof calc_nodo) $this->left->resolved = true;
		if($this->right instanceof calc_nodo) $this->right->resolved = true;
		if($this->operador instanceof calc_nodo) $this->operador->resolved = true;
		$this->resolved = true;
	}

	PUBLIC function get_resolved(){
		return ($this->resolved)?"RESUELTO":"NO RESUELTO";
	}

	// PUBLIC function mostrar_entorno(){	
	// 	$lista=[];

	// 	$lista["yo"] = $this->value;
	// 	$lista["hoja"] = $this->is_leaft();

	// 	$lista["left"] = (isset($this->left->value))?$this->left->value:NULL;
	// 	$lista["right"] = (isset($this->right->value))?$this->right->value:NULL;
	// 	$lista["operador"] = (isset($this->operador->value))?$this->operador->value:NULL;


	// 	ob_start();
		
	// 	echo "<pre>\n entorno Nodo ($this->orden)\n\n";
	// 	var_dump($lista);
	// 	echo "</pre>";
		
	// 	$valor = ob_get_clean();

	// 	//$valor = preg_replace("/\n+(?=string|float)/", "   ", $valor);
	// 	$valor = preg_replace("/=>\n+/", "=>   ", $valor);
	// 	$valor = preg_replace("/(string\(\d+\))\s+\"(.*)\"/", "$2   $1", $valor);
	// 	$valor = preg_replace("/(float\((\d+)\))/", "$2   $1", $valor);
	// 	echo $valor;;

	// }

	PUBLIC function set_nodo_anterior($nodo){
		if($nodo instanceof calc_nodo){
			$this->nodo_anterior = $nodo;
		}
		else{
			throw new Exception("El nodo anterior no es correcto ($this->orden)", 1);
		}
	}

	PUBLIC function get_nodo_anterior(){
		return (isset($this->nodo_anterior)) ? $this->nodo_anterior:NULL;
	}

	PUBLIC function set_total($total,$modificados=false){


		if($modificados===false){
			$modificados = new stdClass();
		}

		$modificados->{"nodo".$this->orden} = true;

		if((!$this->is_leaft()) and $this->resolved === false){
			$this->left->set_total($total, $modificados);
			$this->right->set_total($total, $modificados);
		}
		else{

			if( ($this->left instanceof calc_nodo) and $this->left->resolved === true and ( !isset( $modificados->{"nodo".$this->left->orden} ) ) ){
				$this->left->set_total($total, $modificados);
			}
			if(($this->operador instanceof calc_nodo) and $this->operador->resolved === true and ( !isset( $modificados->{"nodo".$this->operador->orden} ) ) ){
				$this->operador->set_total($total, $modificados);
			}
			if(($this->right instanceof calc_nodo) and $this->right->resolved === true and ( !isset( $modificados->{"nodo".$this->right->orden} ) ) ){
				$this->right->set_total($total, $modificados);
			}
			if(($this->nodo_anterior instanceof calc_nodo) and $this->nodo_anterior->resolved === true and ( !isset( $modificados->{"nodo".$this->nodo_anterior->orden} ) ) ){
				$this->nodo_anterior->set_total($total, $modificados);
			}
		}
		$this->total = $total;

	}
	PUBLIC function get_total(){
		return $this->total;
	}

	PUBLIC function get_unique(){
		return $this->unique;
	}
	PUBLIC function set_unique($value){
		$this->unique = $value;
	}





}

class calc_functions
{
	PRIVATE $name, $func, $arguments, $cache, $control_cache;
	PUBLIC $descrip;
	function __construct($name,$descrip,$func,$arguments=false,$control_cache=false)
	{
		$this->name = $name;
		$this->func = $func;
		$this->arguments = $arguments;
		$this->cache = null;
		$this->control_cache=$control_cache;
		$this->descrip = $descrip;
	}

	PUBLIC function execute($arguments=null){

		if($this->control_cache===true and isset($this->cache)){
			$resp = $this->cache;
		}
		else{

			if($this->arguments){
			if(count($arguments)>0){
				foreach ($arguments as $elem) {
					if(!is_numeric($elem)){
						throw new Exception("Las funciones solo permiten números entre sus argumentos ($this->name)", 1);
					}
				}
				$f_temp = $this->func;
				$resp = $f_temp();
			}
			else{
				throw new Exception("Argumentos esperados en la la función '$this->name'", 1);
				}
			}
			else{
				$f_temp = $this->func;
				$resp = $f_temp();
			}

			if(!is_numeric($resp)){
				throw new Exception("La respuesta de la funcion '$this->name' debe ser un numero", 1);
			}
			if($this->control_cache===true){
				$this->cache = $resp;
			}
		}
		return $resp;
	}
	PUBLIC function has_arguments(){
		if($this->arguments){
			return true;
		}
		else{
			return false;
		}
	}
	PUBLIC function get_name(){
		return $this->name;
	}

	PUBLIC function cl_cache(){
		$this->cache=null;
	}
}



function show_varx($var,$ret = true,$frase = ""){ // TODO quitar estos
	ob_start();
	
	echo "<pre>\n";
	var_dump($var);
	if($frase!=''){
		echo "\n$frase\n";
	}
	echo "</pre>";
	
	$valor = ob_get_clean();
	
	$r["resultado"] = "console";
	$r["mensaje"] = $valor;

	if($ret==true){
		echo json_encode($r);
		exit;
	}
	else{
		echo $valor; exit;
	}
}


function calc_filter_vacio($string){
	if(!preg_match("/^\s*$/", $string)){
		return true;
	}
	else{
		return false;
	}
}

 ?>