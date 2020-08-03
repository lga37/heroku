<?php


$errors=[];
if($_SERVER['REQUEST_METHOD']==='POST'){
    $_POST  = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);           

    $login = $_POST['login'];

    if(!filter_var($login,FILTER_VALIDATE_EMAIL)){
        $errors['login']="login invalido";
    }

    if(empty($errors)){
        $_SESSION['logado']="sim";
        session_regenerate_id();
        redirect('admin.php');

    } else {
        $texto = implode('<br>',$errors);
        msg($texto,"danger");
    }

}
?>

<form method="POST" name="login" action="?a=login">
    <fieldset>
        <legend>Ja sou Cliente</legend>
        <div class="form-group">
            <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-user"></i></span>
                <input class="form-control" name="login" placeholder="Email">
            </div>
        </div>
        <div class="form-group">
            <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-lock"></i></span>
                <input type="password" name="senha" class="form-control" placeholder="Senha">
            </div>
        </div>

        <div class="row">
            <div class="form-group col-xs-12">
                <button class="btn btn-lg btn-success-outline">Login</button>
                
            </div>
        </div>
    </fieldset>    
</form>

