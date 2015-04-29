<!--

Exemplo utilizando a documentação:

http://labs.mercadopago.com.ar/developers/es/solutions/payments/custom-checkout/one-click-charges/javascript/

-->

<?php
    ini_set('display_errors',1);
    ini_set('display_startup_erros',1);
    error_reporting(E_ALL);
    
    require_once ('lib/mercadopago.php');
    
    $mp = new MP('TEST-8720700524978125-042908-c751b4a85e5f6b0f0cc4f2b727b3712b__LA_LB__-182236412');

    $email = "test_user_64815011@testuser.com";
    $filters = array ("email" => $email);
    
    $customer = $mp->get ("/v1/customers/search", $filters);
?>

<html>
    <head>
        <meta charset="utf-8">
        <title>Pagamento com Cartão</title>
        
        <link rel="stylesheet" href="css/style.css">
    </head>
    <body>
        
        <h1>Fomulário de Pagamento</h1>
        
        <div id="validacao">
        </div>
        
        <form action="backend_oneclickpay.php" method="post" id="pay" name="pay" >
            <fieldset>
                <ul>
                    <li>
                        <label>Cartões:</label>
                        <select id="cardId" name="cardId" data-checkout='cardId'>
                        <?php foreach ($customer["response"]['results'][0]["cards"] as $card) { ?>
                                <option value="<?php echo $card["id"]; ?>"
                                        first_six_digits="<?php echo $card["first_six_digits"]; ?>" 
                                        security_code_length="<?php echo $card["security_code"]["length"]; ?>">
                                
                                    <?php echo $card["first_six_digits"]; ?>XXXXXX<?php echo $card["last_four_digits"]; ?>
                                    (<?php echo strtoupper($card["payment_method"]["name"]); ?>)
                                </option>
                        <?php } ?>
                        </select>
                        
                        <span id="icone_bandeira"></span>
                    </li>
                    
                    <li id="cvv">
                        <label for="cvv">Codigo de Segurança:</label>
                        <input type="text" id="cvv" data-checkout="securityCode" placeholder="123" />
                    </li>
                </ul>
                
                <input type="submit" value="Pagar!" />
            </fieldset>
        </form>
        
        
        <script src="https://secure.mlstatic.com/sdk/javascript/v1/mercadopago.js"></script>
        
        <script>
            
            //variavel de controle
            var doSubmit;
            
            //inicia o javascript
            Mercadopago.setPublishableKey("TEST-14a9064b-ea1e-4d96-b727-76e34856e963");
            
            // funcao responsavel por adiciona evento nos elementos
            function addEvent(el, eventName, handler){
                if (el.addEventListener) {
                       el.addEventListener(eventName, handler);
                } else {
                    el.attachEvent('on' + eventName, function(){
                      handler.call(el);
                    });
                }
            };
            
            
            // funcao para criacao do token e submit do form
            function doPay(event){
                event.preventDefault();
                
                if(!doSubmit){
                    var $form = document.querySelector('#pay');
                    
                    Mercadopago.createToken($form, sdkResponseHandler);
            
                    return false;
                }
            };
            
            function cardsHandler(){
                var card = document.querySelector('select[data-checkout="cardId"]');
           
                // check if the security code (ex: Tarshop) is required
                if (card[card.options.selectedIndex].getAttribute('security_code_length')==0){
                     document.querySelector("#cvv").style.display = "none";
                }else if(document.querySelector("#cvv").style.display!="block") {
                     document.querySelector("#cvv").style.display = "block";
                }
           }


            function sdkResponseHandler(status, response) {
                // box para mostrar mensagens de validacao
                var validacao = document.querySelector('#validacao');
                validacao.style.display = 'none';
                
                //clean
                validacao.innerHTML = "";
                
                if (status != 200 && status != 201) {
                    // Mostra os erros retornado ao criar o token
                    
                    var ul = document.createElement('ul');
                    
                    //show box de validacao
                    validacao.style.display = 'block';
                    
                    for(var x in response.cause){
                        var error = response.cause[x];
                        
                        /* utilize a documentacao de errors
                         * http://labs.mercadopago.com.ar/developers/es/solutions/payments/custom-checkout/response-handling/#card-token
                         */
                        
                        //cria elementos LI para adicionar na UL
                        var li = document.createElement('li');
                        li.innerHTML = error.code + " - " + error.description;
                        ul.appendChild(li)
                    }
                    
                    // adiciona UL dentro do box de validacao
                    validacao.appendChild(ul);
                    
                }else{
                    var form = document.querySelector('#pay');
                    var card = document.createElement('input');
                    card.setAttribute('name',"token");
                    card.setAttribute('type',"hidden");
                    card.setAttribute('value',response.id);
                    form.appendChild(card);
                    doSubmit=true;
                    
                    form.submit();
                }
            };
            
            //evento para criação do token (cartão tokenizado)
            addEvent(document.querySelector('#pay'),'submit', doPay);
            addEvent(document.querySelector('select[data-checkout="cardId"]'),'change',cardsHandler);
        </script>
    </body>
</html>