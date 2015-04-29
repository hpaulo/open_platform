<!--


    Documenta‹o utilizada:
    http://labs.mercadopago.com.ar/developers/es/api-docs/account/payments/

    
-->

<?php
    ini_set('display_errors',1);
    ini_set('display_startup_erros',1);
    error_reporting(E_ALL);
    $payment = "";
    
    if(isset($_REQUEST)):
        require_once ('lib/mercadopago.php');
        
        $mp = new MP('TEST-8720700524978125-042908-c751b4a85e5f6b0f0cc4f2b727b3712b__LA_LB__-182236412');    
        
        
        //remove dos parametros recebido da url (get)
        unset($_REQUEST['user_role']);
        
        //indica que a pesquisa sera feita como vendedor
        $params = "user_role=collector";
        
        foreach($_REQUEST as $key => $value){
            
            if($value != ""){
            
                $separator = "";
                
                if($params != ""){
                    $separator = "&";
                }
                
                $params .= $separator . $key . "=" . $value;
            }
            
        }
        
        $payment = $mp->get("/v1/payments/search?" . $params);
        
    endif;
    
?>

<html>
    <head>
        <title>Search de Pagamento</title>
        <link rel="stylesheet" href="css/style.css">
    </head>    

    <body>
        
        <h1>Search de Pagamento</h1>
        
        <form>
            <fieldset>
                <ul>
                    <li id="external_reference">
                        <label for="payment_id">External Reference:</label>
                        <input type="text" name="external_reference" placeholder="54321" value="<?php echo isset($_REQUEST['external_reference'])? $_REQUEST['external_reference'] : "";?>"/>
                    </li>
                    
                    <li id="payer_email">
                        <label for="payment_id">Email do Comprador:</label>
                        <input type="text" name="payer_email" placeholder="test_user_64815011@testuser.com" value="<?php echo isset($_REQUEST['payer_email'])? $_REQUEST['payer_email'] : "";?>"/>
                    </li>
                    
                    <li id="status">
                        <label for="status">Status de Pagamento:</label>
                        <input type="text" name="status" placeholder="approved, rejected, pending, etc..." value="<?php echo isset($_REQUEST['status'])? $_REQUEST['status'] : "";?>"/>
                    </li>
                </ul>
                
                <input type="submit" value="Consultar!" />
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