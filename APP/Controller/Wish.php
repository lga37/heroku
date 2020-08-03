<?php

namespace APP\Controller;
use APP\Model\Produto;
use APP\Model\Carrinho;
use MVC\Controller\Controller;

class Wish extends Controller{

    public $wish;

    public function __construct(){
        $this->wish = new \APP\Model\Wish();
        #var_dump($this->cart);
        parent::__construct('wish.php');
    }

    public function get_index(){
        $this->view->show();
    }
    
    public function get_add(array $get){
        $id = (int) $get['get']["id"];
        $this->wish->add($id);
        $this->view->show();

    }

    public function get_addCart(array $get){
        $id = (int) $get['get']["id"];
        $this->wish->del($id);
        $cart = new Carrinho();
        $cart->add($id);
        $this->view->show();

    }

    public function get_del(array $get){
        $id = (int) $get['get']["id"];
        $this->wish->del($id);
        $this->view->show();

    }

    public function get_upgrade(array $get){
        $this->view->msg("TODO","Implementar UPGRADE","info");
        $this->view->show();

    }


    public function get_clear(): void{
        $this->wish->clear();
        $this->view->show();

    }

}