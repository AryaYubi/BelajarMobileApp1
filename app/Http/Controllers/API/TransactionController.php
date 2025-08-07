<?php

namespace App\Http\Controllers\API;

use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\TransactionItem;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    public function all(Request $request)
    {
       $id = $request->input('id');
       $limit = $request->input('limit');
       $status = $request->input('status');
        if ($id) {
            $transaction = Transaction::with(['items.product'])->find($id);
                if ($transaction) {
                    return ResponseFormatter::success($transaction, 'Transaction found');
                }
                else    {
                    return ResponseFormatter::error(null,'Transaction not found', 404);
                }
        }

        $transactions = \App\Models\Transaction::with(['items.product'])
            ->where('users_id', Auth::user()->id);
        if ($status) {
            $transactions->where('status', $status);
                    }
        return ResponseFormatter::success(
            $transactions->paginate($limit),
            'Transaction list retrieved successfully'
        );
    }

    public function checkout(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'exists:product,id',
            'total_price' => 'required',
            'shipping_price' => 'required',
            'status' => 'in:PENDING,SHIPPING,SHIPPED,SUCCESS,FAILED,CANCELLED',
        ]);

        $transaction = Transaction::create([
            'users_id' => Auth::user()->id,
            'address' => $request->address,
            'total_price' => $request->total_price,
            'shipping_price' => $request->shipping_price,
            'status' => $request->status,
        ]);

        foreach ($request->items as $product) {
            TransactionItem::create([
                'users_id' => Auth::user()->id,
                'products_id' => $product['id'],
                'transactions_id' => $transaction->id,
                'quantity' => $product['quantity'],
            ]);
        }
        return ResponseFormatter::success(
            $transaction->load(['items.product']),
            'Transaction created successfully'
        );
    }
}
