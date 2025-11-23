<?php

namespace App\Http\Controllers;
use Flutterwave\Service\Transactions;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function initiate(Request $request)
    {
        $data = [
            "tx_ref" => time(),
            "amount" => $request->amount,
            "currency" => "NGN",
            "redirect_url" => route('payment.callback'),
            "payment_options" => "card,banktransfer,ussd",
            "customer" => [
                "email" => $request->email,
                "phonenumber" => $request->phone,
                "name" => $request->name
            ],
            "customizations" => [
                "title" => "Payment",
                "description" => "Payment for services"
            ]
        ];

        $transactions = new Transactions();
        $response = $transactions->initializePayment($data);

        if ($response['status'] === 'success') {
            return redirect($response['data']['link']);
        }

        return back()->with('error', 'Payment initialization failed');
    }

    public function callback(Request $request)
    {
        $transactionId = $request->transaction_id;

        $transactions = new Transactions();
        $response = $transactions->verifyTransaction($transactionId);

        if ($response['status'] === 'success' && $response['data']['status'] === 'successful') {
            // Payment successful
            return redirect()->route('payment.success');
        }

        return redirect()->route('payment.failed');
    }
}
