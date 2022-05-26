//abrindo mais informações
function growDiv(name) {
    var forID = name.id;
    var growDiv = document.querySelector('.'+CSS.escape(forID)+'.more-info');
    if (growDiv.clientHeight) {
      growDiv.style.height = 0;
    } else {
      var wrapper = document.querySelector('.'+CSS.escape(forID)+'.info');
      growDiv.style.height = wrapper.clientHeight + "px";
    }
}
//mascara reais
function reais(i) {
    var v = i.value.replace(/\D/g,'');
    v = (v/100).toFixed(2) + '';
    v = v.replace('.', ',');
    v = v.replace(/(\d)(\d{3})(\d{3}),/g, '$1.$2.$3,');
    v = v.replace(/(\d)(\d{3}),/g, '$1.$2,');
    i.value = v;
}
//mascara cpf
function cpf(c){
    var v = c.value.replace(/\D/g,"");          //Remove tudo o que não é dígito
    v = v.replace(/(\d{3})(\d)/,"$1.$2");       //Coloca um ponto
    v = v.replace(/(\d{3})(\d)/,"$1.$2");       //Coloca um ponto
    v = v.replace(/(\d{3})(\d{1,2})$/,"$1-$2"); //Coloca um hífen
    c.value = v;
}
//mascara telefone
function telefone(t){
    var v = t.value.replace(/\D/g,"");    //Remove tudo o que não é dígito
    v = v.replace(/^(\d\d)(\d)/g,"($1) $2") //Coloca parênteses
    v = v.replace(/(\d{4})(\d)/,"$1-$2")    //Coloca hífen
    t.value = v;

    if(v.length == '15') {
        var v = t.value.replace(/\D/g,"");    //Remove tudo o que não é dígito
        v = v.replace(/^(\d\d)(\d)/g,"($1) $2") //Coloca parênteses
        v = v.replace(/(\d{5})(\d)/,"$1-$2")    //Coloca hífen
        t.value = v;
    }
}

//Seleciona todo conteudo ao clicar no input
$(function () {
    var focusedElement;
    $(document).on('click', 'input', function () {
        if (focusedElement == this) return;
        focusedElement = this;
        setTimeout(function () { focusedElement.select(); }, 100);
    });
});

//Inputs do tamanho do conteudo
function resizeInput() {
    $(this).attr('size', $(this).val().length);
}
$('input').keyup(resizeInput).each(resizeInput);

$(document).on('propertychange change input paste', '#preco, .avista, .parcela, .aprazo', function() {
    //SLICE mantem todos os números, exceto os dois ultimos. adiciona um ponto
    //SUBSTR mantem os dois ultimos numeros. apaga todos os outros
    var preco = document.getElementById('preco').value.replace(/[.,]/g, '');
    precoFix = preco.toString().slice(0,-2)+"."+preco.toString().substr(-2);

    var avista = document.getElementById('avista').value.replace(/[.,]/g, '');
    avistaFix = avista.toString().slice(0,-2)+"."+avista.toString().substr(-2);

    var aprazo = document.getElementById('aprazo').value.replace(/[.,]/g, '');
    aprazoFix = aprazo.toString().slice(0,-2)+"."+aprazo.toString().substr(-2);

    var parcela = document.getElementById('parcela').value;

    valordaparcela = (parseFloat(precoFix) - parseFloat(avistaFix))/parseInt(parcela);
    valorparcelaFix = valordaparcela.toFixed(2);
    if (parcela == 0) {
        valorparcelaFix = '0.00';
    } else {
        valorparcelaFix = valorparcelaFix;
    }

    saldo = (parseFloat(avistaFix) - parseFloat(precoFix)) + parseFloat(aprazoFix)*parcela;
    saldoFix = saldo.toFixed(2);

    document.getElementById('total').innerHTML = new Intl.NumberFormat('pt-br', {
        style:'decimal', //ou currency
        currency: 'BRL',
        minimumFractionDigits: 2
    }).format(saldoFix);
    document.getElementById('aprazo').value = new Intl.NumberFormat('pt-br', {
        style:'decimal', //ou currency
        currency: 'BRL',
        minimumFractionDigits: 2
    }).format(valorparcelaFix);
});
$(document).on("click", "#vender", function() {
    $.ajax({
        url: "vender.php",
        type: "POST",
        dataType: "json",
        data: {
            loja:$("#loja").val(),
            cliente:$("#cliente").val(),
            cpf:$("#cpf").val(),
            docn:$("#docn").val(),
            preco:$("#preco").val(),
            avista:$("#avista").val(),
            parcela:$("#parcela").val(),
            valordaparcela:$("#aprazo").val(),
            produto:$("#item").val()
        },
        success:function(ret) {
            if(ret == 'true') {
                alert('Registro concluído!');
                window.setTimeout(function(){
                window.location.href = "index.php";
                }, 500);
            } else {
                alert(ret);
            }
        },
		complete: function() {
            //alert('completo');
        }
    });
});
$(document).on("click", "#receber", function() {
    var ID = $(this).attr('getID');
    //alert(ID);
    $.ajax({
        url: "receber.php",
        type: "POST",
        dataType: "json",
        data: {
            loja:$("#loja").val(),
            id:ID,
            totalParcelas:$("#totalParcelas"+ID).val(),
            ultimaParcela:$("#receber-parcela"+ID).val(),
            valorReceber:$("#valorReceber"+ID).val()
        },
        success:function(ret) {
            if(ret == 'true') {
                alert('Registro concluído!');
                window.setTimeout(function(){
                window.location.href = "index.php";
                }, 500);
            } else {
                alert(ret);
            }
        },
		complete: function() {
            //alert('completo');
        }
    });
});
$(document).on("click", "#deletar", function() {
    var ID = $(this).attr('getID');
    //alert(ID);
    $.ajax({
        url: "deletar.php",
        type: "POST",
        dataType: "json",
        data: {
            loja:$("#loja").val(),
            id:ID
        },
        success:function(ret) {
            if(ret == 'true') {
                alert('Registro concluído!');
                window.setTimeout(function(){
                window.location.href = "index.php";
                }, 500);
            } else {
                alert(ret);
            }
        },
		complete: function() {
            //alert('completo');
        }
    });
});