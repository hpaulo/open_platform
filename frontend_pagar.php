<!--


Exemplo utilizando a documentação:

http://labs.mercadopago.com.ar/developers/es/solutions/payments/custom-checkout/charge-with-creditcard/javascript/


-->

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
        
        <form action="backend_pagar.php" method="post" id="pay" name="pay" >
            <fieldset>
                <ul>

                    <li>
                        <label for="cardNumber">Numero do Cartão:</label>
                        <input type="text" id="cardNumber" data-checkout="cardNumber" placeholder="4509 9535 6623 3704" />
                        <span id="icone_bandeira"></span>
                    </li>
                    
                    <li>
                        <label for="cardExpirationMonth">Mês de expiração:</label>
                        <input type="text" id="cardExpirationMonth" data-checkout="cardExpirationMonth" placeholder="12" />
                    </li>
                    
                    <li>
                        <label for="cardExpirationYear">Ano de expiração:</label>
                        <input type="text" id="cardExpirationYear" data-checkout="cardExpirationYear" placeholder="2015" />
                    </li>
                    
                    <li>
                        <label for="cardholderName">Nome impresso no cartão:</label>
                        <input type="text" id="cardholderName" data-checkout="cardholderName" placeholder="APRO" />
                    </li>
                    
                    <li>
                        <label for="securityCode">Codigo de Segurança:</label>
                        <input type="text" id="securityCode" data-checkout="securityCode" placeholder="123" />
                    </li>
                    
                    <li>
                        <label for="docType">Tipo de Documento:</label>
                        <select id="docType" data-checkout="docType">
                        </select>
                    </li>
                    <li>
                        <label for="docNumber">Numero do Documento:</label>
                        <input type="text" id="docNumber" data-checkout="docNumber" placeholder="12345678" />
                    </li>
                </ul>
                <input type="submit" value="Pagar!" /> <a href="#datateste" id="dataTeste">adicionar dados de teste</a>
            </fieldset>
        </form>
        
        
        <script src="https://secure.mlstatic.com/sdk/javascript/v1/mercadopago.js"></script>
        
        <script>
            
            //variavel de controle
            var doSubmit;
            
            //inicia o javascript
            Mercadopago.setPublishableKey("TEST-14a9064b-ea1e-4d96-b727-76e34856e963");
            
            //pega os documentos aceitos para processar o pagamento
            Mercadopago.getIdentificationTypes();
            
            
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
            
            // funcao responsavel por separar o bin do cartão
            function getBin() {
                var ccNumber = document.querySelector('input[data-checkout="cardNumber"]');
                return ccNumber.value.replace(/[ .-]/g, '').slice(0, 6);
            };
            
            
            // pega a bandeira do cartão
            function guessingPaymentMethod(event) {
                var bin = getBin();
            
                if (event.type == "keyup") {
                    if (bin.length >= 6) {
                        Mercadopago.getPaymentMethod({
                            "bin": bin
                        }, setPaymentMethodInfo);
                    }
                } else {
                    setTimeout(function() {
                        if (bin.length >= 6) {
                            Mercadopago.getPaymentMethod({
                                "bin": bin
                            }, setPaymentMethodInfo);
                        }
                    }, 100);
                }
            };
            
            // adiciona o metodo do pagamento no form
            function setPaymentMethodInfo(status, response) {
                if (status == 200) {
                    // do somethings ex: show logo of the payment method
                    var form = document.querySelector('#pay');
            
                    if (document.querySelector("input[name=paymentMethodId]") == null) {
                        var paymentMethod = document.createElement('input');
                        paymentMethod.setAttribute('name', "paymentMethodId");
                        paymentMethod.setAttribute('type', "hidden");
                        paymentMethod.setAttribute('value', response[0].id);
                        
                        form.appendChild(paymentMethod);
                    } else {
                        document.querySelector("input[name=paymentMethodId]").value = response[0].id;
                    }
                    
                    //adiciona bandeira
                    document.querySelector("#icone_bandeira").innerHTML = '<img src="'+response[0].secure_thumbnail+'">';
                    
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
                        
                        /* Função personalizada para mostrar os errors de validacao do formulario
                         * utilize a documentacao de errors
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
       
            function addDataTest(event){
                
                //adiciona dados para o pagamento teste
                document.querySelector("#cardNumber").setAttribute('value', "4235647728025682");
                //forca o guessing com dados de test (para não fazer a digitacao)
                guessingPaymentMethod(event);
                
                document.querySelector("#cardExpirationMonth").setAttribute('value', "11");
                document.querySelector("#cardExpirationYear").setAttribute('value', "2018");
                document.querySelector("#cardholderName").setAttribute('value', "APRO APRO");
                document.querySelector("#securityCode").setAttribute('value', "123");
                document.querySelector("#docNumber").setAttribute('value', "19119119100");
            }
            
            //adiciona eventos para os elementos da pagina
            //eventos para pegar a bandeira do cartão de credito
            addEvent(document.querySelector('input[data-checkout="cardNumber"]'), 'keyup', guessingPaymentMethod);
            addEvent(document.querySelector('input[data-checkout="cardNumber"]'), 'change', guessingPaymentMethod);
            
            //evento para criação do token (cartão tokenizado)
            addEvent(document.querySelector('#pay'),'submit',doPay);
            
            //evento para adicionar dados de testes
            addEvent(document.querySelector('#dataTeste'),'click', addDataTest);
            
        </script>
    </body>
</html>