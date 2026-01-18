<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InputRaporSdResource\Pages;
use App\Filament\Resources\InputRaporSdResource\RelationManagers;
use App\Models\InputRaporSd;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

use App\Models\Siswa;

class InputRaporSdResource extends Resource
{
    protected static ?string $model = Siswa::class;
    protected static ?string $navigationLabel = 'Input Data Rapor SD';
    protected static ?string $breadcrumb = 'Input Rapor SD';
    protected static ?string $slug = 'input-rapor-sd';
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
                                            ->label('Saran-saran / Catatan Wali Kelas'),
                                        
                                        Forms\Components\Grid::make(3)
                                            ->schema([
                                                Forms\Components\TextInput::make('sakit')->numeric()->default(0),
                                                Forms\Components\TextInput::make('izin')->numeric()->default(0),
                                                Forms\Components\TextInput::make('alpha')->numeric()->default(0),
                                            ]),
                                        
                                        Forms\Components\Textarea::make('uge_report')
                                            ->label('UGE Report (English Narrative)')
                                            ->helperText('Urban Green Education Conversation Report - Deskripsi progress siswa dalam bahasa Inggris.'),
                                        
                                        Forms\Components\Textarea::make('kokurikuler_catatan')
                                            ->label('Catatan Kokurikuler')
                                            ->helperText('Deskripsi kegiatan kokurikuler siswa seperti Circle Time, dll.'),
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
            ->columns([
                //
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ListInputRaporSds::route('/'),
            'create' => Pages\CreateInputRaporSd::route('/create'),
            'view' => Pages\ViewInputRaporSd::route('/{record}'),
            'edit' => Pages\EditInputRaporSd::route('/{record}/edit'),
        ];
    }
}
