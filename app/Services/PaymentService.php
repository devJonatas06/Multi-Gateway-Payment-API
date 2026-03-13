<?php

namespace App\Services;

use App\Models\Client;
use App\Models\Transaction;
use App\Models\Product;
use App\Models\TransactionProduct;
use Illuminate\Support\Facades\Http;

class PaymentService
{

    public function process($data)
    {

        $client = Client::firstOrCreate(
            ['email' => $data['email']],
            ['name' => $data['name']]
        );


        // calcular valor total pelos produtos
        $total = 0;

        foreach ($data['products'] as $item) {

            $product = Product::findOrFail($item['id']);

            $total += $product->amount * $item['quantity'];
        }

        $data['amount'] = $total;


        // tentar gateway 1
        $response = $this->callGatewayOne($data);

        if (!$response['success']) {
            $response = $this->callGatewayTwo($data);
        }


        if ($response['success']) {

            $transaction = Transaction::create([
                'client_id' => $client->id,
                'gateway' => $response['gateway'],
                'external_id' => $response['data']['id'] ?? null,
                'status' => 'approved',
                'amount' => $data['amount'],
                'card_last_numbers' => substr($data['cardNumber'], -4)
            ]);


            foreach ($data['products'] as $item) {

                TransactionProduct::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $item['id'],
                    'quantity' => $item['quantity']
                ]);
            }
        }

        return response()->json($response);
    }



    private function getToken()
    {

        $response = Http::post('http://127.0.0.1:3001/login', [
            "email" => "dev@betalent.tech",
            "token" => "FEC9BB078BF338F464F96B48089EB498"
        ]);

        if ($response->successful()) {
            return $response->json()['token'];
        }

        return null;
    }



    private function callGatewayOne($data)
    {

        $token = $this->getToken();

        if (!$token) {
            return ["success" => false];
        }

        $response = Http::withToken($token)->post(
            'http://127.0.0.1:3001/transactions',
            [
                "amount" => $data['amount'],
                "name" => $data['name'],
                "email" => $data['email'],
                "cardNumber" => $data['cardNumber'],
                "cvv" => $data['cvv']
            ]
        );

        if ($response->successful()) {
            return [
                "success" => true,
                "gateway" => "gateway1",
                "data" => $response->json()
            ];
        }

        return ["success" => false];
    }



    private function callGatewayTwo($data)
{

    $response = Http::withHeaders([
        'Gateway-Auth-Token' => 'tk_f2198cc671b5289fa856',
        'Gateway-Auth-Secret' => '3d15e8ed6131446ea7e3456728b1211f'
    ])->post('http://127.0.0.1:3002/transacoes', [
        "valor" => $data['amount'],
        "nome" => $data['name'],
        "email" => $data['email'],
        "numeroCartao" => $data['cardNumber'],
        "cvv" => $data['cvv']
    ]);

    if ($response->successful()) {
        return [
            "success" => true,
            "gateway" => "gateway2",
            "data" => $response->json()
        ];
    }

    return ["success" => false];
}

}