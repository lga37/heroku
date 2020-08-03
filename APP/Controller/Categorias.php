<?php

namespace APP\Controller;

use MVC\Controller\Controller;


class Categorias extends Controller{

    #public $categorias=[];
    public $categoriaModel;

    public function __construct(){

        #var_dump($this->model);
        parent::__construct('listagem.php');
        $this->categoriaModel = new \APP\Model\Categoria();
    }


}
