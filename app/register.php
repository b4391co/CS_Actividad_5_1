<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
</head>
<body>
    <h1 class="display-1">Register</h1>
    <?php
    require_once 'conexion.php';

    $roles = findAllRoles();
    $email = null;
    $pwd = null;
    $rol = null;
    $rolRegistro = null;
    if(isset($_POST["email"])){
        $email = $_POST["email"];
        $emails = obtener_emails();
        foreach ($emails as $key => $value) {
            if ($value["email"] == $email){
        ?>
            <div class="alert alert-danger" role="alert">El email existe</div>
        <?php
            $email = null;
            break;
            }
        }
      
    }

    if(isset($_POST["roles"])){
        $rolRegistro = $_POST["roles"];
    }

    if(isset($_POST["pwd1"],$_POST["pwd2"]) && ($_POST["pwd1"] == $_POST["pwd2"])){
        
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
            <div class="mb-3">
                <label for="pwd2" class="form-label">Repetir Password</label>
                <input type="password" class="form-control" name="pwd2" id="pwd2">
            </div>
            <div class="mb-3 form-check">
            <div class="form-floating">
            <select name="roles" id="roles"  class="form-select" aria-label="Floating label select example" required>
            <option value="" disabled>----</option>
            <?php
            if (count($roles) > 0) :
                foreach ($roles as $rol) :
            ?>
                    <option value="<?= $rol["id"] ?>"><?= $rol["name"] ?></option>
            <?php
                endforeach;
            endif;
            ?>
            </select>
            <label for="floatingSelect">Works with selects</label>
            </div>
            </div>
            <button type="submit" name="btn_registrar" class="btn btn-primary">Submit</button>
        </form>
    </div>
</body>

<?php
    if ($pwd){
        $pwd_hash = password_hash($pwd, PASSWORD_BCRYPT);
    }
    
    if (isset($_POST["btn_registrar"]) && isset($email) && isset($pwd_hash) && isset($rolRegistro)) : ?>
        <div class="alert alert-success" role="alert">
    <?php
            $crearUsuario = crear_usuario($email,$pwd_hash,$rolRegistro);
            if ($crearUsuario){
    ?>
            El usuario con email: <b> <?= $email ?> </b>se ha creado correctamente
    <?php
            }
    ?>
            </div>
    <?php endif;


function findAllRoles(): array
{
    $conProyecto = getConnection();
    $pdostmt = $conProyecto->prepare("SELECT * FROM rol");

    $pdostmt->execute();
    $array = $pdostmt->fetchAll(PDO::FETCH_ASSOC);

    return $array;
}

function obtener_emails(): array
{
    $emails_array = null;
    try {
        $conProyecto = getConnection();
        $consulta = "SELECT email FROM usuario";
        $stmt = $conProyecto->prepare($consulta);

        $stmt->execute();
        $emails_array = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $ex) {
        die("Error al recuperar los emails " . $ex->getMessage());
    }

    return $emails_array;
}

function crear_usuario($email,$pwd_hash,$rolRegistro): bool
        {
            $exito = false;
        
            try {
                $conProyecto = getConnection();
            
                $conProyecto->beginTransaction();

                $insert_usuario = "insert into usuario (email, pwdhash)
                Values(:email, :pwdhash)";

                $insert_usuario_roles = "insert into usuario_rol(idUsuario, idRol)
                Values(:idUsuario, :idRol)";

                $stmt = $conProyecto->prepare($insert_usuario);
                $stmt-> bindParam(":email", $email);
                $stmt-> bindParam(":pwdhash", $pwd_hash);
                $exito = $stmt->execute();
                
                $usuario_id = $conProyecto->lastInsertId();

                $stmt_usuario_roles = $conProyecto->prepare($insert_usuario_roles);
                $stmt_usuario_roles->bindParam(":idUsuario", $usuario_id);
                $stmt_usuario_roles->bindParam(":idRol", $rolRegistro);
                $exito = $exito && $stmt_usuario_roles->execute();
                
                if ($exito) $conProyecto->commit();
                else $conProyecto->rollBack();

                // echo "<pre>";
                // $stmt->debugDumpParams();
                // $stmt_usuario_roles->debugDumpParams();
                // echo "</pre>";
            } catch (Exception $ex) {
                $conProyecto->rollBack();
                $exito = false;
                echo "Ocurrió un error al registrar: " . $ex->getMessage();
            }
            
            //Devolvemos el resultado de la operación
            return $exito;
        }

?>

</html>