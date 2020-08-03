<h1>Admin Categorias</h1>

<table class="table table-sm table-hover table-striped">
        <form class="form-inline" method="post" action="<?=URL?>?c=admin&a=addcateg">
        <tr>
        <td>Add:</td>
        <td>
          <div class="md-form my-0">
                <input class="form-control mr-sm-2" name="nome">
          </div>
        </td>
        </tr>
        <tr>
        <td colspan="2"><button class="btn btn-block btn-info"><i class='fa white-text fa-refresh'></button></td>
        </tr>
      </form>
</table>

<hr><br>
<table class="table table-sm table-hover table-striped">

  <thead>
    <?php echo ordenate(['id','nome'],'UPD','DEL'); ?>
  </thead>
  <tbody>


    <?php 

    foreach($categorias as $key=>$categoria):
        extract($categoria);


        $action_upd = URL."?c=admin&a=upd_categs&id=$id";


        $bt_del = sprintf("<a href='%s'><i class='fa red-text fa-trash'></i></a>",URL."?c=admin&a=del_categ&id=$id");
        $bt_upd = "<button class='btn btn-sm btn-primary'><i class='fa white-text fa-refresh'></i></button>";

        ?>
        <form class="form-inline" method="POST" name="form_<?=$id?>" action="<?=$action_upd?>">
        <tr>
        <td><label class="form-check-label" for="c_<?=$key?>"><?=$id?></label></td>
        
        <td>
			      <div class="md-form my-0">
                <input class="form-control mr-sm-2" name="nome" value="<?=$nome?>">
            </div>
        </td>
        <td><?=$bt_upd?></td>
        <td><?=$bt_del?></td>
        </tr>
    	</form>
      <?php 
    endforeach;
    ?>

  </tbody>
</table>

<br>



<?php 
$total = count($categorias);

$pag = extraiParametroURL('pag')? extraiParametroURL('pag') : 1;

echo paginate($total,$pag);