function update_reserved_words(){
	var datos = new FormData();
	datos.append("accion","get_calc_reserved_words");
	enviaAjax(datos,function(respuesta, exito, fail){
	
		var lee = JSON.parse(respuesta);
		if(lee.resultado == "get_calc_reserved_words"){

			var found = false;
			palabras_reservadas = {}; // variable global
			document.querySelector("#lista_variables>div.container").innerHTML='';

			for(x of lee.mensaje){
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

	var div = crearElem("div",`id,calc_lista-condicion-${n},class,position-relative`,`
		<label for="calc_condicional">Condición - ${n}</label>
		<input required type="text" class="form-control" id="calc_condicional-condicion-${n}" name="calc_condicional-condicion" data-span="invalid-span-calc_condicional-condicion-${n}" value="${condicional}">
		<span id="invalid-span-calc_condicional-condicion-${n}" class="invalid-span text-danger"></span>

		<label for="calc_formula_input">Formula - ${n}</label>
		<input required type="text" class="form-control" id="calc_formula_input-condicion-${n}" name="calc_formula_input-condicion" data-span="invalid-span-calc_formula_input-condicion-${n}" data-variables_container="list_calc_variables-condicion-${n}" data-condicion="calc_condicional-condicion-${n}" data-orden="${n}" value="${formula}">
		<div class="suggestions" data-input="calc_formula_input-condicion-${n}"></div>
		<span id="invalid-span-calc_formula_input-condicion-${n}" class="invalid-span text-danger"></span>

		<div class="container lista-variables">
			<h4>Formula - ${n} - Variables</h4>
			<div id="list_calc_variables-condicion-${n}"></div>
		</div>`)

	
	document.getElementById('container_condicionales').appendChild(div);
	event_suggestions(div.getElementsByClassName('suggestions')[0]);


	evento_formula(document.getElementById(`calc_formula_input-condicion-${n}`));
	evento_condicional(document.getElementById(`calc_condicional-condicion-${n}`));


	document.getElementById(`calc_formula_input-condicion-${n}`).onfocus = document.getElementById(`calc_condicional-condicion-${n}`).onfocus=function (){
		//alert("hola");

		var x = this.closest("div[id^='calc_lista-condicion']");
		document.getElementById('container_condicionales').querySelectorAll("div[id^='calc_lista-condicion-']").forEach((elem)=>{
			if(elem == x){
				elem.classList.add("infocus");
			}
			else{
				elem.classList.remove("infocus");
			}
		})

	}

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

	//datos.consoleAll();
	return datos;


}

function load_formulas_form(formula,nombre='',descripcion='',variables='',condicional='',lista = false){
	if(Array.isArray(formula)){
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


	document.getElementById('lista_condicionales').onclick();

	document.getElementById('calc_formula_input').onkeyup({key:''});
	document.getElementById(document.getElementById('calc_formula_input').dataset.span).innerHTML='';
	form.action_form = 'testing_calc';
	form.tested_form = false;

	update_reserved_words();
	form.querySelectorAll("input").forEach((x)=>{
		x.classList.remove("is-invalid","is-valid");
	})



}


function event_suggestions(elem){

	if(elem.configured==true){
		return false;
	}
	else{
		elem.configured = true;
	}







	["click","keydown"].forEach((even)=>{
		elem.open=function(){
			this.classList.add("open");
		}
		elem.close=function(){
			this.classList.remove("open");
		}

		elem.addEventListener(even,function(e){
			if(e instanceof MouseEvent || (e instanceof KeyboardEvent && e.key == 'Enter') ){
				if(e instanceof KeyboardEvent){
					e.preventDefault();
				}
				var option = e.target;
				if(e instanceof MouseEvent){
					option = e.target.parentNode;
				}

				var input = document.getElementById(this.dataset.input);
				if( option.classList.contains("suggestion-option") ){
					input.value = input.value.replace(/\b[a-z_]+$/i, option.dataset.value);
					//input.value+= option.dataset.value;
					input.focus();
					option.parentNode.close();
					input.onkeyup({key:''});

				 }
			}
		},true);
	})

	formula_input = document.getElementById(elem.dataset.input);
	formula_input.autocomplete='off';
	formula_input.oninput=function(){
		if(this.value == ''){
			elem.close();
			return false;
		}

		var last_text = this.value.match(/\b[a-z_]+$/i);
		if(last_text !== null){

			var exp = new RegExp(`\\b${last_text[0]}.*\$`,'i');
			var exp2 = new RegExp(`\^${last_text[0]}\$`);

			

			elem.innerHTML='';
			var found = false;
			for ([key, value] of Object.entries(palabras_reservadas)) {
			  if(exp.test(key)){
			  	found = true;
			  	var div = document.createElement("div");
			  	div.className = "suggestion-option row m-0";
			  	div.dataset.value = key;
			  	//div.innerHTML = key;
			  	div.tabIndex=0;

			  	div.innerHTML=`<div class="col d-flex justify-content-center align-items-center">${key}</div>
			  	<div class="col">${value.descrip}</div>`
			  	div.onblur=function(e){
			  		if(( e.relatedTarget &&  e.relatedTarget.classList.contains("suggestion-option")  )){
			  			elem.open();
			  		}
			  		else{
			  			elem.close();
			  		}
			  	}
			  	div.onkeydown=function(e){
			  		// e.preventDefault();
			  		if(e.key =='ArrowDown'){

			  			if(this.nextSibling && this.nextSibling.classList.contains("suggestion-option")){
			  				this.nextSibling.focus();
			  			}
			  		}
			  		else if (e.key == 'ArrowUp'){
			  			if(this.previousSibling && this.previousSibling.classList.contains("suggestion-option")){
			  				this.previousSibling.focus();
			  			}
			  		}

			  	}
			  	elem.appendChild(div);
			  	if(exp2.test(key)){
			  		found = false;
			  	}
			  }
			}
			if(found){
				elem.open();
			}
			else{
				elem.close();
			}



		}
	}

	formula_input.onkeydown=function(e){
		if(e.key==='ArrowDown'){
			e.preventDefault();

			console.log(this.nextSibling.nextSibling);

			if(this.nextSibling.nextSibling.classList.contains("suggestions") && this.nextSibling.nextSibling.classList.contains("open")){

				this.nextSibling.nextSibling.querySelector(".suggestion-option").focus();
			}

		}
	}

	formula_input.onblur=function(e){
		if(!(e.relatedTarget && e.relatedTarget.classList.contains("suggestion-option"))){
			elem.close();
		}
		else{
			elem.open();
		}
	}
}