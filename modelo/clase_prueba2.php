<?php 

class Clase_prueba2 extends Conexion
	{
		use Calculadora2;
		function __construct($con = '')
		{
			if(!($con instanceof PDO)){
				$this->con = $this->conecta();
			}
			else{
				$this->con = $con;
			}
			$this->calc_init();

		}
		
	}

 ?>