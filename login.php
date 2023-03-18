<?php
session_start();
if (!empty($_SESSION['username'])) {
    header("location:index.php");
}
$loginError = '';
$loginMessage = '';
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

if (!empty($_POST['inpEmail'])) {
    include('Chat.php');
    $chat = new Chat();
    $userName = $_POST['inpNombres'];
    $userCelular = $_POST['inpCelular'];
    $userFN = $_POST['inpFechaNac'];
    $userEmail = $_POST['inpEmail'];
    $userPwd = $_POST['inpPwd'];
    $loginMessage = $chat->insertUser($userName, $userCelular, $userFN, $userEmail, $userPwd);
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="./css/styleLogin.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
</head>

<body>
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
                                <a href="#!" class="text-body">Olvido su contraseña</a>
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
                    <form method="post">
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js" integrity="sha384-mQ93GR66B00ZXjt0YO5KlohRA5SY2XofN4zfuZxLkoj1gXtW8ANNCe9d5Y3eG5eD" crossorigin="anonymous"></script>


</body>

</html>