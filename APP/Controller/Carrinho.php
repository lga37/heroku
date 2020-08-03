<?php

namespace APP\Controller;
use APP\Model\Produto;
use APP\Model\Wish;
use MVC\Controller\Controller;

class Carrinho extends Controller{

    public $cart;

    public function __construct(){
        $this->cart = new \APP\Model\Carrinho();
        parent::__construct('carrinho.php');
    }

    public function get_index(){
        $this->view->show();
    }

    public function get_addWish(array $get){
        $id = (int) $get['get']["id"];
        $wish = new Wish();
        $wish->add($id);
        $this->cart->del($id);
        $this->view->show();

    }

    
    public function get_add(array $get){
        $id = (int) $get['get']["id"];
        $this->cart->add($id);
        $this->view->show();

    }

    public function get_del(array $get){
        $id = (int) $get['get']["id"];
        $this->cart->del($id);
        $this->view->show();

    }

    public function post_upd(array $post){
        $id = (int) $_GET['id']; #gambi
        $qtd = (int) $post['post']['qtd'];
        $this->cart->upd($id,$qtd);
        $this->view->show();
    }


    public function post_frete(array $post){
        $cep = (int) $post['post']['cep'];
        $this->cart->setCep($cep)->recalcFrete();
        $this->view->show();
    }


    public function get_clear(): void{
        $this->cart->clear();
        $this->view->show();

    }


    public function get_end(array $get){
        $this->view->msg("TODO","Implementar LOGIN","info");
        $this->view->show();

    }

}