<!DOCTYPE html>
<html lang="es">
<head>

	<?php require_once 'assets/comun/head.php'; ?>

	<title>calculadora - formulas</title>
</head>
<body>

	<form action="" method="POST" onsubmit="return false">
		<div class="variables-fields row">
			<div class="col-12">
				<label>Variable</label>
				<input type="text" class="form-control variables-field" name="variables[]" data-span="invalid-span-variables">
				<span id="invalid-span-variables" class="invalid-span text-danger"></span>
			</div>
		</div>
		<div class="row">
			<div class="col">
				<label for="formula_math">Formula</label>
				<input type="text" class="form-control" id="formula_math" name="formula_math" data-span="invalid-span-formula_math">
				<span id="invalid-span-formula_math" class="invalid-span text-danger"></span>
			</div>
		</div>
	</form>
	
</body>
</html>