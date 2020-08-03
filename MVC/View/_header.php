<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title><?= SITE ?></title>
    <link href="assets/css/font-awesome.min.css" rel="stylesheet">

    <link href="assets/css/style.css" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/5.0.0-alpha1/css/bootstrap.min.css" integrity="sha384-r4NyP46KrjDleawBgD5tp8Y7UzmLA05oM1iAEQ17CSuDqnUK2+k9luXQOfXJCJ4I" crossorigin="anonymous">


</head>

<body class="container-fluid">
    <header>


        <nav class="navbar navbar-light" style="background-color: #e3f2fd;">
            <a class="navbar-brand" href="<?= URL ?>"><b><?= SITE ?></b></a>

            <ul class="nav nav-pills">
                <li class="nav-item">
                    <a class="nav-link active" href="<?= URL ?>">Home
                        <span class="sr-only">(current)</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="<?= URL ?>?c=setup">Setup</a>
                </li>

                <?php if (isset($_SESSION['logado_adm']) && $_SESSION['logado_adm'] === "sim") : ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" id="dropdown_adm" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">Admin
                        </a>
                        <div class="dropdown-menu dropdown-default" aria-labelledby="dropdown_adm">

                            <a class="dropdown-item" href="<?= URL ?>?c=admin&a=categs">Categorias</a>
                            <a class="dropdown-item" href="<?= URL ?>?c=admin&a=produtos">Produtos</a>

                            <a class="dropdown-item" href="<?= URL ?>?c=admin&a=logout">LogOut</a>
                        </div>
                    </li>
                <?php else : ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= URL ?>?c=admin">Admin</a>
                    </li>
                <?php endif; ?>


                <li class="nav-item">
                    <a class="nav-link" href="<?= URL ?>?c=wish">Wish (<?= isset($_SESSION['wish']) ? count($_SESSION['wish']) : 0 ?>)</a>
                </li>
                <li class="nav-item">

                    <a class="nav-link" href="<?= URL ?>?c=carrinho">
                        <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-cart-plus" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M8.5 5a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1 0-1H8V5.5a.5.5 0 0 1 .5-.5z" />
                            <path fill-rule="evenodd" d="M8 7.5a.5.5 0 0 1 .5-.5h2a.5.5 0 0 1 0 1H9v1.5a.5.5 0 0 1-1 0v-2z" />
                            <path fill-rule="evenodd" d="M0 1.5A.5.5 0 0 1 .5 1H2a.5.5 0 0 1 .485.379L2.89 3H14.5a.5.5 0 0 1 .491.592l-1.5 8A.5.5 0 0 1 13 12H4a.5.5 0 0 1-.491-.408L2.01 3.607 1.61 2H.5a.5.5 0 0 1-.5-.5zM3.102 4l1.313 7h8.17l1.313-7H3.102zM5 12a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm7 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm-7 1a1 1 0 1 0 0 2 1 1 0 0 0 0-2zm7 0a1 1 0 1 0 0 2 1 1 0 0 0 0-2z" />
                        </svg>
                        Cart (<?= isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0 ?>)</a>
                </li>


                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-expanded="false">Dropdown</a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#">Action</a></li>
                        <li><a class="dropdown-item" href="#">Another action</a></li>
                        <li><a class="dropdown-item" href="#">Something else here</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item" href="#">Separated link</a></li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Link</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link disabled" href="#" tabindex="-1" aria-disabled="true">Disabled</a>
                </li>
            </ul>

            <form class="d-flex" method="POST" action="<?= URL ?>?c=produtos&a=produtosnome">

                    <input class="form-control mr-sm-2"
                    name="q" type="search" placeholder="Buscar" aria-label="Search">
                    <button class="btn btn-outline-primary">Search</button>

            </form>


        </nav>


    </header>

    <div class="row">

        <?php if (isset($setfull)) : ?>
            <div class="col-md-12">

            <?php else : ?>
                <div class="col-md-2">
                    <div id="menu-lateral">
                        <br>
                        <?php (new \APP\Model\Categoria())->render(); ?>
                        <br><br>
                    </div><!-- menu-lateral -->
                </div><!-- col-md-3 -->

                <div class="col-md-10">

                <?php endif; ?>
