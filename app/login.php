<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
</head>
<body>
    <h1 class="display-1">Login</h1>
    <?php
    require_once 'conexion.php';

    $email = null;
    $pwd = null;
    if(isset($_POST["email"])){
        $email = $_POST["email"];
    }

    if(isset($_POST["pwd1"])){
            $pwd = $_POST["pwd1"];
    }
    ?>
    <div class="container-fluid">
        <form method="post">
            <div class="mb-3">
                <label for="email" class="form-label">Email address</label>
                <input type="email" class="form-control" name="email" aria-describedby="emailHelp">
            </div>
            <div class="mb-3">
                <label for="pwd1" class="form-label">Password</label>
                <input type="password" class="form-control" name="pwd1" id="pwd1">
            </div>
            <button type="submit" name="btn_login" class="btn btn-primary">login</button>
            <a href="register.php" type="submit" name="btn_login" class="btn btn-secondary">Register</a>
        </form>
    </div>
</body>

<?php
    
    if (isset($_POST["btn_login"]) && isset($email) && isset($pwd)) {
        $pwd_hash = getHash($email,$pwd);

        if(password_verify($pwd,$pwd_hash)){
    ?>
            <div class="alert alert-success" role="alert">Inicio de sesion correcto</div>
    <?php
            var_dump(getRolesUsuario($email));
        }else{
    ?>
            <div class="alert alert-danger" role="alert">Inicio de sesion incorrecto</div>
    <?php
    }
    ?>
    <?php 
    }

function getHash($email,$pwd){
        $pwd_hash = null;
        try {
            $conProyecto = getConnection();
            $consulta = "SELECT pwdhash FROM usuario WHERE email = :email";
            $stmt = $conProyecto->prepare($consulta);
            $stmt-> bindParam(":email", $email);
            $stmt->execute();
            $pwd_hash = $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $ex) {
            die("Error: " . $ex->getMessage());
        }
        return $pwd_hash["pwdhash"];
}

function getRolesUsuario($email){
    $id = null;
    try {
        $conProyecto = getConnection();
        $getRol = "SELECT * FROM rol WHERE id = 
        (SELECT idRol FROM usuario_rol WHERE idUsuario = 
        (SELECT id FROM usuario WHERE email = :email))";

        $stmt_rol = $conProyecto->prepare($getRol);
        $stmt_rol-> bindParam(":email", $email);
        $stmt_rol->execute();
        $rol = $stmt_rol ->fetch(PDO::FETCH_ASSOC);

    } catch (PDOException $ex) {
        die("Error: " . $ex->getMessage());
    }
    return $rol;
}

?>

</html>