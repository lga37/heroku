<?php 

namespace APP\Controller;
use MVC\Controller\Controller as ControllerMVC;

class Admin extends ControllerMVC{
	
	public function __construct(){
		parent::__construct('admin_login.php');

	}
	

	public function get_index(){
		#$this->redirect(URL.'?c=admin&a=home');

		if(isset($_SESSION['logado_adm']) && $_SESSION['logado_adm']==="sim"){
			$this->get_home();
		}
		$this->view->show();
	}

	public function post_index($post){
		extract($post['post']);
		if($login==="adm" && $senha==="123"){
			#qq coisa que se faz aqui antes p limpar a session da pau
			$_SESSION["logado_adm"]="sim";session_regenerate_id();
			$this->redirect(URL.'?c=admin&a=home');
		} else {
			$this->view->msg('Erro','Login ou Senha Invalidos.');
			$this->view->show();
		}	

	}

	public function get_logout(){
		unset($_SESSION);session_regenerate_id();
		$this->get_index();
	}


	public function get_home(){
		$this->view->setTemplate('admin_home.php');
		$this->view->show();
	}

	############################################################## CATEGS

	public function get_categs(){
		extract($this->parsePaginacaoOrdenacao()); #order sens where pag modo
		$orderController = $order .' '. $sens;
		$this->view->categorias = $this->model->setTable('categs')->getAllPaginate('*',$orderController,$pag);
		$this->view->setTemplate('admin_crud_categs.php');
		$this->view->show();
	}

	public function get_del_categ(array $get){
		$id = (int) $get['get']['id'];
		$res = $this->model->setTable('categs')->del($id);
		#var_dump($res);
		if($res){
			$this->view->msg("OK","Categ #$id excluida","success");
		} else {
			$this->view->msg("ERRO","Erro ao excluir Categ","danger");
		}
		$this->get_categs();
	}


	public function post_addcateg(array $post){
		$nome = (string) $post['post']['nome'];
		$categoria = compact('nome');
		$model = new \APP\Model\Categoria();
		$res = $model->add($categoria);
		#var_dump($res);die;
		if(is_string($res) && substr($res,0,5)==="ERROR"){
			$this->view->msg("ERRO","Categ nao inserido<br>$res","danger");
		} elseif(is_array($res)) {
			$this->view->msg("ERRO","Categ invalido :<hr>". implode("<br>",$res),"danger");
		} else {	
			$this->view->msg("OK","Categ ($res) inserido","success");
		}
		$this->get_categs();
	}


	public function post_upd_categs(array $post){
		$id = (int) $_GET['id']; #juntar get e post depois
		$nome = (string) $post['post']['nome'];
		$categoria = compact('id','nome');
		$model = new \APP\Model\Categoria();
		$res = $model->upd($categoria);
		#var_dump($res);die;
		if(is_string($res) && substr($res,0,5)==="ERROR"){
			$this->view->msg("ERRO","Categ nao atualizada<br>$res","danger");
		} elseif(is_array($res)) {
			$this->view->msg("ERRO","Erro BD - Categ invalido :<hr>". implode("<br>",$res),"danger");
		} else {	
			$this->view->msg("OK","Categ #$id atualizada","success");
		}
		$this->get_categs();
	}


	############################################################## PRODUTOS

	public function get_produtos(){

		$this->view->setTemplate('admin_crud_produtos.php');
		extract($this->parsePaginacaoOrdenacao()); #order sens where pag modo
		$orderController = $order .' '. $sens;

		$modelProd = new \APP\Model\Produto();
		$modelCateg = new \APP\Model\Categoria();
		#var_dump($modelCateg);
		#var_dump($modelCateg->getAll());
		#$total = (int) $model->count();
		#$this->view->total=$total;

		$this->view->produtos= $modelProd->getAllPaginateByLazyLoad($orderController,$pag); 
		
		$this->view->total= $modelProd->count(); 
		

		$this->view->categorias= $modelCateg->getAll(); 
		#var_dump($this->view);
		#var_dump($this->view->categorias);die;

		$this->view->show();
	}


