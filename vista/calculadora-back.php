<!DOCTYPE html>
<html lang="es">
<head>
	<?php require_once 'assets/comun/head.php'; ?>
	<title>calculadora - back</title>
</head>


<style>
	.suggestions{
		position: absolute;
		width: 100%;
		background-color:#fff;
		padding: 0!important;
		border: 1px solid #d1d3e2;
		border-bottom-right-radius: 3px;
		border-bottom-left-radius: 3px;
		box-shadow: 0 3px 4px -2px #898989;
		overflow: hidden;
		z-index: 5;
		display: none;
	}
	.suggestions.open{
		display: block;
		box-shadow: 0 0 0 black;
		max-height: 200px;
		overflow: auto;
		padding: .3rem;
	}
	.suggestion-option{
		width: 100%;
		color: black;
		background-color: white;
		padding: .3rem;
	}
	.suggestion-option:hover,
	.suggestion-option:focus{
		background-color: #4e73df;
		color: #fff;
	}


	.lista-variables{
		max-height: 0;
		overflow: hidden;
		transition: .6s max-height;
	}
	.lista-variables.open{
		max-height: 500px;
		overflow: auto;
	}
	div[id^=calc_lista-condicion-]{
		border-bottom: 1px solid var(--info);
		padding-bottom: 1rem;
		margin-bottom: 1rem;
	}

		#formulario_calc_lista_condicionales .lista-variables.open,
		.lista-variables{
			max-height: 0;
			overflow: hidden;
			transition: .6s max-height;
		}
		.lista-variables.open{
			max-height: 500px;
			overflow: auto;
		}


		#container_condicionales div[id^="calc_lista-condicion"].infocus .lista-variables.open{
			max-height: 500px;
			overflow: auto;
		}

	/*	#formulario_calc_lista_condicionales [id^='calc_formula_input-condicion']:focus ~ .lista-variables.open {
			max-height: 500px;
			overflow: auto;
		}*/
		div[id^=calc_lista-condicion-]{
			border-bottom: 1px solid var(--info);
			padding-bottom: 1rem;
			margin-bottom: 1rem;
		}

</style>



<body>
	<div class="container">
		<h1 class="w-100">probando back</h1>
	</div>

	<div class="container border-info border pt-2">



		<form action="" method="POST" onsubmit="return false" id="calc_formulario_conjunto">

			<?php require_once './vista/calculadora-form.php'; ?>
			
		</form>

		<script>




			let palabras_reservadas;
			document.addEventListener("DOMContentLoaded", function(){
				load_calc_functions();
				document.getElementById('calc_formulario_conjunto').action_form='testing_calc';
				document.getElementById('calc_formulario_conjunto').onsubmit=function(e){
					e.preventDefault();
					if(this.sending == true){
						return false;
					}


					var datos = new FormData(this);
					datos = calc_formData_maker(datos,this);

					if(datos===false){
						this.action_form='testing_calc';
						return false;
					}

					if(this.action_form=='testing_calc'){
						var lista_inputs = this.querySelectorAll("input:not([disabled]):not([type='checkbox']):not([id='calc_formula_nombre']):not([id='calc_descripcion'])")
					}
					else {
						var lista_inputs = this.querySelectorAll("input:not([disabled]):not([type='checkbox'])")
					}
					valid_form = false;
					lista_inputs.forEach((x)=>{
						if(!x.validarme()){
							if(valid_form===false){
								valid_form = x;
							}
						}
					})

					if(valid_form!== false){
						valid_form.focus();
						return false;
					}


					if(document.getElementById('lista_condicionales').checked){// es una lista de condiciones


						if(this.action_form == "save_calc")
						{
							this.action_form = "testing_calc";
							datos.append("accion","guardar_lista_formulas_condicionales");
						}
						else if(this.action_form == "testing_calc"){
							datos.append("accion","lista_formulas_condicionales");
						}
						else {
							muestraMensaje("Error", "Error al enviar el formulario, contacte con un administrador", "e");
							console.error("position");
						}

					}
					else{ // no es una lista de condiciones

						// TESTING ***********************************

						if(this.action_form == 'testing_calc'){

							var this_form = this; 

							datos.append("accion","calc_formula");
						}
						else if(this.action_form == 'save_calc'){
							this.action_form="testing_calc";

							datos.append("accion","guardar_formula");
						}
						else {
							muestraMensaje("Error", "Error al enviar el formulario, contacte con un administrador", "e");
							console.error("position");
						}


					}

					var this_form = this;
					enviaAjax(datos,function(respuesta, exito, fail){

						var lee = JSON.parse(respuesta);
						if(lee.resultado == "leer_formula"){
							if(lee.tipo=="condicional" && lee.total == null){
								muestraMensaje("Advertencia", "La condicional no se cumplió en ningún caso", "¡");
							}
							else{
								muestraMensaje("Prueba Exitosa", "El total es :"+lee.total, "s");
							}
							this_form.tested_form=true;
						}
						else if(lee.resultado == "calc_guardar_formula"){
							muestraMensaje("Exito", "La formula fue guardada exitosamente", "s");
							reset_calc_form(this_form);
							update_reserved_words();
						}

						else if(lee.resultado == "leer_formula_condicional"){// proabando formulas (lista condicionales)

							if(lee.tipo=="condicional" && lee.total == null){
								muestraMensaje("Advertencia", "La condicional no se cumplió en ningún caso", "¡");
							}
							else{
								muestraMensaje("Exito", `Fue evaluada la formula '${lee.n_formula}' con un resultado de : ${lee.total}`, "s");
							}
							this_form.tested_form = true; 
						}
						else if (lee.resultado == "calc_guardar_formula_lista"){// guardando formulas (lista condicionales)
							muestraMensaje("Exito", "La formula ha sido guardada exitosamente", "s");
							reset_calc_form(this_form);
							
						}
						else if (lee.resultado == 'is-invalid'){
							muestraMensaje(lee.titulo, lee.mensaje,"error");
						}
						else if(lee.resultado == "error"){
							muestraMensaje(lee.titulo, lee.mensaje,"error");
							console.error(lee.mensaje);
						}
						else if(lee.resultado == "console"){
							console.log(lee.mensaje);
						}
						else{
							muestraMensaje(lee.titulo, lee.mensaje,"error");
						}
					}).p.finally((e)=>{
						this_form.sending = undefined;
					});


				


				};
				document.querySelectorAll(".suggestions").forEach((elem)=>{
					event_suggestions(elem);
				})
			});


			




			

		</script>


	</div>

	<script src="vista/calculadora.js"></script>

	
</body>
</html>