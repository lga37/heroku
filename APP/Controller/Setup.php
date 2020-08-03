<?php

namespace APP\Controller;

use MVC\Controller\Controller as ControllerMVC;

class Setup extends ControllerMVC{

	public function __construct(){
		parent::__construct('setup.php');
		$this->bd = new \MVC\Model\BD;
	}

	public function get_index(){
		$this->view->msg='Faca o Setup';
		$this->view->show();
	}

	public function get_reset(){
		$q = "DELETE FROM `prods`; DELETE FROM `prods_categs`; DELETE FROM `categs`;";
		$res = $this->model->query($q);

        echo "<pre>";
        print_r($res);
        echo "</pre>";
		$this->view->msg='Reset Realizado com Sucesso!';

	}

	public function get_installSchema(){


		$categs = <<<CATEGS
			CREATE TABLE `categs` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `nome` varchar(45) NOT NULL,
			  `pai` int(10) unsigned NOT NULL DEFAULT '0',
			  `created_at` timestamp(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
			  PRIMARY KEY (`id`),
			  UNIQUE KEY `index2` (`nome`)
			) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8
			;

CATEGS;

		$prods = <<<PRODS

			CREATE TABLE `prods` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `sku` varchar(45) NOT NULL,
			  `nome` varchar(145) NOT NULL,
			  `descricao` varchar(4500) DEFAULT NULL,
			  `qtd` int(11) NOT NULL DEFAULT '1',
			  `prazo` varchar(15) DEFAULT 'E',
			  `img` varchar(450) DEFAULT NULL,
			  `preco` float NOT NULL,
			  PRIMARY KEY (`id`),
			  UNIQUE KEY `sku_UNIQUE` (`sku`),
			  KEY `index3` (`sku`) USING BTREE
			) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8
			;

PRODS;

		$prods_categs = <<<PRODS_CATEGS

			CREATE TABLE `prods_categs` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `prod_id` int(10) unsigned NOT NULL,
			  `categ_id` int(10) unsigned DEFAULT NULL,
			  PRIMARY KEY (`id`),
			  UNIQUE KEY `index2` (`prod_id`,`categ_id`),
			  KEY `fk_prods_categs_1_idx` (`categ_id`)
			) ENGINE=InnoDB AUTO_INCREMENT=2097 DEFAULT CHARSET=utf8
			;


PRODS_CATEGS;


		extract(HEROKU); # $HOST $NAME $USER $PASS

		$cn = new \mysqli($HOST, $USER, $PASS, $NAME);
		#$cn = new \mysqli($host,$user,$pass,$name);
		if(mysqli_connect_errno()){
			echo mysqli_connect_error();
		}
		$steps = compact('categs','prods','prods_categs');
		#$steps = compact('user');
		#var_dump($steps);
		$tot = count($steps);
		$i=1;
		$m0='';
		foreach ($steps as $step=>$sql) {
			$sql = trim($sql);
			#echo $this->bd->exec($sql)? "OK $i/$tot [$step]<br>" : "Erro: <br>";
			$m0 .= $cn->query($sql)? "OK $i/$tot [$step]<br>" : "Erro:{$cn->error} <br>";
			$i++;
		}

		$this->view->m0 = $m0;
		$this->view->show();


	}

	public function post_seed(array $files){


 		$arquivo = $files['files']['arquivo'];
		$csvAsArray = array_map('str_getcsv', file($arquivo['tmp_name']));
		$csv = array_map('str_getcsv', file($arquivo['tmp_name']));

	    foreach ($csv as $key => $linhas) {
	    	$concat = '';
	    	foreach ($linhas as $linha) {
	    		if(empty($linha)) continue;
	    		$concat .= ' '. $linha;
	    	}
	    	$novo[] = $concat;
	    }

	    #echo "<pre>";print_r($novo);echo "</pre>";

	    unset($novo[0]); #tirei o cabecalho

	    $todas_categs = [];
	    foreach($novo as $linha){
	    	if(strlen($linha)>5){
	    		$parts = explode(";",$linha);
	    		if(count($parts)==6){
	    			$nome = trim($parts[0]);
	    			$sku = trim($parts[1]);
	    			$descricao = trim($parts[2]);
	    			$qtd = trim($parts[3]);
	    			$preco = trim($parts[4]);
	    			$categorias = trim($parts[5]);

	    			if(strlen($categorias)>1){
	    				$categs = explode("|",$categorias);
	    				foreach($categs as $categ){
						    if(!in_array($categ, $todas_categs, true)){
						        array_push($todas_categs, $categ);
						    }
						}

	    			}
	    			$prods[] = compact('nome','sku','descricao','qtd','preco','categs');
	    		}
	    	}
	    }


	    #$a = array_slice($prods, 3,7);
	    $this->popula($prods,$todas_categs);
	    #$this->popula($a,$todas_categs);

	}

	private function popula(array $prods,array $todas_categs){


    	#var_dump($prods);
	    foreach($todas_categs as $categ){
	    	$categ = ['nome'=>$categ];
	    	$categ_id = $this->model->setTable('categs')->upsert($categ);
	    	$categs_bd[] = $categ_id;
	    }

	    $total_produtos=0;
	    $prods_categs_bd = [];
	    foreach($prods as $k=>$prod){
	    	$categs = $prod['categs'];
	    	unset($prod['categs']);

	    	$prod_id = $this->model->setTable('prods')->upsert($prod);

	    	if(is_numeric($prod_id)){
	    		$total_produtos++;
	    	}
		    #echo "<pre>";print_r($categs);echo "</pre>";

		    foreach ($categs as $nome_categ) {
		    	$ret = $this->model->setTable('categs')->getOneBy($nome_categ,'id','nome');
		    	$categ_id = (int) $ret['id'];
		    	if($prod_id>0 && $categ_id>0){
			    	$prod_categ['prod_id']=$prod_id;
			    	$prod_categ['categ_id']=$categ_id;

			    	$prods_categs_bd[] = $this->model->setTable('prods_categs')->upsert($prod_categ);
		    	}

		    }

	    }
	    $this->view->m1= "<h1>Incluidas ". count($categs_bd) ." categorias </h1>";
	    $this->view->m2= "<h1>Incluidas $total_produtos produtos</h1>";
	    $this->view->m3= "<h1>Incluidas ". count($prods_categs_bd) ." prods_categs N:N</h1>";
	    $this->view->show();

	}

}
