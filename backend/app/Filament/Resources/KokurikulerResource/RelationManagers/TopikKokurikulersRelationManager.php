<?php

namespace App\Filament\Resources\KokurikulerResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Database\Eloquent\Model;

class TopikKokurikulersRelationManager extends RelationManager
{
    protected static string $relationship = 'topikKokurikulers';

    protected static ?string $title = 'Daftar Topik Materi';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nama_topik')
                    ->label('Nama Topik / Materi')
                    ->required()
                    ->maxLength(255),
                Forms\Components\DatePicker::make('tanggal')
                    ->label('Tanggal Pelaksanaan')
                    ->required(),
                Forms\Components\Textarea::make('deskripsi')
                    ->label('Ringkasan Materi')
                    ->rows(3)
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('nama_topik')
            ->columns([
                Tables\Columns\TextColumn::make('nama_topik')
                    ->label('Topik')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tanggal')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('deskripsi')
                    ->label('Ringkasan')
                    ->limit(50),
            ])
            ->defaultSort('tanggal', 'desc')
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Tambah Topik'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\Action::make('absensi')
                    ->label('Absensi')
                    ->icon('heroicon-o-clipboard-document-check')
                    ->color('warning')
                    ->modalWidth('4xl')
                    ->fillForm(function (Model $record) {
                        $kokuId = $record->kokurikuler_id;
                        $members = \App\Models\AnggotaKokurikuler::where('kokurikuler_id', $kokuId)->with('siswa')->get();
                        
                        $existing = \App\Models\AbsensiKokurikuler::where('topik_kokurikuler_id', $record->id)
                            ->get()
                            ->keyBy('siswa_id');

                        return [
                            'presensi' => $members->map(function ($member) use ($existing) {
                                $att = $existing->get($member->siswa_id);
                                return [
                                    'siswa_id' => $member->siswa_id,
                                    'nama_siswa' => $member->siswa->nama_lengkap,
                                    'status' => $att ? $att->status : 'H',
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
                                    ->dehydrated(false)
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
                            \App\Models\AbsensiKokurikuler::updateOrCreate(
                                [
                                    'topik_kokurikuler_id' => $record->id,
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
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
