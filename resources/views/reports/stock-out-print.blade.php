<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>WMS Stock Out BKB Archive</title>
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
        .dept-section {
            margin-bottom: 25px;
        }
        .dept-title {
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
        <span style="font-weight: bold; color: #475569;">🖨️ A4 BKB Operational Print Preview</span>
        <button onclick="window.print()" style="background: #0f172a; color: #fff; border: 0; padding: 6px 12px; border-radius: 4px; font-weight: bold; cursor: pointer;">Trigger Print</button>
    </div>

    <div class="header">
        <h1>Bukti Keluar Barang (BKB) - WMS Bridge</h1>
        <p>Operational Transaction Records for Legacy ERP Entry</p>
    </div>

    <div class="meta-info">
        <div>Date Generated: {{ now()->format('Y-m-d H:i:s') }}</div>
        <div>Active Warehouse: {{ session('active_warehouse_code', 'SPAREPART') }}</div>
    </div>

    @php
        $grouped = $transactions->groupBy('department_id');
    @endphp

    @forelse($grouped as $deptId => $txs)
        @php
            $firstTx = $txs->first();
            $deptName = $firstTx && $firstTx->department ? $firstTx->department->name : 'UNMAPPED / GENERAL';
        @endphp
        <div class="dept-section">
            <div class="dept-title">Department: {{ $deptName }}</div>
            <table>
                <thead>
                    <tr>
                        <th style="width: 15%">TX Code</th>
                        <th style="width: 20%">ERP Code</th>
                        <th style="width: 35%">Item Name</th>
                        <th style="width: 10%" class="text-right">Qty</th>
                        <th style="width: 10%">Unit</th>
                        <th style="width: 10%">BKB Ref</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($txs as $tx)
                        @foreach($tx->items as $item)
                            <tr>
                                <td>{{ $tx->code }}</td>
                                <td><strong>{{ $item->erp_code_snapshot ?? $item->variant->erp_code ?? 'N/A' }}</strong></td>
                                <td>{{ $item->item_name_snapshot ?? $item->variant->item->name ?? 'N/A' }}</td>
                                <td class="text-right">{{ $item->qty }}</td>
                                <td>{{ $item->unit_snapshot ?? $item->variant->unit ?? 'PCS' }}</td>
                                <td>{{ $tx->reference ?: 'PENDING' }}</td>
                            </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>
        </div>
    @empty
        <p style="text-align: center; font-weight: bold;">No stock out transactions match the active print filters.</p>
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
