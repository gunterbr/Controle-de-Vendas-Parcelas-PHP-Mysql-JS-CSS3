<?php
    $db = 'controledevendas';
    $tabela = 'batistavariedades';//Altere para o nome da sua loja e seja feliz :)

    $conexao = new mysqli("localhost","root","");
    $create_db = "CREATE DATABASE IF NOT EXISTS $db CHARACTER SET utf8 COLLATE utf8_general_ci";
    if($conexao->connect_error) {
        die("connection error");
    }
    $response = $conexao->query($create_db);
    if(!$response) {
        echo "O Banco de Dados nãp pode ser criado!";
    }
    mysqli_select_db($conexao,$db);
?>