<form method="POST" action="<?=URL.'?c=admin'?>">

    <fieldset>
        <legend>Admin (login:adm e senha:123)</legend>

        <div class="md-form">
            <i class="fa fa-user prefix"></i>
            <input type="text" id="login" name="login" class="form-control validate">
            <label for="login" data-error="wrong" data-success="ok">Login</label>
        </div>

        <div class="md-form">
            <i class="fa fa-lock prefix"></i>
            <input type="password" id="senha"  name="senha" class="form-control validate">
            <label for="senha" data-error="wrong" data-success="ok">Senha</label>
        </div>

       <button class="btn btn-lg btn-block btn-outline-success">LOGIN</button>
       <br>
    </fieldset>    
</form>