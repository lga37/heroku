<?php

namespace APP\Model;

use MVC\Model\Model as ModelMVC;


class Produto extends ModelMVC {

	public function __construct(){
		parent::__construct('prods');
		#$this->setTable('prods')->setPK('id');
	}

	public function addProduto(array $produto){
		$tem_erros = $this->invalid($produto);
		if(!$tem_erros){
			$rr= $this->add($produto); #ele vai retornar 0 pq o id nao e serial
				#echo $rr;die;
			return $rr;
		}
		return (array) $tem_erros;
	}

	/**
	Para a vitrine da home
	*/
	function getProdutosByRand(int $qtd=20): array
	{
	    $tabela = $this->getTable();
	    $sql = sprintf("SELECT * FROM `$tabela` ORDER BY rand() LIMIT $qtd;");
	    $r= $this->query($sql);
	    var_dump($r);die;
		return $this->query($sql);
	    #var_dump($sql);die;
	}



	/**
	Para a vitrine da home
	*/
	function getCategoriasByProdID(int $prod_id): ?array
	{

		$ret = $this->setTable('prods_categs')->getAllBy($prod_id,'prod_id','categ_id');

		$categs = array_column($ret, 'categ_id');

	    return $categs;
	}


	function delProduto(int $prod_id){
		$this->del($prod_id);
		$this->setTable('prods_categs')->delBy('prod_id',$prod_id);
	}


	/**
	Nao funciona o carregamento horizontal getProdutosByCategId
	*/
	function getProdutosByCategId(int $categ_id,string $order='id ASC', int $pag=1): ?array
	{
	    #$where="categ_id=?";
	    #$tabela = $this->getTable();
	    $sql = "
			SELECT p.*
			#,c.nome as nomecateg,c.id as idcateg
			FROM prods_categs pc
			#INNER JOIN categs c ON (pc.categ_id=c.id)
			INNER JOIN prods  p ON (pc.prod_id=p.id)
			WHERE pc.categ_id=?
	    	";

		$sql .= " ORDER BY ". $order;

		$per_pag=POR_PAG;

	    $offset = $pag > 1? ($pag * $per_pag)-$per_pag : 0;
	    $limit = $per_pag;
	    $sql .= sprintf(" LIMIT %d OFFSET %d",$limit,$offset);


	    #echo $sql;#die;
	    $valores=[$categ_id];


	    $ret = $this->query($sql, $valores);
	    #var_dump($ret);
	    return $ret;
	}


	function getCountProdutosByCategId(int $categ_id,string $order='id ASC', int $pag=1): ?int
	{
	    $sql = "

				SELECT count(1) as total FROM (

					SELECT p.id
					FROM prods_categs pc
					INNER JOIN prods  p ON (pc.prod_id=p.id)
					WHERE pc.categ_id=?


				) apelido;

	    	";


	    $valores=[$categ_id];
	    $res = $this->query($sql, $valores);
	    #var_dump($ret);
	    return (int) $res[0]['total']??null;
	}



    public function getAllPaginateByLazyLoad($order='id',$pag=1): ?array
	{

	    $prods_da_pagina = $this->getAllPaginate('*',$order,$pag);
	    foreach ($prods_da_pagina as $k=>$prod) {

	    	$categs_do_registro = $this->getCategoriasByProdID($prod['id']);

	    	$prods_da_pagina[$k]['categs']=$categs_do_registro;
	    }
	    #echo "<pre>";print_r($prods_da_pagina);die;
	    return $prods_da_pagina;


	}




	/**
	Como a instrucao LIKE segue formatacao diferente, criei este metodo so para isso.
	As demais buscas vao direto para o BD.
	*/
	function getProdutosByNome($nome,string $campos='*') {
	    $where="nome=?";
	    $tabela = $this->getTable();
	    $sql = sprintf("SELECT %s FROM %s WHERE lower(nome) LIKE ?;",$campos,$tabela,$where);

	    $valores=['%'.strtolower($nome).'%'];
	    return $this->query($sql, $valores);
	}


	/**
	Essa e a funcao que faz a checagem antes de mandar para o BD
	*/
	function invalid(array $produto){
		extract($produto);
		$errors=[];
		if(!$this->valida__nome($nome)){
			$errors['nome']="nome invalido";
		}
		return empty($errors)? false : $errors;

	}



	#######################################################################

    public function getCountPaginateByJoinCategs(): int
	{
	    #$where="categ_id=?";
	    #$tabela = $this->getTable();
	    $sql = sprintf("
			SELECT DISTINCT p.*
			,c.nome as nomecateg,c.id as idcateg
			FROM prods_categs pc
			INNER JOIN categs c ON (pc.categ_id=c.id)
			INNER JOIN prods  p ON (pc.prod_id=p.id)
	    ");
			#WHERE pc.categ_id=?; nao aceita comentarios dentro do sprintf Nao pode por ;

	    echo $sql;#die;
	    #$valores=[$categ_id];
	    #$campos=[];
	    #$pagina=1;
	    #$order= 'p.' .$orderController;
	    #query(string $sql, array $campos=[],string $order=null,int $pagina=null)
	    $ret = $this->countBy($sql);
	    #print_r($ret);die;
	    return $ret;
	}

	/*
		Esta errado este esquema horizontal
	 */
    public function getAllPaginateByJoinCategs($orderController='id ASC',$pag=1): ?array
	{
	    #$where="categ_id=?";
	    #$tabela = $this->getTable();
	    $sql = sprintf("
			SELECT DISTINCT p.*
			,c.nome as nomecateg,c.id as idcateg
			FROM prods_categs pc
			INNER JOIN categs c ON (pc.categ_id=c.id)
			INNER JOIN prods  p ON (pc.prod_id=p.id)
	    ");
			#WHERE pc.categ_id=?; nao aceita comentarios dentro do sprintf Nao pode por ;

	    echo $sql;#die;
	    #$valores=[$categ_id];
	    $campos=[];
	    #$pagina=1;
	    $order= 'p.' .$orderController;
	    #query(string $sql, array $campos=[],string $order=null,int $pagina=null)
	    $ret = $this->query($sql, $campos, $order, $pag);
	    #print_r($ret);die;
	    return $ret;
	}


	/**
	Aqui vem todas as checagens possiveis
	*/
	private function valida__nome(string $nome) : bool {
		return strlen($nome) > 5 && strlen($nome) < 80;

	}

	function retornaDisponibProduto($estoque,$prazo){
	    if($estoque < 1){
	        $disponib = ($prazo == 'E')? "esgotado" : $prazo." dia(s)";
	    } else {
	        $disponib = "Em stok ($estoque)";
	    }
	    return $disponib;
	}


}
