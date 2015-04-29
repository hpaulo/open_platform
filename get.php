<!--


    Documenta‹o utilizada:
    http://labs.mercadopago.com.ar/developers/es/api-docs/account/payments/

    
-->

<?php
    ini_set('display_errors',1);
    ini_set('display_startup_erros',1);
    error_reporting(E_ALL);
    $payment = "";
    
    if(isset($_REQUEST['payment_id'])):
        require_once ('lib/mercadopago.php');
        
        $mp = new MP('TEST-8720700524978125-042908-c751b4a85e5f6b0f0cc4f2b727b3712b__LA_LB__-182236412');    
    
        $payment = $mp->get("/v1/payments/" . $_GET['payment_id']);
        
    endif;
    
?>

<html>
    <head>
        <title>Get Pagamento</title>
        <link rel="stylesheet" href="css/style.css">
    </head>    

    <body>
        
        <h1>Get Pagamento</h1>
        
        <form>
            <fieldset>
                <ul>
                    <li id="cvv">
                        <label for="payment_id">Id de Pagamento:</label>
                        <input type="text" name="payment_id" placeholder="2579" value="<?php echo isset($_REQUEST['payment_id'])? $_REQUEST['payment_id'] : "";?>"/>
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