<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UnitResource\Pages;
use App\Filament\Resources\UnitResource\RelationManagers;
use App\Models\Unit;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UnitResource extends BaseResource
{
    protected static ?string $model = Unit::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';
    protected static ?string $navigationGroup = 'Master Data Sekolah';
    protected static ?int $navigationSort = 9;
    protected static ?string $navigationLabel = 'Unit Sekolah';

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->hasAnyRole(['super_admin', 'admin']);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Dasar')
                    ->schema([
                        Forms\Components\TextInput::make('nama')
                            ->label('Nama Unit')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('kode')
                            ->label('Kode')
                            ->required()
                            ->maxLength(10)
                            ->helperText('Contoh: TK, SD, SMP'),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Aktif')
                            ->default(true),
                        Forms\Components\TextInput::make('urutan')
                            ->numeric()
                            ->default(0),
                    ])->columns(4),

                Forms\Components\Section::make('Kepala Sekolah')
                    ->schema([
                        Forms\Components\Select::make('guru_id')
                            ->label('Nama Kepala Sekolah')
                            ->relationship('kepalaSekolahGuru', 'nama_lengkap')
                            ->searchable()
                            ->preload()
                            ->live()
                            ->afterStateUpdated(function ($state, Forms\Set $set) {
                                if ($state) {
                                    $guru = \App\Models\Guru::find($state);
                                    if ($guru) {
                                        $set('kepala_sekolah', $guru->nama_lengkap);
                                    }
                                }
                            }),
                        Forms\Components\Hidden::make('kepala_sekolah'),
                        Forms\Components\FileUpload::make('foto_kepala_sekolah')
                            ->label('Foto Kepala Sekolah')
                            ->image()
                            ->directory('units/kepsek')
                            ->maxSize(2048),
                    ])->columns(2),

                Forms\Components\Section::make('Deskripsi & Konten Landing Page')
                    ->schema([
                        Forms\Components\Textarea::make('deskripsi')
                            ->label('Deskripsi Singkat')
                            ->rows(2)
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('sekilas')
                            ->label('Sekilas Tentang Unit')
                            ->rows(3)
                            ->helperText('Penjelasan singkat tentang unit ini')
                            ->columnSpanFull(),
                        Forms\Components\RichEditor::make('konten')
                            ->label('Konten Detail')
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Visi & Misi')
                    ->schema([
                        Forms\Components\Textarea::make('visi')
                            ->label('Visi')
                            ->rows(2),
                        Forms\Components\Textarea::make('misi')
                            ->label('Misi')
                            ->rows(3),
                    ])->columns(2),

                Forms\Components\Section::make('Fasilitas & Info')
                    ->schema([
                        Forms\Components\Textarea::make('fasilitas')
                            ->label('Fasilitas')
                            ->rows(3)
                            ->helperText('Pisahkan dengan baris baru'),
                        Forms\Components\TextInput::make('jam_belajar')
                            ->label('Jam Belajar')
                            ->placeholder('Senin-Jumat: 07:00-13:00'),
                        Forms\Components\TextInput::make('telepon')
                            ->label('Telepon')
                            ->tel(),
                        Forms\Components\TextInput::make('email')
                            ->label('Email')
                            ->email(),
                    ])->columns(2),

                Forms\Components\Section::make('Foto Sekolah')
                    ->schema([
                        Forms\Components\FileUpload::make('foto_sekolah')
                            ->label('Foto Gedung/Sekolah')
                            ->image()
                            ->directory('units/buildings')
                            ->maxSize(5120)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama')
                    ->searchable(),
                Tables\Columns\TextColumn::make('kode')
                    ->searchable(),
                Tables\Columns\TextColumn::make('kepala_sekolah')
                    ->searchable(),
                Tables\Columns\TextColumn::make('urutan')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
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
            'index' => Pages\ListUnits::route('/'),
            'create' => Pages\CreateUnit::route('/create'),
            'edit' => Pages\EditUnit::route('/{record}/edit'),
        ];
    }
}
