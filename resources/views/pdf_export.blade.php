<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Pertamina Gas Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #D71920;
            padding-bottom: 15px;
        }
        .header h1 {
            color: #D71920;
            margin: 0;
            font-size: 24px;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th {
            background-color: #D71920;
            color: white;
            padding: 10px;
            text-align: left;
            font-weight: bold;
        }
        td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #999;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        .summary {
            background-color: #f0f0f0;
            padding: 15px;
            margin-bottom: 20px;
            border-left: 4px solid #D71920;
        }
        .summary p {
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>PERTAMINA GAS</h1>
        <p>Dashboard Penyaluran Gas 2020-2025</p>
        <p>Laporan Data Penyaluran</p>
        <p style="font-size: 9px;">Generated: {{ date('d F Y H:i:s') }}</p>
    </div>

    <div class="summary">
        <p><strong>Total Records:</strong> {{ count($data) }}</p>
        <p><strong>Total Volume:</strong> {{ number_format($data->sum('daily_average_mmsfcd'), 2) }} MMSCFD</p>
        <p><strong>Average Volume:</strong> {{ number_format($data->avg('daily_average_mmsfcd'), 2) }} MMSCFD</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 25%;">Shipper</th>
                <th style="width: 20%;">Bulan</th>
                <th style="width: 20%;">Periode</th>
                <th style="width: 30%; text-align: right;">Daily Average (MMSCFD)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $item->shipper }}</td>
                <td>{{ date('F Y', strtotime($item->bulan)) }}</td>
                <td>{{ $item->periode }}</td>
                <td style="text-align: right;">{{ number_format($item->daily_average_mmsfcd, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>&copy; {{ date('Y') }} Pertamina Gas - Developed for PKL Program</p>
        <p>This is a computer-generated document. No signature is required.</p>
    </div>
</body>
</html>
