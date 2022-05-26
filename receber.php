<?php
include_once('conectDB.php');

$loja = $_REQUEST["loja"];
$id = $_REQUEST["id"];
$totalParcelas = $_REQUEST["totalParcelas"];
$ultimaParcela = $_REQUEST["ultimaParcela"];

$arr = array("." => "","," => ".");
$valorReceber = strtr($_REQUEST["valorReceber"],$arr);

$hoje = date('d-m-Y');
//Parcelas Pagas. Valor padrão 0. Formato: "1,150,2022-05-12;" + valor previo do banco (numero-da-parcela,valor,data;)
//Quitado: S ou N

if($ultimaParcela == $totalParcelas) {//Verifica se é a última parcela
    $quitado = 'S';
} else {
    $quitado = 'N';
}
$parcela = $ultimaParcela.",".$valorReceber.",".$hoje.";";

$sql = "
UPDATE $loja SET `parcelaspagas` = concat('$parcela','',`parcelaspagas`),`quitado`='$quitado' WHERE `id`=$id;
";


if(mysqli_query($conexao, $sql)) {
    $data = 'true';
} else {
    $data = 'Os dados não foram inseridos dessa vez. Tente novamente.';
}

echo json_encode($data);
?>