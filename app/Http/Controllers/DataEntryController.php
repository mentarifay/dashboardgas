<?php

namespace App\Http\Controllers;

use App\Models\VolumeGas;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DataEntryController extends Controller
{
    /**
     * Show manual input form
     */
    public function create()
    {
        // Get distinct shippers for dropdown
        $shippers = DB::table('volume_gas')
            ->select('shipper')
            ->distinct()
            ->orderBy('shipper')
            ->pluck('shipper');

        return view('user.input-data', compact('shippers'));
    }

    /**
     * Store manually inputted data
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'data' => 'required|in:PENYALURAN,PENERIMAAN',
            'shipper' => 'required|string|max:255',
            'tahun' => 'required|integer|min:2020|max:2030',
            'bulan' => 'required|integer|min:1|max:12',
            'daily_average_mmscfd' => 'required|numeric|min:0',
        ], [
            'data.required' => 'Tipe data wajib dipilih',
            'shipper.required' => 'Shipper wajib diisi',
            'tahun.required' => 'Tahun wajib diisi',
            'tahun.min' => 'Tahun minimal 2020',
            'tahun.max' => 'Tahun maksimal 2030',
            'bulan.required' => 'Bulan wajib dipilih',
            'bulan.min' => 'Bulan tidak valid',
            'bulan.max' => 'Bulan tidak valid',
            'daily_average_mmscfd.required' => 'Volume wajib diisi',
            'daily_average_mmscfd.numeric' => 'Volume harus berupa angka',
            'daily_average_mmscfd.min' => 'Volume tidak boleh negatif',
        ]);

        // Create periode (e.g., "Jan-20")
        $monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        $periode = $monthNames[$validated['bulan'] - 1] . '-' . substr($validated['tahun'], 2);

        // Create bulan_date
        $bulanDate = sprintf('%04d-%02d-01', $validated['tahun'], $validated['bulan']);

        // Check if entry already exists
        $exists = VolumeGas::where('data', $validated['data'])
            ->where('shipper', $validated['shipper'])
            ->where('tahun', $validated['tahun'])
            ->where('bulan', $validated['bulan'])
            ->exists();

        if ($exists) {
            return back()->with('error', 'Data untuk shipper, tahun, dan bulan ini sudah ada!')->withInput();
        }

        // Create new entry
        $volumeGas = VolumeGas::create([
            'data' => $validated['data'],
            'shipper' => strtoupper($validated['shipper']),
            'tahun' => $validated['tahun'],
            'bulan' => $validated['bulan'],
            'periode' => $periode,
            'bulan_date' => $bulanDate,
            'daily_average_mmscfd' => $validated['daily_average_mmscfd'],
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);

        // Log activity
        AuditLog::log('create', 'volume_gas', $volumeGas->id, null, $volumeGas->toArray());

        return redirect()->route('data.create')->with('success', 'Data berhasil ditambahkan!');
    }

    /**
     * Show upload CSV form
     */
    public function uploadForm()
    {
        return view('user.upload-data');
    }

    /**
     * Handle CSV upload
     */
    public function uploadCsv(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:2048',
            'data_type' => 'required|in:PENYALURAN,PENERIMAAN',
        ], [
            'csv_file.required' => 'File CSV wajib dipilih',
            'csv_file.mimes' => 'File harus berformat CSV',
            'csv_file.max' => 'Ukuran file maksimal 2MB',
            'data_type.required' => 'Tipe data wajib dipilih',
        ]);

        $file = $request->file('csv_file');
        $dataType = $request->data_type;

        try {
            $csvData = array_map('str_getcsv', file($file->getRealPath()));
            $header = array_shift($csvData); // Remove header row

            // Validate header
            $requiredColumns = ['shipper', 'tahun', 'bulan', 'daily_average_mmscfd'];
            $headerLower = array_map('strtolower', array_map('trim', $header));
            
            foreach ($requiredColumns as $col) {
                if (!in_array($col, $headerLower)) {
                    return back()->with('error', "Kolom '$col' tidak ditemukan di CSV. Header yang diperlukan: shipper, tahun, bulan, daily_average_mmscfd");
                }
            }

            // Map header to indices
            $indices = [];
            foreach ($requiredColumns as $col) {
                $indices[$col] = array_search($col, $headerLower);
            }

            $imported = 0;
            $skipped = 0;
            $errors = [];
            $monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

            DB::beginTransaction();

            foreach ($csvData as $rowIndex => $row) {
                $rowNumber = $rowIndex + 2; // +2 because we removed header and arrays are 0-indexed

                // Skip empty rows
                if (empty(array_filter($row))) {
                    continue;
                }

                try {
                    $shipper = trim($row[$indices['shipper']] ?? '');
                    $tahun = (int)($row[$indices['tahun']] ?? 0);
                    $bulan = (int)($row[$indices['bulan']] ?? 0);
                    $volume = (float)($row[$indices['daily_average_mmscfd']] ?? 0);

                    // Validate data
                    if (empty($shipper)) {
                        $errors[] = "Baris $rowNumber: Shipper kosong";
                        $skipped++;
                        continue;
                    }

                    if ($tahun < 2020 || $tahun > 2030) {
                        $errors[] = "Baris $rowNumber: Tahun tidak valid ($tahun)";
                        $skipped++;
                        continue;
                    }

                    if ($bulan < 1 || $bulan > 12) {
                        $errors[] = "Baris $rowNumber: Bulan tidak valid ($bulan)";
                        $skipped++;
                        continue;
                    }

                    if ($volume < 0) {
                        $errors[] = "Baris $rowNumber: Volume tidak boleh negatif ($volume)";
                        $skipped++;
                        continue;
                    }

                    // Create periode and bulan_date
                    $periode = $monthNames[$bulan - 1] . '-' . substr($tahun, 2);
                    $bulanDate = sprintf('%04d-%02d-01', $tahun, $bulan);

                    // Check if exists
                    $exists = VolumeGas::where('data', $dataType)
                        ->where('shipper', strtoupper($shipper))
                        ->where('tahun', $tahun)
                        ->where('bulan', $bulan)
                        ->exists();

                    if ($exists) {
                        $errors[] = "Baris $rowNumber: Data sudah ada untuk $shipper, tahun $tahun, bulan $bulan";
                        $skipped++;
                        continue;
                    }

                    // Insert data
                    VolumeGas::create([
                        'data' => $dataType,
                        'shipper' => strtoupper($shipper),
                        'tahun' => $tahun,
                        'bulan' => $bulan,
                        'periode' => $periode,
                        'bulan_date' => $bulanDate,
                        'daily_average_mmscfd' => $volume,
                        'created_by' => auth()->id(),
                        'updated_by' => auth()->id(),
                    ]);

                    $imported++;

                } catch (\Exception $e) {
                    $errors[] = "Baris $rowNumber: " . $e->getMessage();
                    $skipped++;
                }
            }

            DB::commit();

            // Log activity
            AuditLog::log('upload_csv', 'volume_gas', null, null, [
                'file_name' => $file->getClientOriginalName(),
                'data_type' => $dataType,
                'imported' => $imported,
                'skipped' => $skipped,
            ]);

            $message = "Import selesai! $imported data berhasil diimport, $skipped data dilewati.";
            
            if (count($errors) > 0) {
                $message .= " Error: " . implode('; ', array_slice($errors, 0, 5));
                if (count($errors) > 5) {
                    $message .= " (dan " . (count($errors) - 5) . " error lainnya)";
                }
            }

            return back()->with($imported > 0 ? 'success' : 'warning', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error saat memproses CSV: ' . $e->getMessage());
        }
    }

    /**
     * Show user's submissions
     */
    public function mySubmissions(Request $request)
    {
        $query = VolumeGas::where('created_by', auth()->id());

        // Filter by data type
        if ($request->data) {
            $query->where('data', $request->data);
        }

        // Filter by shipper
        if ($request->shipper) {
            $query->where('shipper', 'LIKE', '%' . $request->shipper . '%');
        }

        // Filter by year
        if ($request->tahun) {
            $query->where('tahun', $request->tahun);
        }

        $submissions = $query->orderBy('created_at', 'desc')->paginate(20);

        // Get filter options
        $years = VolumeGas::where('created_by', auth()->id())
            ->select('tahun')
            ->distinct()
            ->orderBy('tahun', 'desc')
            ->pluck('tahun');

        $shippers = VolumeGas::where('created_by', auth()->id())
            ->select('shipper')
            ->distinct()
            ->orderBy('shipper')
            ->pluck('shipper');

        return view('user.my-submissions', compact('submissions', 'years', 'shippers'));
    }

    /**
     * Delete data (admin only or own data for users)
     */
    public function destroy($id)
    {
        $volumeGas = VolumeGas::findOrFail($id);

        // Check permission
        if (!auth()->user()->isAdmin() && $volumeGas->created_by !== auth()->id()) {
            abort(403, 'Anda tidak memiliki akses untuk menghapus data ini.');
        }

        $oldData = $volumeGas->toArray();

        // Log activity
        AuditLog::log('delete', 'volume_gas', $volumeGas->id, $oldData, null);

        $volumeGas->delete();

        return back()->with('success', 'Data berhasil dihapus!');
    }
}