	# se tiver i id e um update caso contrario e um insert
	public function post_save_produto(array $params){
		#echo "<pre>";print_r($params);#die;
		extract($params['files']['img']);
		#se vier vazio - name,type,tmp_name vem vazio e error=4 size=0
		#var_dump($foto);die;
		extract($params['post']);#nome descricao id sku
		$descricao=trim($descricao);

		$nome_produto = $sku.'-'.$nome;

		if(!empty($name)){
			$upl = upload($name,$type,$tmp_name,$error,$size,$nome_produto);		
			$path = UPLOADS;
			$pattern = "#".$path."#"; ########## Atencao acho que no PHP7.2 mudou
			$nome_foto_upl = is_array($upl)? "" : preg_replace($pattern, "", $upl);
		}


		$produto = compact('nome','preco','sku','descricao','qtd');
		if(isset($id) && is_numeric($id)){
			$produto['id'] = $id;
		}
		if(!empty($nome_foto_upl)){
			#echo "<h3>$nome_foto_upl</h3>";
			$produto['img'] = $nome_foto_upl;
		}

		#echo "<pre>";print_r($produto);die;


		$model = new \APP\Model\Produto();
		$res = $model->upsert($produto);


		if(is_string($res) && substr($res,0,5)==="ERROR"){
			$this->view->msg("ERRO","Produto nao atualizado<br>$res","danger");
		} elseif(is_array($res)) {
			$this->view->msg("ERRO","Produto invalido :<hr>". implode("<br>",$res),"danger");
		} else {	
			# o certo e claro seria fazer uma transaction aqui
			# fazer a diferenca entre categs e categs_bkp
			
			if(isset($id) && is_numeric($id)){
				$prod_id = $id;

			} else {

				$prod_id=(int) $res;
			}

			#var_dump($res);

			$categs_bkp_explode = isset($categs_bkp_implode)? explode("|", $categs_bkp_implode):[];


			$prods_categs_to_del = array_diff($categs_bkp_explode, $categs);
			#echo "<pre>";print_r($prods_categs_to_del);echo "</pre>";
			$prods_categs_to_add = array_diff($categs, $categs_bkp_explode);
			#echo "<pre>";print_r($prods_categs_to_add);echo "</pre>";
			foreach ($prods_categs_to_del as $categ_id) {
				$prod_categ = [$prod_id,$categ_id]; #Cuidado aqui - passei o valor direto
				$q = "DELETE FROM `prods_categs` WHERE `prod_id`=? AND `categ_id`=? LIMIT 10;";
				$r1=$model->setTable('prods_categs')->query($q,$prod_categ);
			}

			foreach ($prods_categs_to_add as $categ_id) {
				$prod_categ = ['prod_id'=>$prod_id,'categ_id'=>$categ_id];
				$r2=$model->setTable('prods_categs')->add($prod_categ);
			}

			#echo "<pre>";print_r($r1??'nao passou');echo "</pre>";
			#echo "<pre>";print_r($r2??'nao passou');echo "</pre>";


			$this->view->msg("OK","Produto atualizado","success");
		}
		
		$this->get_produtos();
		#$this->redirect('?c=admin&a=planos');# tem q usar o redirect senao ele mantem o action upd/del e o id || porem perco a msg flash 
	}


	public function get_del_produto(array $params){
		$id = (int) $params['get']['id'];
		if($affected_rows=(new \APP\Model\Produto())->delOneBy($id)){
			$this->view->msg("OK","Produto #$id deletado com sucesso ($affected_rows excluido(s))","success");
		} else {
			$this->view->msg("ERRO","Produto nao inserido","danger");
		}
		$this->get_produtos();
		#$this->redirect('?c=admin&a=planos');# tem q usar o redirect senao ele mantem o action upd/del e o id
	}



}