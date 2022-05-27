<?php
include_once('verifica_login.php');
?>
<!DOCTYPE html>
<html lang='pt-br'>
    <head>
        <meta charset='utf-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <script src='https://code.jquery.com/jquery-3.5.0.js'></script>
        <link rel='stylesheet' href='css/style.css'>
        <title><?php echo $MeuNegocio;?></title>
    </head>
    <body>
        <span class='chatbot'>
            <span class='chatbot__loading'></span>
            <span class='chatbot__loading'></span>
            <span class='chatbot__loading'></span>
        </span>
        <header class='header-wrapper'>

<?php
if($user == 'admin') {
    $habilitar = $conexao->query("SELECT * FROM `users` WHERE `active`='0' ORDER BY active ASC, id DESC");
    
    if(mysqli_num_rows($habilitar) > 0) {
        while($linha = $habilitar->fetch_assoc()) {
            echo "
            <label>Nome: <input value='".$linha['nome']."' readonly></label>
            <label>usuário: <input value='".$linha['username']."' readonly></label>
            <label>e-mail: <input value='".$linha['email']."' readonly></label>
            <label>cadastro: <input value='".$linha['cadastro']."' readonly></label>
            
            <form action='habilitar.php' method='POST'>
                <input type='hidden' name='id' value='".$linha['id']."' >
                <label><input type='submit' class='habilitar' value='habilitar' ></label>
            </form>
            <br>";
        }
    } else {
        echo 'Nenhum usuário pendente!';
    }
}
?>

        <label class='logout'>
            <a href='logout.php'>< sair</a>
        </label>

