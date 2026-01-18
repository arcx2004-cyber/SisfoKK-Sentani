<?php

namespace App\Filament\Resources\EkstrakurikulerResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Database\Eloquent\Model;

class KegiatanEkskulsRelationManager extends RelationManager
{
    protected static string $relationship = 'kegiatanEkskuls';

    protected static ?string $title = 'Rencana Kegiatan';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nama_kegiatan')
                    ->label('Nama Kegiatan')
                    ->required()
                    ->maxLength(255),
                Forms\Components\DatePicker::make('tanggal')
                    ->label('Tanggal Pelaksanaan')
                    ->required(),
                Forms\Components\Textarea::make('deskripsi')
                    ->label('Deskripsi Kegiatan')
                    ->rows(3)
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('nama_kegiatan')
            ->columns([
                Tables\Columns\TextColumn::make('nama_kegiatan')
                    ->label('Kegiatan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tanggal')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('deskripsi')
                    ->label('Deskripsi')
                    ->limit(50),
            ])
            ->defaultSort('tanggal', 'desc')
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Tambah Kegiatan')
                    ->visible(fn () => !auth()->user()->hasRole('wali_kelas')),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(fn () => !auth()->user()->hasRole('wali_kelas')),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn () => !auth()->user()->hasRole('wali_kelas')),
                Tables\Actions\Action::make('absensi')
                    ->label('Absensi')
                    ->icon('heroicon-o-clipboard-document-check')
                    ->color('warning')
                    ->visible(fn () => !auth()->user()->hasRole('wali_kelas'))
                    ->modalWidth('4xl')
                    ->fillForm(function (Model $record) {
                        // 1. Get all members (Anggota) of this Ekskul
                        $ekskulId = $record->ekstrakurikuler_id;
                        $members = \App\Models\RombelEkskul::where('ekstrakurikuler_id', $ekskulId)->with('siswa')->get();
                        
                        // 2. Get existing attendance for this Activity
                        $existing = \App\Models\AbsensiEkskul::where('kegiatan_ekskul_id', $record->id)
                            ->get()
                            ->keyBy('siswa_id');

                        // 3. Map to Repeater format
                        return [
                            'presensi' => $members->map(function ($member) use ($existing) {
                                $att = $existing->get($member->siswa_id);
                                return [
                                    'siswa_id' => $member->siswa_id,
                                    'nama_siswa' => $member->siswa->nama_lengkap,
                                    'status' => $att ? $att->status : 'H', // Default Hadir
                                    'keterangan' => $att ? $att->keterangan : null,
                                ];
                            })->toArray(),
                        ];
                    })
                    ->form([
                        Forms\Components\Repeater::make('presensi')
                            ->schema([
                                Forms\Components\Hidden::make('siswa_id'),
                                Forms\Components\TextInput::make('nama_siswa')
                                    ->label('Nama Siswa')
                                    ->disabled()
                                    ->dehydrated(false) // Don't send relative to repeater, we only need ID
                                    ->columnSpan(2),
                                Forms\Components\Radio::make('status')
                                    ->options([
                                        'H' => 'Hadir',
                                        'S' => 'Sakit',
                                        'I' => 'Izin',
                                        'A' => 'Alpha',
                                    ])
                                    ->inline()
                                    ->required()
                                    ->columnSpan(2),
                                Forms\Components\TextInput::make('keterangan')
                                    ->label('Ket.')
                                    ->columnSpan(2),
                            ])
                            ->columns(6)
                            ->addable(false)
                            ->deletable(false)
                            ->reorderable(false)
                    ])
                    ->action(function (Model $record, array $data) {
                        foreach ($data['presensi'] as $item) {
                            \App\Models\AbsensiEkskul::updateOrCreate(
                                [
                                    'kegiatan_ekskul_id' => $record->id,
                                    'siswa_id' => $item['siswa_id'],
                                ],
                                [
                                    'status' => $item['status'],
                                    'keterangan' => $item['keterangan'],
                                ]
                            );
                        }
                        \Filament\Notifications\Notification::make()
                            ->title('Absensi berhasil disimpan')
                            ->success()
                            ->send();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn () => !auth()->user()->hasRole('wali_kelas')),
                ]),
            ]);
    }
}
