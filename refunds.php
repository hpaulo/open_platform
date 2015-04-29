<!--


    Documentação utilizada:
    http://labs.mercadopago.com.ar/developers/es/api-docs/account/payments/

    
-->

<?php
    ini_set('display_errors',1);
    ini_set('display_startup_erros',1);
    error_reporting(E_ALL);
    
    require_once ('lib/mercadopago.php');
    
    $mp = new MP('TEST-8720700524978125-042908-c751b4a85e5f6b0f0cc4f2b727b3712b__LA_LB__-182236412');    
    $payment = "";
    
    //indica que a pesquisa sera feita como vendedor
    $params = "user_role=collector&status=approved&limit=1";
    
    //faz um search de um pagamento aprovado para deixar pre-configurado para devolver
    $payment_refund = $mp->get("/v1/payments/search?" . $params);
    $payment_refund = $payment_refund['response']['results'][0];
    
    if(isset($_REQUEST['payment_id']) && $_REQUEST['transaction_amount']){
        
        $params = array(
            "amount" => (float) $_REQUEST['transaction_amount']
        );
        
        $payment = $mp->post("/v1/payments/" . $_REQUEST['payment_id'] . "/refunds", $params);
    }

    
?>

<html>
    <head>
        <title>Search de Pagamento</title>
        <link rel="stylesheet" href="css/style.css">
    </head>    

    <body>
        
        <h1>Devolução de pagamento (Total ou Parcial)</h1>
        
        <form>
            <fieldset>
                <ul>
                    <li id="payment_id">
                        <label for="payment_id">Id de Pagamento:</label>
                        <input type="text" name="payment_id" placeholder="54321" value="<?php echo $payment_refund['id'];?>"/>
                    </li>
                    
                    <li id="transaction_amount">
                        <label for="transaction_amount">Valor da transação (Devolva valor total ou parcial):</label>
                        <input type="text" name="transaction_amount" value="<?php echo $payment_refund['transaction_amount']; ?>"/>
                    </li>
                    
                </ul>
                
                <input type="submit" value="Devolver!" />
            </fieldset>
        </form>
        
        
        
        
        <?php if(isset($payment) && $payment != ""){ ?>
            <h1>Resultado</h1>
            <pre id="resultado">
                <?php echo json_encode($payment);?>
            </pre>
            
            <script>
                // identar json para mostrar no html
                var resultado = document.querySelector("#resultado");
                var jsonString = resultado.innerHTML;
                var jsonPretty = JSON.stringify(JSON.parse(jsonString),null,2);
                resultado.innerHTML = jsonPretty;
            </script>
        <?php } ?>
        
    </body>
</html>