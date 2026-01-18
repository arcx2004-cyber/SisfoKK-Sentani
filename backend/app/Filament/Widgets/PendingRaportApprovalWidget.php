<?php

namespace App\Filament\Widgets;

use App\Models\Raport;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class PendingRaportApprovalWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = 'full';

    protected static ?string $heading = 'Raport Menunggu Persetujuan';

    public static function canView(): bool
    {
        return Auth::user()->hasAnyRole(['admin', 'kepsek']);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Raport::query()
                    ->where('status', 'pending_approval')
                    ->with(['siswa', 'rombel', 'semester.tahunAjaran'])
            )
            ->columns([
                Tables\Columns\TextColumn::make('siswa.nama_lengkap')
                    ->label('Nama Siswa')
                    ->searchable(),
                Tables\Columns\TextColumn::make('rombel.nama')
                    ->label('Kelas'),
                Tables\Columns\TextColumn::make('semester.tahunAjaran.nama')
                    ->label('Tahun Ajaran'),
                Tables\Columns\TextColumn::make('semester.tipe')
                    ->label('Semester')
                    ->formatStateUsing(fn($state) => ucfirst($state)),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Diajukan')
                    ->since(),
            ])
            ->actions([
                Tables\Actions\Action::make('approve')
                    ->label('Setujui')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function (Raport $record) {
                        $record->update([
                            'status' => 'approved',
                            'approved_by' => Auth::id(),
                            'approved_at' => now(),
                        ]);
                    }),
                Tables\Actions\Action::make('reject')
                    ->label('Tolak')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->form([
                        \Filament\Forms\Components\Textarea::make('catatan_kepala_sekolah')
                            ->label('Catatan/Alasan Penolakan')
                            ->required(),
                    ])
                    ->action(function (Raport $record, array $data) {
                        $record->update([
                            'status' => 'draft',
                            'catatan_kepala_sekolah' => $data['catatan_kepala_sekolah'],
                        ]);
                    }),
            ])
            ->emptyStateHeading('Tidak ada raport yang menunggu persetujuan')
            ->emptyStateIcon('heroicon-o-check-badge');
    }
}
