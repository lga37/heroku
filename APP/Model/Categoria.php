<?php 

namespace APP\Model;
use MVC\Model\Model as ModelMVC;

class Categoria extends ModelMVC {

	public function __construct(){
		parent::__construct('categs');
	}


	public function render(){
		#tirei o <li> pq nao quis alterar CSS
		$categs = $this->getAll();
		$lis="";
		if(is_array($categs)){
			foreach ($categs as $categ) {
				$lis .= "<a href='".URL."?c=produtos&a=produtoscateg&id={$categ['id']}' class='list-group-item list-group-item-action waves-effect'>{$categ['nome']}</a>";
			}

			echo sprintf("<ul class='list-group'>%s</ul>",$lis);
		}else{
			echo "Categorias";
		}
	}

	/** 

	*/
	function getCategsByProdId(int $prod_id,string $campos='*') {
	    $where="prod_id=?";
	    $tabela = $this->getTable();
	    $sql = sprintf("SELECT %s FROM %s WHERE prod_id=?;",$campos,$tabela,$where);

	    #echo $sql;die;
	    $valores=[$prod_id];
	    $ret = $this->query($sql, $valores);
	    var_dump($ret);
	    return $ret;
	}

}