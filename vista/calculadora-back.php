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

				<div id="formulario_calc_normal">

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

						// TESTING ***********************************

						if(this.action_form == "save_calc")
						{
							this.action_form = "testing_calc";
							datos.append("accion","guardar_lista_formulas_condicionales");
						}
						else{
							datos.append("accion","lista_formulas_condicionales");
						}

						var this_form = this; 
						enviaAjax(datos,function(respuesta, exito, fail){
						
							var lee = JSON.parse(respuesta);
							if(lee.resultado == "leer_formula_condicional"){

								if(lee.tipo=="condicional" && lee.total == null){
									muestraMensaje("Advertencia", "La condicional no se cumplió en ningún caso", "¡");
								}
								else{
									muestraMensaje("Exito", `Fue evaluada la formula '${lee.formula}' con un resultado de : ${lee.total}`, "s");
								}
								this_form.tested_form = true; 
							}
							else if (lee.resultado == "calc_guardar_formula_lista"){
								muestraMensaje("Exito", "La formula ha sido guardada exitosamente", "s");
								this_form.reset();
								this_form.tested_form = false;
								update_reserved_words();
								document.getElementById('container_condicionales').innerHTML='';
								add_lista_condicional();
								add_lista_condicional();
								this_form.querySelectorAll("input").forEach((x)=>{
									x.classList.remove("is-invalid","is-valid");
								})
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
						});

						// SAVING  **************************************

					}
					else{ // no es una lista de condiciones

						// TESTING ***********************************

						if(this.action_form == 'testing_calc'){

							var this_form = this; 

							datos.append("accion","calc_formula");
							
							enviaAjax(datos,function(respuesta, exito, fail){
							
								var lee = JSON.parse(respuesta);
								if(lee.resultado == "leer_formula"){
									if(lee.tipo=="condicional" && lee.total == null){
										muestraMensaje("Advertencia", "La condicional no se cumplió en ningún caso", "¡");
									}
									else{
										muestraMensaje("Exito", "El total es :"+lee.total, "s");
									}
									this_form.tested_form=true;
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
								this_form.sending=undefined;
							});
						}
						else if(this.action_form == 'save_calc'){
							this.action_form="testing_calc";

							datos.append("accion","guardar_formula");

							var this_form =this; 
							enviaAjax(datos,function(respuesta, exito, fail){
							
								var lee = JSON.parse(respuesta);
								if(lee.resultado == "calc_guardar_formula"){
									console.log(lee);
									muestraMensaje("Exito", "La formula fue guardada exitosamente", "s");
									reset_calc_form(this_form);
									update_reserved_words();
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
								this_form.sending=undefined;
							});


						}
						else {
							muestraMensaje("Error", "Error al enviar el formulario, contacte con un administrador", "e");
							console.error("position");
						}

						// SAVING  **************************************


					}


					// datos.append("accion","queso");
					// var this_form = this; 
					// enviaAjax(datos,function(respuesta, exito, fail){

					
					// 	var lee = JSON.parse(respuesta);
					// 	if(lee.resultado == "queso"){
							
					// 	}
					// 	else if (lee.resultado == 'is-invalid'){
					// 		muestraMensaje(lee.titulo, lee.mensaje,"error");
					// 	}
					// 	else if(lee.resultado == "error"){
					// 		muestraMensaje(lee.titulo, lee.mensaje,"error");
					// 		console.error(lee.mensaje);
					// 	}
					// 	else if(lee.resultado == "console"){
					// 		console.log(lee.mensaje);
					// 	}
					// 	else{
					// 		muestraMensaje(lee.titulo, lee.mensaje,"error");
					// 	}
					// }).p.finally((x)=>{
					// 	this_form.sending = undefined;
					// });


				};

				/*add_lista_condicional();
				add_lista_condicional();
				
				update_reserved_words();

				

				var datos = new FormData();
				datos.append("accion","get_lista_trabajadores");
				enviaAjax(datos,function(respuesta, exito, fail){
				
					var lee = JSON.parse(respuesta);
					if(lee.resultado == "get_lista_trabajadores"){

						lee.mensaje.forEach((elem)=>{
							document.getElementById('trabajador_prueba-1').appendChild(crearElem("option",`value,${elem.id}`,elem.nombre));
							document.getElementById('trabajador_prueba-2').appendChild(crearElem("option",`value,${elem.id}`,elem.nombre));
						});
						
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
				});

				evento_formula(calc_formula_input);

				document.getElementById('calc_condicional_check').onclick=function(){
					if(this.checked){
						document.getElementById('condicional-container').classList.remove("d-none");
						document.getElementById('calc_condicional').disabled=false;
					}
					else{
						document.getElementById('condicional-container').classList.add("d-none");
						document.getElementById('calc_condicional').disabled=true;	
					}
				};

				document.getElementById('ver_palabras_reservadas').onclick=function(){
					document.getElementById('lista_variables').showModal();
				}
				document.getElementById('cerrar_dialog').onclick=function(){
					document.getElementById('lista_variables').close();
				}

				eventoKeyup("calc_formula_nombre", /^[_]*[a-zA-Z]+(?:[_]+[a-zA-Z]*)*$/, "El nombre no es valido evite utilizar espacios, tildes, números o la letra 'ñ' ");
				eventoKeypress("calc_formula_nombre", /^[a-zA-Z_]*$/);
				eventoKeyup("calc_formula_nombre-2", /^[_]*[a-zA-Z]+(?:[_]+[a-zA-Z]*)*$/, "El nombre no es valido evite utilizar espacios, tildes, números o la letra 'ñ' ");
				eventoKeypress("calc_formula_nombre-2", /^[a-zA-Z_]*$/);

				

				

				eventoKeyup("calc_descripcion", /^[0-9.,\/#!$%\^&\*;:{}=\-_`~()”“\"'…a-zA-Z\säÄëËïÏöÖüÜáéíóúáéíóúÁÉÍÓÚÂÊÎÔÛâêîôûàèìòùÀÈÌÒÙñÑ]+$/, "La descripción no es valida, evite utilizar caracteres especiales");
				eventoKeypress("calc_descripcion", /^[0-9.,\/#!$%\^&\*;:{}=\-_`~()”“\"'…a-zA-Z\säÄëËïÏöÖüÜáéíóúáéíóúÁÉÍÓÚÂÊÎÔÛâêîôûàèìòùÀÈÌÒÙñÑ]*$/);
				eventoKeyup("calc_descripcion-2", /^[0-9.,\/#!$%\^&\*;:{}=\-_`~()”“\"'…a-zA-Z\säÄëËïÏöÖüÜáéíóúáéíóúÁÉÍÓÚÂÊÎÔÛâêîôûàèìòùÀÈÌÒÙñÑ]+$/, "La descripción no es valida, evite utilizar caracteres especiales");
				eventoKeypress("calc_descripcion-2", /^[0-9.,\/#!$%\^&\*;:{}=\-_`~()”“\"'…a-zA-Z\säÄëËïÏöÖüÜáéíóúáéíóúÁÉÍÓÚÂÊÎÔÛâêîôûàèìòùÀÈÌÒÙñÑ]*$/);


				evento_condicional(document.getElementById('calc_condicional'));



*/
			/*	document.getElementById('calc_formulario').action_form="testing_calc";
				document.getElementById('calc_formulario').tested_form=false;
				document.getElementById('calc_formulario').onsubmit=function(e){
					e.preventDefault();
					if(this.sending !== undefined){
						this.sending = true;
					}
					else{
						muestraMensaje("Espere", "Esperando respuesta del servidor", "¡");
						return false;
					}
					if(this.action_form==="testing_calc"){


						temp=this.querySelectorAll("input");

						document.getElementById('trabajador_prueba-1').setCustomValidity('');



						if(document.getElementById('trabajador_prueba-1').value==''){
							document.getElementById('trabajador_prueba-1').setCustomValidity('Selecciona un trabajador en la lista');
							document.getElementById('trabajador_prueba-1').reportValidity();
							return false;
						}

						if(!this.calc_formula_input.validarme()){
							return false;
						}
						var datos = new FormData(this);
						datos.append("accion","calc_formula");

						var lista_variables = document.querySelectorAll("#list_calc_variables input");

						var variablesToSend = [];

						lista_variables.forEach(function (elem){
							variablesToSend.push({'name':elem.dataset.var,'value':elem.value});
						})


						if(variablesToSend.length>0){
							datos.append("variables", JSON.stringify(variablesToSend));
						}

						var this_form = this; 

						enviaAjax(datos,function(respuesta, exito, fail){
						
							var lee = JSON.parse(respuesta);
							if(lee.resultado == "leer_formula"){
								if(lee.tipo=="condicional" && lee.total == null){
									muestraMensaje("Advertencia", "La condicional no se cumplió en ningún caso", "¡");
								}
								else{
									muestraMensaje("Exito", "El total es :"+lee.total, "s");
								}
								this_form.tested_form=true;
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
							this_form.sending=undefined;
						});
					}
					else if(this.action_form==="save_calc"){
						this.action_form="testing_calc";

						if(!this.tested_form){
							muestraMensaje("Error", "Debe probar la formula al menos una vez antes de guardarla", "e");
							return false;
						}

						var form_valid = true;

						this.querySelectorAll("input:not([type='checkbox']):not([disabled]):not([type='hidden'])").forEach((elem)=>{
							if(!elem.validarme()){
								form_valid = elem;
							}
						})

						if(form_valid!==true){
							form_valid.focus();
							return false;
						}

						var datos = new FormData(this);
						datos.append("accion","guardar_formula");

						var lista_variables = document.querySelectorAll("#list_calc_variables input");

						var variablesToSend = [];

						lista_variables.forEach(function (elem){
							variablesToSend.push({'name':elem.dataset.var,'value':elem.value});
						})


						if(variablesToSend.length>0){
							datos.append("variables", JSON.stringify(variablesToSend));
						}
						var this_form =this; 
						enviaAjax(datos,function(respuesta, exito, fail){
						
							var lee = JSON.parse(respuesta);
							if(lee.resultado == "calc_guardar_formula"){
								console.log(lee);
								muestraMensaje("Exito", "La formula fue guardada exitosamente", "s");
								update_reserved_words();
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
							this_form.sending=undefined;
						});

					}
					else if (this.action_form==="modificar_calc"){
						this.action_form="testing_calc";

					}
					else{
						muestraMensaje("Error", "Error en el envío del formulario contacte con un administrador", "e");
						this.sending=undefined;
					}
				};*/
/*

				document.getElementById('trabajador_prueba-1').onchange = document.getElementById('trabajador_prueba-2').onchange=function (e){
					this.setCustomValidity('');
				}

				document.getElementById('save-form-btn-1').onclick = document.getElementById('save-form-btn-2').onclick =function(e){
					e.preventDefault();

					this.closest("form").action_form = "save_calc";
					this.closest("form").querySelector("button[type='submit']").click();
					return false;
				};

*/
			/*	document.getElementById('calc_formulario_lista').action_form="testing_calc";
				document.getElementById('calc_formulario_lista').tested_form=false;
				document.getElementById('calc_formulario_lista').onsubmit=function(e){
					//data-variables_container
					//data-condicion
					e.preventDefault();
					var datos = new FormData(this);



					datos.append("trabajador_prueba", document.getElementById('trabajador_prueba-2').value );

					var formulas = Array();

					lista_formulas = this.querySelectorAll("input[id^='calc_formula_input-condicion-']");

					lista_formulas.forEach((elem)=>{
						var obj = {};
						var condicion = document.getElementById(elem.dataset.condicion).value;
						var formula = elem.value;
						obj.orden = elem.dataset.orden;
						obj.condiciones = condicion;
						obj.formula = formula;
						obj.variables = null;
						var variables = document.querySelectorAll(`#${elem.dataset.variables_container} input`);

						if(variables.length>0){
							obj.variables = {} ;
							variables.forEach((x)=>{
								obj.variables[x.dataset.var] = x.value;
								//obj.variables.push({name:x.dataset.var,value:x.value});
							})
						}

						formulas.push(obj);
					});

					datos.append("formulas",JSON.stringify(formulas));



					var form_valid = true;

					if(this.action_form == "testing_calc"){
						if(this.trabajador_prueba.value == ''){
							this.trabajador_prueba.setCustomValidity('Selecciona un trabajador en la lista');
							this.trabajador_prueba.reportValidity();
							this.querySelector("button[type='submit']").click();
							return
						}
					}
					else if(this.action_form == "save_calc" && (!this.tested_form)){
						this.action_form = "testing_calc";
						muestraMensaje("Error", "Debe probar la lista de formulas al menos una vez antes de guardarla se recomienda probar todas las condicionales para evitar errores", "e");
						return false;
					}

					this.querySelectorAll("input:not([type='checkbox']):not([disabled]):not([type='hidden'])").forEach((elem)=>{
						if(elem.closest("form").action_form === 'testing_calc'){
							if(elem.name != 'calc_formula_nombre' && elem.name != 'calc_descripcion'){
								if(!elem.validarme()){
									form_valid = elem;
								}
							}
						}
						else if(elem.closest("form").action_form === 'save_calc'){
							if(!elem.validarme()){
									form_valid = elem;
									return elem;
							}
						}
					})

					if(this.action_form !== 'save_calc' && this.action_form !== 'testing_calc'){
						muestraMensaje("Error", "Error al enviar el formulario, contacte con un administrador", "s");
						return false;
					}

					if(form_valid!==true){
						this.action_form = "testing_calc";
						form_valid.focus();
						return false;
					}



					if(this.action_form == "save_calc")
					{
						this.action_form = "testing_calc";
						datos.append("accion","guardar_lista_formulas_condicionales");
					}
					else{
						datos.append("accion","lista_formulas_condicionales");
					}



					datos.delete("calc_condicional-condicion");
					datos.delete("calc_formula_input-condicion");
					datos.delete("variables_calc");

					datos.consoleAll();

					var this_form = this; 
					enviaAjax(datos,function(respuesta, exito, fail){
					
						var lee = JSON.parse(respuesta);
						if(lee.resultado == "leer_formula_condicional"){

							if(lee.tipo=="condicional" && lee.total == null){
								muestraMensaje("Advertencia", "La condicional no se cumplió en ningún caso", "¡");
							}
							else{
								muestraMensaje("Exito", `Fue evaluada la formula '${lee.formula}' con un resultado de : ${lee.total}`, "s");
							}
							this_form.tested_form = true; 
						}
						else if (lee.resultado == "calc_guardar_formula_lista"){
							muestraMensaje("Exito", "La formula ha sido guardada exitosamente", "s");
							this_form.reset();
							this_form.tested_form = false;
							update_reserved_words();
							document.getElementById('container_condicionales').innerHTML='';
							add_lista_condicional();
							add_lista_condicional();
							this_form.querySelectorAll("input").forEach((x)=>{
								x.classList.remove("is-invalid","is-valid");
							})
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
					});
				};*/
/*

				document.getElementById('lista_condicionales').onclick=function(){
					if(this.checked){
						document.getElementById('calc_formulario').classList.add("d-none");
						document.getElementById('calc_formulario_lista').classList.remove("d-none");
					}
					else {
						document.getElementById('calc_formulario').classList.remove("d-none");
						document.getElementById('calc_formulario_lista').classList.add("d-none");	
					}
				};
				document.getElementById('lista_condicionales').onclick();*/






				// document.getElementById('calc_formula_input').onkeyup = function(e){
					

					
				// };
			});


			function update_reserved_words(){
				// TODO arreglar esto
				var datos = new FormData();
				datos.append("accion","get_calc_reserved_words");
				enviaAjax(datos,function(respuesta, exito, fail){

				
					var lee = JSON.parse(respuesta);
					console.log(lee);
					var found = false;
					palabras_reservadas = {};
					document.querySelector("#lista_variables>div.container").innerHTML='';

					for(x of lee){
						found = true;
						var name = crearElem("div",'class,col d-flex justify-content-center align-items-center',x.name);
						var descrip = crearElem("div",'class,col no-select',x.descrip);
						var row = crearElem("div","class,row border-top border-info");
						row.appendChild(name);
						row.appendChild(descrip);
						document.querySelector("#lista_variables>div.container").appendChild(row);
						palabras_reservadas[x.name] = {"descrip":x.descrip,"name":x.name};
					}

					if(found==false){
						document.querySelector("#lista_variables>div.container").appendChild(crearElem("div",'class,container text-center',"No Hay Palabras Reservadas"));	
					}
					// load_formulas_form("TIEMPO_TRABAJADOR+a+b",'','',{a:'5','b':'2'});
					// lista_probar = Array();

					// lista_probar.push({formula:'TIEMPO_TRABAJADOR+a+b', variables:{a:'1',b:'97'}, condicional:"2>3"})
					// lista_probar.push({formula:'TIEMPO_TRABAJADOR+a+b', variables:{a:'1',b:'98'}, condicional:"2>4"})
					// lista_probar.push({formula:'TIEMPO_TRABAJADOR+a+b', variables:{a:'1',b:'99'}, condicional:"2>5"})

					// //load_formulas_form(formula,nombre='',descripcion='',variables='',condicional='',lista = false)

					// load_formulas_form(lista_probar,"testing_load",'success');
					// console.error("quitar esto");

				});
			}




			function add_var(name,lista_add){
				var label = crearElem("label",`for,id_calc_var_${name},class,m-0`,name);
				var input = crearElem("input",`type,text,class,form-control,id,id_calc_var_${name},name,variables_calc,data-span,invalid-span-id_calc_var_${name},data-var,${name},required,true`);
				evento_formula(input,false);
				input.autocomplete="off";
				var span = crearElem("span",`id,invalid-span-id_calc_var_${name},class,invalid-span text-danger`);
				var col1 =crearElem("div","class,col-6 d-flex justify-content-end") ;
				col1.appendChild(label);
				var col2 =crearElem("div","class,col");

				if(palabras_reservadas[name]){
					col2.innerHTML = palabras_reservadas[name].descrip;

				}
				else{
					col2.appendChild(input);
					col2.appendChild(span);
				}

				var row = crearElem("div","class,row");
				row.appendChild(col1);
				row.appendChild(col2);

				document.getElementById(lista_add).appendChild(row);

			}

			function evento_formula(elem,variables=true){

				var func = function (elem){
					elem.value = elem.value.replace(/\,/, ".");
					elem.closest("form").tested_form=false;
				}
				var func2 = undefined;

				if(variables===true){

					func2= function(elem){


						var contenedor_variables = elem.dataset.variables_container;
						lista = elem.value.match(/[a-zA-Z](?:[_-]*[a-zA-Z]*)*/g);
						document.getElementById(contenedor_variables).innerHTML='';
						if(lista){
							var found = false;
							var ready = {}
							for(x of lista){
								found = true;
								if(!ready[x]){
									ready[x] = 1;
									add_var(x,contenedor_variables);
								}
							}

							if(!document.getElementById(contenedor_variables).parentNode.classList.contains("open")){
								document.getElementById(contenedor_variables).parentNode.classList.add("open");
							}
						}
						else{
							document.getElementById(contenedor_variables).parentNode.classList.remove("open");
						}
					}
				}

				eventoKeyup(elem, /^[0-9a-zA-Z_\.,+\-*\/{}()\[\]\s]+$/, "El campo solo permite letras sin tilde numeros, operadores (+,-,*,/) y piso (_)", undefined, func,func2);
				eventoKeypress(elem, /^[0-9a-zA-Z_\.,+\-*\/{}()\[\]\s]*$/);

			}


			function evento_condicional(elem){

				eventoKeyup(elem, /^[0-9a-zA-Z_\.,+\-*\/{}()\[\]\s]+(?:[<>=]+[0-9a-zA-Z_\.,+\-*\/{}()\[\]\s]+)?$/, "El campo solo permite letras sin tilde numeros, operadores (+,-,*,/) y piso (_). si utiliza los símbolos para comparar (<, >, =, >=, <=) debe expresar ambos valores ej.(5>2)",undefined,function(x){x.closest("form").tested_form=false});
				eventoKeypress(elem, /^[0-9a-zA-Z_\.,+\-*\/{}()\[\]\s<>=]*$/);

			}


			function add_lista_condicional(condicional='',formula='',variables=''){
				var lista_condicionales_actuales = document.querySelectorAll("#container_condicionales>div[id^='calc_lista-condicion-']");

				var n = lista_condicionales_actuales.length + 1;

				var div = crearElem("div",`id,calc_lista-condicion-${n}`,`
					<label for="calc_condicional">Condición - ${n}</label>
					<input required type="text" class="form-control" id="calc_condicional-condicion-${n}" name="calc_condicional-condicion" data-span="invalid-span-calc_condicional-condicion-${n}" value="${condicional}">
					<span id="invalid-span-calc_condicional-condicion-${n}" class="invalid-span text-danger"></span>

					<label for="calc_formula_input">Formula - ${n}</label>
					<input required type="text" class="form-control" id="calc_formula_input-condicion-${n}" name="calc_formula_input-condicion" data-span="invalid-span-calc_formula_input-condicion-${n}" data-variables_container="list_calc_variables-condicion-${n}" data-condicion="calc_condicional-condicion-${n}" data-orden="${n}" value="${formula}">
					<span id="invalid-span-calc_formula_input-condicion-${n}" class="invalid-span text-danger"></span>

					<div class="container lista-variables">
						<h4>Formula - ${n} - Variables</h4>
						<div id="list_calc_variables-condicion-${n}"></div>
					</div>`)
				document.getElementById('container_condicionales').appendChild(div);

				evento_formula(document.getElementById(`calc_formula_input-condicion-${n}`));
				evento_condicional(document.getElementById(`calc_condicional-condicion-${n}`));

				if(variables!==''){

					document.getElementById(`calc_formula_input-condicion-${n}`).onkeyup({key:''});



					document.getElementById(`calc_formula_input-condicion-${n}`);

					var_container = `list_calc_variables-condicion-${n}`;

					for( [key,value] of Object.entries(variables) ){

						document.getElementById(var_container).querySelectorAll("input").forEach((x)=>{
							if(x.dataset.var==key){
								x.value = value;
							}
						})

					}
				}



			}

			function remove_lista_condicional(){
				var lista_condicionales_actuales = document.querySelectorAll("#container_condicionales>div[id^='calc_lista-condicion-']");

				var n = lista_condicionales_actuales.length;
				if(n>2){
					document.getElementById('container_condicionales').removeChild(document.getElementById(`calc_lista-condicion-${n}`));
				}
			}


			function load_calc_functions(){
				document.getElementById('container_condicionales').innerHTML='';
				add_lista_condicional();
				add_lista_condicional();
				update_reserved_words();
				evento_formula(document.getElementById('calc_formula_input'));


				var datos = new FormData();
				datos.append("accion","get_lista_trabajadores");
				enviaAjax(datos,function(respuesta, exito, fail){
				
					var lee = JSON.parse(respuesta);
					if(lee.resultado == "get_lista_trabajadores"){

						lee.mensaje.forEach((elem)=>{
							document.getElementById('trabajador_prueba-1').appendChild(crearElem("option",`value,${elem.id}`,elem.nombre));
						});
						
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
				});

				document.getElementById('calc_condicional_check').onclick=function(){
					if(this.checked){
						document.getElementById('condicional-container').classList.remove("d-none");
						document.getElementById('calc_condicional').disabled=false;
					}
					else{
						document.getElementById('condicional-container').classList.add("d-none");
						document.getElementById('calc_condicional').disabled=true;	
					}
				};

				document.getElementById('ver_palabras_reservadas').onclick=function(){
					document.getElementById('lista_variables').showModal();
				}
				document.getElementById('cerrar_dialog').onclick=function(){
					document.getElementById('lista_variables').close();
				}

				eventoKeyup("calc_formula_nombre", /^[_]*[a-zA-Z]+(?:[_]+[a-zA-Z]*)*$/, "El nombre no es valido evite utilizar espacios, tildes, números o la letra 'ñ' ");
				eventoKeypress("calc_formula_nombre", /^[a-zA-Z_]*$/);
				eventoKeyup("calc_formula_nombre-2", /^[_]*[a-zA-Z]+(?:[_]+[a-zA-Z]*)*$/, "El nombre no es valido evite utilizar espacios, tildes, números o la letra 'ñ' ");
				eventoKeypress("calc_formula_nombre-2", /^[a-zA-Z_]*$/);
				

				eventoKeyup("calc_descripcion", /^[0-9.,\/#!$%\^&\*;:{}=\-_`~()”“\"'…a-zA-Z\säÄëËïÏöÖüÜáéíóúáéíóúÁÉÍÓÚÂÊÎÔÛâêîôûàèìòùÀÈÌÒÙñÑ]+$/, "La descripción no es valida, evite utilizar caracteres especiales");
				eventoKeypress("calc_descripcion", /^[0-9.,\/#!$%\^&\*;:{}=\-_`~()”“\"'…a-zA-Z\säÄëËïÏöÖüÜáéíóúáéíóúÁÉÍÓÚÂÊÎÔÛâêîôûàèìòùÀÈÌÒÙñÑ]*$/);
				eventoKeyup("calc_descripcion-2", /^[0-9.,\/#!$%\^&\*;:{}=\-_`~()”“\"'…a-zA-Z\säÄëËïÏöÖüÜáéíóúáéíóúÁÉÍÓÚÂÊÎÔÛâêîôûàèìòùÀÈÌÒÙñÑ]+$/, "La descripción no es valida, evite utilizar caracteres especiales");
				eventoKeypress("calc_descripcion-2", /^[0-9.,\/#!$%\^&\*;:{}=\-_`~()”“\"'…a-zA-Z\säÄëËïÏöÖüÜáéíóúáéíóúÁÉÍÓÚÂÊÎÔÛâêîôûàèìòùÀÈÌÒÙñÑ]*$/);


				evento_condicional(document.getElementById('calc_condicional'));


				document.getElementById('lista_condicionales').onclick=function(){
					if(this.checked){
						document.getElementById('formulario_calc_normal').classList.add("d-none");
						document.getElementById('formulario_calc_lista_condicionales').classList.remove("d-none");

						document.getElementById('formulario_calc_normal').querySelectorAll("input").forEach((e)=>{
							e.disabled=true;
						});
						document.getElementById('formulario_calc_lista_condicionales').querySelectorAll("input").forEach((e)=>{
							e.disabled=false;
						});

					}
					else {
						document.getElementById('formulario_calc_normal').classList.remove("d-none");
						document.getElementById('formulario_calc_lista_condicionales').classList.add("d-none");	
						document.getElementById('formulario_calc_lista_condicionales').querySelectorAll("input").forEach((e)=>{
							e.disabled=true;
						});
						document.getElementById('formulario_calc_normal').querySelectorAll("input").forEach((e)=>{
							e.disabled=false;
						});
						document.getElementById('calc_condicional_check').onclick();
					}
				};
				document.getElementById('lista_condicionales').onclick();

				document.getElementById('trabajador_prueba-1').onchange = function (e){
					this.setCustomValidity('');
				}


				document.getElementById('save-form-btn-1').onclick =function(e){
					e.preventDefault();

					this.closest("form").action_form = "save_calc";
					this.closest("form").querySelector("button[type='submit']").click();
					return false;
				};

			}

			function calc_formData_maker(datos=false,form=false){
				if(datos===false){
					muestraMensaje("Error", "debe pasar por argumento el FormData", "e");
					console.error("debe pasar por argumento el FormData");
					return false;
				}
				if(form===false){
					muestraMensaje("Error", "debe pasar por argumento el formulario", "e");
					console.error("debe pasar por argumento el formulario");
					return false;
				}

				if(form.action_form == 'save_calc' && (form.tested_form==false || typeof form.tested_form === 'undefined')){
					muestraMensaje("Error", "Debe probar la formula al menos una vez antes de guardarla", "e");
					return false;
				}


				if(form.action_form == 'testing_calc'){
					document.getElementById('trabajador_prueba-1').setCustomValidity('');
					if(document.getElementById('trabajador_prueba-1').value==''){
						document.getElementById('trabajador_prueba-1').setCustomValidity('Selecciona un trabajador en la lista');
						document.getElementById('trabajador_prueba-1').reportValidity();
						return false;
					}
				}
				else if(form.action_form=='save_calc'){
					var temp1 = form.calc_formula_nombre.validarme();
					var temp2 = form.calc_descripcion.validarme();
					if(!temp1){
						form.calc_formula_nombre.focus();
						return false;
					}
					else if(!temp2){
						form.calc_descripcion.focus();
						return false;
					}

				}
				else{
					muestraMensaje("Error", '', "e");
					console.error("El formulario debe tener la propiedad action_form");
					return false;
				}

				if(document.getElementById('lista_condicionales').checked){//lista de condicionales

					datos.delete("calc_condicional-condicion");
					datos.delete("calc_formula_input-condicion");
					datos.delete("variables_calc");


					formulas_tosend=[];

					lista_formulas = form.querySelectorAll("input[id^='calc_formula_input-condicion-']");

					lista_formulas.forEach((elem)=>{
						var obj = {};
						var condicion = document.getElementById(elem.dataset.condicion).value;
						var formula = elem.value;
						obj.orden = elem.dataset.orden;
						obj.condiciones = condicion;
						obj.formula = formula;
						obj.variables = null;
						var variables = document.querySelectorAll(`#${elem.dataset.variables_container} input`);

						if(variables.length>0){
							obj.variables = {} ;
							variables.forEach((x)=>{
								obj.variables[x.dataset.var] = x.value;
								//obj.variables.push({name:x.dataset.var,value:x.value});
							})
						}

						formulas_tosend.push(obj);
					});

					datos.append("formulas",JSON.stringify(formulas_tosend));



				}
				else{//sin lista de condicionales



					var variables = document.querySelectorAll("#list_calc_variables input");

					if(variables.length>0){
						obj = {} ;
						variables.forEach((x)=>{
							obj[x.dataset.var] = x.value;
						})

						datos.append("variables",JSON.stringify(obj));
					}

				}

				datos.consoleAll();
				return datos;


			}

			function load_formulas_form(formula,nombre='',descripcion='',variables='',condicional='',lista = false){
				if(Array.isArray(formula)){
					console.table(formula);
					document.getElementById('container_condicionales').innerHTML='';
					for(elem of formula){
						elem['variables'] = elem['variables'] || {};
						elem['condicional'] = elem['condicional'] || '';

						document.getElementById('lista_condicionales').checked=true;
						document.getElementById('lista_condicionales').onclick();

						load_formulas_form(elem['formula'] ,nombre ,descripcion ,elem['variables'] ,elem['condicional'],true);
					}
				}else{
					if(lista){
						add_lista_condicional(condicional, formula, variables);
					}
					else{
						document.getElementById('lista_condicionales').checked=false;
						document.getElementById('lista_condicionales').onclick();

						if(condicional!=''){
							document.getElementById('calc_condicional_check').checked=true;
							document.getElementById('calc_condicional').value = condicional;
						}
						else{
							document.getElementById('calc_condicional_check').checked=false;
							document.getElementById('calc_condicional').value = '';
						}
						document.getElementById('calc_condicional_check').onclick();

						document.getElementById('calc_formula_input').value=formula;
						document.getElementById('calc_formula_input').onkeyup({key:''});


						var var_container = document.getElementById('calc_formula_input').dataset.variables_container;




						for( [key,value] of Object.entries(variables) ){

							document.getElementById(var_container).querySelectorAll("input").forEach((x)=>{
								if(x.dataset.var==key){
									x.value = value;
								}
							})

						}

						document.getElementById('calc_formula_nombre').value = nombre;
						document.getElementById('calc_descripcion').value = descripcion;







					}
				}

			}

			function reset_calc_form(form){
				form.reset();
				document.getElementById('container_condicionales').innerHTML='';
				add_lista_condicional();
				add_lista_condicional();

				document.getElementById('calc_condicional_check').checked=false;
				document.getElementById('calc_condicional_check').onclick();

				document.getElementById('calc_formula_input').onkeyup({key:''});
				document.getElementById('calc_formula_input').classList.remove("is-invalid");
				document.getElementById(document.getElementById('calc_formula_input').dataset.span).innerHTML='';
			}

		</script>

		
	</div>

	<div class="container border-info border ">
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