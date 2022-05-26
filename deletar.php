<?php
include_once('conectDB.php');

$loja = $_REQUEST["loja"];
$id = $_REQUEST["id"];

$sql = "
UPDATE $loja SET `deletar`='S' WHERE `id`=$id;
";

if(mysqli_query($conexao, $sql)) {
    $data = 'true';
} else {
    $data = 'Erro. Tente novamente.';
}

echo json_encode($data);
?>