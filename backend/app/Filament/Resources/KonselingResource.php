<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KonselingResource\Pages;
use App\Filament\Resources\KonselingResource\RelationManagers;
use App\Models\BkRecord;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class KonselingResource extends Resource
{
    protected static ?string $model = BkRecord::class;

    protected static ?string $slug = 'konseling';

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';
    protected static ?string $navigationLabel = 'Bimbingan & Konseling';
    
    public static function getLabel(): ?string
    {
        return 'Bimbingan & Konseling';
    }
    public static function canViewAny(): bool
    {
        $user = auth()->user();
        if (!$user) return false;
        
        return $user->hasAnyRole(['super_admin', 'admin', 'administrator', 'kepala_sekolah', 'kepsek', 'ptk', 'guru_bk']); 
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Detail Bimbingan')
                    ->schema([
                        Forms\Components\Select::make('siswa_id')
                            ->relationship('siswa', 'nama_lengkap')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->label('Siswa'),
                        
                        Forms\Components\Select::make('guru_id')
                            ->relationship('guru', 'nama_lengkap')
                            ->default(fn() => auth()->user()->guru?->id)
                            ->searchable()
                            ->preload()
                            ->label('Konselor / Guru')
                            ->required(),

                        Forms\Components\DatePicker::make('tanggal')
                            ->required()
                            ->default(now()),

                        Forms\Components\Select::make('jenis')
                            ->options([
                                'konseling' => 'Konseling',
                                'pelanggaran' => 'Pelanggaran',
                                'prestasi' => 'Prestasi',
                                'lainnya' => 'Lainnya',
                            ])
                            ->required()
                            ->live() // Reactive
                            ->native(false),

                        Forms\Components\TextInput::make('skor')
                            ->numeric()
                            ->default(0)
                            ->label('Poin / Skor')
                            ->helperText('Masukkan angka positif.')
                            ->visible(fn (Forms\Get $get) => in_array($get('jenis'), ['pelanggaran', 'prestasi'])),

                        Forms\Components\Toggle::make('is_confidential')
                            ->label('Bersifat Rahasia')
                            ->onIcon('heroicon-m-lock-closed')
                            ->offIcon('heroicon-m-lock-open')
                            ->onColor('danger')
                            ->default(false),
                    ])->columns(2),

                Forms\Components\Section::make('Catatan')
                    ->schema([
                        Forms\Components\RichEditor::make('deskripsi')
                            ->required()
                            ->columnSpanFull(),
                        
                        Forms\Components\RichEditor::make('tindak_lanjut')
                            ->label('Tindak Lanjut / Solusi')
                            ->columnSpanFull(),
                    ]),

                 Forms\Components\Hidden::make('semester_id')
                    ->default(fn() => \App\Models\Semester::where('is_active', true)->value('id')),
                
                Forms\Components\Hidden::make('tahun_ajaran_id')
                    ->default(fn() => \App\Models\TahunAjaran::where('is_active', true)->value('id')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tanggal')->date('d M Y')->sortable(),
                Tables\Columns\TextColumn::make('siswa.nama_lengkap')->label('Siswa')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('jenis')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pelanggaran' => 'danger',
                        'prestasi' => 'success',
                        'konseling' => 'info',
                        'lainnya' => 'gray',
                    }),
                Tables\Columns\TextColumn::make('skor')->label('Poin')->sortable(),
                Tables\Columns\IconColumn::make('is_confidential')->label('Rahasia')->boolean(),
                Tables\Columns\TextColumn::make('guru.nama_lengkap')->label('Oleh')->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('jenis')
                    ->options([
                        'pelanggaran' => 'Pelanggaran',
                        'prestasi' => 'Prestasi',
                        'konseling' => 'Konseling',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('tanggal', 'desc');
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
            'index' => Pages\ListKonselings::route('/'),
            'create' => Pages\CreateKonseling::route('/create'),
            'edit' => Pages\EditKonseling::route('/{record}/edit'),
        ];
    }
}
