<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SiswaResource\Pages;
use App\Models\Siswa;
use App\Models\Rombel;
use App\Models\AnggotaRombel;
use App\Models\TahunAjaran;
use App\Models\RuangKelas;
use App\Models\Unit;
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
                    ->icon("heroicon-o-arrow-up-tray")
                    ->form([
                        Forms\Components\FileUpload::make("file")
                            ->label("File CSV/Excel")
                            ->disk("local")
                            ->directory("imports")
                            ->required()
                            ->acceptedFileTypes(["text/csv", "text/plain", "application/vnd.ms-excel", "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"]),
                    ])
                    ->action(function (array $data) {
                        try {
                            $filePath = \Illuminate\Support\Facades\Storage::disk("local")->path($data["file"]);
                        } catch (\Exception $e) {
                             \Filament\Notifications\Notification::make()->title("Error Lokasi File")->body($e->getMessage())->danger()->send();
                             return;
                        }

                        if (!file_exists($filePath)) {
                            \Filament\Notifications\Notification::make()->title("File Tidak Ditemukan")->body("File tidak ditemukan di: " . $filePath)->danger()->send();
                            return;
                        }
                        
                        $handle = fopen($filePath, "r");
                        if (!$handle) {
                            \Filament\Notifications\Notification::make()->title("Gagal Membuka File")->danger()->send();
                            return;
                        }

                        $bom = fread($handle, 3);
                        if ($bom != "\xEF\xBB\xBF") {
                            rewind($handle);
                        }

                        $pos = ftell($handle);
                        $firstLine = fgets($handle);
                        fseek($handle, $pos);
                        $delimiter = (substr_count($firstLine, ";") > substr_count($firstLine, ",")) ? ";" : ",";
                        
                        $rawHeader = fgetcsv($handle, 4000, $delimiter);
                        if (!$rawHeader) {
                            fclose($handle);
                            return;
                        }

                        $header = array_map(fn($h) => trim(strtolower($h)), $rawHeader);
                        $count = 0;
                        $errors = [];
                        
                        $tahunAjaran = \App\Models\TahunAjaran::where("is_active", true)->first();
                        if (!$tahunAjaran) {
                            \Filament\Notifications\Notification::make()->title("Gagal Import")->body("Tahun Ajaran aktif tidak ditemukan.")->danger()->send();
                            fclose($handle);
                            return;
                        }

                        while (($row = fgetcsv($handle, 4000, $delimiter)) !== FALSE) {
                            if (empty($row) || (count($row) == 1 && empty($row[0]))) continue;

                            if (count($row) < count($header)) {
                                $row = array_pad($row, count($header), null);
                            } elseif (count($row) > count($header)) {
                                $row = array_slice($row, 0, count($header));
                            }
                            
                            $rawDataRec = array_combine($header, $row);
                            $dataRec = array_map(function($v) {
                                if (!is_string($v)) return $v;
                                $trimmed = trim($v);
                                return ($trimmed === "") ? null : $trimmed;
                            }, $rawDataRec);

                            try {
                                $nama = $dataRec["nama"] ?? $dataRec["nama lengkap"] ?? $dataRec["nama_lengkap"] ?? $dataRec["nama siswa"] ?? null;
                                if (!$nama) {
                                    $errors[] = "Baris " . ($count + 2) . ": Nama tidak ditemukan.";
                                    continue;
                                }

                                $nis = $dataRec["nis"] ?? $dataRec["nipd"] ?? $dataRec["no. induk"] ?? $dataRec["nomor induk"] ?? 
                                       $dataRec["nisn"] ?? $dataRec["nik"] ?? "AUTO-" . strtoupper(uniqid());

                                // Robust Date Parsing
                                $tanggalLahirRaw = $dataRec["tanggal lahir"] ?? $dataRec["tgl lahir"] ?? null;
                                $tanggalLahir = null;
                                if ($tanggalLahirRaw) {
                                    try {
                                        if (strpos($tanggalLahirRaw, "/") !== false) {
                                            $tanggalLahir = \Carbon\Carbon::createFromFormat("d/m/Y", $tanggalLahirRaw)->format("Y-m-d");
                                        } else {
                                            $tanggalLahir = \Carbon\Carbon::parse($tanggalLahirRaw)->format("Y-m-d");
                                        }
                                    } catch (\Exception $de) {
                                        // Ignore parsing error for date, keep it null
                                    }
                                }

                                // NIK Sanitization & Cleanup
                                $nik = $dataRec["nik"] ?? null;
                                if ($nik && (stripos($nik, "E+") !== false || strpos($nik, ",") !== false)) {
                                    $nik = null; // Clear scientific notation garbage
                                }
                                if ($nik && \App\Models\Siswa::where("nik", $nik)->where("nis", "!=", $nis)->exists()) {
                                    $nik = null; // Prevent duplicate crash
                                }

                                $rombelNama = $dataRec["rombel saat ini"] ?? $dataRec["rombel"] ?? $dataRec["kelas"] ?? "";
                                $unit_id = 1; 
                                $tingkat = 0;

                                if (preg_match("/(?:Kelas|Klasi?|Grade)\s*([1-6])/i", $rombelNama, $matches)) {
                                    $unit_id = 2; 
                                    $tingkat = (int)$matches[1];
                                } elseif (preg_match("/(?:Kelas|Klasi?|Grade)\s*([7-9])/i", $rombelNama, $matches)) {
                                    $unit_id = 3; 
                                    $tingkat = (int)$matches[1];
                                } elseif (stripos($rombelNama, "SMP") !== false) { $unit_id = 3; }
                                  elseif (stripos($rombelNama, "SD") !== false) { $unit_id = 2; }

                                $siswa = \App\Models\Siswa::updateOrCreate(
                                    ["nis" => $nis],
                                    [
                                        "unit_id" => $unit_id,
                                        "nama_lengkap" => $nama,
                                        "nisn" => $dataRec["nisn"] ?? null,
                                        "nik" => $nik,
                                        "jenis_kelamin" => substr(strtoupper($dataRec["jk"] ?? $dataRec["jenis kelamin"] ?? "L"), 0, 1),
                                        "tempat_lahir" => $dataRec["tempat lahir"] ?? null,
                                        "tanggal_lahir" => $tanggalLahir,
                                        "agama" => $dataRec["agama"] ?: "Kristen",
                                        "alamat" => $dataRec["alamat"] ?? null,
                                        "rt" => $dataRec["rt"] ?? null,
                                        "rw" => $dataRec["rw"] ?? null,
                                        "dusun" => $dataRec["dusun"] ?? null,
                                        "kelurahan" => $dataRec["kelurahan"] ?? $dataRec["desa"] ?? null,
                                        "kecamatan" => $dataRec["kecamatan"] ?? null,
                                        "kode_pos" => $dataRec["kode pos"] ?? null,
                                        "nama_ayah" => $dataRec["nama ayah"] ?? $dataRec["ayah"] ?? null,
                                        "nama_ibu" => $dataRec["nama ibu"] ?? $dataRec["ibu"] ?? null,
                                        "sekolah_asal" => $dataRec["sekolah asal"] ?? null,
                                        "status" => "aktif",
                                    ]
                                );

                                if (!empty($rombelNama)) {
                                    $ruangKelas = \App\Models\RuangKelas::where("unit_id", $unit_id)->first();
                                    $ruang_kelas_id = $ruangKelas ? $ruangKelas->id : 1;

                                    $rombel = \App\Models\Rombel::firstOrCreate(
                                        ["nama" => $rombelNama, "tahun_ajaran_id" => $tahunAjaran->id, "unit_id" => $unit_id],
                                        ["tingkat" => $tingkat, "ruang_kelas_id" => $ruang_kelas_id]
                                    );

                                    if (!\App\Models\AnggotaRombel::where("siswa_id", $siswa->id)->where("rombel_id", $rombel->id)->exists()) {
                                        \App\Models\AnggotaRombel::create(["siswa_id" => $siswa->id, "rombel_id" => $rombel->id]);
                                    }
                                }
                                $count++;
                            } catch (\Exception $e) {
                                $errors[] = "Baris " . ($count + 2) . ": " . $e->getMessage();
                            }
                        }
                        fclose($handle);

                        if ($count > 0) \Filament\Notifications\Notification::make()->title($count . " Siswa Berhasil Diimpor")->success()->send();
                        if (count($errors) > 0) \Filament\Notifications\Notification::make()->title("Beberapa baris bermasalah")->body(implode("\n", array_slice($errors, 0, 5)))->danger()->persistent()->send();
                    }),
                Tables\Actions\Action::make("cetak_data")
                    ->label("Cetak Data Siswa")
                    ->icon("heroicon-o-printer")
                    ->url(fn () => route("siswa.print-all", match (static::class) {
                        'App\\Filament\\Resources\\SiswaTKResource' => ['unit' => 'TK'],
                        'App\\Filament\\Resources\\SiswaSDResource' => ['unit' => 'SD'],
                        'App\\Filament\\Resources\\SiswaSMPResource' => ['unit' => 'SMP'],
                        default => [],
                    }))
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
                Tables\Columns\TextColumn::make("current_rombel_nama")
                    ->label("Rombel")
                    ->getStateUsing(fn (Siswa $record) => $record->getCurrentRombel()?->nama ?? "-"),
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
                Tables\Filters\SelectFilter::make("rombel_id")
                    ->label("Rombel")
                    ->relationship("rombels", "nama", fn (Builder $query) => $query->where("tahun_ajaran_id", TahunAjaran::where("is_active", true)->first()?->id)),
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