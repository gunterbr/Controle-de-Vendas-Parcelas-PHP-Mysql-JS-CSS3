<!DOCTYPE html>
<html lang='pt-br'>
    <head>
        <meta charset='utf-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <script src='https://code.jquery.com/jquery-3.5.0.js'></script>
        <link rel='stylesheet' href='css/style.css'>
        <title>Batista Variedades</title>
    </head>
<body>
<?php
include_once('conectDB.php');
echo "
<div class='formlogin'>
            <span class='container-form'>
<form action='newuser.php' method='POST'>
    <div>
    <label>Nome da empresa:
        <input name='empresa' placeholder='Meu Negócio' autofocus='' required>
    </label>
    </div>
    <div>
    <label>e-mail:
        <input name='email' type='email' placeholder='email@domain.com' required>
    </label>
    </div>
    <div>
    <label>Usuário:
        <input name='usuario' onkeyup='letra(this)' placeholder='somente letras' required>
    </label>
    </div>
    <div>
    <label>senha:
        <input name='senha' type='password' placeholder='Senha' required>
    </label>
    </div>
    <div>
    <a href='home.php'>< voltar</a>
    <label>
        <input type='submit' value='Registrar' >
    </label>
    </div>
</form>
</div>
</span>
";
if(!empty($_REQUEST['usuario'])) {

    $thisTable = $_REQUEST['usuario'].rand();
    $empresa = $_REQUEST['empresa'];
    $email = $_REQUEST['email'];
    $usuario = $_REQUEST['usuario'];
    $senha = $_REQUEST['senha'];

    $checkTable = mysqli_query($conexao, "SHOW TABLES Like 'users'");
    $exists = mysqli_num_rows($checkTable) > 0 ? true : false;

    if($exists == false) {
        $sql_users = "
        CREATE TABLE IF NOT EXISTS users (
            id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
            tabela VARCHAR( 50 ) NOT NULL,
            nome VARCHAR( 50 ) NOT NULL,
            username VARCHAR( 25 ) NOT NULL,
            passcode VARCHAR( 40 ) NOT NULL,
            email VARCHAR( 100 ) NOT NULL,
            nivel INT(1) UNSIGNED NOT NULL DEFAULT '1',
            active BOOL NOT NULL DEFAULT '1',
            senhafinal VARCHAR(1) NOT NULL DEFAULT 'N',
            cadastro DATETIME NOT NULL,
            PRIMARY KEY (id),
            UNIQUE KEY username (username),
            KEY nivel (nivel)
        ) ENGINE=MyISAM ;
        ";
        $user_default = "
        INSERT INTO users VALUES (NULL, NULL, 'Loja do Adm', 'admin', sha1('froiden'), 'admin@admin.com.br', 1, 1,'S', NOW( ));
        INSERT INTO users VALUES (NULL, '$thisTable', '$empresa', '$usuario', sha1('$senha'), '$email', 2, 0,'S', NOW( ));
        ";
        mysqli_query($conexao, $sql_users);
        if(!mysqli_multi_query($conexao, $user_default)) {
            echo "Existe um erro com o banco de dados :(";
        } else {
            echo "Obrigado :)</br>Seu pedido foi recebido. Entre em contato com o administrador para ativar o seu registro.";
        }
    } else {
        $newuser = "
        INSERT INTO users VALUES (NULL, '$thisTable', '$empresa', '$usuario', sha1('$senha'), '$email', 2, 0,'S', NOW( ));
        ";
        if(!mysqli_query($conexao, $newuser)) {
            echo "Este nome de <b>Usuário</b> já existe.";
        } else {
            echo "Obrigado :)</br>Seu pedido foi recebido.<br>Entre em contato com o administrador para ativar o seu registro. (95) 98802-8564 / eksmolvem@gmail.com";
        }
    }
}
?>
</body>
<script>
    function letra(i) {
        var v = i.value.replace(/[^a-zA-Z]/g,'');
        i.value = v;
    }
</script>
</html>