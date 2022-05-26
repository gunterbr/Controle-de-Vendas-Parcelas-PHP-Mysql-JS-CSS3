<?php
include_once('conectDB.php');

$loja = $_REQUEST["loja"];
$cliente = $_REQUEST["cliente"];
$cpf = $_REQUEST["cpf"];
$docn = $_REQUEST["docn"];
$parcela = $_REQUEST["parcela"];

$arr = array("." => "","," => ".");

$preco = strtr($_REQUEST["preco"],$arr);
$avista = strtr($_REQUEST["avista"],$arr);
$valordaparcela = strtr($_REQUEST["valordaparcela"],$arr);

$produto = $_REQUEST["produto"];
$data = date('Y-m-d h:m:s');
//Parcelas Pagas. Valor padrão 0. Formato: "1,150,12.05.2022;" + valor previo do banco (numero-da-parcela,valor,data;)
//Parcelado: S ou N
//Quitado: S ou N

if($parcela != '0') {
    $parcelado = 'S';
    $quitado = 'N';
} else {
    $parcelado = 'N';
    $quitado = 'S';
}

$sql = "
INSERT INTO $loja
    (cliente,cpf,docn,preco,avista,parcela,valordaparcela,produto,data,parcelaspagas,atrasado,parcelado,quitado,deletar)
VALUES
    ('$cliente','$cpf','$docn','$preco','$avista','$parcela','$valordaparcela','$produto','$data','0','N','$parcelado','$quitado','N');
";


if(mysqli_query($conexao, $sql)) {
    $data = 'true';
} else {
    $data = 'Os dados não foram inseridos dessa vez. Tente novamente.';
}

echo json_encode($data);
?>