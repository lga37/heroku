<h2>CRUD Produtos</h2>
<?php #var_dump(URL) ?>
<form method="post" enctype="multipart/form-data" action="<?=URL.'?c=admin&a=save_produto'?>">

    <div class="form-row">
      <div class="col">
        <div class="md-form">
          <input type="text" class="form-control" name="sku" placeholder="SKU">
        </div>
      </div>
      <div class="col">
        <div class="md-form">
          <input type="text" class="form-control" name="nome" placeholder="Nome">
        </div>
      </div>
      <div class="col">
        <div class="md-form">
          <input type="text" class="form-control" name="preco" placeholder="Preco">
        </div>
      </div>
      <div class="col">
        <div class="md-form">
          <input type="text" class="form-control" name="qtd" placeholder="Qtd">
        </div>
      </div>
    </div>

    <div class="form-row">
      <div class="col">
        <div class="md-form form-group">
            <select class="custom-select browser-default" 
              multiple size="6" name="categs[]" id="categs" required>
                <option value="0" selected disabled>Selecione</option> 
                <?php 
                foreach ($categorias as $k => $categoria) {
                    extract($categoria);#id e nome
                    echo sprintf("<option value=\"%d\">%s</option>",$id,$nome);
                }
                ?>
            </select>
        </div>

      </div>

      <div class="col">
        <div class="md-form form-group file-field">
            <div class="btn btn-primary btn-sm">
                <span>Img</span>
                <input type="file" name="img">
            </div>
            <div class="file-path-wrapper">
               <input class="file-path validate" name="img" type="text" placeholder="Imagem">
            </div>
        </div>

      </div>
    </div>



    <div class="md-form form-group">
        <textarea name="descricao" id="descricao" class="form-control validate">
        </textarea>
        <label for="descricao" data-error="wrong" data-success="right">Descricao</label>
    </div>



    <div class="md-form form-group">
        <button class="btn btn-block btn-success btn-lg">OK</button>
    </div>


</form>



    <table class="table table-sm table-hover table-striped">
      <thead>
        <?php echo ordenate(['id','nome','sku','preco','qtd','-','-','-'],'UPD','DEL'); ?>
      </thead>
      <tbody>
        <?php 
        echo "<pre>";
        #print_r($produtos);
        #print_r($categorias);
        #print_r($categorias);
        echo "</pre>";


        if(isset($produtos) && is_array($produtos)):


          foreach($produtos as $key=>$produto):
              extract($produto);#id nome preco categs - nomecateg idcateg etc - vai dar confusao com categs
              $categs_bkp_implode = implode('|',$categs);
              
              $action_upd = URL."?c=admin&a=save_produto";
              if($img=="") $img="produto-sem-imagem.gif";

              $bt_del = sprintf("<a class='btn btn-sm btn-default' href='%s'><i class='fa red-text fa-trash'></i></a>",URL."?c=admin&a=del_produto&id=$id");

              $options = "";


        echo "<pre>";
        #print_r($categs_bkp);
        #print_r($categorias);
        echo "</pre>";
        #die;

              foreach ($categorias as $k => $categoria) {

                  $nome__categ = $categoria['nome'];
                  $id__categ = $categoria['id'];
                  $selected = in_array($id__categ,$categs)? "selected" : "";
                  $options .="<option $selected value='$id__categ'>$nome__categ</option>";
              }

              $tr = <<<TR

                <form class="form-inline" method="post" enctype="multipart/form-data" name="form_$id" action="$action_upd">
                  <tr>
                    <td style="width:50px;">
                      <input type="hidden" name="id" value="$id">
                      $id

                    <td>
                      <div class="md-form my-0">
                        <input class="form-control mr-sm-2" name="nome" value="$nome">
                      </div>
                    </td>

                    <td>
                      <div class="md-form my-0">
                        <input class="form-control mr-sm-2" style="width:50px;" name="sku" value="$sku">
                      </div>
                    </td>

                    <td>
                      <div class="md-form my-0">
                        <input class="form-control mr-sm-2" style="width:70px;" name="preco" value="$preco">
                      </div>
                    </td>
                    
                    <td>
                      <div class="md-form my-0">
                        <input class="form-control mr-sm-2" style="width:50px;" name="qtd" value="$qtd">
                      </div>
                    </td>

                    <td>
                      <div class="form-group">
                          <input type="hidden" name="categs_bkp_implode" value="$categs_bkp_implode">
                          <select class="custom-select browser-default" size="6" name="categs[]" id="categs" required multiple>
                            $options;
                          </select>
                      </div>   
                    </td>


                    <td>
                      <div class="md-form my-0">
                        <input class="form-control mr-sm-2" name="descricao" value="$descricao">
                      </div>
                    </td>

                    <td>
                      <div class="md-form form-group file-field">
                          <div class="btn btn-sm p-0 m-0">
                              <img src="assets/img/produtos/$img" width="50" height="50">
                              <input type="file" name="img">
                          </div>
                          <div class="file-path-wrapper p-1">
                             <input class="file-path validate" name="img2" type="text" placeholder="Imagem">
                          </div>
                      </div>
                    </td>

                    <td><button class='btn btn-sm btn-info'><i class='fa white-text fa-refresh'></button></td>
                    <td>$bt_del</td>
                  </tr>
                </form>                

TR;
                echo $tr;

          endforeach;
        else:  
          echo "<h1>Nao existem registros</h1>";
        endif;

        ?>
      </tbody>
    </table>


<br>

<?php 

$pag = extraiParametroURL('pag')? extraiParametroURL('pag') : 1;

echo paginate($total,$pag);