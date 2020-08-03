<?php 

function slug($nome){
    $str = strtolower(trim(preg_replace('/[^a-zA-Z0-9]+/', '-', $nome), '-'));
    return $str;
}


#se vier vazio - name,type,tmp_name vem vazio e error=4 size=0
function upload($name,$type,$tmp_name,$error,$size,$nome_produto=false){
    $erroUpload=[];
    
    switch($error){
        case 1:
        case 2:
            $erroUpload['error']="arq mto gde";
            break;
        case 3:
            $erroUpload['error']="arq parcialmente enviado";
            break;
        case 6:
        case 7:
        case 8:
            $erroUpload['error']="erro de sistema";
            break;
        case 4:
        #default:
            $erroUpload['error']="no file";
            break;
        #0 e OK 
    }

    if(!$erroUpload){

        $name = preg_replace("/[^A-Z0-9.-_]/i","_",$name);

        $i = 0;
        $parts = pathinfo($name);
        #nao esta funcionando, acho que o certo e ver pelo base64_encode
        while (file_exists(UPLOADS . $name)) {
            $i++;
            $name = $parts["filename"] . "-" . $i . "." . $parts["extension"];
        }
        #1  IMAGETYPE_GIF
        #2  IMAGETYPE_JPEG
        #3  IMAGETYPE_PNG       
        $fileType = exif_imagetype($tmp_name); #volta um inteiro..........
        #echo "aquiiii";
        $permitidas2 = [IMAGETYPE_GIF,IMAGETYPE_JPEG,IMAGETYPE_PNG];
        #var_dump($fileType);
        #var_dump($permitidas2);
        if (!in_array($fileType, $permitidas2)) {
            $erroUpload['type']="nao e um tipo/ext permitida";
            return $erroUpload; #melhor dar um break 
        }
        #extensoes 2
        $fileinfo = finfo_open(FILEINFO_MIME_TYPE);
        $filetype = finfo_file($fileinfo, $tmp_name);
        finfo_close($fileinfo);
        $mimetype = explode('/',$filetype);
        $mimetype = '.'. $mimetype[1];
        $mimetype = strtolower($mimetype);

        $permitidas2 = ['.jpg','.jpeg','.gif','.png'];
        #var_dump($mimetype);
        #var_dump($filetype);
        #var_dump($permitidas2);
        if(!in_array($mimetype, $permitidas2)){
            $erroUpload['type2']="extensao nao perm";
        }
        if(round($size/1024) > 5512){
            $erroUpload['size']="Tamanho max nao perm";
        }
        #$mime = "application/img; charset=binary";
        #exec("file -bi " . $tmp_name, $out);
        #if ($out[0] != $mime) {
        #   $erroUpload['mimetype']="erro no mimetype";
        #}

        $end = explode('.', $name);
        $end2 = end($end); 
        $extensao = ".".$end2;
        $extensao = strtolower($extensao);

        if(!in_array($extensao, $permitidas2)){
            $erroUpload['type3']="extensao nao perm ... essa e a 1 checagem";
        }
        $slug = $nome_produto? slug($nome_produto) : '';
        $novoNome = $slug.'-'.uniqid().$extensao;
        #echo UPLOADS,'<br>';
        $success = move_uploaded_file($tmp_name,UPLOADS . $novoNome);
        
        if (!$success) { 
            $erroUpload['upload']="erro , nao fez a move_uploaded_file";
        }else{

            @chmod(UPLOADS .'/'. $novoNome, 0644);
            @unlink($tmp_name);

            $caminho_raiz_uploads = UPLOADS . $novoNome;
            $caminho_imgs_prods = IMGS . $novoNome;

            $copied = copy($caminho_raiz_uploads , $caminho_imgs_prods);

            if (!$copied){
                $erroUpload['nao_copiou']="Nao copiou a img para $caminho_imgs_prods";
            }


        }
    }

    if(empty($erroUpload)){
        return UPLOADS . $novoNome;
    } else {
        return $erroUpload;
    }

}


function msg($msg,$h4,$tipo='danger'){
  $div = <<<DIV
    <div class="container alert alert-$tipo alert-dismissible show" role="alert">
        <button class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">x</span></button>
        <h4> $h4 </h4>
        <p> $msg </p>
    </div>
DIV;
  echo $div;
} 

function dd($msg){
    msg($msg,$msg);
    die;
}


#########################################################


function vitrine_tabela(array $produtos){
    #$disponib = retornaDisponibProduto($estoque,$prazo);
    $url = URL;
    $trs="";
    foreach ($produtos as $key => $produto) {
        extract($produto);
        $tr = <<<TR
        <tr>
            <td>$id</td>
            <td><a href="$url?c=produtos&a=detalhe&id=$id" class="card-link">$nome</a></td>
            <td>$preco</td>
            <td>

            <a href="$url?c=wish&a=add&id=$id" class="card-link"><i class="fa fa-heart"></i></a>

            <a href="$url?c=carrinho&a=add&id=$id" class="card-link"><i class="fa fa-shopping-cart"></i></a>
            <a href="$url?c=produtos&a=detalhe&id=$id" class="card-link"><i class="fa fa-external-link"></i></a>
            <a href="#" class="card-link"><i class="fa fa-share-alt"></i></a>
            </td>
        </tr>
TR;
        $trs .= $tr;
    }
    echo sprintf("<table class=\"table table-sm table-hover table-striped\"> %s </table>",$trs);
}


