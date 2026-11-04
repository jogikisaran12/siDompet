
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Cetak Laporan Keuangan - Dompetku</title>

    <script src="{{ asset('js/theme.js') }}"></script>

    <style>
        @page {
            size: A4 portrait;
            margin: 15mm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', ui-sans-serif, system-ui, sans-serif;
            background: #f8fafc;
            color: #0f172a;
            font-size: 12px;
            margin: 0;
            padding: 30px;
        }

        .print-container {
            max-width: 850px;
            margin: auto;
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 5px 25px rgba(0,0,0,0.05);
        }

        .toolbar {
            max-width: 850px;
            margin: 0 auto 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .toolbar h3 {
            margin: 0;
            font-size: 17px;
        }

        .print-button {
            background: #6366f1;
            border: none;
            border-radius: 8px;
            color: white;
            padding: 12px 22px;
            cursor: pointer;
            font-weight: bold;
        }

        .print-button:hover {
            background: #4f46e5;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #202238;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .logo {
            display: inline-block;
            background: #695cf6;
            color: white;
            padding: 10px 13px;
            border-radius: 10px;
            font-size: 22px;
            margin-bottom: 12px;
        }

        .header h2 {
            font-size: 21px;
            letter-spacing: 1px;
            margin: 0;
        }

        .header p {
            color: #777;
            margin: 9px 0 0;
            font-size: 12px;
        }

        .section-title {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 15px;
            padding-left: 10px;
            border-left: 4px solid #695cf6;
        }

        .summary {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        .summary td {
            padding: 12px 15px;
            border-bottom: 1px solid #edf0f5;
        }

        .summary td:last-child {
            text-align: right;
            font-weight: bold;
        }

        .summary .total {
            background: #f1efff;
            font-weight: bold;
        }

        .income {
            color: #159b62;
        }

        .expense {
            color: #e34d65;
        }

        .report-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            font-size: 11px;
        }

        .report-table th {
            background: #f1f2f7;
            color: #34364b;
            font-weight: bold;
            padding: 12px 9px;
            text-align: left;
            border: 1px solid #dfe2ea;
        }

        .report-table td {
            padding: 11px 9px;
            border: 1px solid #e5e7ee;
        }

        .report-table tbody tr:nth-child(even) {
            background: #fafbfe;
        }

        .amount {
            text-align: right !important;
            white-space: nowrap;
        }

        .footer {
            margin-top: 35px;
            border-top: 1px solid #ddd;
            padding-top: 15px;
            display: flex;
            justify-content: space-between;
            color: #777;
            font-size: 10px;
        }

        .footer strong {
            color: #202238;
        }

        @media print {
            body {
                background: white;
                margin: 0;
                padding: 0;
            }

            .toolbar {
                display: none !important;
            }

            .print-container {
                max-width: none;
                padding: 0;
                margin: 0;
                border-radius: 0;
                box-shadow: none;
            }

            .report-table thead {
                display: table-header-group;
            }

            .report-table tr {
                break-inside: avoid;
                page-break-inside: avoid;
            }

            .summary tr {
                break-inside: avoid;
            }

            .header,
            .section-title {
                break-after: avoid;
            }

            * {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
        /* ================= MODE GELAP / TERANG ================= */
        :root {
            color-scheme: light;
        }

        .dark {
            color-scheme: dark;
        }

        .dark body {
            background: #0F172A;
            color: #E2E8F0;
        }
        .dark .print-container {
            background: #1E293B;
            box-shadow: 0 5px 25px rgba(0, 0, 0, 0.45);
        }
        .dark .toolbar h3 {
            color: #F8FAFC;
        }
        .dark .print-button {
            background: #4F46E5;
        }
        .dark .header {
            border-color: #334155;
        }
        .dark .header h2 {
            color: #F8FAFC;
        }
        .dark .header p,
        .dark .footer p {
            color: #94A3B8;
        }
        .dark .summary td {
            border-color: #334155;
        }
        .dark .summary td:last-child {
            color: #F8FAFC;
        }
        .dark .summary .total {
            background: #064E3B;
        }
        .dark .report-table th {
            background: #334155;
            color: #F8FAFC;
            border-color: #475569;
        }
        .dark .report-table td {
            border-color: #334155;
        }
        .dark .report-table tbody tr:nth-child(even) {
            background: #172033;
        }
        .dark .footer {
            border-color: #334155;
        }
        .dark .footer strong {
            color: #F8FAFC;
        }

        @media print {
            body,
            .dark body {
                background: #ffffff;
                color: #0f172a;
            }
            .dark .print-container {
                background: #ffffff;
                box-shadow: none;
            }
            .dark .toolbar h3,
            .dark .header h2,
            .dark .summary td:last-child,
            .dark .footer strong {
                color: #0f172a;
            }
            .dark .header p,
            .dark .footer p {
                color: #777;
            }
            .dark .summary td {
                border-color: #edf0f5;
            }
            .dark .summary .total {
                background: #f1efff;
            }
            .dark .report-table th {
                background: #f1f2f7;
                color: #34364b;
                border-color: #dfe2ea;
            }
            .dark .report-table td {
                border-color: #e5e7ee;
            }
            .dark .report-table tbody tr:nth-child(even) {
                background: #fafbfe;
            }
            .dark .header {
                border-color: #202238;
            }
            .dark .footer {
                border-color: #ddd;
            }
        }
    </style>
</head>

<body>

    <div class="toolbar">
        <h3>Preview Laporan Keuangan</h3>

        <button class="print-button" onclick="window.print()">
            &#128424; Cetak / Simpan PDF
        </button>
    </div>

    <div class="print-container">

        <div class="header">
            <div class="logo">D</div>

            <h2>LAPORAN KEUANGAN DOMPETKU</h2>

            <p>
                Periode:
                {{ \Carbon\Carbon::createFromFormat('!Y-m', $month)->locale('id')->translatedFormat('F Y') }}
            </p>
        </div>

        <div class="section-title">
            Ringkasan Keuangan
        </div>

        <div style="margin-bottom: 15px; color: #666; font-size: 11px;">
            Aktivitas tabungan dicatat secara terpisah dan tidak dimasukkan ke tabel transaksi utama.
        </div>

        <table class="summary">
            <tr>
                <td>Total Pemasukan (Periode)</td>
                <td class="income">
                    Rp {{ number_format($income, 0, ',', '.') }}
                </td>
            </tr>

            <tr>
                <td>Total Pengeluaran (Periode)</td>
                <td class="expense">
                    Rp {{ number_format($expense, 0, ',', '.') }}
                </td>
            </tr>

            <tr>
                <td>Setoran Tabungan (Periode)</td>
                <td>
                    Rp {{ number_format($depositPeriod, 0, ',', '.') }}
                </td>
            </tr>

            <tr>
                <td>Penarikan Tabungan (Periode)</td>
                <td>
                    Rp {{ number_format($withdrawalPeriod, 0, ',', '.') }}
                </td>
            </tr>

            <tr class="total">
                <td>Perubahan Saldo Tersedia (Periode)</td>
                <td>
                    Rp {{ number_format($balancePeriodChange, 0, ',', '.') }}
                </td>
            </tr>
        </table>

        <div class="section-title">
            Saldo Terintegrasi Saat Ini
        </div>

        <table class="summary">
            <tr>
                <td>Saldo Tersedia Saat Ini</td>
                <td>
                    Rp {{ number_format($availableBalanceNow, 0, ',', '.') }}
                </td>
            </tr>
            <tr>
                <td>Total Tabungan Saat Ini</td>
                <td>
                    Rp {{ number_format($totalSavingsNow, 0, ',', '.') }}
                </td>
            </tr>
            <tr class="total">
                <td>Total Uang Keseluruhan</td>
                <td>
                    Rp {{ number_format($totalMoneyNow, 0, ',', '.') }}
                </td>
            </tr>
        </table>

        <div class="section-title">
            Rincian Transaksi
        </div>

        <table class="report-table">
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th width="14%">Tanggal</th>
                    <th width="19%">Kategori</th>
                    <th width="24%">Keterangan</th>
                    <th width="15%">Jenis</th>
                    <th width="23%" class="amount">Jumlah</th>
                </tr>
            </thead>

            <tbody>
                @forelse($transactions as $transaction)
                    <tr>
                        <td>{{ $loop->iteration }}</td>

                        <td>
                            {{ $transaction->transaction_date->format('d/m/Y') }}
                        </td>

                        <td>
                            {{ $transaction->category->name }}
                        </td>

                        <td>
                            {{ $transaction->description ?? '-' }}
                        </td>

                        <td>
                            @if($transaction->type == 'income')
                                <span class="income">Pemasukan</span>
                            @else
                                <span class="expense">Pengeluaran</span>
                            @endif
                        </td>

                        <td class="amount">
                            Rp {{ number_format($transaction->amount, 0, ',', '.') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align:center; padding:25px;">
                            Tidak ada transaksi pada periode ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="footer">
            <div>
                <strong>DOMPETKU</strong>
                <p>Laporan keuangan pribadi</p>
            </div>

            <div style="text-align:right;">
                Dicetak pada:
                {{ now()->locale('id')->translatedFormat('d F Y, H:i') }}
                <p>Dokumen dibuat secara otomatis oleh sistem.</p>
            </div>
        </div>

    </div>

</body>
</html>
