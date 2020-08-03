<?php 

namespace MVC\View;

class View {
	public $data=[];
	protected $template;
	protected $_header;
	protected $_footer;
	private $msg=null;

	public function __construct()
	{
		$this->_header='_header.php';
		$this->_footer='_footer.php';
		$this->setTitulo(SITE);
	}

	public function setTitulo($titulo):void
	{
		$this->data['titulo']=$titulo;	
	}

	public function setTemplate($template):self
	{
		$this->template = $template;
		return $this;
	}

	function __set($key,$value):void
	{
		$this->data[$key]=$value;
	}



	function msg($premsg,$msg,$tipo='danger'):void
	{
		$msg = <<<MSG
		<br>
		<div class="alert alert-$tipo alert-dismissible fade show" role="alert">
		  <strong>$premsg</strong> $msg
		  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
		    <span aria-hidden="true">&times;</span>
		  </button>
		</div>
MSG;
		$this->msg =$msg;	
	}  




	private function render()
	{
		if(!headers_sent()){
			ob_start();

			#var_dump($this->data);die;

			extract($this->data);
			include $this->_header;
			#msg do tipo flash
			if($this->msg)
				echo $this->msg;


			include '../'.SITE.'/APP/View/'. $this->template;

			include $this->_footer;

			$html = ob_get_clean();
			return $html;
		}
	}

	public function show():void
	{
		echo $this->render();
		die;
	}
}