#function montaItemVitrine($id,$nome,$img,$preco,$prazo,$estoque){

function vitrine(array $produtos,int $cols=4){

  $html = "<div class=\"row\">";
  foreach ($produtos as $k => $produto) {
    $k++;
    extract($produto);
    $url = URL;
    #$disponib = retornaDisponibProduto($estoque,$prazo);
    ##### tem que mexer tb no cols-md-X de acordo com o num de colunas
    $nome = strtoupper($nome);
    $limite=24;
    if(strlen($nome)>$limite){
        $nome = substr($nome, 0, $limite).' ...';
    }
    $prazo=mt_rand(1,20);
    $img = $img ?? 'produto-sem-imagem.gif'; 
    #echo $img;
    $card = <<<CARD
    <div class="col-md-3">
      <div class="card">
        <p class="card-title">$nome</p>
        <img src="assets/img/produtos/$img" width="220" height="220">    
        <div class="card-img-overlay">
          <h5 class="pull-right card-subtitle"></h5>
        </div>
        <ul class="list-group list-group-flush">
          <li class="list-group-item">

            <a href="$url?c=wish&a=add&id=$id" class="card-link"><i class="fa fa-heart"></i></a>

            <a href="$url?c=carrinho&a=add&id=$id" class="card-link"><i class="fa fa-shopping-cart"></i></a>
            <a href="$url?c=produtos&a=detalhe&id=$id" class="card-link"><i class="fa fa-external-link"></i></a>
            <a href="#" class="card-link"><i class="fa fa-share-alt"></i></a>
          </li>
        </ul>
        <div class="card-footer">R$ $preco - $prazo dias</div>
      </div>
    </div>
CARD;

    $html .= $card;

    $html .= ($k % $cols == 0)? "</div>\n<br><div class=\"row\">\n" : "";

  }
  $html .= "\n</div>";


  echo $html;
}



function ordenate(array $cols,$upd='UPD',$del='DEL'){
    $sens = extraiParametroURL('sens');
    $order = extraiParametroURL('order');

    $sens_oposto = $sens=='asc'?'desc':'asc';
    $tr = "<tr>";
    foreach ($cols as $k => $col) {
      if($col==$order){
        $link = replaceLink(['order'=>$col,'sens'=>$sens_oposto]);
        $tr .= "<th><a href='$link'><b>$col</b> <i class='fa green-text fa-sort-$sens_oposto'></i></a></th>";
      } elseif($col=='-') {
        $tr .= "<th>-</th>";
      } else {
        $link = replaceLink(['order'=>$col,'sens'=>'asc','pag'=>1]);
        $tr .= "<th><a href='$link'>$col <i class='fa green-text fa-sort-asc'></i></a></th>";
      }      
    }

    if($upd) $tr .= "<th>$upd</th>";
    if($del) $tr .= "<th>$del</th>";
    $tr .= "</tr>";
    return $tr;
}

function extraiParametroURL($param){
    parse_str(parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY), $params);
    return $params[$param]?? false;
}


function replaceLink(array $params){
    parse_str(parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY), $url);    
    $uniao = array_merge($url,$params);
    $res = URL.'?'.http_build_query($uniao);
    return $res;
}

/**
Funcao responsavel por fazer a paginacao, inclusive com paginas adjacentes para dar uma experiencia mais amigavel de navegacao
*/

function paginate(int $total,int $pag=1,int $per_page=5){
  
    $totalPaginas = ceil($total / $per_page);

    $result = range(1, $totalPaginas);
    $adjacents = 4;

    if (($adjacents = floor($adjacents / 2) * 2 + 1) >= 1){
        $result = array_slice($result, max(0, min(count($result) - $adjacents, intval($pag) - ceil($adjacents / 2))), $adjacents);
    }

    $link_primeira = replaceLink(['pag'=>1]);
    $link_ultima = replaceLink(['pag'=>$totalPaginas]);
    
    $link_anterior = replaceLink(['pag'=>$pag-1]);
    $link_proxima = replaceLink(['pag'=>$pag+1]);

    if($pag>3){
        $paginas[] = sprintf('<li class="page-item"><a class="page-link" href="%s">1</a></li>',$link_primeira);
        $paginas[] = sprintf('<li class="page-item">...</li>');
    }
    if($pag>1){
        $paginas[] = sprintf('<li class="page-item"><a class="page-link" href="%s"><i class="fa red-text fa-arrow-left"></i></a></li>',$link_anterior);
    }

    foreach ($result as $k => $i) {
        $link = replaceLink(['pag'=>$i]);
        if($i==$pag){
            $active="active";
            $paginas[] = sprintf('<li class="page-item %s"><a class="page-link" href="%s">%d</a></li>',$active,$link,$i);
        }else{
            $active="";
            $paginas[] = sprintf('<li class="page-item %s"><a class="page-link" href="%s">%d</a></li>',$active,$link,$i);
        }
    }

    if($pag!=$totalPaginas && $totalPaginas >4){
        $paginas[] = sprintf('<li class="page-item"><a class="page-link" href="%s"><i class="fa red-text fa-arrow-right"></i></a></li>',$link_proxima);
    }

    if($pag<$totalPaginas && $totalPaginas >4){
        $paginas[] = sprintf('<li class="page-item">...</li>');
        $paginas[] = sprintf('<li class="page-item"><a class="page-link" href="%s">%d</i></a></li>',$link_ultima,$totalPaginas);
    }

    return sprintf("<nav><ul class=\"pagination pg-teal\">\n%s\n</ul></nav>",implode("\n",$paginas));
}



