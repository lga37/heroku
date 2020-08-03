<?php

namespace APP\Controller;

use MVC\Controller\Controller;


class Produtos extends Controller{

    public $produtos=[];
    public $produtoModel;

    public function __construct(){

        #var_dump($this->model);
        parent::__construct('listagem.php');
        $this->produtoModel = new \APP\Model\Produto();
    }


    public function get_produtoscateg(array $get){
        #$query['id'] = (int) filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        
        extract($this->parsePaginacaoOrdenacao()); #order sens where pag modo
        $orderController = $order .' '. $sens;
        
        $id = (int) $get['get']['id'];


        $this->view->total = $this->produtoModel->getCountProdutosByCategId($id,$orderController,$pag);
        $this->view->produtos = $this->produtoModel->getProdutosByCategId($id,$orderController,$pag);
        $this->view->show();

    }


    public function post_produtosnome(array $post){
        $this->view->verbo='POST';
        $q = (string) filter_var($post['post']['q'],FILTER_SANITIZE_STRING);

        $this->view->produtos = $this->produtoModel->getProdutosByNome($q);

        $this->view->show();

    }




    public function get_detalhe(array $get){
        $this->view->setTemplate('detalhes.php');
        extract($get['get']);#id
        if(isset($id) && is_numeric($id)){
            $id = (int) $id;
            $this->view->produto = $this->produtoModel->getOne($id);
        } else {
            $this->view->msg = "Erro";
        }
        $this->view->show();
    }


}
