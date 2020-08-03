<?php 

namespace MVC\Model;

/**

*/

class Model extends BD{

	private $table;
	private $pk;

	/**

	*/
	public function __construct(string $table='prods', string $pk='id'){
		parent::__construct();
		$this->setTable($table)->setPK($pk);
	}


	public function setTable($t){
		$this->table=$t;
		return $this;
	}

	public function getTable(){
		return $this->table;
	}

	public function setPK($pk){
		$this->pk=$pk;
	}

	public function getPK(){
		return $this->pk;
	}


	/**

	*/
	function getOneBy($valor,string $campos='*',string $where='id') {
	    $PK=$where."=?";
	    $tabela = $this->getTable();
	    $sql = sprintf("SELECT %s FROM %s WHERE %s LIMIT 1;",$campos,$tabela,$PK);

	    #echo $sql;
	    
	    $valores=[$valor];
	    $res = $this->query($sql, $valores);

	    if(is_string($res))
	    	dd("Erro fatal do BD: $res");
	    return $res[0]??$res; #################### ATENCAO 
	    ######## no caso de msg de erro ele sempre vai ter o res[0] que e a 1 letra
	}


	/**

	*/
	function getOne($valor,string $campos='*') {
	    $PK=$this->getPK()."=?";
	    #$PK="id=?";
	    $sql = sprintf("SELECT %s FROM %s WHERE %s LIMIT 1;",$campos,$this->getTable(),$PK);
	    $valores=[(int) $valor];

		#echo '<hr>'.$this->debugSQL($this->getTable(), $sql, $campos, $valores,null);
	    #var_dump($valores);

	    $res = $this->query($sql, $valores);
	    if(is_string($res))
	    	dd("Erro fatal do BD: $res  <br>$sql");

	    #return array_key_exists(0, $res)?$res[0]:$res; 
	    return $res[0]??$res;  
	}


	#function getAllBySQL($sql)
	#manda a funca para a query direto formato : SELECT xxx FROM tabela WHERE a=? and b=? -- junto com ['a'=>123,'b'=>'abc']


	/**

	*/

	function getAllBy($valor, $where, string $campos='*') {
	    #echo $where .'<hr>';
	    $w = $where ."=?";
	    $valores=[$valor];
	    $tabela = $this->getTable();
	    #echo $where;
	    $sql = sprintf("SELECT $campos FROM %s WHERE %s;",$tabela,$w);
	    
	    $debug = sprintf("SELECT $campos FROM %s WHERE %s=%s;",$tabela,$where,$valor);

	    #echo $debug;

	    $retorno = $this->query($sql,$valores);
	    #echo "<pre>";
	    #print_r($retorno);
	    return $retorno;
	}



	function getAll(string $campos='*') {
	    $sql = sprintf("SELECT $campos FROM %s;",$this->getTable());
	    $retorno = $this->query($sql);
	    #var_dump($retorno);die;
	    return $retorno;
	}

	/**

	*/
	function getAllPaginate(string $campos='*',string $order='id ASC',int $pag=1){
	    $sql = sprintf("SELECT $campos FROM %s ",$this->getTable());

		return $this->query($sql, [], $order, $pag);

	}

	/**

	*/
	function count(): int{
		$res = $this->query('SELECT count(1) as total FROM '.$this->getTable().';');
		#var_dump($res);
		return $res[0]['total']??false;
	}

	function countBy($sql): int{

		$q = "
				SELECT count(1) as total FROM ( 
				$sql 
				) apelido;
			";

		$res = $this->query($q);	
		#var_dump($res);die;
		return $res[0]['total']??false;

	}


	/**

	*/
	function save(array $post){
		$pk = $this->getPK();
		return array_key_exists($pk, $post) && is_numeric($post[$pk])? $this->upd($post) : $this->add($post);
	}



