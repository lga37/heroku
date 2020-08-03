<?php

$categs = array(
                array('id'=>'1', 'nome'=>'Eletron', 'pai'=>'0'),
                array('id'=>'2', 'nome'=>'Hortifruti', 'pai'=>'0'),
                array('id'=>'3', 'nome'=>'Inform', 'pai'=>'0'),
                array('id'=>'4', 'nome'=>'Carnes', 'pai'=>'0'),
                array('id'=>'5', 'nome'=>'Frutas', 'pai'=>'2'),
                array('id'=>'6', 'nome'=>'Ovos', 'pai'=>'2'),
                array('id'=>'7', 'nome'=>'Verdura', 'pai'=>'2'),
                array('id'=>'8', 'nome'=>'Banana', 'pai'=>'5'),
                array('id'=>'9', 'nome'=>'Maca', 'pai'=>'5'),
                array('id'=>'10', 'nome'=>'Prata', 'pai'=>'8'),
                array('id'=>'11', 'nome'=>'Nanica', 'pai'=>'8'),
                array('id'=>'12', 'nome'=>'Nan Madura', 'pai'=>'11'),
                array('id'=>'13', 'nome'=>'Nanica Verde', 'pai'=>'11'),
                array('id'=>'14', 'nome'=>'Bovinas', 'pai'=>'4'),
                array('id'=>'15', 'nome'=>'Maca Gala', 'pai'=>'9'),
              );

#echo "<pre>";
#echo montaTrilha($categs, 8);
#echo "<br>";
#echo montaTrilhaReversa($categs, 8);
#print_r($categs);

function breadcrumber(array $categs,int $id, &$result=[]){
    foreach ($categs as $k => $categ) {
        if($categ['id']==$id){
            if($categ['pai']==0){

                $result[]= '[ '. $categ['nome'] .' ]';
            } else {
                $result[]=$categ['nome'];

            }
            unset($categs[$k]);
            breadcrumber($categs,$categ['pai'],$result);
        }
    }
    return $result;

}


function breadcumb($cidade,$bairro,$cep,$place,$place_id=false,$categ_id=false,$produto=false){
  $nav = "<nav aria-label='breadcrumb'>";
  $nav .= "<ol class='breadcrumb cyan lighten-3'>";

  $nav .= "<li class='breadcrumb-item'><a class='' href='listagem.php?q=$cidade'>$cidade</a></li>";
  $nav .= "<li class='breadcrumb-item'><a class='' href='listagem.php?q=$bairro'>$bairro</a></li>";
  $nav .= "<li class='breadcrumb-item'><a class='' href='listagem.php?q=$cep'>$cep</a></li>";
  if($place && $place_id && $categ_id){
    $nav .= "<li class='breadcrumb-item'><a class='' href='place.php?place_id=$place_id'>$place</a></li>";
  } else {
    $nav .= "<li class='breadcrumb-item active'>$place</li>";

  }
  if($categ_id){
      $categs = array_reverse(getTrilhaByCategID($categ_id));
      foreach ($categs as $categ_id => $categ) {
        $nav .= "<li class='breadcrumb-item'><a class='' href='#$categ_id'>$categ</a></li>";
      }
   }

  if($produto){
    $nav .= "<li class='breadcrumb-item active'>$produto</li>";

  }

  if($place_id){
    $nav .= "<li class='breadcrumb-item'>".montaIconeCarrinho($place_id)."</li>";
  }

  $nav .= "</ol></nav>";
  return $nav;
}


function montaTrilhaReversa(array $categs,int $id){
    return implode(" / ",array_reverse(breadcrumber($categs,$id)));
}

function montaTrilha(array $categs,int $id){
    return implode(" / ",breadcrumber($categs,$id));
}


#############################################################
function cmp($a, $b) {
    if($a['nome'] == $b['nome']) {
        return 0;
    }
    return ($a['nome'] < $b['nome']) ? -1 : 1;
}
#uasort($categs, 'cmp');

/*
uasort($categs, function ($a, $b) {
    return $a['nome'] <=> $b['nome'];
});
*/
#############################################################




#############################################################
#############################################################
#echo "<pre>";
#extract(preProcessOptGroup($categs)); #categs_modificado e hasChildren
#echo '<select name="category_id">';
#echo menuOptGroup($categs,$categs_modificado, $hasChildren);
#echo '</select>';

