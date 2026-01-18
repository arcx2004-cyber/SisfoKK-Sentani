<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SiswaResource\Pages;
use App\Filament\Resources\SiswaResource\RelationManagers;
use App\Models\Siswa;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Traits\HasUnitFiltering;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SiswaResource extends BaseResource
{
    use HasUnitFiltering;

    protected static ?string $model = Siswa::class;
    
    protected static bool $shouldRegisterNavigation = true;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    public static function getNavigationGroup(): ?string
    {
        if (auth()->user()->hasAnyRole(['kepala_sekolah', 'kepsek'])) {
            return 'Administrasi Kepala Sekolah';
        }
        return 'Master Data Sekolah';
    }

    protected static ?int $navigationSort = 3;
    protected static ?string $navigationLabel = 'Peserta Didik';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('unit_id')
                    ->relationship('unit', 'nama')
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\TextInput::make('nis')
                    ->label('NIS')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                Forms\Components\TextInput::make('nisn')
                    ->label('NISN')
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                Forms\Components\TextInput::make('nik')
                    ->label('NIK')
                    ->unique(ignoreRecord: true)
                    ->maxLength(16),
                Forms\Components\TextInput::make('nama_lengkap')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('jenis_kelamin')
                    ->options([
                        'L' => 'Laki-laki',
                        'P' => 'Perempuan',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('tempat_lahir')
                    ->maxLength(255),
                Forms\Components\DatePicker::make('tanggal_lahir'),
                Forms\Components\Select::make('agama')
                    ->options([
                        'Islam' => 'Islam',
                        'Kristen Protestan' => 'Kristen Protestan',
                        'Kristen Katolik' => 'Kristen Katolik',
                        'Hindu' => 'Hindu',
                        'Buddha' => 'Buddha',
                        'Konghucu' => 'Konghucu',
                    ])
                    ->searchable(),
                Forms\Components\Textarea::make('alamat')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('no_telepon')
                    ->tel()
                    ->maxLength(255),
                Forms\Components\TextInput::make('nama_ayah')
                    ->maxLength(255),
                Forms\Components\TextInput::make('pekerjaan_ayah')
                    ->maxLength(255),
                Forms\Components\TextInput::make('nama_ibu')
                    ->maxLength(255),
                Forms\Components\TextInput::make('pekerjaan_ibu')
                    ->maxLength(255),
                Forms\Components\TextInput::make('no_telepon_ortu')
                    ->tel()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email_ortu')
                    ->email()
                    ->maxLength(255),
                Forms\Components\FileUpload::make('foto')
                    ->directory('siswa-photos')
                    ->image()
                    ->maxSize(2048),
                Forms\Components\DatePicker::make('tanggal_masuk'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->headerActions([
                 Tables\Actions\Action::make('cetak_data')
                    ->label('Cetak Data Siswa')
                    ->icon('heroicon-o-printer')
                    ->url(fn () => route('siswa.print-all'))
                    ->openUrlInNewTab(),
            ])
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('unit.nama')
                    ->label('Unit')
                    ->sortable()
                    ->hidden(fn () => auth()->user()->hasAnyRole(['kepala_sekolah', 'kepsek'])), // Hide for Kepsek as they only see their unit
                Tables\Columns\TextColumn::make('current_rombel_nama') // Custom attribute accessor
                    ->label('Rombel')
                    ->getStateUsing(fn (Siswa $record) => $record->getCurrentRombel()?->nama ?? '-'),
                Tables\Columns\TextColumn::make('nis')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nisn')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('nik')
                    ->label('NIK')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('nama_lengkap')
                    ->searchable(),
                Tables\Columns\TextColumn::make('jenis_kelamin'),
                Tables\Columns\TextColumn::make('tempat_lahir')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('tanggal_lahir')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('agama')
                    ->searchable(),
                Tables\Columns\TextColumn::make('no_telepon')
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
                Tables\Columns\TextColumn::make('email_ortu')
                    ->searchable(),
                Tables\Columns\TextColumn::make('foto')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tanggal_masuk')
                    ->date()
                    ->sortable(),
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
                Tables\Filters\SelectFilter::make('agama')
                    ->options([
                        'Islam' => 'Islam',
                        'Kristen Protestan' => 'Kristen Protestan',
                        'Kristen Katolik' => 'Kristen Katolik',
                        'Hindu' => 'Hindu',
                        'Buddha' => 'Buddha',
                        'Konghucu' => 'Konghucu',
                    ]),
                 Tables\Filters\Filter::make('has_rombel')
                    ->query(fn (Builder $query) => $query->whereHas('rombels', function($q) {
                        $q->where('isActive', true); // Assuming logic exists, or just basic exists
                    }))
                    ->label('Sudah Masuk Rombel')
                    ->indicator('Mempunyai Rombel'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('cetak_sts')
                        ->label('Cetak STS')
                        ->icon('heroicon-o-printer')
                        ->url(fn (Siswa $record) => route('raport.sts', $record))
                        ->openUrlInNewTab(),
                    Tables\Actions\Action::make('cetak_sas')
                        ->label('Cetak SAS')
                        ->icon('heroicon-o-printer')
                        ->url(fn (Siswa $record) => route('raport.sas', $record))
                        ->openUrlInNewTab(),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                Tables\Actions\BulkAction::make('generateUser')
                    ->label('Generate Akun Pengguna')
                    ->icon('heroicon-o-user-plus')
                    ->requiresConfirmation()
                    ->action(function (Collection $records) {
                        $count = 0;
                        foreach ($records as $record) {
                            if (!$record->user_id) {
                                // Create User
                                $user = \App\Models\User::create([
                                    'name' => $record->nama_lengkap,
                                    'email' => strtolower(str_replace(' ', '.', $record->nama_lengkap)) . '@sisfokk.sch.id', // Simplified email generation
                                    'password' => bcrypt('password123'), // Default password
                                    'role' => 'siswa',
                                ]);
                                
                                $user->assignRole('siswa');
                                
                                $record->user_id = $user->id;
                                $record->save();
                                $count++;
                            }
                        }
                        \Filament\Notifications\Notification::make()
                            ->title("$count Akun Pengguna Berhasil Dibuat")
                            ->success()
                            ->send();
                    })
                    ->deselectRecordsAfterCompletion(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = auth()->user();

        if ($user->hasAnyRole(['kepala_sekolah', 'kepsek'])) {
            // Check if user is linked to a unit via Guru profile ?
            // Assuming we migrated Guru to PTK, but we might still have a way to check Unit relationships.
            // If the User model doesn't have direct unit_id, we need to find it.
            // Earlier in RAPBS we used $user->guru->unit_id. Let's stick to that pattern as 'guru' relationship on User likely still exists even if Role is gone.
            // Wait, did I delete the Guru Model? NO. I only merged the Role. The 'guru' relationship on User model should still point to the Guru profile.
            
            if ($user->guru && $user->guru->unit_id) {
                 $query->where('unit_id', $user->guru->unit_id);
            }
        }
        
        return $query;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSiswas::route('/'),
            'create' => Pages\CreateSiswa::route('/create'),
            'edit' => Pages\EditSiswa::route('/{record}/edit'),
        ];
    }
}
