<?php
session_start();
if (!empty($_SESSION['username'])) {
    header("location:index.php");
}
$loginError = '';
$loginMessage = '';
$recuperar = false;
//Validar logueo
if (!empty($_POST['username']) && !empty($_POST['pwd'])) {
    include('Chat.php');
    $chat = new Chat();
    $user = $chat->loginUsers($_POST['username']);
    if (!empty($user)) {
        if (password_verify($_POST['pwd'], $user[0]['password'])) {
            $_SESSION['username'] = $user[0]['username'];
            $_SESSION['userid'] = $user[0]['userid'];
            $chat->updateUserOnline($user[0]['userid'], 1);
            $lastInsertId = $chat->insertUserLoginDetails($user[0]['userid']);
            $_SESSION['login_details_id'] = $lastInsertId;
            header("Location:index.php");
        } else {
            $loginError = "Contraseña Invalida";
        }
    } else {
        $loginError = "Usuario Invalido";
    }
}

//realiza registro
if (!empty($_POST['inpEmail']) && isset($_FILES['avatar']['name'])) {
    include('Chat.php');
    $chat = new Chat();
    $userName = $_POST['inpNombres'];
    $userCelular = $_POST['inpCelular'];
    $userFN = $_POST['inpFechaNac'];
    $userEmail = $_POST['inpEmail'];
    $userPwd = $_POST['inpPwd'];
    $userPregunta1 = $_POST['inpPregunta1'];
    $userPregunta2 = $_POST['inpPregunta2'];
    $userPregunta3 = $_POST['inpPregunta3'];

    //avatar
    $tipoArchivo = $_FILES['avatar']['type'];
    $nombreArchivo = $_FILES['avatar']['name'];
    $tamanoArchivo = $_FILES['avatar']['size'];
    $imagenSubida = fopen($_FILES['avatar']['tmp_name'], 'r');
    $binariosImagen = fread($imagenSubida, $tamanoArchivo);


    if ($tipoArchivo == "image/jpeg" || $tipoArchivo == "image/png") {
        $loginMessage = $chat->insertUser($userName, $userCelular, $userFN, $userEmail, $userPwd, $binariosImagen, $userPregunta1, $userPregunta2, $userPregunta3);
    } else {
        $loginMessage = "<div class='alert alert-warning'>No fue posible resgistrarte, la foto de perfil debe ser jpeg o png</div>";
    }
}

