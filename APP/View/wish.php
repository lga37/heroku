<?php

    if(!isset($_SESSION['wish']) || count($_SESSION['wish'])<1){
        echo "<h1>Wish vazio</h1>";
    } else {
        ?>
        <table class="table table-striped table-hover table-bordered table-condensed">
            <thead class="thead-default">
              <tr>
                <th>ID</th>
                <th>Produto</th>
                <th>Preco</th>
                <th>Cart</th>
                <th>Del</th>
              </tr>
            </thead>
        <?php

        foreach($_SESSION['wish'] as $item){
            extract($item);
            $linkDel = "<a class=\"btn btn-info\" href=".URL."?c=wish&a=del&id=".$id."><i class=\"fa fa-trash\"></i></a>";

            $linkCart = "<a class=\"btn btn-success\" href=".URL."?c=wish&a=addCart&id=".$id."><i class=\"fa fa-shopping-cart\"></i></a>";

            $url = URL;

            $tr = <<<TR
            <tr>
                <form class="form-inline" method="POST" action="$url?c=wish&a=upd&id=$id">
                    <td>$id</td>
                    <td>$nome</td>
                    <td>$preco</td>
                    <td>$linkCart</td>
                    <td>$linkDel</td>
                </form>
            </tr>
TR;
            echo $tr;

        }

        ?>


        </table>
        <a href="<?=URL?>?c=wish&a=clear" class="btn pull-left btn-outline-danger btn-lg">Cancel</a>
        <a href="<?=URL?>?c=wish&a=upgrade" class="btn pull-right btn-outline-success btn-lg">Upgrade</a>
        <?php 
    }