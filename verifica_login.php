<?php
session_start();

include_once('conectDB.php');

$user = $_SESSION['usuario'];

$checkTable = mysqli_query($conexao, "SHOW TABLES Like 'users'");
$exists = mysqli_num_rows($checkTable) > 0 ? true : false;

if($exists == true) {

$query = "select * FROM users WHERE username = '{$user}' AND active = '1'";
$result = mysqli_query($conexao, $query);
$row = mysqli_num_rows($result);

$senha_final = "select * FROM users WHERE username = '{$user}' AND active = '1' AND senhafinal = 'N'";
$result_senha = mysqli_query($conexao, $senha_final);
$row_senha = mysqli_num_rows($result_senha);

$checkTable = mysqli_fetch_assoc($result);
$tabela = $checkTable['tabela'];
$MeuNegocio = $checkTable['nome'];

}

if (!$_SESSION['usuario']) {
	header('Location: home.php');//volta pro login
	echo "sessao nao aberta";
	exit();
}

if ($row == 0) {
	header('Location: home.php');//volta pro login
	echo "usuario nao encontrado";
	exit();
}

if ($row_senha > 0) {
	header('Location: trocarsenha.php');//trocar senha
	echo "senha deve ser trocada";
	exit();
}
?>