if (!empty($_POST['inpPwdRecuperar'])) {
    include('Chat.php');
    $chat = new Chat();

    $userEmail = $_POST['inpEmailRecuperar2'];
    $userPwd = $_POST['inpPwdRecuperar'];
    $loginMessage = $chat->updatePwdUser($userEmail, $userPwd);
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="./css/styleLogin.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.6.4.js" integrity="sha256-a9jBBRygX1Bh5lt8GZjXDzyOB+bWve9EiO7tROUtj/E=" crossorigin="anonymous"></script>
</head>

<body>
    <?php
    if (!empty($_POST['inpEmailRecuperar'])) {
        include('Chat.php');
        $chat = new Chat();
        $userEmail = $_POST['inpEmailRecuperar'];
        $userPregunta1 = $_POST['inpPregunta1R'];
        $userPregunta2 = $_POST['inpPregunta2R'];
        $userPregunta3 = $_POST['inpPregunta3R'];
        $loginMessage = $chat->recuperarUser($userEmail, $userPregunta1, $userPregunta2, $userPregunta3);
        if ($loginMessage == "<div class='alert alert-success'>Las respuestas fueron correctas, puedes realizar cambio de contraseña</div>") {
            echo "<script>";
            echo "$( document ).ready(function() {";
            echo "        $('#modalCambioPwd').modal('toggle')";
            echo "});";
            echo "</script>";
        }
    }

    ?>
    <div class="container">
        <div class="card text-center mt-5">
            <div class="card-body">
                <?php if ($loginMessage) {
                    echo $loginMessage;
                } ?>
                <div class="row">
                    <div class="col-md-8 col-lg-6 col-xl-5">
                        <img src="./img/chat.png" class="img-fluid" alt="chat">
                    </div>

                    <div class="col-md-8 col-lg-6 col-xl-4 offset-xl-1 pt-4">
                        <?php if ($loginError) { ?>
                            <div class="alert alert-danger"><?php echo $loginError; ?></div>
                        <?php } ?>
                        <form method="post">
                            <!-- Email input -->
                            <div class="form-outline mb-4">
                                <input type="email" id="username" name="username" class="form-control form-control-lg" placeholder="Ingrese un correo valido" required />
                                <label class="form-label" for="form3Example3">Correo</label>
                            </div>

                            <!-- Password input -->
                            <div class="form-outline mb-3">
                                <input type="password" id="pwd" name="pwd" class="form-control form-control-lg" placeholder="ingrese contraseña" required />
                                <label class="form-label" for="form3Example4">Contraseña</label>
                            </div>

                            <div class="d-flex justify-content-between align-items-center">
                                <a href="#!" class="text-body" data-bs-toggle="modal" data-bs-target="#modalRecuperar">Olvido su contraseña</a>
                            </div>

                            <div class="text-center text-lg-start mt-4 pt-2">
                                <button type="submit" class="btn btn-primary btn-lg">Ingresa</button>
                                <p class="small fw-bold mt-2 pt-1 mb-0">No tienes cuenta? <a data-bs-toggle="modal" data-bs-target="#modalRegistro" class="link-danger">Registrate</a></p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="modalRegistro" tabindex="-1" aria-labelledby="modalRegistroLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="modalRegistroLabel">Registrate</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="post" enctype="multipart/form-data">
                        <div class="mb-3">
                            <input type="text" class="form-control" name="inpNombres" placeholder="Nombre y Apellidos" required>
                        </div>
                        <div class="mb-3">
                            <input type="text" class="form-control" name="inpCelular" placeholder="Celular" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Fecha de nacimiento</label>
                            <input type="date" class="form-control" name="inpFechaNac" required>
                        </div>
                        <div class="mb-3">
                            <input type="email" class="form-control" name="inpEmail" placeholder="Correo electronico" required>
                        </div>
                        <div class="mb-3">
                            <input type="password" class="form-control" name="inpPwd" placeholder="Contraseña" required>
                        </div>
                        <div class="mb-3">
                            <label for="" class="form-label">Foto de perfil </label>
                            <input type="file" class="form-control" name="avatar" id="avatar" placeholder="" aria-describedby="fileHelpId" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Pregunta de seguridad 1</label>
                            <input type="text" class="form-control" name="inpPregunta1" placeholder="¿Cuál es su comida favorita?" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Pregunta de seguridad 2</label>
                            <input type="text" class="form-control" name="inpPregunta2" placeholder="¿Cuál era el nombre de su héroe de la infancia?" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Pregunta de seguridad 3</label>
                            <input type="text" class="form-control" name="inpPregunta3" placeholder="¿Cuál era el nombre de su primera mascota?" required>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <!-- <button type="submit" class="btn btn-primary">Registrarse</button> -->
                    <input type="submit" value="Registrarse" class="btn btn-primary">
                </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal recuperar-->
    <div class="modal fade" id="modalRecuperar" tabindex="-1" aria-labelledby="modalRecuperarLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="modalRecuperarLabel">Olvidaste tu contraseña?</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="post" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label class="form-label">Correo registrado:</label>
                            <input type="email" class="form-control" name="inpEmailRecuperar" placeholder="Correo electronico" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Pregunta de seguridad 1</label>
                            <input type="text" class="form-control" name="inpPregunta1R" placeholder="¿Cuál es su comida favorita?" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Pregunta de seguridad 2</label>
                            <input type="text" class="form-control" name="inpPregunta2R" placeholder="¿Cuál era el nombre de su héroe de la infancia?" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Pregunta de seguridad 3</label>
                            <input type="text" class="form-control" name="inpPregunta3R" placeholder="¿Cuál era el nombre de su primera mascota?" required>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <!-- <button type="submit" class="btn btn-primary">Registrarse</button> -->
                    <input type="submit" value="Recuperar contraseña" class="btn btn-primary">
                </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal cambio contraseña-->
    <div class="modal fade" id="modalCambioPwd" tabindex="-1" aria-labelledby="modalCambioPwdLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="modalCambioPwdLabel">Contraseña nueva</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="post" enctype="multipart/form-data">
                        <div class="mb-3">
                            <input type="password" class="form-control" name="inpPwdRecuperar" placeholder="Contraseña nueva" required>
                            <input type="hidden" name="inpEmailRecuperar2" value="<?= $_POST['inpEmailRecuperar'] ?>">
                        </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <!-- <button type="submit" class="btn btn-primary">Registrarse</button> -->
                    <input type="submit" value="Recuperar contraseña" class="btn btn-primary">
                </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js" integrity="sha384-mQ93GR66B00ZXjt0YO5KlohRA5SY2XofN4zfuZxLkoj1gXtW8ANNCe9d5Y3eG5eD" crossorigin="anonymous"></script>

</body>

</html>