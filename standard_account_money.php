<?php
    ini_set('display_errors',1);
    ini_set('display_startup_erros',1);
    error_reporting(E_ALL);
    
    require_once ('lib/mercadopago.php');
    
    $mp = new MP('APP_USR-8720700524978125-042908-5cac7d347392ccb0b80156fdc7d6f50f__LB_LD__-182236412');
    
    $preference_data = array(
        "items" => array(
            array(
                "title" => "Produto Teste",
                "quantity" => 1,
                "unit_price" => (float) rand(50, 140.99)
            )
        ),
        
        "payer" => array(
            "name" => "Apro",
            "surname" => "Apro",
            "email" => "test_user_40076148@testuser.com",
        ),
        
        "payment_methods" => array(
            "excluded_payment_types" => array(
                array("id" => "ticket")
            )
        )
    );
    
    
    $preference = $mp->create_preference($preference_data);
    
    header("Location:" . $preference['response']['init_point']);
    exit;