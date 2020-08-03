<?php

namespace APP\Model;

#Nao estendi da Model so pq uso os mesmos nomes de metodos como add, del
class Wish  {

	public function __construct(){
		if(!isset($_SESSION['wish'])){
			$_SESSION['wish'] = [];
		}
		return $_SESSION['wish'];
	}


	function has($id){
		return array_key_exists($id, $_SESSION['wish']) && $_SESSION['wish'][$id]['qtd']>0;
	}


	function add(int $id){
		$productModel = new \APP\Model\Produto();
		#var_dump($productModel);

		$prd = $productModel->getOne($id);
		#var_dump($prd);die;
		#poderiamos tb usar extract.
		$prod_nome = $prd['nome'];
		$id = $prd['id'];
		$estoque = $prd['qtd']; #atencao aqui p nao fazer confusao

		if(is_array($prd)){

			if(!$this->has($id)){
				$_SESSION['wish'][$id] = $prd;
				return true;
			}
		} else {

			return false;
		}
	}

	/*
	Deleta um item do carrinho.
	@param qtd int
	@param wish array
	@return bool
	*/
	function del($id){
		unset($_SESSION['wish'][$id]);
		# aqui entra um caso particular qdo deletamos o ultimo item do carrinho.
		# Neste caso excluimos ocarrinho inteiro, assim deletamos os valores.
		if(count($_SESSION['wish'])==0){
	        $this->clear();
		}
	}

	function clear(){
		unset($_SESSION['wish']);
	}

}