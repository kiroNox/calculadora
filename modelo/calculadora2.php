<?php 
trait Calculadora2{


	PRIVATE $calc_status = false;
	PRIVATE $calc_var;
	PRIVATE $calc_f;
	PRIVATE $calc_formula;
	PRIVATE $calc_separadores;
	PRIVATE $calc_posicion;
	PRIVATE $calc_items;
	PRIVATE $counter_loop;
	PRIVATE $calc_error;



	PUBLIC function calc_init(){
		$this->calc_var = new stdClass();
		$this->calc_f = new stdClass();
		$this->calc_var->vacio=true; // TODO quitar esto
		$this->calc_error = null;

		$this->calc_separadores[] = '\\+';
		$this->calc_separadores[] = '\\-';
		$this->calc_separadores[] = '\\/';
		$this->calc_separadores[] = '\\*';
		$this->calc_separadores[] = '\\(';
		$this->calc_separadores[] = '\\)';
		$this->calc_separadores[] = '\\[';
		$this->calc_separadores[] = '\\]';
		$this->calc_separadores[] = '\\{';
		$this->calc_separadores[] = '\\}';
		$this->calc_separadores[] = '[a-zA-Z]+(?:[_]*[a-zA-Z]*)?';
		$this->calc_separadores[] = '[0-9]+([\\.][0-9]+)?';
		$this->calc_separadores[] = '__.*__';

		$this->calc_posicion = 0;
		$this->counter_loop = 0;

		$this->calc_status = true;

		$this->add_calc_system_function();

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
		$this->set_calc_function("TIEMPO_TRABAJADOR",$fun,false);

		$fun = function(){
			return $this->id_trabajador;
		};
		$this->set_calc_function("GALLETAS",$fun,false);

		// otras funciones....
	}



	PUBLIC function leer_formula($string){

		try {
			$this->calc_check_status(); // check constructor
			$this->set_calc_formula($string); // guardo la formula original
			$this->calc_separador(); // separo los elementos
			$this->calc_variables(); // remplazo las variables
			//$this->calc_items = $this->calc_functions(); // evalua las funciones del sistema
			$this->calc_items = $this->calc_groups(); // asigno los grupos

			$r["total"] = $this->resolve_groups();
			$r["formula"] = $this->calc_formula;
			//$this->calc_nodes();
			//$r["total"] = $this->resolve_nodes();

			//$this->calc_resolve();


			

			
			//$r[] = $this->calc_formula;

			if(!$this->calc_var->vacio){
				$r["variables"] = $this->get_all_var();
			}

			//$r[] = $this->calc_items;

		} catch (Exception $e) {
			if($this->con instanceof PDO){
				if($this->con->inTransaction()){
					$this->con->rollBack();
				}
			}
			
			$r['resultado'] = 'error';
			$r['titulo'] = 'Error';
			$r["formula"] = $string;

			if($e->getCode() == 118){
				$r['mensaje'] =  $e->getMessage();
			}
			else{
				$r['mensaje'] =  "La formula no pudo ser calculada";
				$r["console"] = $e->getMessage();
				$r["calc_error"] = $e->getCode();
				// 103 = Las agrupaciones no pueden estar vaciás
				// 118 = La variable/function ... no existe
			}

			//$r["path"] = $e->getTrace();
			$r["path2"] = $e->getTraceAsString();
			$r["lista"] = $this->calc_items;
		}
		return $r;

	}

	PUBLIC function resolve_groups($formula_array = ''){
		if($formula_array ==''){
			$formula_array = $this->calc_items;
		}



		for($i=0;$i<count($formula_array);$i++){
			$token = &$formula_array[$i];
			if(is_array($token)){
				$token = $this->resolve_groups($token);
			}
		}

		$this->calc_nodes($formula_array);
		return $this->resolve_nodes($formula_array);

	}

	PRIVATE function calc_check_status(){
		if(!$this->calc_status) throw new Exception("Calculadora no inicializada", 1);
		if($this->calc_error) throw new Exception($this->calc_error, 1);
	}

