<?php 

class Clase_prueba2 extends Conexion
	{
		PRIVATE $id_trabajador;
		use Calculadora2;
		function __construct($con = '')
		{
			$this->id_trabajador = 2;
			if(!($con instanceof PDO)){
				$this->con = $this->conecta();
			}
			else{
				$this->con = $con;
			}
			$this->calc_init();

		}

		PUBLIC function get_id_trabajador(){
			return $this->id_trabajador;
		}
		PUBLIC function set_id_trabajador($value){
			$this->id_trabajador = $value;
		}


		
	}

 ?>