function preProcessOptGroup(array $categs){
    $all = [];
    $hasChildren = [];
    foreach($categs as $k=>$row){
        $categs_modificado[$row["id"]] = $row;
        $hasChildren[$row["pai"]] = true;
    }
    return ['categs_modificado'=>$categs_modificado,'hasChildren'=>$hasChildren];

}
################## rows = categs porem com as chaves diferentes
function menuOptGroup($categs,$categs_modificado, $hasChildren, $parentId=0,$categ_id=false, $num_loop=0) {
    #print_r($categs_modificado);die;
    #var_dump($categ_id);
    echo (!$categ_id && $num_loop==0)? '<option selected disabled>Selecione</option>':'';
    foreach ($categs_modificado as $id => $row) {
        $id=$row['id'];
        $trilha = implode(' / ',array_reverse(breadcrumber($categs,$id)));

        if($categ_id){
            $selected = $id==$categ_id?'selected':'';
        }

        if ($row["pai"] == $parentId) {
            if (isset($hasChildren[$id])) {
                $line = "\n<optgroup label=\"$trilha\"></optgroup>";
            } else {
                $line = "\n<option $selected value=\"$id\">$trilha</option>";
            }
            echo $line . "\n";
            if (isset($hasChildren[$id])) {
                menuOptGroup($categs,$categs_modificado, $hasChildren, $id, $categ_id, ++$num_loop);
            }

        }
    }
}
#############################################################
#############################################################





################################################################################
/*echo "<pre>";
print_r($categs);
$subs = geraArraySubs($categs);
$options = selectPermiteTodosExibeUltimaFolha($subs);
echo sprintf("<select name=pai class=\"form-control\">%s</select>",$options);
die;*/

function geraArraySubs(array $categs,$pai=0){
    $tmp=[];
    foreach($categs as $categ){
        if($categ['pai']==$pai){
            $categ['subs']=geraArraySubs($categs,$categ['id']);
            $tmp[]=$categ;
        }
    }
    return $tmp;
}

function selectPermiteTodosExibeUltimaFolha($subs,$selected=0,$d=0,&$res=""){
    if($selected==0 && $res==""){
        $res .= "\n<option selected value=0>Pai</option>";
    }
    $checked="";
    foreach($subs as $k=>$sub){

        if($selected>0){
            $checked = $sub['id']==$selected?'selected':'';
        }

        $res .= "\n<option ".$checked." value=".$sub['id'].">". str_repeat("-", $d) . $sub['nome'].'</option>';
        if(isset($sub['subs'])){
            $d++;
            selectPermiteTodosExibeUltimaFolha($sub['subs'],$selected,$d,$res);
            $d--;
        }
    }
    return $res;
}

#################################################################################
/*echo "<pre>";
print_r($categs);
$subs = geraArraySubs($categs);
$options = selectPermiteTodosExibeTrilha($categs,$subs,14);
echo sprintf("<select name='pai' class=\"form-control\">%s</select>",$options);
die;
*/
function selectPermiteTodosExibeTrilha($categs,$subs,$selected=0,$d=0,&$res=""){
    #print_r($subs);die;

    if($selected==0 && $res==""){
        $res .= "\n<option selected value='0'>Selecione</option>";
    }
    $checked="";
    foreach($subs as $k=>$sub){
        $id=$sub['id'];
        $trilha = implode(' / ',array_reverse(breadcrumber($categs,$id)));

        if($selected>0){
            $checked = $sub['id']==$selected?'selected':'';
        }

        $valor_selected = $checked=='selected'? $sub['pai'] : $sub['id'];
        $res .= "\n<option ".$checked." value='$valor_selected'>$trilha</option>";
        if(isset($sub['subs'])){
            $d++;
            selectPermiteTodosExibeTrilha($categs,$sub['subs'],$selected,$d,$res);
            $d--;
        }
    }
    return $res;
}




###########################################################################
$subs = geraArraySubs($categs);
#echo generateNavHTML($subs);

function generateNavHTML($subs){
    $html = '';
    foreach($subs as $sub){
        $html .= '<ul><li>';
        $html .= '<a href="#' . $sub['id'] . '">' . $sub['nome'] . '</a>';
        $html .= generateNavHTML($sub['subs']);
        $html .= '</li></ul>';
    }
    return $html;
}






