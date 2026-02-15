<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportVolumeGas extends Command
{
    protected $signature = 'import:volumegas';
    protected $description = 'Import data volume gas';

    public function handle()
    {
        $filePath = storage_path('app/volume_gas.csv');
        
        if (!file_exists($filePath)) {
            $this->error('File tidak ditemukan!');
            return;
        }

        $file = fopen($filePath, 'r');
        
        // Skip baris pertama (header)
        $header = fgetcsv($file);
        $this->info('Header: ' . implode(', ', $header));
        
        $count = 0;
        while (($row = fgetcsv($file)) !== false) {
            // Debug: tampilkan isi row
            $this->info('Row: ' . print_r($row, true));
            
            // Skip baris kosong atau yang gak lengkap
            if (!isset($row[0]) || !isset($row[1]) || !isset($row[4])) {
                $this->warn('Skip row: data tidak lengkap');
                continue;
            }
            
            DB::table('volume_gas')->insert([
                'data' => 'PENYALURAN',
                'tahun' => $row[1],
                'bulan' => $row[2] ?? null,
                'periode' => $row[3] ?? null,
                'nilai' => $row[4] ?? null,
            ]);
            
            $count++;
            
            // Stop setelah 3 baris untuk testing
            if ($count >= 3) {
                $this->warn('Stopping after 3 rows for testing...');
                break;
            }
        }

        fclose($file);
        
        $this->info("Berhasil import {$count} data!");
    }
}