function enviaEmail($emailDeOrigem,$nomeDeOrigem,$senha,$emailDeDestino,$nomeDeDestino,$assunto,$msg){
    $mail = new PHPMailer();
    // Define os dados do servidor e tipo de conexão
    //$mail->SMTPDebug  = 2;
    //$mail->SMTPAuth = true; // Usa autenticação SMTP? (opcional)
    //$mail->Username = 'seumail@dominio.net'; // Usuário do servidor SMTP
    //$mail->Password = 'senha'; // Senha do servidor SMTP

    // Config Gmail
    $mail->IsSMTP(); // Define que a mensagem será SMTP
    $mail->SMTPAuth   = true;                  // enable SMTP authentication
    $mail->SMTPSecure = "tls";                 // sets the prefix to the servier
    $mail->Host       = "smtp.gmail.com";      // sets GMAIL as the SMTP server
    $mail->Port       = 587;                   // set the SMTP port for the GMAIL server
    $mail->Username   = $emailDeOrigem;         // GMAIL username
    $mail->Password   = $senha;                 // GMAIL password

    // Define o remetente
    $mail->SetFrom($emailDeOrigem, $nomeDeOrigem);
    $mail->AddReplyTo($emailDeOrigem, $nomeDeOrigem);

    // Define os destinatário(s)
    $mail->AddAddress($emailDeDestino, $nomeDeOrigem);
    //$mail->AddAddress('ciclano@site.net');
    //$mail->AddCC('ciclano@site.net', 'Ciclano'); // Copia
    //$mail->AddBCC('fulano@dominio.com.br', 'Fulano da Silva'); // Cópia Oculta

    // Define os dados técnicos da Mensagem
    $mail->ContentType = 'text/plain';
    #$mail->IsHTML(true);
    $mail->CharSet = 'UTF-8'; // Charset da mensagem (opcional)

    // Define a mensagem (Texto e Assunto)
    $mail->Subject  = $assunto; // Assunto da mensagem
    $mail->Body = $msg;
    $mail->AltBody = $msg; #texto PURO

    // Define os anexos (opcional)
    //$mail->AddAttachment("c:/temp/documento.pdf", "novo_nome.pdf");  // Insere um anexo
    $emailEnviado = $mail->Send();
    // Limpa os destinatários e os anexos
    $mail->ClearAllRecipients();
    #$mail->ClearAttachments();
  if (!$emailEnviado) {
      $m= "Informações do erro: <pre>" . print_r($mail->ErrorInfo) ."</pre>";
        msg("Não foi possível enviar o e-mail",$m,"danger");
        return false;
  }
    return true; #booleano
}


## Funcoes de Validacao
###################################################################
function limpaDeixaSomenteDigitos($input){
    return preg_replace('/[^\d]/', '', $input); 
}
function limpaDeixaSomenteLetras($input){
    return preg_replace('/[^\w]/', '', $input); 
}

function limpaDeixaSomenteAlfaNumUnderline($underline=true){
  return limpaDeixaSomenteLetras(limpaDeixaSomenteDigitos($input));
}

function formataMoeda(float $valor,$cifrao=''){
  return $cifrao . number_format($valor,2,",",".");
}

function validateNome($nome) {
  return preg_match('/^[\w \'.-]{3,90}$/i',$nome);
}

function validateEmail($email) {
  return filter_var($email,FILTER_VALIDATE_EMAIL);
}

function validateNumero($num) {
  $num = limpaDeixaSomenteDigitos($num);
  return preg_match('/[\d]/',$num);
}



function validateSenha($senha) {
  #return preg_match('/^(\w*(?=\w*\d)(?=\w*[a-z])(?=\w*[A-Z])\w*){6,}$/' , $senha);
  return strlen($senha)>=3 && strlen($senha)<=60;
}



function limpaStr2BD($str){
    ######## intervalos permitidos : da white list
    #32 - 38 = espaco - &
    #40 - 90 = ( - Z
    #97 - 122 = a - z

    $permitidos1 = range(32,38);
    $permitidos2 = range(40,90);
    $permitidos3 = range(97,122);
    $permit = array_merge($permitidos1,$permitidos2,$permitidos3);

    $str_nova = "";

    for($i=0;$i<strlen($str);$i++){
        if(in_array(ord($str[$i]),$permit)){
            $str_nova .= $str[$i]; 
        } 
    }

    return $str_nova;
}

