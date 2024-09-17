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

	<div class="container border-info border pt-2">



		<form action="" method="POST" onsubmit="return false" id="calc_formulario_conjunto">

			<div id="calculadora_formulario_content">
				<style>
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
				</style>

				<div class="d-flex justify-content-between align-items-center">
					<div class="d-flex justify-content-start align-items-center">
						<input type="checkbox" class="check-button" id="lista_condicionales">
						<label for="lista_condicionales" class="check-button"></label>
						<label for="lista_condicionales" class="cursor-pointer no-select mb-0 ml-2">Lista de condiciones</label>
					</div>

					<div>
						<button class="btn btn-info" id="ver_palabras_reservadas" type="button">Ver palabras reservadas</button>
					</div>

				</div>
				<div class="container text-center my-3">
					<select class="form-control" name="trabajador_prueba" id="trabajador_prueba-1">
						<option value="">- Seleccione un trabajador de pruebas - </option>
					</select>
				</div>

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

				</style>

				<div id="formulario_calc_normal">

					<div class="d-flex justify-content-start align-items-center">
						<input type="checkbox" class="check-button" id="calc_condicional_check" name="calc_condicional_check" data-span="invalid-span-calc_condicional_check">
						<label for="calc_condicional_check" class="check-button"></label>
						<label class="cursor-pointer no-select mb-0 ml-2" for="calc_condicional_check">Condicional</label>
					</div>

					<br>
					<div class="d-none position-relative" id="condicional-container">
						<label for="calc_condicional">Condición</label>
						<input required disabled="true" type="text" class="form-control" id="calc_condicional" name="calc_condicional" data-span="invalid-span-calc_condicional">
						<div class="suggestions" data-input="calc_condicional"></div>
						<span id="invalid-span-calc_condicional" class="invalid-span text-danger"></span>
					</div>
					<div class="position-relative">
						<label for="calc_formula_input">Formula</label>
						<input required type="text" class="form-control" id="calc_formula_input" name="calc_formula_input" data-span="invalid-span-calc_formula_input" data-variables_container="list_calc_variables">
						<div class="suggestions" data-input="calc_formula_input"></div>
						<span id="invalid-span-calc_formula_input" class="invalid-span text-danger"></span>
					</div>

					<script>
						




					</script>


					<div class="container lista-variables my-3">
						<h4>Variables</h4>
						<div id="list_calc_variables"></div>
					</div>
					
				</div>


				<!-- lista de condicionales -->


				<div id="formulario_calc_lista_condicionales" class="d-none">

					<div class="container" id="container_condicionales">
						<!-- <div id="calc_lista-condicion-1">
							<label for="calc_condicional">Condición - 1</label>
							<input required type="text" class="form-control" id="calc_condicional-condicion-1" name="calc_condicional-condicion-1" data-span="invalid-span-calc_condicional-condicion-1">
							<span id="invalid-span-calc_condicional-condicion-1" class="invalid-span text-danger"></span>

							<label for="calc_formula_input">Formula - 1</label>
							<input required type="text" class="form-control" id="calc_formula_input-condicion-1" name="calc_formula_input-condicion-1" data-span="invalid-span-calc_formula_input-condicion-1">
							<span id="invalid-span-calc_formula_input-condicion-1" class="invalid-span text-danger"></span>

							<div class="container lista-variables">
								<h4>Formula - 1 - Variables</h4>
								<div id="list_calc_variables-condicion-1"></div>
							</div>
						</div> -->
					</div>

					<div class="text-right">
						<button type="button" class="btn btn-info" title="Añadir Condición" onclick="add_lista_condicional()">+</button>
						<button type="button" class="btn btn-info" title="Eliminar Condición" onclick="remove_lista_condicional()">-</button>
					</div>


					
				</div>

				<!-- lista de condicionales -->


				<div>
					<label for="calc_formula_nombre">Nombre de formula</label>
					<input type="text" class="form-control" id="calc_formula_nombre" name="calc_formula_nombre" data-span="invalid-span-calc_formula_nombre">
					<span id="invalid-span-calc_formula_nombre" class="invalid-span text-danger"></span>
				</div>
				<div>
					<label for="calc_descripcion">Descripción de formula</label>
					<input type="text" class="form-control" id="calc_descripcion" name="calc_descripcion" data-span="invalid-span-calc_descripcion">
					<span id="invalid-span-calc_descripcion" class="invalid-span text-danger"></span>
				</div>


				<div class="container text-center my-3">
					<button type="submit" class="btn btn-info">Probar Formula</button>
					<button type="button" class="btn btn-info" id="save-form-btn-1">Guardar Formula</button>
				</div>



			</div>
			
		</form>

		<dialog id="lista_variables">
			<div class="h2 text-center">Palabras reservadas</div>
			<div class="container p-0">
			</div>
			<div class="container text-right">
				<button class="btn btn-danger" id="cerrar_dialog">Cerrar</button>
				
			</div>
		</dialog>

		<form action="" method="POST" onsubmit="return false" id="calc_formulario" class="d-none">
			<!-- <div class="container text-center my-3">
				<select class="form-control" name="trabajador_prueba" id="trabajador_prueba-1">
					<option value="">- Seleccione un trabajador de pruebas - </option>
				</select>
			</div> -->


			<div class="d-flex justify-content-start align-items-center">
				<input type="checkbox" class="check-button" id="calc_condicional_check" name="calc_condicional_check" data-span="invalid-span-calc_condicional_check">
				<label for="calc_condicional_check" class="check-button"></label>
				<label class="cursor-pointer no-select mb-0 ml-2" for="calc_condicional_check">Condicional</label>
			</div>

			<br>
			<div class="d-none" id="condicional-container">
				<label for="calc_condicional">Condición</label>
				<input required disabled="true" type="text" class="form-control" id="calc_condicional" name="calc_condicional" data-span="invalid-span-calc_condicional">
				<span id="invalid-span-calc_condicional" class="invalid-span text-danger"></span>
			</div>
			<div>
				<label for="calc_formula_input">Formula</label>
				<input required type="text" class="form-control" id="calc_formula_input" name="calc_formula_input" data-span="invalid-span-calc_formula_input" data-variables_container="list_calc_variables">
				<span id="invalid-span-calc_formula_input" class="invalid-span text-danger"></span>
			</div>


			<style>
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


			<div class="container lista-variables my-3">
				<h4>Variables</h4>
				<div id="list_calc_variables"></div>
			</div>

			<div>
				<label for="calc_formula_nombre">Nombre de formula</label>
				<input type="text" class="form-control" id="calc_formula_nombre" name="calc_formula_nombre" data-span="invalid-span-calc_formula_nombre">
				<span id="invalid-span-calc_formula_nombre" class="invalid-span text-danger"></span>
			</div>
			<div>
				<label for="calc_descripcion">Descripción de formula</label>
				<input type="text" class="form-control" id="calc_descripcion" name="calc_descripcion" data-span="invalid-span-calc_descripcion">
				<span id="invalid-span-calc_descripcion" class="invalid-span text-danger"></span>
			</div>


			<div class="container text-center my-3 d-none">
				<button type="submit" class="btn btn-info">Probar Formula</button>
				<button type="button" class="btn btn-info" id="save-form-btn-1">Guardar Formula</button>
			</div>
		</form>

		<form action="" method="POST" onsubmit="return false" id="calc_formulario_lista" class="d-none">

			<div class="container text-center my-3">
				<select class="form-control" name="trabajador_prueba" id="trabajador_prueba-2">
					<option value="">- Seleccione un trabajador de pruebas - </option>
				</select>
			</div>


			<div class="container" id="container_condicionales_old">
				<!-- <div id="calc_lista-condicion-1">
					<label for="calc_condicional">Condición - 1</label>
					<input required type="text" class="form-control" id="calc_condicional-condicion-1" name="calc_condicional-condicion-1" data-span="invalid-span-calc_condicional-condicion-1">
					<span id="invalid-span-calc_condicional-condicion-1" class="invalid-span text-danger"></span>

					<label for="calc_formula_input">Formula - 1</label>
					<input required type="text" class="form-control" id="calc_formula_input-condicion-1" name="calc_formula_input-condicion-1" data-span="invalid-span-calc_formula_input-condicion-1">
					<span id="invalid-span-calc_formula_input-condicion-1" class="invalid-span text-danger"></span>

					<div class="container lista-variables">
						<h4>Formula - 1 - Variables</h4>
						<div id="list_calc_variables-condicion-1"></div>
					</div>
				</div> -->
			</div>

			<div class="text-right">
				<button type="button" class="btn btn-info" title="Añadir Condición" onclick="add_lista_condicional()">+</button>
				<button type="button" class="btn btn-info" title="Eliminar Condición" onclick="remove_lista_condicional()">-</button>
			</div>

			<div>
				<label for="calc_formula_nombre">Nombre de formula</label>
				<input type="text" class="form-control" id="calc_formula_nombre-2" name="calc_formula_nombre" data-span="invalid-span-calc_formula_nombre-2">
				<span id="invalid-span-calc_formula_nombre-2" class="invalid-span text-danger"></span>
			</div>
			<div>
				<label for="calc_descripcion">Descripción de formula</label>
				<input type="text" class="form-control" id="calc_descripcion-2" name="calc_descripcion" data-span="invalid-span-calc_descripcion-2">
				<span id="invalid-span-calc_descripcion-2" class="invalid-span text-danger"></span>
			</div>

			<div class="text-center d-none">
				<button type="submit" class="btn btn-info">Probar Formula</button>
				<button type="button" class="btn btn-info" id="save-form-btn-2">Guardar Formula</button>
			</div>
			
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