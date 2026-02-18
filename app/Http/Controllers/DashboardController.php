<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $query = DB::table('volume_gas')
                    ->where('data', 'PENYALURAN');
        
        // Filter - handle multiple shipper
        if ($request->shipper) {
            if (is_array($request->shipper)) {
                $query->whereIn('shipper', $request->shipper);
            } elseif (strpos($request->shipper, ',') !== false) {
                $shippers = explode(',', $request->shipper);
                $shippers = array_filter($shippers);
                $query->whereIn('shipper', $shippers);
            } else {
                $query->where('shipper', 'LIKE', '%' . $request->shipper . '%');
            }
        }
        
        if ($request->tahun_dari) {
            $query->whereYear('bulan_date', '>=', $request->tahun_dari);
        }
        if ($request->tahun_sampai) {
            $query->whereYear('bulan_date', '<=', $request->tahun_sampai);
        }
        
        if ($request->bulan) {
            if (is_array($request->bulan)) {
                $query->whereIn(DB::raw('MONTH(bulan_date)'), $request->bulan);
            } else {
                $query->whereMonth('bulan_date', $request->bulan);
            }
        }
        
        $data = $query->selectRaw('
        shipper, 
        bulan_date,
        daily_average_mmscfd,
        DATE_FORMAT(bulan_date, "%Y-%m") as periode
        ')
        ->orderBy('bulan_date', 'desc')
        ->paginate(20);
        
        $shippers = DB::table('volume_gas')
                     ->where('data', 'PENYALURAN')
                     ->select('shipper')
                     ->distinct()
                     ->orderBy('shipper')
                     ->pluck('shipper');
        
        $tahuns = DB::table('volume_gas')
                   ->where('data', 'PENYALURAN')
                   ->selectRaw('DISTINCT YEAR(bulan_date) as tahun')
                   ->orderBy('tahun', 'desc')
                   ->pluck('tahun');
        
        // Apply same filters to statistics
        $statsQuery = DB::table('volume_gas')->where('data', 'PENYALURAN');
        
        if ($request->shipper) {
            if (is_array($request->shipper)) {
                $statsQuery->whereIn('shipper', $request->shipper);
            } elseif (strpos($request->shipper, ',') !== false) {
                $shippers = explode(',', $request->shipper);
                $shippers = array_filter($shippers);
                $statsQuery->whereIn('shipper', $shippers);
            } else {
                $statsQuery->where('shipper', 'LIKE', '%' . $request->shipper . '%');
            }
        }
        
        if ($request->tahun_dari) {
            $statsQuery->whereYear('bulan_date', '>=', $request->tahun_dari);
        }
        if ($request->tahun_sampai) {
            $statsQuery->whereYear('bulan_date', '<=', $request->tahun_sampai);
        }
        
        if ($request->bulan) {
            if (is_array($request->bulan)) {
                $statsQuery->whereIn(DB::raw('MONTH(bulan_date)'), $request->bulan);
            } else {
                $statsQuery->whereMonth('bulan_date', $request->bulan);
            }
        }
        
        $totalVolume = $statsQuery->sum('daily_average_mmscfd');
        $totalRecords = $statsQuery->count();
        $avgVolume = $statsQuery->avg('daily_average_mmscfd');
        
        // Query baru untuk volume tertinggi
        $volumeTertinggi = DB::table('volume_gas')
                            ->where('data', 'PENYALURAN')
                            ->select('shipper', 'bulan_date', 'daily_average_mmscfd');

        // Apply same filters
        if ($request->shipper) {
            if (is_array($request->shipper)) {
                $volumeTertinggi->whereIn('shipper', $request->shipper);
            }
        }
        if ($request->tahun_dari) {
            $volumeTertinggi->whereYear('bulan_date', '>=', $request->tahun_dari);
        }
        if ($request->tahun_sampai) {
            $volumeTertinggi->whereYear('bulan_date', '<=', $request->tahun_sampai);
        }
        if ($request->bulan) {
            if (is_array($request->bulan)) {
                $volumeTertinggi->whereIn(DB::raw('MONTH(bulan_date)'), $request->bulan);
            }
        }

        $volumeTertinggi = $volumeTertinggi->orderBy('daily_average_mmscfd', 'desc')->first();

        if (!$volumeTertinggi) {
            $volumeTertinggi = (object)[
                'daily_average_mmscfd' => 0,
                'shipper' => '-',
                'bulan_date' => null
            ];
        }

        // SAMA BUAT TERENDAH
        $volumeTerendah = DB::table('volume_gas')
                            ->where('data', 'PENYALURAN')
                            ->where('daily_average_mmscfd', '>', 0)
                            ->select('shipper', 'bulan_date', 'daily_average_mmscfd');

        if ($request->shipper) {
            if (is_array($request->shipper)) {
                $volumeTerendah->whereIn('shipper', $request->shipper);
            }
        }
        if ($request->tahun_dari) {
            $volumeTerendah->whereYear('bulan_date', '>=', $request->tahun_dari);
        }
        if ($request->tahun_sampai) {
            $volumeTerendah->whereYear('bulan_date', '<=', $request->tahun_sampai);
        }
        if ($request->bulan) {
            if (is_array($request->bulan)) {
                $volumeTerendah->whereIn(DB::raw('MONTH(bulan_date)'), $request->bulan);
            }
        }

        $volumeTerendah = $volumeTerendah->orderBy('daily_average_mmscfd', 'asc')->first();

        if (!$volumeTerendah) {
            $volumeTerendah = (object)[
                'daily_average_mmscfd' => 0,
                'shipper' => '-',
                'bulan_date' => null
            ];
        }
        
        return view('dashboard', compact(
            'data', 
            'shippers', 
            'tahuns', 
            'totalVolume', 
            'totalRecords', 
            'avgVolume',
            'volumeTertinggi',
            'volumeTerendah'
        ));
    }
    
    public function chart(Request $request)
    {
        $query = DB::table('volume_gas')
                    ->where('data', 'PENYALURAN')
                    ->select(
                        'shipper',
                        DB::raw('DATE_FORMAT(bulan_date, "%b-%y") as periode_label'),
                        DB::raw('DATE_FORMAT(bulan_date, "%Y-%m") as periode_sort'),
                        DB::raw('SUM(daily_average_mmscfd) as total')
                    )
                    ->groupBy('shipper', 'periode_label', 'periode_sort');
        
        // Filters
        if ($request->shipper) {
            if (is_array($request->shipper)) {
                $query->whereIn('shipper', $request->shipper);
            } else {
                $query->where('shipper', $request->shipper);
            }
        }
        
        if ($request->tahun_dari) {
            $query->whereYear('bulan_date', '>=', $request->tahun_dari);
        }
        if ($request->tahun_sampai) {
            $query->whereYear('bulan_date', '<=', $request->tahun_sampai);
        }
        
        if ($request->bulan) {
            if (is_array($request->bulan)) {
                $query->whereIn(DB::raw('MONTH(bulan_date)'), $request->bulan);
            } else {
                $query->whereMonth('bulan_date', $request->bulan);
            }
        }
        
        $rows = $query->orderBy('periode_sort')->get();

        // Build labels and series
        $labels = [];
        $seriesMap = [];

        foreach ($rows as $row) {
            if (!in_array($row->periode_label, $labels)) {
                $labels[] = $row->periode_label;
            }
            
            if (!isset($seriesMap[$row->shipper])) {
                $seriesMap[$row->shipper] = [];
            }
            
            $seriesMap[$row->shipper][$row->periode_label] = (float) $row->total;
        }

        // Convert to ApexCharts format
        $series = [];
        foreach ($seriesMap as $shipper => $dataMap) {
            $values = [];
            foreach ($labels as $label) {
                $values[] = $dataMap[$label] ?? 0;
            }
            $series[] = [
                'name' => $shipper,
                'data' => $values,
            ];
        }

        return response()->json([
            'labels' => $labels,
            'series' => $series,
        ]);
    }
        
    public function topData(Request $request)
    {
        $query = DB::table('volume_gas')
                    ->where('data', 'PENYALURAN')
                    ->select('shipper', DB::raw('SUM(daily_average_mmscfd) as total_volume'))
                    ->groupBy('shipper');
        
        if ($request->shipper) {
            if (is_array($request->shipper)) {
                $query->whereIn('shipper', $request->shipper);
            } elseif (strpos($request->shipper, ',') !== false) {
                $shippers = explode(',', $request->shipper);
                $shippers = array_filter($shippers);
                $query->whereIn('shipper', $shippers);
            } else {
                $query->where('shipper', 'LIKE', '%' . $request->shipper . '%');
            }
        }
        
        if ($request->tahun_dari) {
            $query->whereYear('bulan_date', '>=', $request->tahun_dari);
        }
        if ($request->tahun_sampai) {
            $query->whereYear('bulan_date', '<=', $request->tahun_sampai);
        }
        
        if ($request->bulan) {
            if (is_array($request->bulan)) {
                $query->whereIn(DB::raw('MONTH(bulan_date)'), $request->bulan);
            } else {
                $query->whereMonth('bulan_date', $request->bulan);
            }
        }
        
        $top = $query->orderBy('total_volume', 'desc')
                     ->limit(5)
                     ->get();
        
        return response()->json($top);
    }
    
    public function trendAnalysis(Request $request)
    {
        if (!$request->shipper) {
            return response()->json(['error' => 'Shipper harus dipilih'], 400);
        }
        
        $query = DB::table('volume_gas')
                    ->where('data', 'PENYALURAN')
                    ->where('shipper', $request->shipper)
                    ->select(
                        DB::raw('YEAR(bulan_date) as tahun'),
                        DB::raw('MONTH(bulan_date) as bulan_num'),
                        DB::raw('DATE_FORMAT(MAX(bulan_date), "%Y-%m") as periode'),
                        DB::raw('SUM(daily_average_mmscfd) as volume')
                    )
                    ->groupBy(DB::raw('YEAR(bulan_date)'), DB::raw('MONTH(bulan_date)'))
                    ->orderBy('tahun')
                    ->orderBy('bulan_num')
                    ->get();
        
        if ($query->count() < 2) {
            return response()->json(['error' => 'Data tidak cukup untuk analisis'], 400);
        }
        
        $trends = [];
        $previousVolume = null;
        
        foreach ($query as $index => $item) {
            if ($previousVolume !== null) {
                $change = $item->volume - $previousVolume;
                $percentChange = $previousVolume != 0 
                    ? ($change / $previousVolume) * 100 
                    : 0;
                
                $isAnomaly = abs($percentChange) > 20;
                
                $status = $change > 0 ? 'naik' : ($change < 0 ? 'turun' : 'stabil');
                
                $trends[] = [
                    'periode' => $item->periode,
                    'tahun' => $item->tahun,
                    'bulan' => $item->bulan_num,
                    'volume' => round($item->volume, 2),
                    'previous_volume' => round($previousVolume, 2),
                    'change' => round($change, 2),
                    'percent_change' => round($percentChange, 2),
                    'status' => $status,
                    'is_anomaly' => $isAnomaly,
                    'anomaly_type' => $isAnomaly 
                        ? ($percentChange > 0 ? 'lonjakan_drastis' : 'penurunan_drastis') 
                        : null
                ];
            }
            
            $previousVolume = $item->volume;
        }
        
        return response()->json([
            'shipper' => $request->shipper,
            'trends' => $trends,
            'total_periods' => count($trends),
            'anomaly_count' => count(array_filter($trends, fn($t) => $t['is_anomaly']))
        ]);
    }
    
    public function comparisonData(Request $request)
    {
        $shippers = $request->shippers;
        
        if (!$shippers || !is_array($shippers) || count($shippers) < 2) {
            return response()->json(['error' => 'Minimal 2 shipper untuk perbandingan'], 400);
        }
        
        $query = DB::table('volume_gas')
                    ->where('data', 'PENYALURAN')
                    ->whereIn('shipper', $shippers)
                    ->select(
                        'shipper',
                        DB::raw('DATE_FORMAT(bulan_date, "%Y-%m") as periode'),
                        DB::raw('YEAR(bulan_date) as tahun'),
                        DB::raw('MONTH(bulan_date) as bulan_num'),
                        DB::raw('SUM(daily_average_mmscfd) as volume')
                    )
                    ->groupBy('shipper', 'periode', 'tahun', 'bulan_num');
        
        if ($request->tahun_dari) {
            $query->whereYear('bulan_date', '>=', $request->tahun_dari);
        }
        if ($request->tahun_sampai) {
            $query->whereYear('bulan_date', '<=', $request->tahun_sampai);
        }
        
        $data = $query->orderBy('tahun')
                     ->orderBy('bulan_num')
                     ->get();
        
        $grouped = $data->groupBy('shipper');
        
        $result = [];
        foreach ($grouped as $shipper => $records) {
            $result[$shipper] = [
                'total_volume' => $records->sum('volume'),
                'avg_volume' => $records->avg('volume'),
                'max_volume' => $records->max('volume'),
                'min_volume' => $records->min('volume'),
                'data_points' => $records->count(),
                'data' => $records->values()
            ];
        }
        
        return response()->json($result);
    }
    
    public function allShippersData(Request $request)
    {
        $query = DB::table('volume_gas')
                    ->where('data', 'PENYALURAN')
                    ->select('shipper', DB::raw('SUM(daily_average_mmscfd) as total_volume'))
                    ->groupBy('shipper');
        
        if ($request->shipper) {
            if (is_array($request->shipper)) {
                $query->whereIn('shipper', $request->shipper);
            } elseif (strpos($request->shipper, ',') !== false) {
                $shippers = explode(',', $request->shipper);
                $shippers = array_filter($shippers);
                $query->whereIn('shipper', $shippers);
            } else {
                $query->where('shipper', 'LIKE', '%' . $request->shipper . '%');
            }
        }
        
        if ($request->tahun_dari) {
            $query->whereYear('bulan_date', '>=', $request->tahun_dari);
        }
        if ($request->tahun_sampai) {
            $query->whereYear('bulan_date', '<=', $request->tahun_sampai);
        }
        
        if ($request->bulan) {
            if (is_array($request->bulan)) {
                $query->whereIn(DB::raw('MONTH(bulan_date)'), $request->bulan);
            } else {
                $query->whereMonth('bulan_date', $request->bulan);
            }
        }
        
        $allShippers = $query->orderBy('total_volume', 'desc')
                            ->get();
        
        return response()->json($allShippers);
    }
    
    // ========== EXPORT FUNCTIONS ==========
    
    public function exportExcel(Request $request)
    {
        $data = $this->getFilteredData($request);
        
        $filename = 'pertamina_gas_' . date('YmdHis') . '.xls';
        
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        echo '<html xmlns:x="urn:schemas-microsoft-com:office:excel">';
        echo '<head>';
        echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
        echo '<style>';
        echo 'table { border-collapse: collapse; width: 100%; }';
        echo 'th { background-color: #D71920; color: white; font-weight: bold; padding: 8px; border: 1px solid #ddd; }';
        echo 'td { padding: 8px; border: 1px solid #ddd; }';
        echo 'tr:nth-child(even) { background-color: #f9f9f9; }';
        echo '</style>';
        echo '</head>';
        echo '<body>';
        
        echo '<h2 style="color: #D71920;">PERTAMINA GAS - Dashboard Penyaluran Gas 2020-2025</h2>';
        echo '<p>Laporan Data Penyaluran - Generated: ' . date('d F Y H:i:s') . '</p>';
        echo '<hr>';
        
        echo '<h3>Summary</h3>';
        echo '<p><strong>Total Records:</strong> ' . count($data) . '</p>';
        echo '<p><strong>Total Volume:</strong> ' . number_format($data->sum('daily_average_mmscfd'), 2) . ' MMSCFD</p>';
        echo '<p><strong>Average Volume:</strong> ' . number_format($data->avg('daily_average_mmscfd'), 2) . ' MMSCFD</p>';
        echo '<br>';
        
        echo '<table border="1">';
        echo '<thead>';
        echo '<tr>';
        echo '<th>No</th>';
        echo '<th>Shipper</th>';
        echo '<th>Bulan</th>';
        echo '<th>Periode</th>';
        echo '<th>Daily Average (MMSCFD)</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';
        
        foreach ($data as $index => $item) {
            echo '<tr>';
            echo '<td>' . ($index + 1) . '</td>';
            echo '<td>' . htmlspecialchars($item->shipper) . '</td>';
            echo '<td>' . date('F Y', strtotime($item->bulan_date)) . '</td>';
            echo '<td>' . htmlspecialchars($item->periode) . '</td>';
            echo '<td>' . number_format($item->daily_average_mmscfd, 2) . '</td>';
            echo '</tr>';
        }
        
        echo '</tbody>';
        echo '</table>';
        echo '</body>';
        echo '</html>';
        
        exit;
    }
    
    public function exportCsv(Request $request)
    {
        $data = $this->getFilteredData($request);
        
        $filename = 'pertamina_gas_' . date('YmdHis') . '.csv';
        
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $output = fopen('php://output', 'w');
        
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        fputcsv($output, ['No', 'Shipper', 'Bulan', 'Periode', 'Daily Average (MMSCFD)']);
        
        foreach ($data as $index => $item) {
            fputcsv($output, [
                $index + 1,
                $item->shipper,
                date('F Y', strtotime($item->bulan_date)),
                $item->periode,
                number_format($item->daily_average_mmscfd, 2)
            ]);
        }
        
        fclose($output);
        exit;
    }
    
    public function exportPdf(Request $request)
    {
        $data = $this->getFilteredData($request);
        
        echo '<!DOCTYPE html>';
        echo '<html>';
        echo '<head>';
        echo '<meta charset="UTF-8">';
        echo '<title>Pertamina Gas Report - ' . date('d-M-Y') . '</title>';
        echo '<style>';
        echo '@media print { @page { margin: 1cm; } body { margin: 0; } }';
        echo 'body { font-family: Arial, sans-serif; font-size: 11px; color: #333; margin: 20px; }';
        echo '.header { text-align: center; margin-bottom: 20px; border-bottom: 3px solid #D71920; padding-bottom: 15px; }';
        echo '.header h1 { color: #D71920; margin: 0; font-size: 24px; }';
        echo '.header p { margin: 5px 0; color: #666; font-size: 10px; }';
        echo '.kpi-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; margin-bottom: 20px; }';
        echo '.kpi-box { background: #fff; border: 2px solid #D71920; border-radius: 8px; padding: 15px; text-align: center; }';
        echo '.kpi-box .label { font-size: 9px; color: #666; text-transform: uppercase; font-weight: bold; }';
        echo '.kpi-box .value { font-size: 20px; font-weight: bold; color: #D71920; margin: 8px 0; }';
        echo '.kpi-box .unit { font-size: 8px; color: #999; }';
        echo '.summary { background-color: #f0f0f0; padding: 15px; margin-bottom: 20px; border-left: 4px solid #D71920; page-break-inside: avoid; }';
        echo '.summary p { margin: 5px 0; }';
        echo 'table { width: 100%; border-collapse: collapse; margin-top: 10px; }';
        echo 'th { background-color: #D71920; color: white; padding: 8px; text-align: left; font-weight: bold; font-size: 10px; }';
        echo 'td { padding: 6px 8px; border-bottom: 1px solid #ddd; font-size: 10px; }';
        echo 'tr:nth-child(even) { background-color: #f9f9f9; }';
        echo '.footer { margin-top: 20px; text-align: center; font-size: 9px; color: #999; border-top: 1px solid #ddd; padding-top: 10px; page-break-inside: avoid; }';
        echo '.print-btn { position: fixed; top: 20px; right: 20px; background: #D71920; color: white; border: none; padding: 12px 24px; border-radius: 8px; cursor: pointer; font-weight: bold; font-size: 14px; box-shadow: 0 4px 12px rgba(0,0,0,0.2); z-index: 1000; }';
        echo '.print-btn:hover { background: #8B0000; transform: translateY(-2px); box-shadow: 0 6px 16px rgba(0,0,0,0.3); }';
        echo '@media print { .print-btn { display: none; } }';
        echo '</style>';
        echo '</head>';
        echo '<body>';
        
        echo '<button class="print-btn" onclick="window.print()">';
        echo '<i class="fas fa-print"></i> Cetak / Save PDF';
        echo '</button>';
        
        echo '<div class="header">';
        echo '<h1>PERTAMINA GAS</h1>';
        echo '<p>Dashboard Penyaluran Gas 2020-2025</p>';
        echo '<p>Laporan Data Penyaluran</p>';
        echo '<p>Generated: ' . date('d F Y H:i:s') . '</p>';
        echo '</div>';
        
        echo '<div class="kpi-grid">';
        echo '<div class="kpi-box">';
        echo '<div class="label">Total Volume Penyaluran</div>';
        echo '<div class="value">' . number_format($data->sum('daily_average_mmscfd'), 2) . '</div>';
        echo '<div class="unit">MMSCFD</div>';
        echo '</div>';
        echo '<div class="kpi-box">';
        echo '<div class="label">Total Records</div>';
        echo '<div class="value">' . number_format(count($data)) . '</div>';
        echo '<div class="unit">Data Points</div>';
        echo '</div>';
        echo '<div class="kpi-box">';
        echo '<div class="label">Average Volume</div>';
        echo '<div class="value">' . number_format($data->avg('daily_average_mmscfd'), 2) . '</div>';
        echo '<div class="unit">MMSCFD</div>';
        echo '</div>';
        echo '</div>';
        
        echo '<div class="summary">';
        echo '<p><strong>Total Records:</strong> ' . count($data) . '</p>';
        echo '<p><strong>Total Volume:</strong> ' . number_format($data->sum('daily_average_mmscfd'), 2) . ' MMSCFD</p>';
        echo '<p><strong>Average Volume:</strong> ' . number_format($data->avg('daily_average_mmscfd'), 2) . ' MMSCFD</p>';
        echo '</div>';
        
        echo '<table>';
        echo '<thead>';
        echo '<tr>';
        echo '<th style="width: 5%;">No</th>';
        echo '<th style="width: 25%;">Shipper</th>';
        echo '<th style="width: 20%;">Bulan</th>';
        echo '<th style="width: 20%;">Periode</th>';
        echo '<th style="width: 30%; text-align: right;">Daily Average (MMSCFD)</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';
        
        foreach ($data as $index => $item) {
            echo '<tr>';
            echo '<td>' . ($index + 1) . '</td>';
            echo '<td>' . htmlspecialchars($item->shipper) . '</td>';
            echo '<td>' . date('F Y', strtotime($item->bulan_date)) . '</td>';
            echo '<td>' . htmlspecialchars($item->periode) . '</td>';
            echo '<td style="text-align: right;">' . number_format($item->daily_average_mmscfd, 2) . '</td>';
            echo '</tr>';
        }
        
        echo '</tbody>';
        echo '</table>';
        
        echo '<div class="footer">';
        echo '<p>&copy; ' . date('Y') . ' Pertamina Gas - Developed for PKL Program</p>';
        echo '<p>This is a computer-generated document. No signature is required.</p>';
        echo '</div>';
        
        echo '<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>';
        
        echo '</body>';
        echo '</html>';
        
        exit;
    }
    
    // ========== COMPARISON PENERIMAAN VS PENYALURAN ==========

    public function comparison(Request $request)
    {
        $shippers = DB::table('volume_gas')
                    ->select('shipper')
                    ->distinct()
                    ->orderBy('shipper')
                    ->pluck('shipper');
        
        $tahuns = DB::table('volume_gas')
                ->selectRaw('DISTINCT YEAR(bulan_date) as tahun')
                ->orderBy('tahun', 'desc')
                ->pluck('tahun');
        
        return view('comparison', compact('shippers', 'tahuns'));
    }

    public function comparisonChartData(Request $request)
    {
        $penerimaanQuery = DB::table('volume_gas')
                        ->select(
                            'shipper',
                            DB::raw('DATE_FORMAT(bulan_date, "%Y-%m-01") as periode'),
                            DB::raw('YEAR(bulan_date) as tahun'),
                            DB::raw('MONTH(bulan_date) as bulan_num'),
                            DB::raw('SUM(daily_average_mmscfd) as total')
                        )
                        ->where('data', 'PENERIMAAN')
                        ->groupBy('shipper', 'periode', 'tahun', 'bulan_num');
        
        $penyaluranQuery = DB::table('volume_gas')
                        ->select(
                            'shipper',
                            DB::raw('DATE_FORMAT(bulan_date, "%Y-%m-01") as periode'),
                            DB::raw('YEAR(bulan_date) as tahun'),
                            DB::raw('MONTH(bulan_date) as bulan_num'),
                            DB::raw('SUM(daily_average_mmscfd) as total')
                        )
                        ->where('data', 'PENYALURAN')
                        ->groupBy('shipper', 'periode', 'tahun', 'bulan_num');
        
        if ($request->shipper) {
            $penerimaanQuery->where('shipper', $request->shipper);
            $penyaluranQuery->where('shipper', $request->shipper);
        }
        
        if ($request->tahun_dari) {
            $penerimaanQuery->whereYear('bulan_date', '>=', $request->tahun_dari);
            $penyaluranQuery->whereYear('bulan_date', '>=', $request->tahun_dari);
        }
        if ($request->tahun_sampai) {
            $penerimaanQuery->whereYear('bulan_date', '<=', $request->tahun_sampai);
            $penyaluranQuery->whereYear('bulan_date', '<=', $request->tahun_sampai);
        }
        
        $penerimaanRows = $penerimaanQuery->orderBy('tahun')->orderBy('bulan_num')->get();
        $penyaluranRows = $penyaluranQuery->orderBy('tahun')->orderBy('bulan_num')->get();

        $labels = [];
        $penerimaan = [];
        $penyaluran = [];

        foreach ($penerimaanRows as $row) {
            $bulanNama = date('M', mktime(0, 0, 0, (int)$row->bulan_num, 1));
            $label = $bulanNama . '-' . substr((string)$row->tahun, 2);

            if (!in_array($label, $labels)) {
                $labels[] = $label;
            }
            if (!isset($penerimaan[$label])) {
                $penerimaan[$label] = 0;
            }
            $penerimaan[$label] += (float) $row->total;
        }

        foreach ($penyaluranRows as $row) {
            $bulanNama = date('M', mktime(0, 0, 0, (int)$row->bulan_num, 1));
            $label = $bulanNama . '-' . substr((string)$row->tahun, 2);

            if (!in_array($label, $labels)) {
                $labels[] = $label;
            }
            if (!isset($penyaluran[$label])) {
                $penyaluran[$label] = 0;
            }
            $penyaluran[$label] += (float) $row->total;
        }

        foreach ($labels as $label) {
            if (!isset($penerimaan[$label])) $penerimaan[$label] = 0;
            if (!isset($penyaluran[$label])) $penyaluran[$label] = 0;
        }

        $totalPenerimaan = array_sum($penerimaan);
        $totalPenyaluran = array_sum($penyaluran);
        $totalGap = $totalPenerimaan - $totalPenyaluran;
        $efficiencyRatio = $totalPenerimaan > 0 ? ($totalPenyaluran / $totalPenerimaan * 100) : 0;

        $gap = [];
        $ratio = [];
        foreach ($labels as $label) {
            $terima = $penerimaan[$label] ?? 0;
            $salur = $penyaluran[$label] ?? 0;
            $gap[$label] = $terima - $salur;
            $ratio[$label] = $terima > 0 ? ($salur / $terima * 100) : 0;
        }

        return response()->json([
            'labels' => $labels,
            'penerimaan' => array_values($penerimaan),
            'penyaluran' => array_values($penyaluran),
            'gap' => array_values($gap),
            'ratio' => array_values($ratio),
            'totalPenerimaan' => round($totalPenerimaan, 2),
            'totalPenyaluran' => round($totalPenyaluran, 2),
            'totalGap' => round($totalGap, 2),
            'efficiencyRatio' => round($efficiencyRatio, 2)
        ]);
    }

    public function comparisonSummary(Request $request)
    {
        $penerimaanQuery = DB::table('volume_gas')
                            ->select(DB::raw('SUM(daily_average_mmscfd) as total'))
                            ->where('data', 'PENERIMAAN');
        
        $penyaluranQuery = DB::table('volume_gas')
                            ->select(DB::raw('SUM(daily_average_mmscfd) as total'))
                            ->where('data', 'PENYALURAN');
        
        if ($request->shipper) {
            $penerimaanQuery->where('shipper', $request->shipper);
            $penyaluranQuery->where('shipper', $request->shipper);
        }
        
        if ($request->tahun_dari) {
            $penerimaanQuery->whereYear('bulan_date', '>=', $request->tahun_dari);
            $penyaluranQuery->whereYear('bulan_date', '>=', $request->tahun_dari);
        }
        
        if ($request->tahun_sampai) {
            $penerimaanQuery->whereYear('bulan_date', '<=', $request->tahun_sampai);
            $penyaluranQuery->whereYear('bulan_date', '<=', $request->tahun_sampai);
        }
        
        $totalPenerimaan = $penerimaanQuery->value('total') ?? 0;
        $totalPenyaluran = $penyaluranQuery->value('total') ?? 0;
        $totalGap = $totalPenerimaan - $totalPenyaluran;
        $efficiencyRatio = $totalPenerimaan > 0 ? ($totalPenyaluran / $totalPenerimaan * 100) : 0;
        
        return response()->json([
            'totalPenerimaan' => round($totalPenerimaan, 2),
            'totalPenyaluran' => round($totalPenyaluran, 2),
            'totalGap' => round($totalGap, 2),
            'efficiencyRatio' => round($efficiencyRatio, 2)
        ]);
    }

    public function comparisonPerShipper(Request $request)
    {
        $penerimaanQuery = DB::table('volume_gas')
                        ->select(
                            'shipper',
                            DB::raw('SUM(daily_average_mmscfd) as total')
                        )
                        ->where('data', 'PENERIMAAN')
                        ->groupBy('shipper');
        
        $penyaluranQuery = DB::table('volume_gas')
                        ->select(
                            'shipper',
                            DB::raw('SUM(daily_average_mmscfd) as total')
                        )
                        ->where('data', 'PENYALURAN')
                        ->groupBy('shipper');
        
        if ($request->shipper) {
            $penerimaanQuery->where('shipper', $request->shipper);
            $penyaluranQuery->where('shipper', $request->shipper);
        }
        
        if ($request->tahun_dari) {
            $penerimaanQuery->whereYear('bulan_date', '>=', $request->tahun_dari);
            $penyaluranQuery->whereYear('bulan_date', '>=', $request->tahun_dari);
        }
        
        if ($request->tahun_sampai) {
            $penerimaanQuery->whereYear('bulan_date', '<=', $request->tahun_sampai);
            $penyaluranQuery->whereYear('bulan_date', '<=', $request->tahun_sampai);
        }
        
        $penerimaanRows = $penerimaanQuery->get();
        $penyaluranRows = $penyaluranQuery->get();
        
        $shippers = [];
        $penerimaan = [];
        $penyaluran = [];
        
        foreach ($penerimaanRows as $row) {
            if (!in_array($row->shipper, $shippers)) {
                $shippers[] = $row->shipper;
            }
            $penerimaan[$row->shipper] = (float) $row->total;
        }
        
        foreach ($penyaluranRows as $row) {
            if (!in_array($row->shipper, $shippers)) {
                $shippers[] = $row->shipper;
            }
            $penyaluran[$row->shipper] = (float) $row->total;
        }
        
        foreach ($shippers as $shipper) {
            if (!isset($penerimaan[$shipper])) $penerimaan[$shipper] = 0;
            if (!isset($penyaluran[$shipper])) $penyaluran[$shipper] = 0;
        }
        
        $gap = [];
        $ratio = [];
        foreach ($shippers as $shipper) {
            $terima = $penerimaan[$shipper];
            $salur = $penyaluran[$shipper];
            $gap[$shipper] = $terima - $salur;
            $ratio[$shipper] = $terima > 0 ? ($salur / $terima * 100) : 0;
        }
        
        return response()->json([
            'shippers' => $shippers,
            'penerimaan' => array_values($penerimaan),
            'penyaluran' => array_values($penyaluran),
            'gap' => array_values($gap),
            'ratio' => array_values($ratio)
        ]);
    }

    private function getFilteredData(Request $request)
    {
        $query = DB::table('volume_gas')
                    ->where('data', 'PENYALURAN');
        
        if ($request->shipper) {
            if (is_array($request->shipper)) {
                $query->whereIn('shipper', $request->shipper);
            } elseif (strpos($request->shipper, ',') !== false) {
                $shippers = explode(',', $request->shipper);
                $shippers = array_filter($shippers);
                $query->whereIn('shipper', $shippers);
            } else {
                $query->where('shipper', 'LIKE', '%' . $request->shipper . '%');
            }
        }
        
        if ($request->tahun_dari) {
            $query->whereYear('bulan_date', '>=', $request->tahun_dari);
        }
        if ($request->tahun_sampai) {
            $query->whereYear('bulan_date', '<=', $request->tahun_sampai);
        }
        
        if ($request->bulan) {
            if (is_array($request->bulan)) {
                $query->whereIn(DB::raw('MONTH(bulan_date)'), $request->bulan);
            } else {
                $query->whereMonth('bulan_date', $request->bulan);
            }
        }
        
        return $query->selectRaw('
            shipper, 
            bulan_date,
            daily_average_mmscfd,
            DATE_FORMAT(bulan_date, "%Y-%m") as periode
        ')
        ->orderBy('bulan_date', 'desc')
        ->get();
    }
}