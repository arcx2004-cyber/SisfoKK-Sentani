<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RppResource\Pages;
use App\Models\Rpp;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class RppResource extends BaseResource
{
    protected static ?string $model = Rpp::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    public static function getNavigationGroup(): ?string
    {
        return auth()->user()->hasRole('ptk') ? 'Akademik Guru' : 'Akademik';
    }
    protected static ?int $navigationSort = 3;
    protected static ?string $navigationLabel = 'RPP (AI)';
    protected static ?string $modelLabel = 'Rencana Pelaksanaan Pembelajaran';

    public static function shouldRegisterNavigation(): bool
    {
        if (auth()->user()->hasRole('super_admin')) {
            return false;
        }
        return auth()->user()->hasAnyRole(['ptk', 'admin', 'administrator', 'kepsek']);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('capaian_pembelajaran_id')
                    ->relationship('capaianPembelajaran', 'kode')
                    ->label('Kode CP')
                    ->disabled(), // Auto-linked usually
                Forms\Components\Select::make('guru_id')
                    ->relationship('guru', 'nama_lengkap')
                    ->label('Guru')
                    ->disabled(),
                Forms\Components\MarkdownEditor::make('konten_rpp')
                    ->label('Konten RPP')
                    ->required()
                    ->columnSpanFull()
                    ->disabled(fn ($record) => $record && $record->status === 'approved'),
                Forms\Components\Select::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'generated' => 'Generated',
                        'submitted' => 'Menunggu Approval',
                        'approved' => 'Disetujui',
                        'rejected' => 'Ditolak',
                    ])
                    ->required()
                    ->disabled(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('capaianPembelajaran.mataPelajaran.nama')
                    ->label('Mata Pelajaran')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('capaianPembelajaran.kode')
                    ->label('Kode CP')
                    ->sortable(),
                Tables\Columns\TextColumn::make('capaianPembelajaran.kelas')
                    ->label('Kelas/Fase')
                    ->sortable(),
                Tables\Columns\TextColumn::make('guru.nama_lengkap')
                    ->label('Guru')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'generated' => 'info',
                        'submitted' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Periksa & Edit')
                    ->icon('heroicon-o-pencil-square')
                    ->color('primary')
                    ->button() 
                    ->visible(fn (Rpp $record) => $record->status !== 'approved'),
                
                Tables\Actions\Action::make('submit')
                    ->label('Ajukan Approval')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->action(fn (Rpp $record) => $record->update(['status' => 'submitted']))
                    ->visible(fn (Rpp $record) => in_array($record->status, ['draft', 'generated', 'rejected']) && auth()->user()->hasRole('ptk')),

                Tables\Actions\Action::make('approve')
                    ->label('Setujui')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(fn (Rpp $record) => $record->update(['status' => 'approved']))
                    ->visible(fn (Rpp $record) => $record->status === 'submitted' && auth()->user()->hasAnyRole(['kepsek', 'admin', 'super_admin'])),

                Tables\Actions\Action::make('reject')
                    ->label('Tolak')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(fn (Rpp $record) => $record->update(['status' => 'rejected']))
                    ->visible(fn (Rpp $record) => $record->status === 'submitted' && auth()->user()->hasAnyRole(['kepsek', 'admin', 'super_admin'])),

                Tables\Actions\Action::make('print')
                    ->label('Cetak PDF')
                    ->icon('heroicon-o-printer')
                    ->color('success')
                    ->url(fn (Rpp $record) => route('rpp.print', $record))
                    ->openUrlInNewTab()
                    ->visible(fn (Rpp $record) => $record->status === 'approved'),
                
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListRpps::route('/'),
            'create' => Pages\CreateRpp::route('/create'),
            'edit' => Pages\EditRpp::route('/{record}/edit'),
        ];
    }
}
