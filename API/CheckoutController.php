<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCheckoutRequest; // Import the Form Request
use App\Models\Banks;
use App\Models\Order;
use App\Models\Checkout;
use App\Models\Billinginfo;
use App\Models\OrderedItems;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    public function index()
    {
        $cartItems = \Cart::getContent();
        return response()->json($cartItems);
    }

    public function store(StoreCheckoutRequest $request) // Use the Form Request here
    {
        $validated = $request->validated(); // Retrieve validated data
        
        do {
            $randomNumber = random_int(10000, 99999);
        } while (Order::where('order_id', $randomNumber)->exists());
    
        $cartItems = \Cart::getContent();
        $order = Order::create(['order_id' => $randomNumber, 'totalprice' => \Cart::getTotal()]);
    
        $input = $validated;
        $input['order_id'] = $order->id;
        Billinginfo::create($input);
    
        foreach ($cartItems as $key => $value) {
            OrderedItems::create([
                'order_id' => $order->id,
                'product_id' => $value->id,
                'qty' => $value->quantity,
                'price' => $value->price,
            ]);
        }
    
        $this->ordersms($request->phone, $randomNumber);
    
        return response()->json(['success' => true, 'order_id' => $order->id]);
    }

    private function ordersms($phone, $orderid)
    {
        // $postdata = json_encode([
        //     "accessKey" => "4562321aaf6e4e76ad61bbd862666e40",
        //     "secretKey" => "8442f0685652465e9466256182b0b9ca",
        //     "from" => "ODAA-E",
        //     "to" => "251" . substr($phone, 1),
        //     "message" => " lakkofsi order keessan ." . $orderid . "kanaan kaffalti barbachisu xumuruun hordofadha!",
        //     "callbackUrl" => "https://api.xxxx",
        // ]);

        // $opts = [
        //     'http' => [
        //         'method' => 'POST',
        //         'header' => 'Content-Type: application/json',
        //         'content' => $postdata,
        //     ],
        // ];

        // $context = stream_context_create($opts);

        // $result = file_get_contents('http://api.kmicloud.com/sms/send/v1/notify', false, $context);
    }

    public function show(Checkout $checkout)
    {
        // Your implementation here
    }

    public function edit(Checkout $checkout)
    {
        // Your implementation here
    }

    public function update(Request $request, Checkout $checkout)
    {
        // Your implementation here
    }

    public function destroy(Checkout $checkout)
    {
        // Your implementation here
    }
}
