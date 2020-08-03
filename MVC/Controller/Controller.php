<?php

namespace MVC\Controller;

use MVC\View\View as View;
use MVC\Model\Model as Model;


class Controller {
	protected $view;
	protected $model;

	public function __construct($template){
		$this->view = new View();
		$this->view->setTemplate($template);
		$this->model = new Model();

	}

	protected function redirect($pag){
		header('Location: '.$pag);die;
	}


	protected function clear(){
		unset($_SESSION);
	    #setcookie(session_name(), '', time() - 42000);
	    #session_regenerate_id();
		#session_destroy();
		return $this;
	}

	function parsePaginacaoOrdenacao(){
	    
	    parse_str(parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY), $url);    

	    $order = $url['order'] ?? 'id';
	    $sens = $url['sens'] ?? 'asc';
	    $where = $order .' '.$sens;
	    $pag = $url['pag'] ?? 1;
	    $modo = $url['modo'] ?? 't';

	    return compact('order','sens','where','pag','modo');
	}







}
