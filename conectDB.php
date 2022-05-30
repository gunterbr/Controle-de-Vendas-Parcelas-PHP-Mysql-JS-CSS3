<?php

    $db = 'gunter06_loja';

    $conexao = new mysqli("localhost","root","");
    $create_db = "CREATE DATABASE IF NOT EXISTS $db CHARACTER SET utf8 COLLATE utf8_general_ci";
    if($conexao->connect_error) {
        die("connection error");
    }
    $response = $conexao->query($create_db);
    if(!$response) {
        echo "O Banco de Dados não pode ser criado!";
    }
    mysqli_select_db($conexao,$db);
?>
