<?php
if(!isset($produto) || !is_array($produto)){
    msg("Nenhum registro/produto encontrado","");die;
}
#var_dump($produto);
extract($produto);
?>
	<div class="row">
	    <div class="col-md-12 col-sm-12">

	        <br>

	        <div class="row">
            <div class="col-sm-6"><!-- 1 coluna -->
			
                  <img src="assets/img/produtos/<?= $img?>">

            </div><!-- .col-sm-6 -->

            <div class="col-sm-6"><!-- 2 coluna -->
              <div class="card">
                <div class="card-block">
                  <h4 class="card-title"><?=$nome?></h4>
                  <p class="card-text"><?=$descricao?></p>
                </div>
                <ul class="list-group list-group-flush">
                  <li class="list-group-item">R$ <?=$preco?></li>
                  <li class="list-group-item">Prazo:<?=mt_rand(0,20);?> dias</li>
                  <li class="list-group-item">Estoque:<?=$qtd?> Unids</li>
                </ul>
                <div class="card-block">

		            <a href="<?=URL?>?c=wish&a=add&id=<?=$id?>" class="card-link"><i class="fa fa-2x fa-heart"></i></a>
		            <a href="<?=URL?>?c=carrinho&a=add&id=<?=$id?>" class="card-link"><i class="fa fa-2x fa-shopping-cart"></i></a>
		            <a href="#" class="card-link"><i class="fa fa-2x fa-share-alt"></i></a>


                </div>
              </div>

            </div><!-- col-sm-6 -->
          </div><!-- .row -->
		
		<br>


	    </div><!-- col-md-12 -->
	</div><!-- row -->
