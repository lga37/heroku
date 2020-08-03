<?php
/*

require("vendor/autoload.php");
$pagarme = new PagarMe\Client('SUA_CHAVE_DE_API');



$paymentLink = $pagarme->paymentLinks()->create([
  'amount' => 10000,
  'items' => [
    [
      'id' => '1',
      'title' => "Fighter's Sword",
      'unit_price' => 4000,
      'quantity' => 1,
      'tangible' => true,
      'category' => 'weapon',
      'venue' => 'A Link To The Past',
      'date' => '1991-11-21'
    ],
    [
      'id' => '2',
      'title' => 'Kokiri Sword',
      'unit_price' => 6000,
      'quantity' => 1,
      'tangible' => true,
      'category' => 'weapon',
      'venue' => "Majora's Mask",
      'date' => '2000-04-27'
    ],
  ],
  'payment_config' => [
    'boleto' => [
      'enabled' => true,
      'expires_in' => 20
    ],
    'credit_card' => [
      'enabled' => true,
      'free_installments' => 4,
      'interest_rate' => 25,
      'max_installments' => 12
    ],
    'default_payment_method' => 'boleto'
  ],
  'max_orders' => 1,
  'expires_in' => 60
]);
 200 OK
 400 Bad Request
{
    "amount": 1000,
    "date_created": "2018-08-02T14:29:47.128Z",
    "date_updated": "2018-08-02T14:29:47.128Z",
    "expires_at": "2018-08-02T15:29:47.126Z",
    "id": "pl_cjkcnpnug01w3nx6d7rz1dgit",
    "items": [
        {
            "category": null,
            "created_at": "2018-08-02T14:29:47.149Z",
            "date": null,
            "external_id": "1",
            "id": "cjkcnpnv101w4nx6djl4kz1xw",
            "model": "payment_link",
            "model_id": "pl_cjkcnpnug01w3nx6d7rz1dgit",
            "quantity": 1,
            "tangible": true,
            "title": "Bola de futebol",
            "transaction_id": null,
            "unit_price": 400,
            "updated_at": "2018-08-02T14:29:47.149Z",
            "venue": null
        },
        {
            "category": null,
            "created_at": "2018-08-02T14:29:47.149Z",
            "date": null,
            "external_id": "a123",
            "id": "cjkcnpnv101w5nx6d7bq9rkg3",
            "model": "payment_link",
            "model_id": "pl_cjkcnpnug01w3nx6d7rz1dgit",
            "quantity": 1,
            "tangible": true,
            "title": "Caderno do Goku",
            "transaction_id": null,
            "unit_price": 600,
            "updated_at": "2018-08-02T14:29:47.149Z",
            "venue": null
        }
    ],
    "max_orders": 1,
    "object": "payment_link",
    "payment_config": {
        "boleto": {
            "enabled": true,
            "expires_in": 20
        },
        "credit_card": {
            "enabled": true,
            "free_installments": 4,
            "interest_rate": 25,
            "max_installments": 12
        },
        "default_payment_method": "boleto"
    },
    "customer_config": {
        "customer": {
            "external_id": "#123456789",
            "name": "Fulano",
            "type": "individual",
            "country": "br",
            "email": "fulano@email.com",
            "documents": [
                {
                    "type": "cpf",
                    "number": "71404665560"
                }
            ],
            "phone_numbers": [
                "+5511999998888",
                "+5511888889999"
            ],
            "birthday": "1985-01-01"
        },
        "billing": {
            "name": "Ciclano de Tal",
            "address": {
                "country": "br",
                "state": "SP",
                "city": "São Paulo",
                "neighborhood": "Fulanos bairro",
                "street": "Rua dos fulanos",
                "street_number": "123",
                "zipcode": "05170060"
            }
        },
        "shipping": {
            "name": "Ciclano de Tal",
            "fee": 12345,
            "delivery_date": "2017-12-25",
            "expedited": true,
            "address": {
                "country": "br",
                "state": "SP",
                "city": "São Paulo",
                "neighborhood": "Fulanos bairro",
                "street": "Rua dos fulanos",
                "street_number": "123",
                "zipcode": "05170060"
            }
        }
    },
    "postback_config": {
      "orders": "http://postback.url/orders",
      "transactions": "http://postback.url/transactions"
    },
    "short_id": "tBJ7Dr9xSX",
    "status": "active",
    "url": "https://link.sandbox.pagar.me/tBJ7Dr9xSX"
}



$transaction = $pagarme->transactions()->create([
  'amount' => 100,
  'card_id' => 'card_ci6l9fx8f0042rt16rtb477gj',
  'payment_method' => 'credit_card',
  'postback_url' => 'http://requestb.in/pkt7pgpk',
  'customer' => [
    'external_id' => '0001',
    'name' => 'Aardvark Silva',
    'email' => 'aardvark.silva@pagar.me',
    'type' => 'individual',
      'country' => 'br',
      'documents' => [
        [
          'type' => 'cpf',
          'number' => '67415765095'
        ]
      ],
      'phone_numbers' => [ '+551199999999' ]
  ],
  'billing' => [
      'name' => 'Nome do pagador',
      'address' => [
        'country' => 'br',
        'street' => 'Avenida Brigadeiro Faria Lima',
        'street_number' => '1811',
        'state' => 'sp',
        'city' => 'Sao Paulo',
        'neighborhood' => 'Jardim Paulistano',
        'zipcode' => '01451001'
      ]
  ],
  'shipping' => [
      'name' => 'Nome de quem receberá o produto',
      'fee' => 1020,
      'delivery_date' => '2018-09-22',
      'expedited' => false,
      'address' => [
        'country' => 'br',
        'street' => 'Avenida Brigadeiro Faria Lima',
        'street_number' => '1811',
        'state' => 'sp',
        'city' => 'Sao Paulo',
        'neighborhood' => 'Jardim Paulistano',
        'zipcode' => '01451001'
      ]
  ],
  'items' => [
      [
        'id' => '1',
        'title' => 'R2D2',
        'unit_price' => 300,
        'quantity' => 1,
        'tangible' => true
      ],
      [
        'id' => '2',
        'title' => 'C-3PO',
        'unit_price' => 700,
        'quantity' => 1,
        'tangible' => true
      ]
  ]
]);
 200 OK
{
    "object": "transaction",
    "status": "paid",
    "refse_reason": null,
    "status_reason": "acquirer",
    "acquirer_response_code": "0000",
    "acquirer_name": "pagarme",
    "acquirer_id": "5969170917bce0470c8bf099",
    "authorization_code": "65208",
    "soft_descriptor": null,
    "tid": 1830855,
    "nsu": 1830855,
    "date_created": "2017-08-14T20:35:46.046Z",
    "date_updated": "2017-08-14T20:35:46.455Z",
    "amount": 10000,
    "authorized_amount": 10000,
    "paid_amount": 10000,
    "refunded_amount": 0,
    "installments": 1,
    "id": 1830855,
    "cost": 50,
    "card_holder_name": "Morpheus Fishburne",
    "card_last_digits": "1111",
    "card_first_digits": "411111",
    "card_brand": "visa",
    "card_pin_mode": null,
    "postback_url": null,
    "payment_method": "credit_card",
    "capture_method": "ecommerce",
    "antifraud_score": null,
    "boleto_url": null,
    "boleto_barcode": null,
    "boleto_expiration_date": null,
    "referer": "api_key",
    "ip": "10.2.11.17",
    "subscription_id": null,
    "phone": null,
    "address": null,
    "customer": {
        "object": "customer",
        "id": 233238,
        "external_id": "#3311",
        "type": "individual",
        "country": "br",
        "document_number": null,
        "document_type": "cpf",
        "name": "Morpheus Fishburne",
        "email": "mopheus@nabucodonozor.com",
        "phone_numbers": [
            "+5511999998888",
            "+5511888889999"
        ],
        "born_at": null,
        "birthday": "1965-01-01",
        "gender": null,
        "date_created": "2017-08-14T20:35:45.963Z",
        "documents": [
            {
                "object": "document",
                "id": "doc_cj6cmcm2l01z5696dyamemdnf",
                "type": "cpf",
                "number": "30621143049"
            }
        ]
    },
    "billing": {
        "address": {
            "object": "address",
            "street": "Rua Matrix",
            "complementary": null,
            "street_number": "9999",
            "neighborhood": "Rio Cotia",
            "city": "Cotia",
            "state": "sp",
            "zipcode": "06714360",
            "country": "br",
            "id": 145818
        },
        "object": "billing",
        "id": 30,
        "name": "Trinity Moss"
    },
    "shipping": {
        "address": {
            "object": "address",
            "street": "Rua Matrix",
            "complementary": null,
            "street_number": "9999",
            "neighborhood": "Rio Cotia",
            "city": "Cotia",
            "state": "sp",
            "zipcode": "06714360",
            "country": "br",
            "id": 145819
        },
        "object": "shipping",
        "id": 25,
        "name": "Neo Reeves",
        "fee": 1000,
        "delivery_date": "2000-12-21",
        "expedited": true
    },
    "items": [
        {
            "object": "item",
            "id": "r123",
            "title": "Red pill",
            "unit_price": 10000,
            "quantity": 1,
            "category": null,
            "tangible": true,
            "venue": null,
            "date": null
        },
        {
            "object": "item",
            "id": "b123",
            "title": "Blue pill",
            "unit_price": 10000,
            "quantity": 1,
            "category": null,
            "tangible": true,
            "venue": null,
            "date": null
        }
    ],
    "card": {
        "object": "card",
        "id": "card_cj6cmcm4301z6696dt3wypskk",
        "date_created": "2017-08-14T20:35:46.036Z",
        "date_updated": "2017-08-14T20:35:46.524Z",
        "brand": "visa",
        "holder_name": "Morpheus Fishburne",
        "first_digits": "411111",
        "last_digits": "1111",
        "country": "UNITED STATES",
        "fingerprint": "3ace8040fba3f5c3a0690ea7964ea87d97123437",
        "valid": true,
        "expiration_date": "0922"
    },
    "split_rules": null,
    "metadata": {},
    "antifraud_metadata": {},
    "reference_key": null
}



$customer = $pagarme->customers()->create([
  'external_id' => '#123456789',
  'name' => 'João das Neves',
  'type' => 'individual',
  'country' => 'br',
  'email' => 'joaoneves@norte.com',
  'documents' => [
    [
      'type' => 'cpf',
      'number' => '11111111111'
    ]
  ],
  'phone_numbers' => [
    '+5511999999999',
    '+5511888888888'
  ],
  'birthday' => '1985-01-01'
]);




$card = $pagarme->cards()->create([
  'holder_name' => 'Yoda',
  'number' => '4242424242424242',
  'expiration_date' => '1225',
  'cvv' => '123'
]);
 200 OK
{
  "object": "card",
  "id": "card_cj428xxsx01dt3f6dvre6belx",
  "date_created": "2017-06-18T05:03:19.907Z",
  "date_updated": "2017-06-18T05:03:20.318Z",
  "brand": "visa",
  "holder_name": "Aardvark Silva",
  "first_digits": "401872",
  "last_digits": "8048",
  "country": "RU",
  "fingerprint": "TaApkY+9emV9",
  "customer": null,
  "valid": true,
  "expiration_date": "1122"
}

*/




