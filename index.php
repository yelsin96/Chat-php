<?php
session_start();
include('header.php');
include('Chat.php');
$chat = new Chat();
$loginMessage = '';

if (!empty($_POST['modificar'])) {
	$userName = $_POST['inpNombres'];
	$userCelular = $_POST['inpCelular'];
	$userFN = $_POST['inpFechaNac'];
	$userPwd = false;
	$binariosImagen = false;
	$userEmail = $_POST['inpEmail'];
	if (!empty($_POST['inpPwd'])) {
		$userPwd = $_POST['inpPwd'];
	}
	if (!empty($_FILES['avatar']['name'])) {
		//avatar
		$tipoArchivo = $_FILES['avatar']['type'];
		$nombreArchivo = $_FILES['avatar']['name'];
		$tamanoArchivo = $_FILES['avatar']['size'];
		$imagenSubida = fopen($_FILES['avatar']['tmp_name'], 'r');
		$binariosImagen = fread($imagenSubida, $tamanoArchivo);
	}

	$loginMessage = $chat->updateUser($userName, $userCelular, $userFN, $userPwd, $userEmail, $binariosImagen);
}

?>
<title>CHAT</title>
<link rel='stylesheet prefetch' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.6.2/css/font-awesome.min.css'>
<link href="css/style.css" rel="stylesheet">
<script src="js/chat.js"></script>
</head>

