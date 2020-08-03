
<h1>Listagem de Produtos</h1>
<?php

if(!isset($verbo)):
  ?>
  <div class="row">
    <div class="col-md-4">
      <div class="md-form">
        <a href="<?=replaceLink(['modo'=>'v']) ?>"><i class="fa blue-text fa-th fa-2x"></i></a>
        <a href="<?=replaceLink(['modo'=>'t']) ?>"><i class="fa blue-text fa-table fa-2x ml-4"></i></a>  
      </div>
    </div>
  </div>

  <table class="table table-sm table-hover table-striped">
    <thead>
      <?php echo ordenate(['id','nome','preco'],'',''); ?>
    </thead>
  </table>
  <?php
  $pag = extraiParametroURL('pag')? extraiParametroURL('pag') : 1;
  echo paginate($total,$pag);

endif;

if(!empty($produtos)){
    echo extraiParametroURL('modo')==='t'? vitrine_tabela($produtos) : vitrine($produtos);

} else {
    #neste caso nossa consulta nao retornou dados. #echo "<h1>Nenhum registro encontrado</h1>";
    msg("Nenhum registro encontrado","Faça sua busca por palavra-chave, categoria ou cod produto");
}
