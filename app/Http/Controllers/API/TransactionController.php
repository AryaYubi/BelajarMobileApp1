<?php

namespace App\Http\Controllers\API;

use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    public function all(Request $request)
    {
       $id = $request->input()->id;
       $limit = $request->input()->limit;
       $status = $request->input()->status;
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
}