	PRIVATE function calc_separador (){
		try {
			$string = $this->calc_formula;


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
			
			$r = array_filter($r);

			

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

			$this->calc_items = $r;





			//$r = $string;
			
		} catch (Exception $e) {

			throw $e;
		}
	}


	PRIVATE function calc_variables(){
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

		foreach ($this->calc_items as $key => $value) {
			if(preg_match("/[a-zA-Z]+(?:[_]*[a-zA-Z]*)?/", $this->calc_items[$key] )){
				if($temp = $this->get_calc_function($this->calc_items[$key],$key)){
					$this->calc_items[$key] = $temp;
					continue;
				}

				else if($temp = $this->get_var($this->calc_items[$key])){ // si es una variable del usuario
					$temp = floatval(number_format($temp,2,'.',''));

					$temp_key = intval($key);

					if (isset($this->calc_items[$temp_key - 1]) and preg_match("/^\+$ | ^\-$|/", $this->calc_items[$temp_key - 1])){
						$anterior = $this->calc_items[$temp_key - 1];
						// si el anterior al actual existe y si es un signo de "+" o "-"

						


						if ((isset($this->calc_items[$temp_key - 2]) and preg_match("/$operador/", $this->calc_items[$temp_key - 2])) or ($temp_key - 2) < 0 ){
							if(($temp < 0 or $temp > 0 ) and $anterior == '-'){

								$temp = -1 * $temp;

								$this->calc_items[$key] = $temp;
								unset($this->calc_items[$temp_key - 1]);
								$this->calc_items = array_values($this->calc_items);
								continue;
							}
						}
					}






					$this->calc_items[$key] = $temp;
					continue;
				}
				else{
					
					throw new Exception("La variable/function '".$this->calc_items[$key]."' no existe", 118);
					
				}
			}
		}
	}

	PRIVATE function calc_groups($formula_array = ''){




		if($this->counter_loop>200){
			throw new Exception("Error loop process", 1);
		}
		else{
			$this->counter_loop++;
		}

		$new_formula_array = [];

		if($formula_array == ''){
			$formula_array = $this->calc_items;
		}

		$end = count($formula_array);
		$i = 0;
		$ignore = 0;
		$open[0] = false; // se abre un grupo
		$close[0] = false; // se cierra un grupo

		$group_found = [];

	
		for ($i;$i<$end;$i++){



			$token = $formula_array[$i];

			

			if($open[0] === false ){ // si todavia no ha encontrado un grupo
				switch ($token) {
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
						array_merge($new_formula_array,$group_found);
					}
					else{
						$new_formula_array[] = $group_found;
					}
					
				}
			}

			//echo "<pre>$i < $end and $open[0]</pre>";

			if((!($i<$end)) and $open[0] !== false){
				throw new Exception("Se encontro un`'".$open[0]."' sin cerrar", 1);
			}


		}
		return $new_formula_array;
	}










	PUBLIC function calc_nodes(&$formula_array = '' ){ // convierte todos los numero y los operadores en nodos
		$replace_array = false;

		if($formula_array == ''){
			unset($formula_array);
			$formula_array = $this->calc_items;
			$replace_array = true;
		}
		$anterior = false;
		for($i=0;$i<count($formula_array);$i++){
			unset($token);
			$token = &$formula_array[$i];

			if(!$anterior){
				$anterior=true;
				$valor = $token;
				$token = new calc_nodo($token,true,$i);
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
					$token_anterior->add_operador($token);
					$token->set_value($valor);

					$token->set_nodo_anterior($token_anterior);

				}
				else{
					$valor = $token;
					$token = new calc_nodo($token,true,$i);
					$token->set_value($valor);
					$formula_array[($i-1)]->add_right($token);
					$token->set_nodo_anterior($formula_array[($i-1)]);
				}


			}
		}

		if($replace_array){
			$this->calc_items = $formula_array;
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






	PUBLIC function set_calc_function($name,$func,$arguments){//nombre de la funcion, la funcion misma, si tendra argumentos de la funcion si o no BOOL
		$this->calc_f->{$name} = new calc_functions($name,$func,$arguments);
	}

	PUBLIC function get_calc_function($name,$key_item){


		if(isset($this->calc_f->{$name})){
			return $this->calc_f->{$name}->execute();


			// if($this->calc_f->{$name}['arguments']){// si los argumentos estan habilitados bool true
			// 	if($arguments !== null){ // y los argumentos pasados no son nulos 
			// 		$resp = $this->calc_f->{$name}["func"]($arguments);//ejecuta la funcion con los argumentos
			// 		if(!is_numeric($resp)){
			// 			throw new Exception("La respuesta de la funcion '".$this->calc_f->{$name}["name"]."' debe ser un numero", 1);
						
			// 		}
			// 	}
			// 	else{
			// 		throw new Exception("Argumentos esperados en la la funcion '".$this->calc_f->{$name}["name"]."'", 1);
			// 	}
			// }
			// else{

			// 	$resp = $this->calc_f->{$name}["func"]();//ejecuta la funcion sin los argumentos
			// 	if(!is_numeric($resp)){
			// 		throw new Exception("La respuesta de la funcion '".$this->calc_f->{$name}["name"]."' debe ser un numero", 1);
					
			// 	}
			// }

		}
		else{
			return null;
		}
	}





	PUBLIC function set_calc_formula($value){
		$this->calc_formula = preg_replace("/\s+/", "", $value);
	}

	PUBLIC function get_var($key){
		if( isset($this->calc_var->{$key}) ){
			return $this->calc_var->{$key};
		}
		else{
			return false;
		}
	}
	PUBLIC function get_all_var(){
		return json_encode($this->calc_var);
	}
	PUBLIC function add_var($key,$value){
		if(!isset($this->calc_f->{$key})){
			$this->calc_var->{$key} = $value;
		}
		else{
			$this->calc_error = "La variable '$key' no puede ser utilizada, es una variable/función del sistema reservada" ;
		}
	}


}

