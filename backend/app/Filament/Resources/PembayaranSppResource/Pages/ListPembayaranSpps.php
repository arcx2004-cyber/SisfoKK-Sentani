<?php

namespace App\Filament\Resources\PembayaranSppResource\Pages;

use App\Filament\Resources\PembayaranSppResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPembayaranSpps extends ListRecords
{
    protected static string $resource = PembayaranSppResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('generate_tagihan')
                ->label('Generate Tagihan')
                ->icon('heroicon-o-document-plus')
                ->form([
                    \Filament\Forms\Components\Select::make('tahun_ajaran_id')
                        ->label('Tahun Ajaran')
                        ->options(\App\Models\TahunAjaran::pluck('nama', 'id'))
                        ->default(fn() => \App\Models\TahunAjaran::where('is_active', true)->value('id'))
                        ->required(),
                    \Filament\Forms\Components\Select::make('unit_id')
                        ->label('Unit (Opsional)')
                        ->helperText('Jika kosong, akan generate untuk SEMUA siswa aktif di semua unit.')
                        ->options(\App\Models\Unit::pluck('nama', 'id'))
                        ->placeholder('Semua Unit'),
                ])
                ->action(function (array $data) {
                    $tahunAjaranId = $data['tahun_ajaran_id'];
                    $unitId = $data['unit_id'] ?? null;
                    
                    // Get Students
                    $studentsQuery = \App\Models\Siswa::where('status', 'aktif'); // Assuming 'status' column exists or similar logic
                    if ($unitId) {
                        $studentsQuery->where('unit_id', $unitId);
                    }
                    $students = $studentsQuery->get();

                    if ($students->isEmpty()) {
                        \Filament\Notifications\Notification::make()
                            ->title('Tidak ada siswa aktif ditemukan.')
                            ->warning()
                            ->send();
                        return;
                    }

                    $count = 0;
                    $months = [
                        7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 
                        11 => 'November', 12 => 'Desember', 1 => 'Januari', 2 => 'Februari', 
                        3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni'
                    ];

                    foreach ($students as $siswa) {
                        // Find Tariff for this student's unit & selected year
                        $tarif = \App\Models\TarifSpp::where('unit_id', $siswa->unit_id)
                            ->where('tahun_ajaran_id', $tahunAjaranId)
                            ->first();
                        
                        // If no tariff, skip (or maybe log warning? For now skipping to avoid errors)
                        if (!$tarif) continue;

                        foreach ($months as $monthNum => $monthName) {
                            // Correct year for the month (Jul-Dec = year start, Jan-Jun = year start + 1)
                            // Actually, SPP table usually stores 'tahun' as string or integer of the YEAR (e.g. 2025).
                            // But usually we track by "School Year" context.
                            // The `tahun` column in `pembayaran_spps` table is just an integer.
                             // Logic: If TA is 2025/2026. 
                            // Jul 2025 .. Dec 2025. Jan 2026 .. Jun 2026.
                            
                            // Let's get the starting year from the TA name usually "2025/2026"
                            $ta = \App\Models\TahunAjaran::find($tahunAjaranId);
                            $startYear = (int) substr($ta->nama, 0, 4);
                            
                            $currentYear = ($monthNum >= 7) ? $startYear : $startYear + 1;

                            // Check if exists
                            $exists = \App\Models\PembayaranSpp::where('siswa_id', $siswa->id)
                                ->where('bulan', $monthNum)
                                ->where('tahun', $currentYear) // Strictly checking date-based year
                                ->exists();
                            
                            if (!$exists) {
                                \App\Models\PembayaranSpp::create([
                                    'siswa_id' => $siswa->id,
                                    'tarif_spp_id' => $tarif->id,
                                    'bulan' => $monthNum,
                                    'tahun' => $currentYear,
                                    'nominal' => $tarif->nominal,
                                    'nominal_bayar' => 0, // Belum bayar
                                    'tanggal_bayar' => null, // Belum bayar
                                    'status' => 'belum_lunas',
                                    'metode_pembayaran' => null, // Not paid yet
                                    'created_by' => auth()->id(),
                                ]);
                                $count++;
                            }
                        }
                    }

                    \Filament\Notifications\Notification::make()
                        ->title("Berhasil generate $count tagihan SPP.")
                        ->success()
                        ->send();
                }),
            Actions\CreateAction::make(),
        ];
    }
}
