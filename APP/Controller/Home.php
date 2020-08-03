<?php 

namespace APP\Controller;

use APP\Model\Produto;
use MVC\Controller\Controller as ControllerMVC;

class Home extends ControllerMVC{
	
	public function __construct(){
		parent::__construct('home.php');
	}
	
	public function get_index(){

		$this->view->msg='Ola Mundo';
		$this->view->produtos = (new Produto())->getProdutosByRand();
		$this->view->show();
	}


}