####################################################################################
####################################################################################
/* $all = [];
$hasChildren = [];
foreach($categs as $k=>$row){
    $all[$row["id"]] = $row;
    $hasChildren[$row["pai"]] = true;
}
echo menuInfinitoBS4($all, $hasChildren, "0");
die;
 */



function menuInfinitoBS4($rows, $hasChildren, $parentId, $param='place_id',$valor=false,$level = 0) {

    #$url=getUrlAtualSemParametros();
    $link="?$param=$valor";

    if($parentId==0){
      $menu_html = PHP_EOL.'<ul class="nav flex-column flex-nowrap overflow-hidden">';
    }else{
      if (isset($hasChildren[$parentId])) {
        $menu_html = PHP_EOL.'<ul class="flex-column pl-2 nav" id="submenu-'.$parentId.'">';
      }
    }

    foreach ($rows as $id => $row) {
        if ($row["pai"] == $parentId) {
            if (isset($hasChildren[$id])) {
                $menu_html .= '<li class="nav-item"><a class="nav-link collapsed py-1"
                data-toggle="collapse" id="btn-'.$row['id'].'"
                data-target="#submenu-'.$row['id'].'" href="#">'.$row["nome"].'+</a>';
                $menu_html .= menuInfinitoBS4($rows,$hasChildren,$id,$param,$valor,$level + 1);
                $menu_html .= '</li>';
            }else{
                $id=$row['id'];
                $nome=$row['nome'];
                $menu_html .= "<li class='nav-item'><a class='nav-link text-truncate' href='$link&categ_id=$id'>$nome</a></li>";
            }
        }
    }
    ################################## atencao , falta um div aqui nos menus filhos
    $menu_html .= '</ul>';
    return $menu_html;
}

/*

                <ul class="nav flex-column flex-nowrap overflow-hidden">
                    <li class="nav-item">
                        <a class="nav-link text-truncate" href="#"><i class="fa fa-home"></i> <span class="d-none d-sm-inline">Overview</span></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link collapsed text-truncate" href="#submenu1" data-toggle="collapse" data-target="#submenu1"><i class="fa fa-table"></i> <span class="d-none d-sm-inline">Reports</span></a>
                        <div class="collapse" id="submenu1" aria-expanded="false">
                            <ul class="flex-column pl-2 nav">
                                <li class="nav-item"><a class="nav-link py-0" href="#"><span>Orders</span></a></li>
                                <li class="nav-item">
                                    <a class="nav-link collapsed py-1" href="#submenu1sub1" data-toggle="collapse" data-target="#submenu1sub1"><span>Customers</span></a>
                                    <div class="collapse" id="submenu1sub1" aria-expanded="false">
                                        <ul class="flex-column nav pl-4">
                                            <li class="nav-item">
                                                <a class="nav-link p-1" href="#">
                                                    <i class="fa fa-fw fa-clock-o"></i> Daily </a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link p-1" href="#">
                                                    <i class="fa fa-fw fa-dashboard"></i> Dashboard </a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link p-1" href="#">
                                                    <i class="fa fa-fw fa-bar-chart"></i> Charts </a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link p-1" href="#">
                                                    <i class="fa fa-fw fa-compass"></i> Areas </a>
                                            </li>
                                        </ul>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </li>
                    <li class="nav-item"><a class="nav-link text-truncate" href="#"><i class="fa fa-bar-chart"></i> <span class="d-none d-sm-inline">Analytics</span></a></li>
                    <li class="nav-item"><a class="nav-link text-truncate" href="#"><i class="fa fa-download"></i> <span class="d-none d-sm-inline">Export</span></a></li>
                </ul>


*/







function getPaiByCategID(int $categ_id, $d=0){
  $c = (new Model('categs'))->getOneBy($categ_id,'*','id');
  if($c['pai']==0 || $d>4){
    $x = $c['id'];
    return $x;
  } else {
    return getPaiByCategID($c['pai'],$d+1);
  }
}

function getTrilhaByCategID(int $categ_id, $trilha=[],$d=0){
  $c = (new Model('categs'))->getOneBy($categ_id,'*','id');
  if($c['pai']==0 || $d>4){
    $trilha[$c['id']]=$c['nome'];
    return $trilha;
  } else {
    $trilha[$c['id']]=$c['nome'];
    return getTrilhaByCategID($c['pai'],$trilha,$d);
  }
}
