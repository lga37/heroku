<?php

    if(!isset($_SESSION['cart']) || count($_SESSION['cart'])<1){
        echo "<h1>Carrinho vazio</h1>";
    } else {
        ?>
        <table class="table table-striped table-hover table-bordered table-condensed">
            <thead class="thead-default">
              <tr>
                <th>ID</th>
                <th>Produto</th>
                <th>Preco</th>
                <th>Qtd</th>
                <th>Total</th>
                <th>Upd</th>
                <th>Del</th>
                <th>Wish</th>
              </tr>
            </thead>
        <?php
        $subTotal = 0;
        if(!isset($_SESSION['frete'])){
            $_SESSION['frete'] = mt_rand(0,100);
        }
        $frete = $_SESSION['frete'];
        $cep = $_SESSION['cep']??"";
        foreach($_SESSION['cart'] as $item){
            extract($item);
            $linkDel = "<a class=\"btn btn-danger\" href=".URL."?c=carrinho&a=del&id=".$id."><i class=\"fa fa-trash\"></i></a>";

            $linkWish = "<a class=\"btn btn-primary\" href=".URL."?c=carrinho&a=addWish&id=".$id."><i class=\"fa fa-heart\"></i></a>";

            $buttonUpd = "<button class=\"btn btn-info\"><i class=\"fa fa-refresh\"></i></button>";

            $inputUpd = "1";
            $url = URL;
            $subtotal_item = (float) $preco*$qtd;
            $tr = <<<TR
            <tr><form class="form-inline" method="POST" action="$url?c=carrinho&a=upd&id=$id">
                <td>$id</td><td>$nome</td><td>$preco</td>
                <td> 
                    <div class="md-form my-0">
                        <input class="form-control mr-sm-2" style="width:40px;" name="qtd" value="$qtd" type="number">
                    </div>
                </td>
                <td>$subtotal_item</td><td>$buttonUpd</td><td>$linkDel</td><td>$linkWish</td>
                </form>
            </tr>
TR;
            echo $tr;
            $subTotal += $subtotal_item;
        }
        $total = (float) $subTotal+$frete;
        ?>

        <tr class="warning">
            <td colspan="6">SubTotal</td>
            <td colspan="2"><?php echo number_format($subTotal,2,",","."); ?></td>
        </tr>
        <tr>
            <td>CEP</td>
            <td colspan="5">
            <form name="frete" class="form-inline" method="POST" action="<?=URL?>?c=carrinho&a=frete">
                <input type="number" value="<?=$cep?>" class="form-control" name="cep">
                <button class="btn btn-info"><i class="fa fa-truck"></i></button>
            </form>
            </td>
            <td colspan="2"><?php echo number_format($frete,2,",","."); ?></td>
        </tr>

        <tr class="info">
            <td class="font-weight-bold" colspan="6">Total</td>
            <td class="font-weight-bold" colspan="2"><b><?php echo number_format($total,2,",","."); ?></b></td>
        </tr>

        </table>
        <a href="<?=URL?>?c=carrinho&a=clear" class="btn pull-left btn-outline-danger btn-lg">Cancel</a>
        <a href="<?=URL?>?c=carrinho&a=end" class="btn pull-right btn-outline-success btn-lg">Checkout</a>
        <?php 
    }