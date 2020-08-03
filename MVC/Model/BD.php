<?php

namespace MVC\Model;


use Monolog\Logger;
use Monolog\Handler\RotatingFileHandler;


/**
Classe que gerencia toda a logica de BD. Compativel com mysql postgresql
*/

class BD {
	private static $instance;
	private static $cn;

	#private $log;
	function __construct($type="mysql"){
		#$log = new Logger(SITE);
		#$log->pushHandler(new RotatingFileHandler(LOGS.'log-'.date('d-m-Y').'-'.SITE, 5, Logger::DEBUG));

		#$this->log = $log;
        #echo "<pre>";print_r($_SERVER);die;
		$_SERVER['HTTP_HOST']=='localhost'? extract(BD) : extract(HEROKU); # $HOST $NAME $USER $PASS

		if($type==="mysql"){
			$dsn = sprintf("mysql:host=%s;dbname=%s",$HOST,$NAME);
			$options = [ \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'UTF8'", ];

		} else {
			$options = [];
			$dsn = sprintf("pgsql:host=%s;dbname=%s",$HOST,$NAME);
		}

		try {
		    #$p = new \PDO($dsn,$USER,$PASS,$options);
		    self::$cn = new \PDO($dsn,$USER,$PASS,$options);
		} catch (\PDOException $e) {
			#echo "ddddddddddddddddddddddddddddd";
		    echo "Exception do PDO :".$e->getMessage();
		}

	}

	/**
	Utilizacao do padrao de projeto singleton, garantira uma unica instancia da conexao BD
	*/

	public static function getInstance(){
		if(empty(self::$instance)){
			self::$instance = new self();
		}
		return self::$instance;
	}

	private static function getCN(){
		self::getInstance();
		return self::$cn;
	}

	public static function cn(){
		return self::getCN();
	}

	/**
	Essa e a funcao que faz toda a logica de persistencia e crud, ja foi bem testada
	Voltada para prepared statements. Suporta paginacao e ordenacao
	Se quiser aqui vc faz o escape de toda a saida do BD
	Mandar o order em string pura mesmo por causa de asc e desc
	*/

	function query(string $sql, array $campos=[],string $order=null,int $pagina=null) {
	    $pdo = self::getCN();

	    $sql = trim($sql);
		$tipo = strtolower(substr($sql, 0, 6));

		if($order){
			$sql .= " ORDER BY ". $order;
		}
		if($pagina){
			$per_pag=POR_PAG;

		    $offset = $pagina > 1? ($pagina * $per_pag)-$per_pag : 0;
		    $limit = $per_pag;
		    $sql .= sprintf(" LIMIT %d OFFSET %d",$limit,$offset);
		}

        $stmt = $pdo->prepare($sql);
        if(count($campos)>0){
            foreach ($campos as $i => $valor) {
                $i++;
                #$stmt->bindValue($i,$valor);
                $stmt->bindValue($i,$valor,$this->param_type($valor));
                #ta com erro em param_type, postgre nao funciona
            }
        }

		#$this->log->info($sql);
        #echo "<hr>";
        #echo $sql;
        #echo print_r($campos);
        #echo "<hr>";



        if(!$stmt->execute()){
       		return $stmt->errorInfo()[2]; # a chave 2 e que contem a descricao do erro

        } else {
			switch ($tipo) {
				case 'select':
		            #if($stmt->columnCount() == 1){
		            # return $stmt->fetchColumn();
		            # nao pode usar este recurso, pois no caso de select campo from x da erro
		            #}
					#No mysql ele vem sempre no formato da chave 0 contendo os dados
		        	$res = $stmt->fetchAll(\PDO::FETCH_ASSOC); #array vazio se nao tem nada
		        	#$array = array_walk_recursive($res, 'htmlspecialchars');
		        	#var_dump($res);die;
		        	#return $res?$res[0]:false;


		        	return $res;
				break;

				case 'insert':
		            return (int) $pdo->lastInsertId();
				break;

				case 'update':
				case 'delete':
					return (int) $stmt->rowCount();
					#se ele nao mudar nada no mysql vem 0, no postgre parece que vem sempre 1
				break;

				default:
					echo "metodo nao suportado";die;
				break;
			}

        }

	}

	/**
	Funcao aux que serve para informar o tipo de dado no prepared st
	Somente mysql. No postgre nao funciona
	*/

	private function param_type($param) {

	   	if (ctype_digit($param)) # atencao - deu erro no cnpj come o zero a esq
	        return $param <= PHP_INT_MAX ? \PDO::PARAM_INT :  \PDO::PARAM_STR;
	    if (is_bool($param))
	        return \PDO::PARAM_BOOL;
	    if (is_null($param))
	        return \PDO::PARAM_NULL;

	    return \PDO::PARAM_STR;

	}

	/**
	Funcao que monta a query para se debugar erros de SQL, pois com prepared st vc nao consegue printar a query do jeito correto
	*/

	protected function debugSQL($tabela, $sql,$post,$valores,$where){

		$q='';
		if(substr($sql,0,6)==="UPDATE"){
	        $set = "";
	        foreach ($post as $key => $value) {
	        	$set .= "$key = '$value',";
	        }
	        $set = trim($set,",");
    	    $q= "UPDATE $tabela SET $set WHERE $where;";

		} else if(substr($sql,0,6)==="INSERT"){
	        $q= "INSERT INTO $tabela ($post) VALUES ('". trim(implode("','",$valores),',') ."');";

		} else if(substr($sql,0,6)==="DELETE"){
	        $q= "DELETE FROM $tabela WHERE $where;";

		} else {
			$q= "ERRO em DEBUG";
		}

		#$this->log->info($q);

		#echo sprintf("<hr>%s<hr>",$q);

	}

}
