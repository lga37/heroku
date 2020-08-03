<?php 

namespace MVC\Controller;

/*

'PATH_INFO' => string '/v1/produto/455' (length=15)

REQUEST_METHOD

 */


class Api extends Controller{
	
	public function __construct(){

	}
	
	public function get_produtos(){
		$this->response((new \APP\Model\Produto())->getAll());
	}


	public function post_delproduto(array $params){
		#colocar CSRF depois

		$id = (int) $params['post']['id'];
		$model = new \APP\Model\Produto();

		if($model->getOne($id)){
			#var_dump(delOneBy($id));die;
			if($model->delOneBy($id)){
				$this->responseOK("Registro #$id excluido com sucesso.");
			} else {
				$this->responseError("Erro interno do BD.");
			}
		} else {
			$this->responseError("Registro #$id inexistente.");
		}
	}


	public function get_produto(array $params){
		$id = (int) $params['get']['id'];
		$model = new \APP\Model\Produto();

		if($model->getOne($id)){
			#var_dump(delOneBy($id));die;
			$this->response((new \APP\Model\Produto())->getOne($id));
		} else {
			$this->responseError("Registro #$id inexistente.");
		}
	}

	###############################################################
	public function get_index(){
		http_response_code(403);die('Forbidden');
	}

	private function responseError($txt){
		$array['ERROR']=$txt;
		$this->response($array,400);
	}
	private function responseOK($txt,int $code=200){
		$array['SUCCESS']=$txt;
		$this->response($array,$code);
	}

	private function response($array,int $code=200){
		http_response_code($code);
		header('Content-Type: application/json');
		echo json_encode($array,true);die;	
	}



}
