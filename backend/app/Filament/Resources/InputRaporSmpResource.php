<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InputRaporSmpResource\Pages;
use App\Filament\Resources\InputRaporSmpResource\RelationManagers;
use App\Models\InputRaporSmp;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

use App\Models\Siswa;

class InputRaporSmpResource extends Resource
{
    protected static ?string $model = Siswa::class;
    protected static ?string $navigationLabel = 'Input Data Rapor SMP';
    protected static ?string $breadcrumb = 'Input Rapor SMP';
    protected static ?string $slug = 'input-rapor-smp';
    protected static ?string $navigationGroup = 'Akademik';
    protected static ?string $navigationIcon = 'heroicon-o-pencil-square';

    public static function shouldRegisterNavigation(): bool
    {
        if (auth()->user()->hasRole('super_admin')) {
             return false;
        }
        return parent::shouldRegisterNavigation();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Identitas Siswa')
                    ->schema([
                        Forms\Components\TextInput::make('nama_lengkap')->disabled(),
                        Forms\Components\TextInput::make('nis')->disabled(),
                    ])->columns(2),

                Forms\Components\Tabs::make('Data Rapor')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Kesehatan & Fisik')
                            ->schema([
                                Forms\Components\Repeater::make('kesehatans')
                                    ->relationship()
                                    ->schema([
                                        Forms\Components\Hidden::make('semester_id')
                                            ->default(fn () => \App\Models\Semester::getActive()->id),
                                        Forms\Components\TextInput::make('pendengaran')->label('Pendengaran'),
                                        Forms\Components\TextInput::make('penglihatan')->label('Penglihatan'),
                                        Forms\Components\TextInput::make('gigi')->label('Gigi'),
                                        Forms\Components\TextInput::make('lainnya')->label('Lainnya'),
                                    ])
                                    ->maxItems(1)
                                    ->defaultItems(1)
                                    ->label('Kondisi Kesehatan (Semester Ini)'),
                                
                                Forms\Components\Repeater::make('dataTubuhs')
                                    ->relationship()
                                    ->schema([
                                        Forms\Components\Hidden::make('semester_id')
                                            ->default(fn () => \App\Models\Semester::getActive()->id),
                                        Forms\Components\TextInput::make('tinggi_badan')->numeric()->suffix('cm'),
                                        Forms\Components\TextInput::make('berat_badan')->numeric()->suffix('kg'),
                                    ])
                                    ->maxItems(1)
                                    ->defaultItems(1)
                                    ->label('Data Tubuh (Semester Ini)'),
                            ]),
                        
                        Forms\Components\Tabs\Tab::make('Prestasi')
                            ->schema([
                                Forms\Components\Repeater::make('prestasis')
                                    ->relationship()
                                    ->schema([
                                        Forms\Components\Hidden::make('semester_id')
                                            ->default(fn () => \App\Models\Semester::getActive()->id),
                                        Forms\Components\TextInput::make('jenis')->label('Jenis Prestasi'),
                                        Forms\Components\Textarea::make('keterangan'),
                                    ])
                                    ->label('Daftar Prestasi'),
                            ]),

                        Forms\Components\Tabs\Tab::make('Catatan & Absensi')
                            ->schema([
                                Forms\Components\Repeater::make('catatanAkhirs')
                                    ->relationship()
                                    ->schema([
                                        Forms\Components\Hidden::make('semester_id')
                                            ->default(fn () => \App\Models\Semester::getActive()->id),
                                        Forms\Components\Textarea::make('catatan')
                                            ->label('Catatan Wali Kelas / Saran'),
                                        
                                        Forms\Components\Grid::make(3)
                                            ->schema([
                                                Forms\Components\TextInput::make('sakit')->numeric()->default(0),
                                                Forms\Components\TextInput::make('izin')->numeric()->default(0),
                                                Forms\Components\TextInput::make('alpha')->numeric()->default(0),
                                            ]),
                                    ])
                                    ->maxItems(1)
                                    ->defaultItems(1)
                                    ->label('Catatan Akhir Semester'),
                            ]),
                    ])->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->eloquentQuery(fn(Builder $query) => $query->whereHas('unit', fn($q) => $q->where('nama', 'SMP')))
            ->columns([
                Tables\Columns\TextColumn::make('nama_lengkap')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('nis')->searchable(),
                Tables\Columns\TextColumn::make('rombel.nama')->label('Kelas')
                    ->state(fn (Siswa $record) => $record->activeRombel->nama ?? '-'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('Input Data'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListInputRaporSmps::route('/'),
            'create' => Pages\CreateInputRaporSmp::route('/create'),
            'view' => Pages\ViewInputRaporSmp::route('/{record}'),
            'edit' => Pages\EditInputRaporSmp::route('/{record}/edit'),
        ];
    }
}
