<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class VolumeGasSeeder extends Seeder
{
    public function run()
    {
        $csvFile = base_path("volume_gas.csv");
        
        if (!File::exists($csvFile)) {
            $this->command->error("❌ File CSV tidak ditemukan: $csvFile");
            return;
        }
        
        $this->command->info("📂 Membaca file: $csvFile");
        
        // UBAH delimiter jadi semicolon (;)
        $file = fopen($csvFile, "r");
        
        if (!$file) {
            $this->command->error("❌ Gagal membuka file CSV!");
            return;
        }
        
        $firstline = true;
        $count = 0;
        $errors = 0;
        
        while (($row = fgetcsv($file, 2000, ";")) !== FALSE) {  // ✅ GANTI "," jadi ";"
            
            // Skip header row
            if ($firstline) {
                $firstline = false;
                $this->command->info("📋 Header: " . implode(", ", $row));
                continue;
            }
            
            // Skip empty rows
            if (empty($row[0]) || trim($row[0]) == '') {
                continue;
            }
            
            try {
                // CSV struktur: SHIPPER | TAHUN | BULAN | PERIODE | DAILY AVERAGE (MMSCFD)
                // Index:          0        1       2        3              4
                
                $shipper = trim($row[0]);
                $tahun = (int)$row[1];
                $bulan = (int)$row[2];
                $periode = trim($row[3]);
                $dailyAverage = (float)str_replace(',', '.', $row[4]); // handle decimal comma
                
                // Generate bulan_date dari tahun dan bulan
                $bulanDate = date('Y-m-d', strtotime("$tahun-$bulan-01"));
                
                // TAMBAH data default "PENYALURAN" (karena CSV tidak ada kolom ini)
                // Nanti kamu bisa bikin CSV terpisah untuk PENERIMAAN
                
                DB::table('volume_gas')->insert([
                    'data' => 'PENYALURAN',  // ✅ Default PENYALURAN
                    'shipper' => $shipper,
                    'tahun' => $tahun,
                    'bulan' => $bulan,
                    'bulan_date' => $bulanDate,
                    'periode' => $periode,
                    'daily_average_mmscfd' => $dailyAverage,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                
                $count++;
                
                // Progress indicator
                if ($count % 50 == 0) {
                    $this->command->info("⏳ Imported $count rows...");
                }
                
            } catch (\Exception $e) {
                $errors++;
                $this->command->error("❌ Error row " . ($count + $errors) . ": " . $e->getMessage());
                
                // Debug: tampilkan data row yang error
                $this->command->warn("Row data: " . implode(" | ", $row));
                
                // Stop kalau error lebih dari 10
                if ($errors > 10) {
                    $this->command->error("❌ Terlalu banyak error, stop import!");
                    break;
                }
            }
        }
        
        fclose($file);
        
        $this->command->info("✅ Selesai!");
        $this->command->info("📊 Total berhasil import: $count rows");
        
        if ($errors > 0) {
            $this->command->warn("⚠️  Total error: $errors rows");
        }
    }
}