/**
 * 
 */


class calc_nodo{
	PRIVATE $left,$right,$operador,$leaft,$orden,$resolved,$value,$nodo_anterior,$total;
	PRIVATE $unique;
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
						throw new Exception("No se puede dividir entre cero en la posicion ($this->orden)", 1);
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
				
				default:
					throw new Exception("El operador ($this->value) no es valido", 1);
				
			}
		}

		if($this->is_leaft() and $this->resolved != true and $this->left==null and $this->right==null and $this->operador == null){




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

	PUBLIC function mostrar_entorno(){	
		$lista=[];

		$lista["yo"] = $this->value;
		$lista["hoja"] = $this->is_leaft();

		$lista["left"] = (isset($this->left->value))?$this->left->value:NULL;
		$lista["right"] = (isset($this->right->value))?$this->right->value:NULL;
		$lista["operador"] = (isset($this->operador->value))?$this->operador->value:NULL;


		ob_start();
		
		echo "<pre>\n entorno Nodo ($this->orden)\n\n";
		var_dump($lista);
		echo "</pre>";
		
		$valor = ob_get_clean();

		//$valor = preg_replace("/\n+(?=string|float)/", "   ", $valor);
		$valor = preg_replace("/=>\n+/", "=>   ", $valor);
		$valor = preg_replace("/(string\(\d+\))\s+\"(.*)\"/", "$2   $1", $valor);
		$valor = preg_replace("/(float\((\d+)\))/", "$2   $1", $valor);
		echo $valor;;

	}

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

		echo "<pre>\nModifico el total del nodo ($this->orden)\n</pre>";

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


/**
 * 
 */
class calc_functions
{
	PRIVATE $name, $func, $arguments;
	function __construct($name,$func,$arguments=false)
	{
		$this->name = $name;
		$this->func = $func;
		$this->arguments = $arguments;
	}

	PUBLIC function execute($arguments=null){
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
}







function show_varx($var,$frase = "function"){ // TODO quitar estos
	ob_start();
	
	echo "<pre>\n\n";
	var_dump($var);
	echo "\n||||$frase||||\n </pre>";
	
	$valor = ob_get_clean();
	
	
	echo $valor;
}

 ?>