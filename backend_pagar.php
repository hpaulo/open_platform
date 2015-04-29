<?php
    ini_set('display_errors',1);
    ini_set('display_startup_erros',1);
    error_reporting(E_ALL);
    
    require_once ('lib/mercadopago.php');
    
    $mp = new MP('TEST-8720700524978125-042908-c751b4a85e5f6b0f0cc4f2b727b3712b__LA_LB__-182236412');
    
    //valor randomico para teste
    $valor = (float) rand(0.1, 140.99);
    
    //email comprador
    $email = "test_user_64815011@testuser.com";
    
    //obtem o token (cartao tokenizado)
    $token = $_POST['token'];
    
    //obtem metodo de pagamento (visa, master, etc..)
    $payment_method_id = $_POST['paymentMethodId'];
    
    $payment_data = array(
        "transaction_amount" => $valor,
        "token" => $token,
        "description" => "Title of what you are paying for",
        "installments" => 1, //parcela fixa - a vista
        "payment_method_id" => $payment_method_id,
        "external_reference" => "54321", //parametro para conciliacao (ex: id do pedido)
        "payer" => array (
            "email" => $email
        )
    );
    
    $payment = $mp->post("/v1/payments", $payment_data);
    
    //caso o pagamento seja aprovado, adiciona na "carteira virtual"
    if($payment['status'] == 201 && $payment['response']['status'] == "approved" ){
        
        // faz o search do usu‡rio a partir do email
        $filters = array ("email" => $email);
        
        $customer = $mp->get ("/v1/customers/search", $filters);
        
        //caso n‹o retornou nada no search, insere o user na carteira virtual
        if($customer['status'] == 200 && $customer['response']['paging']['total'] == 0){
            $customer = $mp->post ("/v1/customers", $filters);
            $customer = $customer['response'];
        }else{
            
            $customer = $customer['response']['results'][0];
        }
        
        //obtem o customer_id para fazer o post do cart‹o
        $customer_id = $customer['id'];
        
        //adiciona o cart‹o na carteira virtual
        $card = $mp->post ("/v1/customers/".$customer_id."/cards", array("token" => $token));
        
    }
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