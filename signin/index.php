<?php 

	session_start();


?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>CPI-7 - Login</title>

	<link rel="stylesheet" href="../public/css/style.css">
	<link rel="stylesheet" href="../public/css/util.css">
	<link rel="stylesheet" href="../public/css/main.css">


	<!-- Bootstrap -->
	<link rel="stylesheet" href="../public/assets/bootstrap/css/bootstrap.min.css">

	<!-- Ion Icon -->
	<!-- <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
	<script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script> -->

	<link rel="stylesheet" href="../public/fonts/font-awesome-4.7.0/css/font-awesome.min.css">
	<link rel="stylesheet" type="text/css" href="../public/fonts/iconic/css/material-design-iconic-font.min.css">

</head>

<body>

	<div class="limiter">
		<div class="container-login100">
			<div class="wrap-login100">
				<form class="login100-form validate-form" method="POST" action="check-login.php">
					<span class="login100-form-title p-b-28">

						<img src="../public/assets/images/cpi7.png" width="130px" class="login__img_logo" alt="">
						
<!-- <h3 class="login100-form-title p-t-26">Bem-vindo</h5> -->
						
					</span>


					<div class="wrap-input100 validate-input" data-validate="Insira o CPF">
						<input class="input100" type="text" name="cpf" id="cpf" maxlength="11">
						<span class="focus-input100" data-placeholder="CPF"></span>
					</div>

					<div class="wrap-input100 validate-input" data-validate="Insira a senha">
						<span class="btn-show-pass">
							<i class="zmdi zmdi-eye"></i>
						</span>
						<input class="input100" type="password" name="senha" id="senha">
						<span class="focus-input100" data-placeholder="Senha"></span>
					</div>

					<?php
						if (isset($_SESSION['erro'])) {
							$erro = $_SESSION['erro'];
							unset($_SESSION['erro']);
							echo "
										<div class='alert alert-danger'>
											$erro
										</div>
								";
						}
						//destruir a sessão
						if (isset($_GET['logout']) && $_GET['logout'] == 'true') {
							session_unset();
						}
					?>


					<div class="container-login100-form-btn">
						<div class="wrap-login100-form-btn">
							<div class="login100-form-bgbtn"></div>
							<button class="login100-form-btn">
								Login
							</button>
						</div>
					</div>

					<div class="text-center p-t-70">
						<span class="txt1">
							Comando de Policiamento do Interior Sete
							<br>&copy; 2025
						</span>
						<br>
						<span class="txt1">

						</span>

					</div>
				</form>
			</div>
		</div>
	</div>


	<script src="../vendor/jquery/jquery-3.2.1.min.js"></script>
	<script src="../public/js/main.js"></script>


	<script>

		document.getElementById('cpf').addEventListener('input', function (e) {
			// Remover caractere que não seja numérico
			this.value = this.value.replace(/[^0-9]/g, '');
		});

	</script>



</body>

</html>