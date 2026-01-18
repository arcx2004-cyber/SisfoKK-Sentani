<?php

namespace App\Filament\Resources\PembayaranKegiatanResource\Pages;

use App\Filament\Resources\PembayaranKegiatanResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPembayaranKegiatans extends ListRecords
{
    protected static string $resource = PembayaranKegiatanResource::class;

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
                        ->required()
                        ->live(),
                    \Filament\Forms\Components\Select::make('tarif_kegiatan_id')
                        ->label('Pilih Kegiatan')
                        ->options(fn (\Filament\Forms\Get $get) => 
                            \App\Models\TarifKegiatan::where('tahun_ajaran_id', $get('tahun_ajaran_id'))
                                ->get()
                                ->mapWithKeys(fn ($item) => [$item->id => "$item->nama_kegiatan (Unit: {$item->unit->nama})"])
                        )
                        ->required()
                        ->searchable(),
                ])
                ->action(function (array $data) {
                    $tarifId = $data['tarif_kegiatan_id'];
                    $tarif = \App\Models\TarifKegiatan::find($tarifId);

                    if (!$tarif) {
                         \Filament\Notifications\Notification::make()
                            ->title('Kegiatan tidak ditemukan.')
                            ->danger()
                            ->send();
                        return;
                    }

                    // Get Students in that Unit
                    $unitId = $tarif->unit_id;
                    $students = \App\Models\Siswa::where('status', 'aktif')
                        ->where('unit_id', $unitId)
                        ->get();

                    if ($students->isEmpty()) {
                        \Filament\Notifications\Notification::make()
                            ->title('Tidak ada siswa aktif di unit kegiatan ini.')
                            ->warning()
                            ->send();
                        return;
                    }

                    $count = 0;
                    foreach ($students as $siswa) {
                        // Check if invoice exists for this specific Activity & Student
                        $exists = \App\Models\PembayaranKegiatan::where('siswa_id', $siswa->id)
                            ->where('tarif_kegiatan_id', $tarif->id)
                            ->exists();

                        if (!$exists) {
                            \App\Models\PembayaranKegiatan::create([
                                'siswa_id' => $siswa->id,
                                'tarif_kegiatan_id' => $tarif->id,
                                'nominal' => $tarif->nominal,
                                'nominal_bayar' => 0,
                                'tanggal_bayar' => null,
                                'status' => 'belum_lunas',
                                'metode_pembayaran' => null,
                                'created_by' => auth()->id(),
                            ]);
                            $count++;
                        }
                    }

                    \Filament\Notifications\Notification::make()
                        ->title("Berhasil generate $count tagihan untuk {$tarif->nama_kegiatan}.")
                        ->success()
                        ->send();
                }),
            Actions\CreateAction::make(),
        ];
    }
}