$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_POST  = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

    $login = $_POST['login'];

    if (!filter_var($login, FILTER_VALIDATE_EMAIL)) {
        $errors['login'] = "login invalido";
    }

    if (empty($errors)) {
        $_SESSION['logado'] = "sim";
        session_regenerate_id();
        redirect('admin.php');
    } else {
        $texto = implode('<br>', $errors);
        msg($texto, "danger");
    }
}
?>

<form method="POST" name="login" action="?a=login">
    <fieldset>
        <legend>Pagar com pagar.me</legend>
        <div class="form-group">
            <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-user"></i></span>
                <input class="form-control" name="login" placeholder="Email">
            </div>
        </div>

        <div class="input-group mb-3">
            <span class="input-group-text">CEP</span>
            <input type="text" class="form-control" aria-label="Amount (to the nearest dollar)">
            <span class="input-group-text">auto</span>
        </div>

        <div class="input-group mb-3">
            <span class="input-group-text">$</span>
            <input type="text" class="form-control" aria-label="Amount (to the nearest dollar)">
            <span class="input-group-text">.00</span>
        </div>

        <div class="row">
            <div class="form-group col-xs-12">
                <button class="btn btn-lg btn-success-outline">Pagar</button>
            </div>
        </div>
    </fieldset>
</form>
