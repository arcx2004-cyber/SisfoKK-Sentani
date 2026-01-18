<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PendaftaranResource\Pages;
use App\Filament\Resources\PendaftaranResource\RelationManagers;
use App\Models\Pendaftaran;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PendaftaranResource extends BaseResource
{
    protected static ?string $model = Pendaftaran::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document';
    protected static ?string $navigationGroup = 'PPDB';
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationLabel = 'Pendaftaran';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Wizard::make([
                    Forms\Components\Wizard\Step::make('Data Calon Siswa')
                        ->schema([
                            Forms\Components\Select::make('ppdb_setting_id')
                                ->relationship('ppdbSetting', 'id') // TODO: Check if we can show a better label logic later
                                ->label('Gelombang PPDB') 
                                ->required(),
                            Forms\Components\TextInput::make('nomor_pendaftaran')
                                ->disabled() 
                                ->placeholder('Otomatis')
                                ->label('No. Pendaftaran (Auto)'),
                            Forms\Components\TextInput::make('nama_lengkap')->required(),
                            Forms\Components\Select::make('jenis_kelamin')
                                ->options(['L' => 'Laki-laki', 'P' => 'Perempuan'])
                                ->required(),
                            Forms\Components\TextInput::make('tempat_lahir')->required(),
                            Forms\Components\DatePicker::make('tanggal_lahir')->required(),
                            Forms\Components\Select::make('agama')
                                ->options(['Islam' => 'Islam', 'Kristen' => 'Kristen', 'Katolik' => 'Katolik', 'Hindu' => 'Hindu', 'Buddha' => 'Buddha', 'Khonghucu' => 'Khonghucu']),
                             Forms\Components\Textarea::make('alamat')->required()->columnSpanFull(),
                        ])->columns(2),
                        
                    Forms\Components\Wizard\Step::make('Data Orang Tua / Wali')
                        ->schema([
                            Forms\Components\TextInput::make('nama_ayah')->required(),
                            Forms\Components\TextInput::make('pekerjaan_ayah'),
                            Forms\Components\TextInput::make('nama_ibu')->required(),
                            Forms\Components\TextInput::make('pekerjaan_ibu'),
                            Forms\Components\TextInput::make('no_telepon_ortu')->tel()->required()->label('No. HP Ortu'),
                            Forms\Components\TextInput::make('no_wa')->tel()->required()->label('No. WhatsApp'),
                            Forms\Components\TextInput::make('email')->email()->required()->label('Email Ortu'),
                        ])->columns(2),
                        
                    Forms\Components\Wizard\Step::make('Asal Sekolah & Status')
                        ->schema([
                            Forms\Components\TextInput::make('asal_sekolah')->label('Nama Sekolah Asal'),
                            Forms\Components\Hidden::make('status')->default('pending'),
                            
                            Forms\Components\Section::make('Verifikasi Panitia')
                                ->schema([
                                     Forms\Components\Select::make('status')
                                        ->options([
                                            'pending' => 'Pending',
                                            'verifikasi' => 'Sedang Diverifikasi',
                                            'diterima' => 'Diterima',
                                            'ditolak' => 'Ditolak',
                                        ])
                                        ->default('pending')
                                        ->required(),
                                     Forms\Components\Textarea::make('catatan_admin')
                                        ->label('Catatan Panitia'),
                                ])
                                ->visible(fn () => auth()->user() && (auth()->user()->hasRole('super_admin') || auth()->user()->hasRole('panitia_ppdb'))),
                        ]),
                        
                    Forms\Components\Wizard\Step::make('Berkas Dokumen')
                        ->schema([
                             Forms\Components\Repeater::make('dokumenPendaftarans')
                                ->relationship()
                                ->schema([
                                    Forms\Components\Select::make('jenis_dokumen')
                                        ->options([
                                            'akta_lahir' => 'Akta Kelahiran', 
                                            'kartu_keluarga' => 'Kartu Keluarga', 
                                            'ijazah' => 'Ijazah/SKL', 
                                            'foto' => 'Pas Foto', 
                                            'lainnya' => 'Lainnya'
                                        ])
                                        ->required(),
                                    Forms\Components\FileUpload::make('path')
                                        ->label('Upload File')
                                        ->disk('public')
                                        ->directory('dokumen-ppdb')
                                        ->required(),
                                ])
                                ->addActionLabel('Tambah Dokumen')
                        ]),
                ])->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('ppdbSetting.id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('nomor_pendaftaran')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nama_lengkap')
                    ->searchable(),
                Tables\Columns\TextColumn::make('jenis_kelamin'),
                Tables\Columns\TextColumn::make('tempat_lahir')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tanggal_lahir')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('agama')
                    ->searchable(),
                Tables\Columns\TextColumn::make('asal_sekolah')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('no_wa')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nama_ayah')
                    ->searchable(),
                Tables\Columns\TextColumn::make('pekerjaan_ayah')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nama_ibu')
                    ->searchable(),
                Tables\Columns\TextColumn::make('pekerjaan_ibu')
                    ->searchable(),
                Tables\Columns\TextColumn::make('no_telepon_ortu')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('accept')
                    ->label('Terima Siswa')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function (Pendaftaran $record) {
                        try {
                            \Illuminate\Support\Facades\DB::transaction(function () use ($record) {
                                // 1. Create User
                                $password = 'Siswa123'; // Default password
                                $user = \App\Models\User::create([
                                    'name' => $record->nama_lengkap,
                                    'email' => $record->email,
                                    'password' => \Illuminate\Support\Facades\Hash::make($password),
                                    'is_active' => true,
                                ]);
                                $user->assignRole('siswa');

                                // 2. Get Unit ID from PpdbSetting
                                $unitId = $record->ppdbSetting->unit_id ?? 1; // Fallback or strict?

                                // 3. Create Siswa
                                \App\Models\Siswa::create([
                                    'user_id' => $user->id,
                                    'unit_id' => $unitId,
                                    'nama_lengkap' => $record->nama_lengkap,
                                    'jenis_kelamin' => $record->jenis_kelamin,
                                    'tempat_lahir' => $record->tempat_lahir,
                                    'tanggal_lahir' => $record->tanggal_lahir,
                                    'agama' => $record->agama,
                                    'alamat' => $record->alamat,
                                    'asal_sekolah' => $record->asal_sekolah,
                                    'email_ortu' => $record->email, // Using student email as contact for now
                                    'no_telepon' => $record->no_wa,
                                    'nama_ayah' => $record->nama_ayah,
                                    'pekerjaan_ayah' => $record->pekerjaan_ayah,
                                    'nama_ibu' => $record->nama_ibu,
                                    'pekerjaan_ibu' => $record->pekerjaan_ibu,
                                    'no_telepon_ortu' => $record->no_telepon_ortu,
                                    'status' => 'aktif',
                                    'tanggal_masuk' => now(),
                                ]);

                                // 4. Update Pendaftaran Status
                                $record->update(['status' => 'diterima']);
                                
                                \Filament\Notifications\Notification::make()
                                    ->title('Siswa berhasil diterima')
                                    ->body("Akun siswa telah dibuat. Password default: $password")
                                    ->success()
                                    ->send();
                            });
                        } catch (\Exception $e) {
                            \Filament\Notifications\Notification::make()
                                ->title('Gagal menerima siswa')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    })
                    ->visible(fn (Pendaftaran $record) => $record->status !== 'diterima'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPendaftarans::route('/'),
            'create' => Pages\CreatePendaftaran::route('/create'),
            'edit' => Pages\EditPendaftaran::route('/{record}/edit'),
        ];
    }
}
