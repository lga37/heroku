<?php

namespace APP\Model;

#Nao estendi da Model so pq uso os mesmos nomes de metodos como add, del
class Carrinho  {

	public $cart;

	public function __construct(){

		if(!isset($_SESSION['cart'])){
			$_SESSION['cart'] = [];
		}
		return $_SESSION['cart'];

	}


	/*
	Analisa se o item ja existe no carrinho atual.
	@param id int
	@param cart array
	@return bool
	*/
	function has($id){
		return array_key_exists($id, $_SESSION['cart']) && $_SESSION['cart'][$id]['qtd']>0;
	}

	/*
	Analisa se o item pode ser adicionado ao carrinho em função do estoque e prazo.
	@param qtd int
	@param estoque int
	@param prazo string

	@return bool
	*/
	function can($qtd,$estoque,$prazo){
		return true;
		#return !($qtd > $estoque && $prazo=='E');
	}

	/*
	Filtra a qtd que pode ser alterada, deixando sempre entre 1 e 100.
	@param qtd int

	@return int
	*/
	function validateQtd($qtd){
		if(is_numeric($qtd)){
			if($qtd < 1){
				$qtd = 1;
			}
			if($qtd > 100){
				$qtd = 100;
			}
		} else {
			$qtd = 1;
		}
		return intval($qtd);
	}

	/*
	Adiciona um item ao carrinho.
	@param qtd int
	@param cart array

	@return bool
	*/
	function add(int $id){
		$productModel = new \APP\Model\Produto();
		#var_dump($productModel);

		$prd = $productModel->getOne($id);
		#var_dump($prd);die;
		#poderiamos tb usar extract.
		$prod_nome = $prd['nome'];
		$id = $prd['id'];
		$prazo = $prd['prazo'];
		$estoque = $prd['qtd']; #atencao aqui p nao fazer confusao
		$qtd = 1;

		if(is_array($prd)){
			$prd['qtd'] = $qtd;
			# so coloco a qtd se vier certo do banco, senao ele cria um array vazio so com qtd.
			#print_r($prd);die;
			#var_dump(!$this->has($id));die;
			if(!$this->has($id) && $this->can($qtd,$estoque,$prazo)){
				$_SESSION['cart'][$id] = $prd;
				#tenho que dar um refresh para alterar frete, pesos, descontos
				return true;
			#}else{
			#	throw new \Exception("Produto Esgotado - $prod_nome ");
			}
		} else {

			return false;
		}
	}

	/*
	Deleta um item do carrinho.
	@param qtd int
	@param cart array
	@return bool
	*/
	function del($id){
		unset($_SESSION['cart'][$id]);
		# aqui entra um caso particular qdo deletamos o ultimo item do carrinho.
		# Neste caso excluimos ocarrinho inteiro, assim deletamos os valores.
		if(count($_SESSION['cart'])==0){
	        $this->clear();
		}
	}

	function clear(){
		unset($_SESSION['cart']);
		unset($_SESSION['frete']);
		unset($_SESSION['cep']);
	}

	function upd($id, $qtd){

		$qtd = $this->validateQtd($qtd);
		$_SESSION['cart'][$id]['qtd']=$qtd;
		$this->refresh();
		return true;                    
	}


	function getSubTotal(){
		$sub = 0;
		$cart = $_SESSION['cart'];
		foreach($cart as $item){
			$sub += $item['preco']* $item['qtd'];
		}
		return $sub;
	}

	function getPeso(){
		$tot = 0;
		$cart = $_SESSION['cart'];
		foreach($cart as $item){
			$tot += $item['peso']* $item['qtd'];
		}
		return $tot;
	}

	function getFrete(){
		return $_SESSION['frete'];
	}

	function getDesconto(){
		return 0;
	}

	function getTotal(){
		#aproveitei para usar aqui o operador ternario curto.
		#return getSubTotal()+getFrete()-getDesconto() ?: "";
		if($this->getSubTotal()+$this->getFrete()-$this->getDesconto()){
			return $this->getSubTotal()+$this->getFrete()-$this->getDesconto();
		}
		return "";
	}


	/*
	Retorna o valor do cep
	@return string
	*/
	function getCep(){
		return $_SESSION['cep']?? "";
	}

	/*
	Retorna o valor do cupom
	@return string
	*/
	function getCupom(){
		return $_SESSION['cupom']?? "";
	}


	/*
	Efetua o calculo do desconto e grava na session.
	A logica varia muito e cada pessoa tem uma maneira de calcular descontos.
	exemplo se a pessoa digita FR vai para o 1 item do switch
	@return bool
	*/

	function recalcDesconto(){
		$cupom = $this->getCupom();
		# ou pela data
		#XXX(F|P|B)XXX()
		$desconto = "";
		switch($cupom){
			case 'FR': #frete
				$desconto = $this->getFrete();
			break;
			case 'PE': #percent
				$percent=0.1;
				$desconto = number_format($this->getSubTotal() * $percent);
			break;
			case 'BR': #vlr bruto
				$valor=50;
				$desconto = $valor;
			break;
			default:
				$desconto = "";

		}
		$_SESSION['desconto']=$desconto;
		#return true;
	}

	public function setCep($cep){
		$_SESSION['cep']=$cep;
		return $this;
	}


	/*
	Efetua o calculo do frete e grava na session. Usa um fator de calculo.
	Exemplo - Carrinho deu 4kg - Sudeste , fator=3 , entao frete = 3*4 = 12
	Exemplo - Carrinho deu 7kg - Sudeste , fator=3 , entao frete = 3*7 = 21

	Exemplo - Carrinho deu 7kg - Nordeste , fator=6 , entao frete = 6*7 = 42
	Exemplo - Carrinho deu 0kg - Nordeste , fator=6 , como peso==0, entao frete = 6
	Poderiamos usar varios tipos de funções aqui.

	@return bool
	*/
	function recalcFrete(){
		return mt_rand(0,100);
		#o primeiro digito de um cep identifica a regiao.
		$cep = $this->getCep();
		$peso = $this->getPeso();
		$frete = "";
		if(empty($cep)){
			$_SESSION['frete']="";
		}else{
			switch(substr($cep,1)){
				#case '0': #Gde SP , esta dando erro ...
				case '1': #Interior SP
				case '2': # RJ,ES
				case '3': # MG
					#NESTA REGRA A CADA QUILO A MAIS ADICIONAMOS 3r$ de frete
					$fator = 3;
					$frete = $peso >0? $peso * $fator : $fator;
					break;

				case '4': # BA, SE
				case '5': # PE, AL, PB, RN
				case '6': #regiao Norte + CE, PI, MA
				case '7': #regiao C-Oeste + RO
				case '8': #PR, SC
				case '9': #RS
					#NESTA REGRA A CADA QUILO A MAIS ADICIONAMOS 6r$ de frete
					$fator = 6;
					$frete = $peso >0? $peso * $fator : $fator;
					break;

				default:
					#Se a regiao nao existe, vamos deixar engessado como 10.00
					$frete = 10;
			}
			$_SESSION['frete']=number_format($frete,2);
		}

		#return true;
	}


	/*
	Atualiza os valores do carrinho, a toda operacao que envolve trocar/deletar a qtd.
	@return void
	*/
	function refresh(): void{
		$this->recalcFrete();
		$this->recalcDesconto();
	}


}