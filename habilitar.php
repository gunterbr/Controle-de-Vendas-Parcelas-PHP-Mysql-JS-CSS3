<?php

include_once('verifica_login.php');

if(!empty($user)) {
    if(!empty($_REQUEST['id'])) {

        $id = $_REQUEST['id'];
        
        $active = "
        UPDATE users SET `active` = '1' WHERE `id`=$id;
        ";
        if(!mysqli_query($conexao, $active)) {
            echo "Não foi possível habilitar!";
            echo "<meta http-equiv=\"refresh\" content=\"3;URL=index.php?msg=err\">";
        } else {
            echo "Você ativou um usuário com sucesso!";
            echo "<meta http-equiv=\"refresh\" content=\"3;URL=index.php?msg=sucess\">";
        }
    }
}

?>