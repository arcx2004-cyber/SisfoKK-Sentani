<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SiswaBiodataResource\Pages;
use App\Models\Siswa;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class SiswaBiodataResource extends Resource
{
    protected static ?string $model = Siswa::class;
    protected static ?string $navigationIcon = 'heroicon-o-user';
    protected static ?string $navigationLabel = 'Biodata Diri';
    protected static ?string $slug = 'biodata-diri';
    protected static ?int $navigationSort = 1;

    public static function canViewAny(): bool
    {
        return Auth::user() && Auth::user()->hasRole('siswa');
    }

    // Only allow editing if needed, or just viewing
    public static function canCreate(): bool { return false; }
    public static function canDeleteAny(): bool { return false; }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Pribadi')
                    ->schema([
                        Forms\Components\FileUpload::make('foto')
                            ->image()
                            ->avatar()
                            ->directory('siswa-photos')
                            ->columnSpanFull()
                            ->label('Foto Profil (Bisa Diubah)'),
                        Forms\Components\TextInput::make('nis')
                            ->label('NIS')
                            ->readOnly()
                            ->dehydrated(false),
                        Forms\Components\TextInput::make('nisn')
                            ->label('NISN')
                            ->readOnly()
                            ->dehydrated(false),
                        Forms\Components\TextInput::make('nama_lengkap')
                            ->label('Nama Lengkap')
                            ->readOnly()
                            ->dehydrated(false),
                        Forms\Components\TextInput::make('tempat_lahir')
                            ->label('Tempat Lahir')
                            ->readOnly()
                            ->dehydrated(false),
                        Forms\Components\DatePicker::make('tanggal_lahir')
                            ->label('Tanggal Lahir')
                            ->readOnly()
                            ->dehydrated(false),
                        Forms\Components\TextInput::make('jenis_kelamin')
                            ->label('Jenis Kelamin')
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'L' => 'Laki-laki',
                                'P' => 'Perempuan',
                                default => $state,
                            })
                            ->readOnly()
                            ->dehydrated(false),
                    ])->columns(2),
                
                Forms\Components\Section::make('Data Orang Tua')
                    ->schema([
                        Forms\Components\TextInput::make('nama_ayah')
                            ->label('Nama Ayah')
                            ->readOnly()
                            ->dehydrated(false),
                        Forms\Components\TextInput::make('nama_ibu')
                            ->label('Nama Ibu')
                            ->readOnly()
                            ->dehydrated(false),
                        Forms\Components\TextInput::make('alamat')
                            ->label('Alamat')
                            ->columnSpanFull()
                            ->readOnly()
                            ->dehydrated(false),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                 Tables\Columns\ImageColumn::make('foto')
                    ->circular()
                    ->label('Foto'),
                Tables\Columns\TextColumn::make('nis')->label('NIS'),
                Tables\Columns\TextColumn::make('nama_lengkap')->label('Nama'),
                Tables\Columns\TextColumn::make('unit.nama')->label('Unit'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'aktif' => 'success',
                        'lulus' => 'info',
                        'keluar' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                    ->label('Ubah Foto / Data'),
            ])
            ->paginated(false);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('user_id', Auth::id());
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSiswaBiodatas::route('/'),
            'view' => Pages\ViewSiswaBiodata::route('/{record}'),
            'edit' => Pages\EditSiswaBiodata::route('/{record}/edit'),
        ];
    }
}
