<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Report</title>

        <style>
            /* ===== Page setup (DomPDF) ===== */
            @page {
                margin: 18mm 12mm 14mm 12mm;
                /* top right bottom left */
            }

            body {
                font-family: DejaVu Sans, Arial, sans-serif;
                font-size: 11px;
                color: #000;
            }

            /* ===== Utilities ===== */
            .text-left {
                text-align: left;
            }

            .text-right {
                text-align: right;
            }

            .text-center {
                text-align: center;
            }

            .align-top {
                vertical-align: top;
            }

            .align-middle {
                vertical-align: middle;
            }

            .mb-6 {
                margin-bottom: 6px;
            }

            .mb-10 {
                margin-bottom: 10px;
            }

            .mt-10 {
                margin-top: 10px;
            }

            .mt-16 {
                margin-top: 16px;
            }

            .w-50 {
                width: 50%;
            }

            /* ===== Header ===== */
            .report-header {
                text-align: center;
                margin-bottom: 10px;
                padding-bottom: 8px;
                border-bottom: 1px solid #333;
            }

            .report-header img {
                height: 50px;
                margin-bottom: 6px;
            }

            .report-header .title {
                font-size: 14px;
                font-weight: 700;
                margin: 0;
                line-height: 1.2;
            }

            .report-header .subtitle {
                margin: 2px 0 0 0;
                font-size: 10px;
                line-height: 1.3;
            }

            /* ===== Info table ===== */
            .info-table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 10px;
                margin-bottom: 12px;
            }

            .info-table td {
                padding: 2px 0;
                vertical-align: top;
            }

            .info-table strong {
                display: inline-block;
                min-width: 130px;
                /* rapikan titik dua */
            }

            /* ===== Main table ===== */
            .excel-table {
                width: 100%;
                border-collapse: collapse;
                table-layout: fixed;
                font-size: 10.5px;
            }

            .excel-table th,
            .excel-table td {
                border: 1px solid #333;
                padding: 5px 6px;
                text-align: center;
                vertical-align: middle;
                word-wrap: break-word;
            }

            .excel-table thead th {
                font-weight: 700;
            }

            /* repeat header on new pages (DomPDF friendly) */
            thead {
                display: table-header-group;
            }

            tfoot {
                display: table-row-group;
            }

            /* (opsional) */
            tr {
                page-break-inside: avoid;
            }

            .thead-bg {
                background: #79ADF6;
            }

            .total-row td {
                background: #79ADF6;
                font-weight: 700;
            }

            /* Optional: tighter first col */
            .col-bak {
                width: 90px;
            }
        </style>
    </head>

    <body>
        <!-- Header -->
        <div class="report-header">
            <img src="{{ public_path('img/logo-removebg.png') }}" alt="Logo" />
            <p class="title">PT BAHARI PRIMA MANUNGGAL</p>
            <p class="subtitle">JL. Bakti Mulya 2 No. 58, Kel. Tegal Alur, Kec. Kalideres, Jakarta Barat</p>
        </div>

        <!-- Date & Supplier Information -->
        <table class="info-table">
            <tr>
                <td class="w-50 align-center text-left">
                    <div class="mb-6">
                        <strong>Tanggal Service</strong>: {{ $tgl_service }}
                    </div>
                    <div class="mb-6"><strong>Tanggal Injek CO</strong>: {{ $tgl_injek_co }}</div>
                    <div class="mb-6">
                        <strong>Tanggal Penerimaan</strong>: {{ $tgl_penerimaan }}
                    </div>
                    <div class="mb-6">
                        <strong>Jenis Penerimaan</strong>: {{ $jenis_penerimaan }}
                    </div>
                </td>
                <td class="w-50 align-center text-left">
                </td>
            </tr>
        </table>

        <!-- Main Table -->
        <table class="excel-table">
            <thead>
                <tr class="thead-bg">
                    <th class="col-bak" rowspan="2">No. Batch</th>
                    @for ($i = 1; $i <= 7; $i++)
                        <th colspan="2">
                            {{ $kategori_byproduk_ct->firstWhere('kategori_byproduk_id', $selectedKategoriByproduk[$i])?->nama_produk ?? '-' }}
                        </th>
                    @endfor
                </tr>
                <tr class="thead-bg">
                    <th>Berat (Kg)</th>
                    <th>Total (Pcs)</th>
                    <th>Berat (Kg)</th>
                    <th>Total (Pcs)</th>
                    <th>Berat (Kg)</th>
                    <th>Total (Pcs)</th>
                    <th>Berat (Kg)</th>
                    <th>Total (Pcs)</th>
                    <th>Berat (Kg)</th>
                    <th>Total (Pcs)</th>
                    <th>Berat (Kg)</th>
                    <th>Total (Pcs)</th>
                    <th>Berat (Kg)</th>
                    <th>Total (Pcs)</th>
                </tr>
            </thead>

            <tbody>
                @forelse (($data ?? []) as $index => $row)
                    <tr>
                        {{-- No. Batch --}}
                        <td>
                            {{ $row['no_batch'] ?? '-' }}
                        </td>
                        {{-- Berat & Total Produk --}}
                        @for ($i = 1; $i <= 7; $i++)
                            <td>
                                @if ($row['berat_produk' . $i])
                                    {{ number_format($row['berat_produk' . $i] ?? 0, 2) }} kg
                                @endif
                            </td>
                            <td>
                                @if ($row['berat_produk' . $i])
                                    {{ $row['total_produk' . $i] ?? 0 }}
                                @endif
                            </td>
                        @endfor
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center" style="padding: 10px;">
                            Data tidak tersedia
                        </td>
                    </tr>
                @endforelse
            </tbody>

            @if (!empty($data) && count($data) > 0)
                <tfoot>
                    <tr class="total-row">
                        <td>Total</td>
                        @for ($i = 1; $i <= 7; $i++)
                            <td>{{ number_format($total_berat[$i] ?? 0, 2) }} kg</td>
                            <td>{{ number_format($total_pcs[$i] ?? 0, 0) }} pcs</td>
                        @endfor
                    </tr>
                </tfoot>
            @endif
        </table>
    </body>

</html>
