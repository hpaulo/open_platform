<?php
    ini_set('display_errors',1);
    ini_set('display_startup_erros',1);
    error_reporting(E_ALL);
    
    require_once ('lib/mercadopago.php');
    
    $mp = new MP('TEST-8720700524978125-042908-c751b4a85e5f6b0f0cc4f2b727b3712b__LA_LB__-182236412');
    
    //valor randomico para teste
    $valor = (float) rand(0.1, 140.99);
    
    //email do comprador
    $email = "test_user_64815011@testuser.com";
    
    //tokeniza‹o do cart‹o
    $token = $_POST['token'];
    
    
    /*
     * Detalhe importante:
     * No post de pagamento com um cart‹o j‡ cadastro Ž necess‡rio utilizar o ID do usuario
     */
    
    //pega id do usuario na api de search
    $filters = array ("email" => $email);
    $customer = $mp->get ("/v1/customers/search", $filters);
    $customer = $customer['response']['results'][0];
    $customer_id = $customer['id'];
    
    $payment_data = array(
        "transaction_amount" => $valor,
        "token" => $token,
        "description" => "Title of what you are paying for",
        "installments" => 1, //parcela fixa - a vista
        "external_reference" => "mp-teste-1234", //parametro para conciliacao (ex: id do pedido)
        "payer" => array (
            "id" => $customer_id
        )
    );
    
    //post do pagamento
    $payment = $mp->post("/v1/payments", $payment_data);

?>
        
        

<html>
    <head>
        <title>Status do Pagamento</title>
    </head>    

    <body>
        
        <h1>Retorno do post do Pagamento</h1>
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
        
    </body>
</html>