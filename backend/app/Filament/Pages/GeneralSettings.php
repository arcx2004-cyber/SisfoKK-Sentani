<?php

namespace App\Filament\Pages;

use App\Models\SchoolSetting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Storage;

class GeneralSettings extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string $view = 'filament.pages.general-settings';

    protected static ?string $title = 'Pengaturan Sekolah';

    protected static ?string $navigationLabel = 'Pengaturan Umum';

    protected static ?string $navigationGroup = 'Pengaturan';

    protected static ?int $navigationSort = 0;

    public static function canAccess(): bool
    {
        return auth()->user()->hasRole('super_admin');
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canAccess();
    }

    public ?array $data = [];

    public function mount(): void
    {
        $settings = SchoolSetting::all()->pluck('value', 'key')->toArray();
        $this->form->fill($settings);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Settings')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Identitas Sekolah')
                            ->icon('heroicon-o-building-office')
                            ->schema([
                                Forms\Components\FileUpload::make('school_logo')
                                    ->label('Logo Sekolah')
                                    ->image()
                                    ->directory('settings')
                                    ->columnSpanFull(),
                                Forms\Components\TextInput::make('school_name')
                                    ->label('Nama Sekolah Lengkap')
                                    ->required(),
                                Forms\Components\TextInput::make('school_short_name')
                                    ->label('Nama Singkat'),
                                Forms\Components\TextInput::make('school_motto')
                                    ->label('Motto Sekolah'),
                                Forms\Components\Textarea::make('school_address')
                                    ->label('Alamat Lengkap')
                                    ->rows(3)
                                    ->columnSpanFull(),
                            ])
                            ->columns(2),

                        Forms\Components\Tabs\Tab::make('Kontak')
                            ->icon('heroicon-o-phone')
                            ->schema([
                                Forms\Components\TextInput::make('school_phone')
                                    ->label('No. Telepon')
                                    ->tel(),
                                Forms\Components\TextInput::make('school_email')
                                    ->label('Email')
                                    ->email(),
                                Forms\Components\TextInput::make('school_whatsapp')
                                    ->label('WhatsApp'),
                                Forms\Components\TextInput::make('school_instagram')
                                    ->label('Instagram')
                                    ->prefix('@'),
                                Forms\Components\TextInput::make('school_facebook')
                                    ->label('Facebook'),
                                Forms\Components\TextInput::make('school_youtube')
                                    ->label('YouTube Channel'),
                            ])
                            ->columns(2),

                        Forms\Components\Tabs\Tab::make('Google Maps')
                            ->icon('heroicon-o-map-pin')
                            ->schema([
                                Forms\Components\Textarea::make('google_maps_embed')
                                    ->label('Embed Code Google Maps')
                                    ->helperText('Paste kode embed dari Google Maps di sini')
                                    ->rows(5)
                                    ->columnSpanFull(),
                                Forms\Components\TextInput::make('google_maps_latitude')
                                    ->label('Latitude')
                                    ->numeric(),
                                Forms\Components\TextInput::make('google_maps_longitude')
                                    ->label('Longitude')
                                    ->numeric(),
                            ])
                            ->columns(2),

                        Forms\Components\Tabs\Tab::make('Tema')
                            ->icon('heroicon-o-paint-brush')
                            ->schema([
                                Forms\Components\ColorPicker::make('primary_color')
                                    ->label('Warna Utama'),
                                Forms\Components\ColorPicker::make('secondary_color')
                                    ->label('Warna Sekunder'),
                            ])
                            ->columns(2),
                    ])
                    ->columnSpanFull(),
            ])
            ->statePath('data');
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Simpan Pengaturan')
                ->submit('save'),
        ];
    }

    public function save(): void
    {
        $data = $this->form->getState();

        foreach ($data as $key => $value) {
            if ($value !== null) {
                SchoolSetting::updateOrCreate(
                    ['key' => $key],
                    ['value' => $value, 'type' => $this->getSettingType($key)]
                );
            }
        }

        Notification::make()
            ->title('Pengaturan berhasil disimpan')
            ->success()
            ->send();
    }

    private function getSettingType(string $key): string
    {
        return match (true) {
            str_contains($key, 'logo') => 'image',
            str_contains($key, 'address') || str_contains($key, 'embed') => 'textarea',
            str_contains($key, 'color') => 'color',
            default => 'text',
        };
    }
}
