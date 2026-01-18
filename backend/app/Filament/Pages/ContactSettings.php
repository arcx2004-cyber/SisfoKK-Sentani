<?php

namespace App\Filament\Pages;

use App\Models\SchoolSetting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Actions\Action;

class ContactSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-phone';
    protected static ?string $navigationGroup = 'Pengaturan';
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationLabel = 'Pengaturan Kontak';
    protected static ?string $title = 'Pengaturan Kontak';
    protected static string $view = 'filament.pages.contact-settings';

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
        $settings = SchoolSetting::where('group', 'contact')->pluck('value', 'key')->toArray();
        
        $this->form->fill([
            'contact_address' => $settings['contact_address'] ?? '',
            'contact_phone' => $settings['contact_phone'] ?? '',
            'contact_whatsapp' => $settings['contact_whatsapp'] ?? '',
            'contact_email1' => $settings['contact_email1'] ?? '',
            'contact_email2' => $settings['contact_email2'] ?? '',
            'contact_map_lat' => $settings['contact_map_lat'] ?? '',
            'contact_map_lng' => $settings['contact_map_lng'] ?? '',
            'contact_operational_hours' => $settings['contact_operational_hours'] ?? '',
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Alamat')
                    ->description('Alamat lengkap sekolah yang akan ditampilkan di website')
                    ->icon('heroicon-o-map-pin')
                    ->schema([
                        Forms\Components\Textarea::make('contact_address')
                            ->label('Alamat Lengkap')
                            ->placeholder('Jl. Raya Sentani, Sentani, Jayapura, Papua 99352')
                            ->rows(3)
                            ->required(),
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('contact_map_lat')
                                    ->label('Latitude (Lintang)')
                                    ->placeholder('-2.5697')
                                    ->helperText('Koordinat lintang lokasi sekolah')
                                    ->numeric(),
                                Forms\Components\TextInput::make('contact_map_lng')
                                    ->label('Longitude (Bujur)')
                                    ->placeholder('140.5047')
                                    ->helperText('Koordinat bujur lokasi sekolah')
                                    ->numeric(),
                            ]),
                    ]),
                
                Forms\Components\Section::make('Kontak')
                    ->description('Nomor telepon dan WhatsApp untuk dihubungi')
                    ->icon('heroicon-o-phone')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('contact_phone')
                                    ->label('Nomor Telepon')
                                    ->placeholder('(0967) 123456')
                                    ->tel()
                                    ->required(),
                                Forms\Components\TextInput::make('contact_whatsapp')
                                    ->label('Nomor WhatsApp')
                                    ->placeholder('62967123456')
                                    ->helperText('Format: 62xxxxxxxxxx (tanpa + atau spasi)')
                                    ->required(),
                            ]),
                    ]),
                
                Forms\Components\Section::make('Email')
                    ->description('Alamat email untuk komunikasi')
                    ->icon('heroicon-o-envelope')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('contact_email1')
                                    ->label('Email Utama')
                                    ->placeholder('info@sisfokk.sch.id')
                                    ->email()
                                    ->required(),
                                Forms\Components\TextInput::make('contact_email2')
                                    ->label('Email Kedua')
                                    ->placeholder('admin@sisfokk.sch.id')
                                    ->email(),
                            ]),
                    ]),
                
                Forms\Components\Section::make('Jam Operasional')
                    ->description('Jam buka kantor sekolah')
                    ->icon('heroicon-o-clock')
                    ->schema([
                        Forms\Components\TextInput::make('contact_operational_hours')
                            ->label('Jam Operasional')
                            ->placeholder('Senin - Jumat: 07:00 - 15:00 WIT, Sabtu: 07:00 - 12:00 WIT'),
                    ]),
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
            SchoolSetting::updateOrCreate(
                ['key' => $key],
                [
                    'value' => $value ?? '',
                    'type' => $key === 'contact_address' ? 'textarea' : 'text',
                    'group' => 'contact',
                ]
            );
        }

        Notification::make()
            ->title('Berhasil!')
            ->body('Pengaturan kontak berhasil disimpan.')
            ->success()
            ->send();
    }
}
