<?php

namespace App\Http\Controllers;

use App\Models\Sale;

class TransactionPrintController extends Controller
{
    public function show($id)
    {
        $sale = Sale::with('items.product')->find($id);
        
        if (!$sale) {
            abort(404);
        }
        
        return view('transactions.print', ['sale' => $sale]);
    }
}
