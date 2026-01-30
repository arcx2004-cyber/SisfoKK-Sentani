<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RapbsResource\Pages;
use App\Models\Rapbs;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class RapbsResource extends Resource
{
    protected static ?string $model = Rapbs::class;

    protected static ?string $navigationIcon = "heroicon-o-document-currency-dollar";
    protected static ?string $navigationGroup = "Keuangan";
    protected static ?string $label = "RAPBS";
    protected static ?string $pluralLabel = "RAPBS";

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make("Informasi Umum")
                    ->schema([
                        Forms\Components\Select::make("unit_id")
                            ->relationship("unit", "nama")
                            ->default(function () {
                                $user = auth()->user();
                                if ($user->hasRole(["kepala_sekolah", "kepsek"]) && $user->guru) {
                                    return $user->guru->unit_id;
                                }
                                return null;
                            })
                            ->required()
                            ->disabled(fn ($record) => ($record && $record->status !== "draft") || auth()->user()->hasRole(["kepala_sekolah", "kepsek"]))
                            ->dehydrated(),
                        Forms\Components\Select::make("tahun_ajaran_id")
                            ->relationship("tahunAjaran", "nama", fn (Builder $query) => $query->where("is_active", true))
                            ->default(fn () => \App\Models\TahunAjaran::where("is_active", true)->first()?->id)
                            ->required()
                            ->disabled(fn ($record) => $record && $record->status !== "draft"),
                        Forms\Components\Select::make("status")
                            ->options([
                                "draft" => "Draft",
                                "diajukan" => "Diajukan",
                                "disetujui" => "Disetujui",
                                "ditolak" => "Ditolak",
                            ])
                            ->default("draft")
                            ->required()
                            ->disabled()
                            ->dehydrated(),
                    ])->columns(3),

                Forms\Components\Section::make("Keuangan")
                    ->schema([
                         Forms\Components\TextInput::make("alokasi_dana_kegiatan")
                            ->label("Alokasi Dana Kegiatan (Dari Direktur)")
                            ->helperText("Diisi oleh Direktur Pelaksana")
                            ->numeric()
                            ->prefix("Rp")
                            ->default(0)
                            ->disabled(fn () => !auth()->user()->hasRole(["direktur_pelaksana", "super_admin"])) // Only Director/SuperAdmin can edit
                            ->dehydrated()
                            ->columnSpanFull(),
                         
                         Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make("bosp_tahap_1")
                                    ->label("Penerimaan BOSP Tahap 1 (Jan-Jun)")
                                    ->numeric()
                                    ->prefix("Rp")
                                    ->default(0),
                                Forms\Components\TextInput::make("bosp_tahap_2")
                                    ->label("Penerimaan BOSP Tahap 2 (Jul-Des)")
                                    ->numeric()
                                    ->prefix("Rp")
                                    ->default(0),
                            ]),
                    ]),

                Forms\Components\Section::make("Penyetujuan")
                    ->schema([
                        Forms\Components\Textarea::make("catatan_direktur")
                            ->label("Catatan Direktur")
                            ->disabled(fn () => !auth()->user()->hasRole("kepala_sekolah")), // Only Director can edit (Using inverse logic properly later)
                            // Todo: Check actual role. Assuming approval is done via Actions, not direct edit.
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make("unit.nama")->sortable(),
                Tables\Columns\TextColumn::make("tahunAjaran.nama")->sortable(),
                Tables\Columns\TextColumn::make("total_pendapatan")->money("IDR"),
                Tables\Columns\TextColumn::make("total_pengeluaran")->money("IDR"),
                Tables\Columns\BadgeColumn::make("status")
                    ->colors([
                        "gray" => "draft",
                        "warning" => "diajukan",
                        "success" => "disetujui",
                        "danger" => "ditolak",
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                
                Tables\Actions\Action::make("ajukan")
                    ->label("Ajukan")
                    ->color("primary")
                    ->icon("heroicon-o-paper-airplane")
                    ->requiresConfirmation()
                    ->visible(fn (Rapbs $record) => ($record->status === "draft" || $record->status === "ditolak") && auth()->user()->hasRole(["kepala_sekolah", "super_admin"])) // Updated visibility
                    ->action(fn (Rapbs $record) => $record->update(["status" => "diajukan"])),
                
                Tables\Actions\Action::make("setujui")
                    ->label("Setujui")
                    ->color("success")
                    ->icon("heroicon-o-check")
                    ->requiresConfirmation()
                    ->visible(fn (Rapbs $record) => $record->status === "diajukan" && auth()->user()->hasRole(["direktur_pelaksana", "super_admin"])) // Director Only
                    ->action(fn (Rapbs $record) => $record->update(["status" => "disetujui", "approved_by" => auth()->id()])),

                Tables\Actions\Action::make("tolak")
                    ->label("Tolak")
                    ->color("danger")
                    ->icon("heroicon-o-x-mark")
                    ->form([
                        Forms\Components\Textarea::make("catatan_direktur")->required(),
                    ])
                    ->visible(fn (Rapbs $record) => $record->status === "diajukan" && auth()->user()->hasRole(["direktur_pelaksana", "super_admin"])) // Director Only
                    ->action(fn (Rapbs $record, array $data) => $record->update([
                        "status" => "ditolak",
                        "catatan_direktur" => $data["catatan_direktur"]
                    ])),

                Tables\Actions\Action::make("cetak")
                    ->label("Cetak")
                    ->icon("heroicon-o-printer")
                    ->url(fn (Rapbs $record) => route("rapbs.print", $record))
                    ->openUrlInNewTab()
                    ->visible(fn (Rapbs $record) => $record->status === "disetujui"),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
    
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = auth()->user();

        if ($user->hasRole("kepala_sekolah")) {
            if ($user->guru && $user->guru->unit_id) {
                 $query->where("unit_id", $user->guru->unit_id);
            }
        }
        
        return $query;
    }
    
    public static function getRelations(): array
    {
        return [
            \App\Filament\Resources\RapbsResource\RelationManagers\DetailsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            "index" => Pages\ListRapbs::route("/"),
            "create" => Pages\CreateRapbs::route("/create"),
            "edit" => Pages\EditRapbs::route("/{record}/edit"),
        ];
    }
    public static function shouldRegisterNavigation(): bool
    {
        if (auth()->user()->hasRole("super_admin")) {
             return true;
        }
        return auth()->user()->hasRole(["tendik", "kepala_sekolah", "kepsek", "direktur_pelaksana"]);
    }
}
