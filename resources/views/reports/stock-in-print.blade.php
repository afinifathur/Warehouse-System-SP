<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>WMS Stock In BPB Archive</title>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 10px;
            color: #000;
            background: #fff;
            margin: 0;
            padding: 20px;
            line-height: 1.3;
        }
        .header {
            text-align: center;
            border-bottom: 2px double #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header h1 {
            font-size: 14px;
            margin: 0 0 5px 0;
            text-transform: uppercase;
            font-weight: 900;
        }
        .header p {
            margin: 0;
            font-size: 9px;
            font-weight: bold;
        }
        .meta-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            font-size: 9px;
            font-weight: bold;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        th, td {
            border: 1px solid #000;
            padding: 4px 6px;
            text-align: left;
            vertical-align: top;
        }
        th {
            background-color: #f2f2f2 !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
            font-weight: bold;
        }
        .text-right {
            text-align: right;
        }
        .batch-section {
            margin-bottom: 25px;
        }
        .batch-title {
            font-size: 11px;
            font-weight: 900;
            border-bottom: 1px solid #000;
            padding-bottom: 2px;
            margin-bottom: 8px;
            text-transform: uppercase;
        }
        .footer-sig {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
            page-break-inside: avoid;
        }
        .sig-box {
            text-align: center;
            width: 200px;
        }
        .sig-line {
            margin-top: 40px;
            border-top: 1px solid #000;
            padding-top: 4px;
            font-weight: bold;
        }
        @media print {
            body {
                padding: 0;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>

    <div class="no-print" style="background: #f8fafc; border: 1px solid #e2e8f0; padding: 10px; margin-bottom: 20px; border-radius: 6px; display: flex; justify-content: space-between; items: center;">
        <span style="font-weight: bold; color: #475569;">🖨️ A4 BPB Inbound Print Preview</span>
        <button onclick="window.print()" style="background: #0f172a; color: #fff; border: 0; padding: 6px 12px; border-radius: 4px; font-weight: bold; cursor: pointer;">Trigger Print</button>
    </div>

    <div class="header">
        <h1>Bukti Penerimaan Barang (BPB) - WMS Bridge</h1>
        <p>Operational Receiving Records for Legacy ERP Entry</p>
    </div>

    <div class="meta-info">
        <div>Date Generated: {{ now()->format('Y-m-d H:i:s') }}</div>
        <div>Active Warehouse: {{ session('active_warehouse_code', 'SPAREPART') }}</div>
    </div>

    @forelse($receipts as $receipt)
        <div class="batch-section">
            <div class="batch-title">Receipt Code: {{ $receipt->receipt_code }} | PO Reference: {{ $receipt->purchase_order_ref ?: 'N/A' }}</div>
            <div style="font-[8px]; margin-bottom: 6px; font-weight: bold;">
                Operator: {{ $receipt->operator->name ?? 'N/A' }} | Date: {{ $receipt->created_at->format('Y-m-d H:i') }} | Supplier: {{ $receipt->supplier->name ?? 'N/A' }}
            </div>
            <table>
                <thead>
                    <tr>
                        <th style="width: 25%">ERP Code</th>
                        <th style="width: 45%">Item Name</th>
                        <th style="width: 15%" class="text-right">Qty Received</th>
                        <th style="width: 15%">Bin Coordinate</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($receipt->items as $item)
                        <tr>
                            <td><strong>{{ $item->variant->erp_code ?? 'N/A' }}</strong></td>
                            <td>{{ $item->variant->item->name ?? 'N/A' }}</td>
                            <td class="text-right">{{ $item->qty }}</td>
                            <td>{{ $item->bin->code ?? 'N/A' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @empty
        <p style="text-align: center; font-weight: bold;">No receiving session batches match the active print filters.</p>
    @endforelse

    <div class="footer-sig">
        <div class="sig-box">
            <p>Admin WMS</p>
            <div class="sig-line">({{ auth()->user()->name }})</div>
        </div>
        <div class="sig-box">
            <p>Warehouse Supervisor</p>
            <div class="sig-line">(                    )</div>
        </div>
        <div class="sig-box">
            <p>ERP Data Inputter</p>
            <div class="sig-line">(                    )</div>
        </div>
    </div>

    <script>
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 500);
        };
    </script>
</body>
</html>
