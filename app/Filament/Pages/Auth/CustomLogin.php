<?php

namespace App\Filament\Pages\Auth;

use Filament\Forms\Components\Component;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Auth\Login as BaseAuth;
use Illuminate\Validation\ValidationException;

class CustomLogin extends BaseAuth
{
    /**
     * @return array<int | string, string | Form>
     */
    protected function getForms(): array
    {
        return [
            'form' => $this->form(
                $this->makeForm()
                    ->schema([
                        $this->getNikFormComponent(),
                        $this->getPasswordFormComponent(),
                        $this->getRememberFormComponent(),
                    ])
                    ->statePath('data'),
            ),
        ];
    }

    protected function getNikFormComponent(): Component
    {
        return TextInput::make('nik')
            ->label('NIK')
            ->required()
            ->autocomplete()
            ->autofocus()
            ->extraInputAttributes(['tabindex' => 1]);
    }

    protected function getCredentialsFromFormData(array $data): array
    {
        return [
            'nik' => $data['nik'],
            'password' => $data['password'],
        ];
    }

    public function getHeading(): string|\Illuminate\Contracts\Support\Htmlable
    {
        return '';
    }

    protected function throwFailureValidationException(): never
    {
        $data = $this->form->getState();
        $nik = $data['nik'] ?? null;

        $userExists = \App\Models\User::where('nik', $nik)->exists();

        if ($userExists) {
            $message = 'Password yang Anda masukkan salah.';
            $field = 'data.password';
        } else {
            $message = 'NIK yang Anda masukkan belum terdaftar.';
            $field = 'data.nik';
        }

        \Filament\Notifications\Notification::make()
            ->title('Login Gagal')
            ->body($message)
            ->danger()
            ->send();

        throw ValidationException::withMessages([
            $field => $message,
        ]);
    }
}