	/**

	*/
	function upd(array $post){
	    $tabela = $this->getTable();
	    #var_dump($post);
	    if(count($post) > 0){
	    	#echo "aaaaaaaaaaaaaaaaaaaaaaaaaaa";
	    
	    	$pk = $this->getPK();
	    	#var_dump($post);
	        ########################## usar array_push para por o id no final
	        if(!array_key_exists($pk, $post)){
	        	dd('Nao tem chave primaria');
  	        	#echo json_encode(['ERROR'=>'Nao tem chave primaria'],true);die;
	        }

	        $id=(int) $post[$pk];
	        unset($post[$pk]);
	        $where=$pk."=?";

	        $valores=array_values($post);#fazer antes de eliminar a pk
	        array_push($valores, $id); #colocar o id no final
	        $campos_pre=array_keys($post);

			$campos = array_map(function($val) { return '`'.$val.'`'; }, $campos_pre);

	        $campos=implode("=?,",$campos);
	        $campos.="=?";
	        
	        #echo 'cccccccccccc';
	        $sql = "UPDATE $tabela SET %s WHERE %s;";
	        #echo 'w:',$where;

	        $q = sprintf($sql, $campos, $where);
	        #echo $q;

	        $this->debugSQL($tabela, $sql,$post,$valores,'id='.$id);

	        return $this->query($q, $valores);

	    } else {
	    	msg('Erro - dados para update vazios');
        	#echo json_encode(['ERROR'=>'array vazio'],true);die;
	    }
		
	}



	function upsert(array $post){
	    $tabela = $this->getTable();
	    if(count($post) > 0){
	        $campos='`'.implode('`,`',array_keys($post)).'`';
	        $valores=array_values($post);
	        $valores_dupl = array_merge($valores,$valores);	

	        $values = trim(str_repeat('?,',count($post)),',');

	        $upd=array_keys($post);
	        $upd=implode("=?,",$upd);
	        $upd.="=?";

	        $sql = "INSERT INTO $tabela (%s) VALUES (%s) ON DUPLICATE KEY UPDATE %s;";
	        
	        $q = sprintf($sql, $campos, $values, $upd);

	        #echo $q;

			$this->debugSQL($tabela, $sql, $campos, $valores,null);
	        return $this->query($q, $valores_dupl);
	    } else {
	    	msg('Erro - dados para update vazios');
        	#echo json_encode(['ERROR'=>'array vazio'],true);die;
	    }
	}



	function add(array $post,$incluirPK=false){
	    $tabela = $this->getTable();
	    if(count($post) > 0){
	        if($incluirPK && array_key_exists($this->getPK(), $post)){
	        	unset($post[$this->getPK()]);
	        }

	        $campos='`'.implode('`,`',array_keys($post)).'`';
	        $valores=array_values($post);
	        $values = trim(str_repeat('?,',count($post)),',');

	        #$q = "INSERT INTO $tabela (nome) VALUES (?) ON CONFLICT (nome) DO UPDATE SET nome=?;";
	        $sql = "INSERT INTO $tabela (%s) VALUES (%s);";
	        
	        $q = sprintf($sql, $campos, $values);

			$this->debugSQL($tabela, $sql, $campos, $valores,null);
	        return $this->query($q, $valores);
	    } else {
	    	msg('Erro - dados para update vazios');
        	#echo json_encode(['ERROR'=>'array vazio'],true);die;
	    }
	}


	function del($valor){
	    $tabela = $this->getTable();
	    $pk = $this->getPK();
	    $where=$pk."=?";
	    $sql = sprintf("DELETE FROM %s WHERE %s LIMIT 1;",$tabela,$where);
	    $valores=[$valor];

	    return (bool) $this->query($sql, $valores); # se repetir 2x da erro pois ele deleta oq ja foi deletado	
	}



	function delOneBy($valor,$campo='id'){
	    $tabela = $this->getTable();
	    $PK=$campo."=?";
	    $sql = sprintf("DELETE FROM %s WHERE %s;",$tabela,$PK);
	    $valores=[$valor];

	    return (bool) $this->query($sql, $valores); # se repetir 2x da erro pois ele deleta oq ja foi deletado	
	}


}



