<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
</head>
<body>
    <?php
    require_once 'conexion.php';

    $roles = findAllRoles();
    ?>
    <div class="container-fluid">
        <form>
            <div class="mb-3">
                <label for="exampleInputEmail1" class="form-label">Email address</label>
                <input type="email" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp">
                <div id="emailHelp" class="form-text">We'll never share your email with anyone else.</div>
            </div>
            <div class="mb-3">
                <label for="exampleInputPassword1" class="form-label">Password</label>
                <input type="password" class="form-control" id="exampleInputPassword1">
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
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>
</body>

<?php
function findAllRoles(): array
{
    $conProyecto = getConnection();
    $pdostmt = $conProyecto->prepare("SELECT * FROM rol");

    $pdostmt->execute();
    $array = $pdostmt->fetchAll(PDO::FETCH_ASSOC);

    return $array;
}
?>

</html>
