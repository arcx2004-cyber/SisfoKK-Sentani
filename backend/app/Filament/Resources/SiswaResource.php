<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SiswaResource\Pages;
use App\Models\Siswa;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class SiswaResource extends Resource
{
    protected static ?string $model = Siswa::class;

    protected static ?string $navigationIcon = "heroicon-o-users";
    protected static ?string $navigationGroup = "Kesiswaan";
    protected static ?string $label = "Siswa";
    protected static ?string $pluralLabel = "Data Siswa";

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make("Akun & Unit")
                    ->schema([
                        Forms\Components\Select::make("user_id")
                            ->relationship("user", "name")
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make("unit_id")
                            ->relationship("unit", "nama")
                            ->required(),
                    ])->columns(2),
                
                Forms\Components\Section::make("Biodata Siswa")
                    ->schema([
                        Forms\Components\TextInput::make("nama_lengkap")
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make("nis")
                                    ->label("NIPD / NIS")
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(255),
                                Forms\Components\TextInput::make("nisn")
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(255),
                                Forms\Components\TextInput::make("nik")
                                    ->label("NIK")
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(255),
                            ]),
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\Select::make("jenis_kelamin")
                                    ->label("JK")
                                    ->options([
                                        "L" => "Laki-laki",
                                        "P" => "Perempuan",
                                    ])
                                    ->required(),
                                Forms\Components\TextInput::make("tempat_lahir")
                                    ->maxLength(255),
                                Forms\Components\DatePicker::make("tanggal_lahir")
                                    ->format("Y-m-d"),
                            ]),
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make("agama")
                                    ->options([
                                        "Kristen" => "Kristen",
                                        "Katolik" => "Katolik",
                                        "Islam" => "Islam",
                                        "Hindu" => "Hindu",
                                        "Buddha" => "Buddha",
                                        "Konghucu" => "Konghucu",
                                    ]),
                                Forms\Components\TextInput::make("no_telepon")
                                    ->label("HP")
                                    ->tel()
                                    ->maxLength(255),
                            ]),
                        Forms\Components\Textarea::make("alamat")
                            ->columnSpanFull(),
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make("rt")->label("RT")->maxLength(10),
                                Forms\Components\TextInput::make("rw")->label("RW")->maxLength(10),
                                Forms\Components\TextInput::make("kode_pos")->label("Kode Pos")->maxLength(10),
                            ]),
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make("kelurahan")->maxLength(255),
                                Forms\Components\TextInput::make("kecamatan")->maxLength(255),
                            ]),
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make("jenis_tinggal")->maxLength(255),
                                Forms\Components\TextInput::make("alat_transportasi")->maxLength(255),
                            ]),
                    ]),
                
                Forms\Components\Section::make("Data Orang Tua")
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make("nama_ayah")->maxLength(255),
                                Forms\Components\TextInput::make("pekerjaan_ayah")->maxLength(255),
                                Forms\Components\TextInput::make("nama_ibu")->maxLength(255),
                                Forms\Components\TextInput::make("pekerjaan_ibu")->maxLength(255),
                                Forms\Components\TextInput::make("no_telepon_ortu")->tel()->maxLength(255),
                                Forms\Components\TextInput::make("email_ortu")->label("E-Mail Ortu")->email()->maxLength(255),
                            ]),
                    ]),

                Forms\Components\Section::make("Status & Sekolah Asal")
                    ->schema([
                        Forms\Components\TextInput::make("sekolah_asal")->maxLength(255),
                        Forms\Components\DatePicker::make("tanggal_masuk"),
                        Forms\Components\Select::make("status")
                            ->options([
                                "aktif" => "Aktif",
                                "lulus" => "Lulus",
                                "pindah" => "Pindah",
                                "keluar" => "Keluar",
                            ])
                            ->default("aktif")
                            ->required(),
                        Forms\Components\FileUpload::make("foto")
                            ->image()
                            ->directory("fotos"),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->headerActions([
                 Tables\Actions\Action::make("download_template")
                    ->label("Download Template CSV")
                    ->icon("heroicon-o-arrow-down-tray")
                    ->color("gray")
                    ->action(function () {
                        $headers = ["Nama", "NIPD", "JK", "NISN", "Tempat Lahir", "Tanggal Lahir", "NIK", "Agama", "Alamat", "RT", "RW", "Kelurahan", "Kecamatan", "Kode Pos", "Jenis Tinggal", "Alat Transportasi", "HP", "E-Mail", "Nama Ayah", "Pekerjaan Ayah", "Nama Ibu", "Pekerjaan Ibu", "Rombel Saat Ini", "Sekolah Asal"];
                        $callback = function() {
                            $file = fopen("php://output", "w");
                            fputcsv($file, ["Nama", "NIPD", "JK", "NISN", "Tempat Lahir", "Tanggal Lahir", "NIK", "Agama", "Alamat", "RT", "RW", "Kelurahan", "Kecamatan", "Kode Pos", "Jenis Tinggal", "Alat Transportasi", "HP", "E-Mail", "Nama Ayah", "Pekerjaan Ayah", "Nama Ibu", "Pekerjaan Ibu", "Rombel Saat Ini", "Sekolah Asal"]);
                            fclose($file);
                        };
                        return response()->streamDownload($callback, "template_siswa_skkk.csv", ["Content-Type" => "text/csv"]);
                    }),
                 Tables\Actions\Action::make("import_siswa")
                    ->label("Import Siswa")
                    ->icon("heroicon-o-document-arrow-up")
                    ->color("primary")
                    ->form([
                        Forms\Components\FileUpload::make("file")
                            ->label("File CSV")
                            ->acceptedFileTypes(["text/csv", "application/vnd.ms-excel", "text/plain"])
                            ->required()
                            ->storeFiles(false),
                    ])
                    ->action(function (array $data) {
                        $file = $data["file"];
                        $handle = fopen($file->getRealPath(), "r");
                        // Clear BOM if exists
                        $bom = fread($handle, 3);
                        if ($bom != "\xEF\xBB\xBF") {
                            rewind($handle);
                        }
                        
                        $header = fgetcsv($handle, 2000, ",");
                        
                        $count = 0;
                        $errors = [];
                        
                        while (($row = fgetcsv($handle, 2000, ",")) !== FALSE) {
                            if (empty($row[0])) continue; // Skip empty rows
                            $dataRec = array_combine($header, $row);
                            
                            try {
                                // Logic to determine Unit from "Rombel Saat Ini"
                                $rombelInfo = $dataRec["Rombel Saat Ini"] ?? "";
                                $unit_id = 1; // Default to TK

                                if (preg_match("/Kelas [1-6]/i", $rombelInfo)) {
                                    $unit_id = 2; // SD
                                } elseif (preg_match("/Kelas [7-9]/i", $rombelInfo)) {
                                    $unit_id = 3; // SMP
                                }

                                \App\Models\Siswa::updateOrCreate(
                                    ["nis" => $dataRec["NIPD"]],
                                    [
                                        "unit_id" => $unit_id,
                                        "nama_lengkap" => $dataRec["Nama"],
                                        "nisn" => $dataRec["NISN"] ?? null,
                                        "nik" => $dataRec["NIK"] ?? null,
                                        "jenis_kelamin" => substr(strtoupper($dataRec["JK"] ?? "L"), 0, 1),
                                        "tempat_lahir" => $dataRec["Tempat Lahir"] ?? null,
                                        "tanggal_lahir" => $dataRec["Tanggal Lahir"] ?: null,
                                        "agama" => $dataRec["Agama"] ?: "Kristen",
                                        "alamat" => $dataRec["Alamat"] ?? null,
                                        "rt" => $dataRec["RT"] ?? null,
                                        "rw" => $dataRec["RW"] ?? null,
                                        "kelurahan" => $dataRec["Kelurahan"] ?? null,
                                        "kecamatan" => $dataRec["Kecamatan"] ?? null,
                                        "kode_pos" => $dataRec["Kode Pos"] ?? null,
                                        "jenis_tinggal" => $dataRec["Jenis Tinggal"] ?? null,
                                        "alat_transportasi" => $dataRec["Alat Transportasi"] ?? null,
                                        "no_telepon" => $dataRec["HP"] ?? null,
                                        "email_ortu" => $dataRec["E-Mail"] ?? null,
                                        "nama_ayah" => $dataRec["Nama Ayah"] ?? null,
                                        "pekerjaan_ayah" => $dataRec["Pekerjaan Ayah"] ?? null,
                                        "nama_ibu" => $dataRec["Nama Ibu"] ?? null,
                                        "pekerjaan_ibu" => $dataRec["Pekerjaan Ibu"] ?? null,
                                        "sekolah_asal" => $dataRec["Sekolah Asal"] ?? null,
                                        "status" => "aktif",
                                    ]
                                );
                                $count++;
                            } catch (\Exception $e) {
                                $errors[] = "Baris " . ($count + 2) . ": " . $e->getMessage();
                            }
                        }
                        fclose($handle);

                        if ($count > 0) {
                            \Filament\Notifications\Notification::make()
                                ->title($count . " Siswa Berhasil Diimpor")
                                ->success()
                                ->send();
                        }

                        if (count($errors) > 0) {
                            \Filament\Notifications\Notification::make()
                                ->title("Beberapa baris gagal diimpor")
                                ->body(implode("\n", array_slice($errors, 0, 5)))
                                ->danger()
                                ->persistent()
                                ->send();
                        }
                    }),
                 Tables\Actions\Action::make("cetak_data")
                    ->label("Cetak Data Siswa")
                    ->icon("heroicon-o-printer")
                    ->url(fn () => route("siswa.print-all"))
                    ->openUrlInNewTab(),
            ])
            ->columns([
                Tables\Columns\TextColumn::make("user.name")
                    ->numeric()
                    ->placeholder("Belum ada akun")
                    ->sortable(),
                Tables\Columns\TextColumn::make("unit.nama")
                    ->label("Unit")
                    ->sortable(),
                Tables\Columns\TextColumn::make("nis")
                    ->label("NIPD")
                    ->searchable(),
                Tables\Columns\TextColumn::make("nama_lengkap")
                    ->searchable(),
                Tables\Columns\TextColumn::make("jenis_kelamin")
                    ->label("JK"),
                Tables\Columns\TextColumn::make("no_telepon")
                    ->label("HP")
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make("status"),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make("agama"),
                Tables\Filters\SelectFilter::make("unit_id")
                    ->relationship("unit", "nama"),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make("cetak_sts")
                        ->label("Cetak STS")
                        ->icon("heroicon-o-printer")
                        ->url(fn (Siswa $record) => route("raport.sts", $record))
                        ->openUrlInNewTab(),
                    Tables\Actions\Action::make("cetak_sas")
                        ->label("Cetak SAS")
                        ->icon("heroicon-o-printer")
                        ->url(fn (Siswa $record) => route("raport.sas", $record))
                        ->openUrlInNewTab(),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make("generateUser")
                        ->label("Generate Akun Pengguna")
                        ->icon("heroicon-o-user-plus")
                        ->requiresConfirmation()
                        ->action(function (Collection $records) {
                            $count = 0;
                            foreach ($records as $record) {
                                if (!$record->user_id) {
                                    $user = \App\Models\User::create([
                                        "name" => $record->nama_lengkap,
                                        "email" => strtolower(str_replace(" ", ".", $record->nama_lengkap)) . "@sisfokk.sch.id",
                                        "password" => bcrypt("password123"),
                                        "role" => "siswa",
                                    ]);
                                    $user->assignRole("siswa");
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
        return [];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = auth()->user();
        if ($user->hasAnyRole(["kepala_sekolah", "kepsek"])) {
            if ($user->guru && $user->guru->unit_id) {
                 $query->where("unit_id", $user->guru->unit_id);
            }
        }
        return $query;
    }

    public static function getPages(): array
    {
        return [
            "index" => Pages\ListSiswas::route("/"),
            "create" => Pages\CreateSiswa::route("/create"),
            "edit" => Pages\EditSiswa::route("/{record}/edit"),
        ];
    }
}