<?php

    if(!empty($tabela)) {
        echo "
            <span class='header'>
                <span class='checkbox-container'>
                    <span class='checkbox-wrapper'>
                        <input type='checkbox' id='toggle'>
                        <label class='checkbox' for='toggle'>
                            <span class='trace'></span>
                            <span class='trace'></span>
                            <span class='trace'></span>
                        </label>
                        <span class='menu'></span>
                        <span class='menu-items'>
        ";

    include_once('conectDB.php');

    $TabelaLoja = "
    CREATE TABLE IF NOT EXISTS $tabela(
        id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
        cliente VARCHAR(50) NOT NULL,
        cpf VARCHAR(14) NOT NULL,
        docn VARCHAR(50) NOT NULL,
        preco VARCHAR(9) NOT NULL,
        avista VARCHAR(9) NOT NULL,
        parcela VARCHAR(2) NOT NULL,
        valordaparcela VARCHAR(9) NOT NULL,
        produto VARCHAR(100) NOT NULL,
        data DATETIME,
        parcelaspagas VARCHAR(200) NOT NULL,
        atrasado VARCHAR(1) NOT NULL,
        parcelado VARCHAR(1) NOT NULL,
        quitado VARCHAR(1) NOT NULL,
        deletar VARCHAR(1) NOT NULL);
    ";
    mysqli_query($conexao, $TabelaLoja);

    $clienteParcelado = $conexao->query("SELECT * FROM $tabela WHERE quitado='N' AND deletar='N' ORDER BY parcelado DESC, atrasado DESC, id DESC");
    
    if(mysqli_num_rows($clienteParcelado) > 0) {
        $montante = 0;
        while($row = $clienteParcelado->fetch_assoc()) {

            $id = $row['id'];

            $match = explode(',', $row['parcelaspagas']);
            $Parcela_u = $match[0];
            $ultimaParcela = $Parcela_u + 1;
            $total_de_parcelas = $row['parcela'];

            $dataVenda = date("d-m-Y", strtotime($row['data']));
            $dias_vencimento = $ultimaParcela*30;
            $vencimento = date('d-m-Y', strtotime("+$dias_vencimento days", strtotime($dataVenda)));

            $hoje = date('d-m-Y');

            $historico = $row['parcelaspagas'];
            $historicoFormat = str_replace(';','<br><br>',$historico);
            $historicoFormat = str_replace(',','/',$historicoFormat);
            

            if(strtotime($vencimento) >= strtotime($hoje)) {
                $msg = 'Vence em:';
                $status = 'emDias';
                $respQuery = 'N';
            } else {
                $msg = 'Venceu em:';
                $status = 'vencida';
                $respQuery = 'S';
                if($row['atrasado'] == 'N') {
                    echo "<meta http-equiv=\"refresh\" content=\"0;URL=index.php?msg=reordenado\">";
                }
            }

            mysqli_query($conexao, "
            UPDATE $tabela SET `atrasado` = '$respQuery' WHERE `id`=$id;
            ");

            preg_match_all("/[0-9]+\.+[0-9][0-9]/",$historico,$pagos);
            $sum = 0;
            foreach($pagos as $line) {
                foreach($line as $k) {
                    $sum+= $k;
                }
            }
            $quitado = $sum + $row['avista'];

            $balanca = $sum + $row['avista'] - $row['preco'];

            $montante += $balanca;
            
            //Ou $parcelaExibir = $row['valordaparcela'] para mostrar o valor original da parcela
            $parcelaExibir = ($row['preco'] - $quitado) / ($total_de_parcelas - $Parcela_u);//Divide o saldo devedor pela quantidade de parcelas restantes

            echo "
                            <span class='contato-social list parcelado'>
                                <label>CLIENTE:
                                    <input type='text' id='".$row['cliente']."' value='".$row['cliente']."' readonly >
                                    <input type='hidden' id='totalParcelas".$id."' value='".$total_de_parcelas."' >
                                </label>
                                <label>
                                    <input type='tel' class='".$status."' value='A parcela nº(".$ultimaParcela."/".$row['parcela'].")".$msg."".$vencimento."' readonly >
                                </label>
                                <label>Parcela:
                                    <select id='receber-parcela".$id."'>
                                        <option value='".$ultimaParcela."'>".$ultimaParcela."ª</option>
                                    </select>
                                </label>
                                <label>de R$:
                                    <input type='tel' id='valorReceber".$id."' onkeyup='reais(this)' autocomplete='off' maxlength='9' value='".number_format($parcelaExibir,2,',','.')."' >
                                </label>
                                <label>
                                    <input type='submit' id='receber' value='receber' getID='".$id."'  >
                                </label>
                                <label>
                                    <input type='submit' id='deletar' value='Deletar' getID='".$id."' >
                                </label>
                                <label>
                                    <span class='balanca'>Valor Pago/Devido:
                                        (R$&nbsp;<b>".number_format($quitado,2,',','.')."/".number_format($row['preco'],2,',','.')."</b>)
                                    &nbsp;
                                    Balança:
                                        (R$&nbsp;<b>".number_format($balanca,2,',','.')."</b>)
                                    </span>
                                </label>
                            </span>
                            <input type='checkbox' id='".$id."-more' class='hidden'>
                            <label class='more' id='id".$id."' for='".$id."-more' onclick='growDiv(this)'>
                                <span class='trace'></span>
                                <span class='trace'></span>
                            </label>
                            <div class='id".$id." more-info'>
                                <div class='id".$id." info'>
                                    <span class='detalhes'>
                                    <span class='resumo'>
                                        RESUMO DA VENDA:
                                    </span>
                                    <label>CPF:
                                        <input value ='".$row['cpf']."' readonly >
                                    </label>
                                    <label>Telefone:
                                        <input value ='".$row['docn']."' readonly >
                                    </label>
                                    <label>Produto:
                                        <input value ='".$row['produto']."' readonly >
                                    </label>
                                    <label>Preço:
                                        R$ <b>".number_format($row['preco'],2,',','.')."</b>
                                    </label>
                                    <label>Acordo de Pagamento: R$ <b>".number_format($row['avista'],2,',','.')." + (".$row['parcela']."x ".number_format($row['valordaparcela'],2,',','.').")</b>
                                    </label>
                                    <label>Data:
                                        <input value ='".$dataVenda."' readonly >
                                    </label>
                                    <span class='title'>
                                        HISTÓRICO:
                                    </span>
                                    <span class='historico'>
                                        <br>".$historicoFormat."<br>
                                    </span>
                                    </span>
                                </div>
                            </div>";
        }
        echo "
        <span class='montanteParcelado'>
            O Montante atual em parcelamento é de: R$ ".number_format($montante,2,',','.')."
        </span>
        ";
    }

    $clienteAvista = $conexao->query("SELECT * FROM $tabela WHERE quitado='S' AND deletar='N' ORDER BY id DESC");
    
    if(mysqli_num_rows($clienteAvista) > 0) {
        while($row2 = $clienteAvista->fetch_assoc()) {

            $id2 = $row2['id'];

            $dataVenda2 = date("d-m-Y", strtotime($row2['data']));

            $historico2 = $row2['parcelaspagas'];
            $historicoFormat2 = str_replace(';','<br><br>',$historico2);
            $historicoFormat2 = str_replace(',','/',$historicoFormat2);

            preg_match_all("/[0-9]+\.+[0-9][0-9]/",$historico2,$pagos2);
            $sum2 = 0;
            foreach($pagos2 as $line2) {
                foreach($line2 as $k2) {
                    $sum2+= $k2;
                }
            }
            
            $quitado2 = $sum2 + $row2['avista'];

            $balanca2 = $sum2 + $row2['avista'] - $row2['preco'];

            echo "
                            <span class='contato-social list recebido'>
                                <label>CLIENTE:
                                    <input type='text' id='".$row2['cliente']."' value='".$row2['cliente']."' readonly >
                                </label>
                                <label>
                                    <span class='balanca'>Valor Pago/Devido:
                                        (R$&nbsp;<b>".number_format($quitado2,2,',','.')."/".number_format($row2['preco'],2,',','.')."</b>)
                                    &nbsp;
                                    Balança:
                                        (R$&nbsp;<b>".number_format($balanca2,2,',','.')."</b>)
                                    </span>
                                </label>
                                <label>
                                    <input type='submit' id='deletar' value='Deletar' getID='".$id2."' >
                                </label>
                            </span>
                            <input type='checkbox' id='".$id2."-more' class='hidden'>
                            <label class='more' id='id".$id2."' for='".$id2."-more' onclick='growDiv(this)'>
                                <span class='trace'></span>
                                <span class='trace'></span>
                            </label>
                            <div class='id".$id2." more-info'>
                                <div class='id".$id2." info'>
                                    <span class='detalhes'>
                                    <span class='resumo'>
                                        RESUMO DA VENDA:
                                    </span>
                                    <label>CPF:
                                        <input value ='".$row2['cpf']."' readonly >
                                    </label>
                                    <label>Telefone:
                                        <input value ='".$row2['docn']."' readonly >
                                    </label>
                                    <label>Produto:
                                        <input value ='".$row2['produto']."' readonly >
                                    </label>
                                    <label>Preço:
                                        R$ <b>".number_format($row2['preco'],2,',','.')."</b>
                                    </label>
                                    <label>Acordo de Pagamento: R$ <b>".number_format($row2['avista'],2,',','.')." + (".$row2['parcela']."x ".number_format($row2['valordaparcela'],2,',','.').")</b>
                                    </label>
                                    <label>Data:
                                        <input value ='".$dataVenda2."' readonly >
                                    </label>
                                    <span class='title'>
                                        HISTÓRICO:
                                    </span>
                                    <span class='historico'>
                                        <br>".$historicoFormat2."<br>
                                    </span>
                                    </span>
                                </div>
                            </div>
                ";
        }
    }
echo "
                        </span><!--Fim menu-items-->
                    </span>
                </span>
            </span>
            <span class='autor'>
                <h1>".$MeuNegocio."</h1>
                <h2>Vendas à Vista/Prazo</h2>
            </span>
            <input type='hidden' id='loja' value='".$tabela."' >
            <span class='contato-social master'>
                <label>CLIENTE:
                    <input type='text' id='cliente' class='cliente' autocomplete='off' autofocus >
                </label>
                <label>CPF:
                    <input type='tel' id='cpf' class='cpf' onkeyup='cpf(this)' autocomplete='off' maxlength='14' >
                </label>
                <label>Telefone:
                    <input type='text' id='docn' class='docn' onkeyup='telefone(this)' autocomplete='off' maxlength='15' >
                </label>
            </span>
            <span class='contato-social silver'>
                <label>Preço:
                    <input type='tel' id='preco' class='total' onkeyup='reais(this)' autocomplete='off' maxlength='9' value='0,00' >
                </label>
                <label>Recebido à Vista:
                    <input type='tel' id='avista' class='avista' onkeyup='reais(this)' autocomplete='off' maxlength='9' value='0,00' >
                </label>
                <label>Parcelar em:
                    <select id='parcela' class='parcela'>
                        <option value='0'>0x</option>
                        <option value='1'>1x</option>
                        <option value='2'>2x</option>
                        <option value='3'>3x</option>
                        <option value='4'>4x</option>
                        <option value='5'>5x</option>
                        <option value='6'>6x</option>
                        <option value='7'>7x</option>
                        <option value='8'>8x</option>
                        <option value='9'>9x</option>
                        <option value='10'>10x</option>
                        <option value='11'>11x</option>
                        <option value='12'>12x</option>
                    </select>
                </label>
                <label>De:
                    <input type='tel' id='aprazo' class='aprazo' onkeyup='reais(this)' autocomplete='off' maxlength='9' value='0,00' readonly >
                </label>
                <label>Balança:
                    <p id='total' class='total'>0,00</p>
                </label>
                <label>Produto:
                    <input type='text' id='item' class='item' >
                </label>
                <label>
                    <input type='submit' id='vender' value='vender' >
                </label>
            </span>
";
}
?>

        </header>
        <footer class='footer'>
            <h2>Em breve, mais novidades!</h2>
        </footer>
<script src='js/script.js'></script>
    </body>
</html>