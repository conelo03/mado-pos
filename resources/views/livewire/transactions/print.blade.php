<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt - {{ $sale->invoice_no }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Courier New', monospace;
            width: 80mm;
            margin: 0 auto;
            padding: 10px;
            background: white;
        }
        
        .receipt {
            width: 100%;
            text-align: center;
        }
        
        .header {
            margin-bottom: 10px;
            border-bottom: 1px dashed #000;
            padding-bottom: 10px;
        }
        
        .store-name {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .invoice-info {
            font-size: 12px;
            margin-bottom: 5px;
        }
        
        .divider {
            border-bottom: 1px dashed #000;
            margin: 10px 0;
        }
        
        .items-section {
            text-align: left;
            margin: 10px 0;
        }
        
        .item-header {
            display: flex;
            justify-content: space-between;
            font-size: 11px;
            font-weight: bold;
            margin-bottom: 5px;
            border-bottom: 1px solid #000;
            padding-bottom: 3px;
        }
        
        .item-row {
            display: flex;
            justify-content: space-between;
            font-size: 11px;
            margin-bottom: 3px;
            flex-wrap: wrap;
        }
        
        .item-name {
            flex: 1;
        }
        
        .item-qty {
            width: 30px;
            text-align: center;
        }
        
        .item-price {
            width: 50px;
            text-align: right;
        }
        
        .summary-section {
            margin-top: 10px;
            border-top: 1px dashed #000;
            padding-top: 10px;
        }
        
        .summary-row {
            display: flex;
            justify-content: space-between;
            font-size: 12px;
            margin-bottom: 5px;
        }
        
        .summary-row.total {
            font-weight: bold;
            font-size: 13px;
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
            padding: 5px 0;
        }
        
        .summary-row.change {
            font-weight: bold;
            margin-top: 5px;
        }
        
        .footer {
            margin-top: 15px;
            font-size: 11px;
            text-align: center;
            border-top: 1px dashed #000;
            padding-top: 10px;
        }
        
        .thank-you {
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .date-time {
            font-size: 10px;
            color: #666;
        }
        
        @media print {
            body {
                width: 80mm;
                margin: 0;
                padding: 0;
            }
            
            .no-print {
                display: none;
            }
        }
        
        .print-button {
            margin-top: 20px;
            text-align: center;
        }
        
        .print-button button {
            padding: 10px 20px;
            background: #333;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }
        
        .print-button button:hover {
            background: #555;
        }
    </style>
</head>
<body>
    <div class="receipt">
        <div class="header">
            <div class="store-name">MADO POS</div>
            <div class="invoice-info">
                <div>Invoice: {{ $sale->invoice_no }}</div>
                <div>{{ $sale->created_at->format('d/m/Y H:i') }}</div>
            </div>
        </div>
        
        <div class="divider"></div>
        
        <div class="items-section">
            <div class="item-header">
                <span class="item-name">Item</span>
                <span class="item-qty">Qty</span>
                <span class="item-price">Total</span>
            </div>
            
            @foreach($sale->items as $item)
                <div class="item-row">
                    <span class="item-name">{{ $item->product->name }}</span>
                </div>
                <div class="item-row">
                    <span style="font-size: 10px; color: #666;">
                        Rp {{ number_format($item->price, 0, ',', '.') }} x {{ $item->qty }}
                    </span>
                    <span class="item-price">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                </div>
            @endforeach
        </div>
        
        <div class="divider"></div>
        
        <div class="summary-section">
            <div class="summary-row">
                <span>Subtotal:</span>
                <span>Rp {{ number_format($sale->items->sum('subtotal'), 0, ',', '.') }}</span>
            </div>
            
            @if($sale->discount > 0)
                <div class="summary-row">
                    <span>Discount:</span>
                    <span>-Rp {{ number_format($sale->discount, 0, ',', '.') }}</span>
                </div>
            @endif
            
            <div class="summary-row total">
                <span>Total:</span>
                <span>Rp {{ number_format($sale->total_price, 0, ',', '.') }}</span>
            </div>
            
            <div class="summary-row">
                <span>Paid:</span>
                <span>Rp {{ number_format($sale->paid_amount, 0, ',', '.') }}</span>
            </div>
            
            @if($sale->change_amount > 0)
                <div class="summary-row change">
                    <span>Change:</span>
                    <span>Rp {{ number_format($sale->change_amount, 0, ',', '.') }}</span>
                </div>
            @endif
        </div>
        
        <div class="footer">
            <div class="thank-you">Thank You!</div>
            <div class="date-time">{{ now()->format('d/m/Y H:i:s') }}</div>
        </div>
    </div>
    
    <div class="print-button no-print">
        <button onclick="window.print()">Print Receipt</button>
        <button onclick="window.history.back()" style="margin-left: 10px; background: #666;">Back</button>
    </div>
    
    <script>
        // Auto print on page load
        window.addEventListener('load', function() {
            setTimeout(function() {
                window.print();
            }, 500);
        });
    </script>
</body>
</html>