<body>
	<div class="container-fluid">
		<?php if (isset($_SESSION['userid']) && $_SESSION['userid']) { ?>
			<?php if ($loginMessage) {
				echo $loginMessage;
			} ?>
			
			
			
			
			<div class="chat">
				<div id="frame">

					<div class="row" style="height: 100%;">
						<div id="sidepanel" class="col-1 col-sm-3 col-md-4 ">

							<div class="accordion accordion-flush" id="accordionFlushExample">
								<div class="accordion-item">
									<h2 class="accordion-header" id="flush-headingOne">
										<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseOne" aria-expanded="false" aria-controls="flush-collapseOne">
											<?php
											$loggedUser = $chat->getUserDetails($_SESSION['userid']);
											$currentSession = '';
											$currentSession = $loggedUser[0]['current_session'];
											echo '<img id="profile-img" src="data:image/png;base64, ' . base64_encode($loggedUser[0]['avatar']) . ' " class="rounded-circle" style="height: 45px;" alt="" />';
											echo  '<spam>' . $loggedUser[0]['username'] . '</spam>';
											?>
										</button>
									</h2>
									<div id="flush-collapseOne" class="accordion-collapse collapse" aria-labelledby="flush-headingOne" data-bs-parent="#accordionFlushExample">
										<div class="accordion-body">
											<div class="list-group">
												<a href="logout.php" data-bs-toggle="modal" data-bs-target="#modalRegistro" style="text-decoration:none"><button type="button" class="list-group-item list-group-item-action">
														Mi perfil
													</button></a>
												<a href="logout.php" style="text-decoration:none"><button type="button" class="list-group-item list-group-item-action">Salir</button>
												</a>


											</div>
										</div>
									</div>
								</div>
							</div>


							<!-- <div id="search">
							<label for=""><i class="fa fa-search" aria-hidden="true"></i></label>
							<input type="text" placeholder="Buscar Contactos..." />
						</div> -->
							<div id="contacts">
								<?php
								echo '<ul>';
								$chatUsers = $chat->chatUsers($_SESSION['userid']);
								foreach ($chatUsers as $user) {
									$status = 'offline';
									if ($user['online']) {
										$status = 'online';
									}
									$activeUser = '';
									if ($user['userid'] == $currentSession) {
										$activeUser = "active";
									}
									echo '<li id="' . $user['userid'] . '" class="contact ' . $activeUser . '" data-touserid="' . $user['userid'] . '" data-tousername="' . $user['username'] . '">';
									echo '<div class="wrap">';
									echo '<span id="status_' . $user['userid'] . '" class="contact-status ' . $status . '"></span>';
									echo '<img src="data:image/png;base64, ' . base64_encode($user['avatar']) . ' " alt="" />';
									echo '<div class="meta">';
									echo '<p class="name d-none d-sm-block">' . $user['username'] . '<span id="unread_' . $user['userid'] . '" class="unread">' . $chat->getUnreadMessageCount($user['userid'], $_SESSION['userid']) . '</span></p>';
									echo '<p class="preview"><span id="isTyping_' . $user['userid'] . '" class="isTyping"></span></p>';
									echo '</div>';
									echo '</div>';
									echo '</li>';
								}
								echo '</ul>';
								?>
							</div>
						</div>
						<div class="content col-11 col-sm-9 col-md-8  " id="content">
							<div class="contact-profile" id="userSection">
								<?php
								$userDetails = $chat->getUserDetails($currentSession);
								foreach ($userDetails as $user) {
									echo '<img src="data:image/png;base64, ' . base64_encode($user['avatar']) . ' " alt="" />';
									echo '<p>' . $user['username'] . '</p>';
								}
								?>
							</div>
							<div class="messages" id="conversation">
								<?php
								echo $chat->getUserChat($_SESSION['userid'], $currentSession);
								?>
							</div>
							<div class="message-input" id="replySection">
								<div class="message-input" id="replyContainer">
									<div class="wrap">
										<input type="text" class="chatMessage" id="chatMessage<?php echo $currentSession; ?>" placeholder="Escribe tu mensaje..." />
										<button class="submit chatButton" id="chatButton<?php echo $currentSession; ?>"><i class="fa fa-paper-plane" aria-hidden="true"></i></button>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		<?php } else {
			header("location:login.php");
		} ?>
	</div>

	<!-- Modal -->
	<div class="modal fade" id="modalRegistro" tabindex="-1" aria-labelledby="modalRegistroLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h1 class="modal-title fs-5" id="modalRegistroLabel">Mi perfil</h1>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					<form method="post" enctype="multipart/form-data">
						<div class="mb-3">
							<input type="text" class="form-control" name="inpNombres" placeholder="Nombre y Apellidos" value="<?= $loggedUser[0]['username'] ?>" required>
						</div>
						<div class="mb-3">
							<input type="email" class="form-control" disabled placeholder="Correo electronico" value="<?= $loggedUser[0]['email'] ?>">
							<input type="hidden" name="inpEmail" value="<?= $loggedUser[0]['email'] ?>">
						</div>
						<div class="mb-3">
							<input type="text" class="form-control" name="inpCelular" placeholder="Celular" value="<?= $loggedUser[0]['celular'] ?>" required>
						</div>
						<div class="mb-3">
							<label class="form-label">Fecha de nacimiento</label>
							<input type="date" class="form-control" name="inpFechaNac" value="<?= $loggedUser[0]['fecha_nacimiento'] ?>" required>
						</div>
						<div class="mb-3">
							<label class="form-label">Edad</label>
							<input type="text" class="form-control" name="inpEdad" value="<?= $chat->obtener_edad($loggedUser[0]['fecha_nacimiento']) ?>" disabled>
						</div>
						<div class="mb-3">
							<label class="form-label">Fecha registro</label>
							<input type="text" class="form-control" name="inpEdad" value="<?= $loggedUser[0]['fecha_registro'] ?>" disabled>
						</div>
						<div class="mb-3">
							<label for="" class="form-label">Foto de perfil </label>
							<input type="file" class="form-control" name="avatar" id="avatar" placeholder="" aria-describedby="fileHelpId">
						</div>
						<div class="mb-3">
							<input type="password" class="form-control" name="inpPwd" placeholder="Cambiar contraseña si desea">
						</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
					<!-- <button type="submit" class="btn btn-primary">Registrarse</button> -->
					<input type="submit" value="Modificar" name="modificar" class="btn btn-primary">
				</div>
				</form>
			</div>
		</div>
	</div>

	<?php include('footer.php'); ?>