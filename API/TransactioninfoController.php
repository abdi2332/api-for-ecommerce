<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTransactioninfoRequest;
use App\Http\Requests\UpdateTransactioninfoRequest;
use App\Http\Requests\ShowTransactioninfoRequest;
use App\Models\Banks;
use App\Models\Order;
use App\Models\Transactioninfo;
use Illuminate\Http\Request;

class TransactioninfoController extends Controller
{
    public function index()
    {
        return response()->json(Transactioninfo::all());
    }

    public function store(StoreTransactioninfoRequest $request)
    {
        $validated = $request->validated();
        $transaction = Transactioninfo::create($validated);

        return response()->json([
            'success' => true,
            'transaction' => $transaction,
        ], 201);
    }

    public function show(ShowTransactioninfoRequest $request, $id)
    {
        $transaction = Transactioninfo::find($id);

        if (!$transaction) {
            return response()->json(['error' => 'Transaction not found'], 404);
        }

        $order = Order::find($transaction->order_id);
        $banks = Banks::all();
        $cartItems = \Cart::getContent();

        return response()->json([
            'transaction' => $transaction,
            'order' => $order,
            'banks' => $banks,
            'cartItems' => $cartItems,
        ]);
    }

    public function update(UpdateTransactioninfoRequest $request, $id)
    {
        $transaction = Transactioninfo::find($id);

        if (!$transaction) {
            return response()->json(['error' => 'Transaction not found'], 404);
        }

        $input = $request->validated();

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $fileName = "odaaelectronics-" . rand(100, 999) . '-' . $image->getClientOriginalName();
            $image->storeAs('public/resit', $fileName);
            $input['resit'] = $fileName;
        }

        $transaction->update($input);

        return response()->json([
            'success' => true,
            'transaction' => $transaction,
        ]);
    }

    public function destroy($id)
    {
        $transaction = Transactioninfo::find($id);

        if (!$transaction) {
            return response()->json(['error' => 'Transaction not found'], 404);
        }

        $transaction->delete();

        return response()->json(['success' => true]);
    }
}
