<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;

class AdsSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-cog';
    protected static ?string $navigationGroup = 'Configurações';
    protected static ?string $navigationLabel = 'Anúncios';
    protected static ?string $title = 'Anúncios AdSense';
    protected static string $view = 'filament.pages.ads-settings';

    public $ads_enabled = true;

    public function mount(): void
    {
        $this->form->fill([
            'ads_enabled' => Setting::get('ads_enabled', '1') === '1',
        ]);
    }

    protected function getFormSchema(): array
    {
        return [
            Toggle::make('ads_enabled')
                ->label('Mostrar anúncios AdSense no site')
                ->helperText('Quando desligado, todos os anúncios do AdSense deixam de aparecer no site público.'),
        ];
    }

    public function save(): void
    {
        $state = $this->form->getState();

        Setting::set('ads_enabled', !empty($state['ads_enabled']) ? '1' : '0');

        $this->notify('success', 'Definições de anúncios guardadas.');
    }
}
