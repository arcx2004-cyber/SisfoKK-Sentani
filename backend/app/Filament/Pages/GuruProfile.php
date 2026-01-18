<?php

namespace App\Filament\Pages;

use App\Models\Guru;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class GuruProfile extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-user-circle';

    protected static string $view = 'filament.pages.guru-profile';

    protected static ?string $title = 'Profil Saya';

    protected static ?string $navigationLabel = 'Profil Saya';

    protected static ?int $navigationSort = 99;

    public ?array $data = [];

    public static function canAccess(): bool
    {
        $user = Auth::user();
        return $user->hasAnyRole(['ptk', 'wali_kelas', 'kepsek']) && $user->guru;
    }

    public function mount(): void
    {
        $guru = Auth::user()->guru;
        
        if ($guru) {
            $data = $guru->toArray();
            // Do not fill 'foto' into the upload field. 
            // Keep upload field empty for new uploads only.
            unset($data['foto']);
            $this->form->fill($data);
        }
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Foto Profil')
                    ->schema([
                        Forms\Components\Split::make([
                            Forms\Components\Placeholder::make('current_foto')
                                ->label('Preview Saat Ini')
                                ->content(function () {
                                    return new \Illuminate\Support\HtmlString(
                                        '<div class="flex justify-center items-center h-full">
                                            <img src="' . Auth::user()->getFilamentAvatarUrl() . '" class="w-32 h-32 rounded-full object-cover border-4 border-white shadow-lg">'
                                    );
                                }),
                                
                            Forms\Components\FileUpload::make('foto')
                                ->label('Upload Foto Baru')
                                ->image()
                                ->imageEditor()
                                ->circleCropper()
                                ->directory('guru-photos')
                                ->maxSize(2048)
                                ->alignCenter()
                                ->helperText('Format: JPG, PNG. Klip foto akan otomatis dibulatkan.'),
                        ])->from('md'),
                    ])
                    ->collapsible(),

                Forms\Components\Section::make('Data Pribadi')
                    ->schema([
                        Forms\Components\TextInput::make('nip')
                            ->label('NIP')
                            ->disabled(),
                        Forms\Components\TextInput::make('nuptk')
                            ->label('NUPTK')
                            ->disabled(),
                        Forms\Components\TextInput::make('nama_lengkap')
                            ->label('Nama Lengkap')
                            ->disabled(),
                        Forms\Components\TextInput::make('jenis_kelamin')
                            ->label('Jenis Kelamin')
                            ->formatStateUsing(fn($state) => $state === 'L' ? 'Laki-laki' : 'Perempuan')
                            ->disabled(),
                        Forms\Components\TextInput::make('tempat_lahir')
                            ->label('Tempat Lahir')
                            ->disabled(),
                        Forms\Components\DatePicker::make('tanggal_lahir')
                            ->label('Tanggal Lahir')
                            ->disabled(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Kontak')
                    ->schema([
                        Forms\Components\Textarea::make('alamat')
                            ->label('Alamat')
                            ->disabled(),
                        Forms\Components\TextInput::make('no_telepon')
                            ->label('No. Telepon')
                            ->tel(),
                        Forms\Components\TextInput::make('pendidikan_terakhir')
                            ->label('Pendidikan Terakhir')
                            ->disabled(),
                    ])
                    ->columns(2),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $guru = Auth::user()->guru;
        
        $updateData = [
            'no_telepon' => $data['no_telepon'],
        ];

        // Only update photo if a new one is uploaded
        if (!empty($data['foto'])) {
            $updateData['foto'] = $data['foto'];
        }
        
        $guru->update($updateData);
        
        Notification::make()
            ->title('Profil berhasil diperbarui')
            ->success()
            ->send();
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Simpan Perubahan')
                ->submit('save'),
        ];
    }
}
