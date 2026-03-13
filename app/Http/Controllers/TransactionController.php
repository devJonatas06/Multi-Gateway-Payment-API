<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PaymentService;

class TransactionController extends Controller
{
   public function purchase(Request $request)
{
    $data = $request->validate([
        'products' => 'required|array|min:1',
        'products.*.id' => 'required|integer',
        'products.*.quantity' => 'required|integer|min:1',
        'name' => 'required|string',
        'email' => 'required|email',
        'cardNumber' => 'required|string|size:16',
        'cvv' => 'required|string|min:3|max:4'
    ]);

    $paymentService = new PaymentService();

return $paymentService->process($data);}
}