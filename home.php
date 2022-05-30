<?php
include_once('conectDB.php');

$checkTable = mysqli_query($conexao, "SHOW TABLES Like 'users'");
$exists = mysqli_num_rows($checkTable) > 0 ? true : false;

echo "
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
";

if($exists == true) {
    echo "

<div class='formlogin'>
    <span class='container-form'>
<form action='login.php' method='POST'>
<div class='field'>
<input name='usuario' name='text' class='loginput' placeholder='Usuário' autofocus='' required>
</div>
<div class='field'>
<input name='senha' class='loginput' type='password' placeholder='Senha' required>
</div>
<div class='field'>
<input type='submit' class='loginputsend' value='Log-In' >
</div>
<a href='newuser.php'>Registre-se</a>
";

if(isset($_SESSION['nao_autenticado'])):

echo "
<div class='err_color font-login'>
<p>Usuário ou senha inválidos.</p>
</div>";

endif;
unset($_SESSION['nao_autenticado']);

echo "
</form>
    </span>
</div>
";
} else {

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
    INSERT INTO users VALUES (NULL, '', 'Loja do Adm', 'admin', sha1('froiden'), 'admin@admin.com.br', 1, 1,'S', NOW( ));
    ";
    mysqli_query($conexao, $sql_users);
    mysqli_query($conexao, $user_default);
    echo "<meta http-equiv=\"refresh\" content=\"0;URL=home.php?msg=new-table-users\">";
}
echo "
</body>
</html>
";
?>


