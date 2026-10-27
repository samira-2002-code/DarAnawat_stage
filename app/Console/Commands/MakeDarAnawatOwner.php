<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class MakeDarAnawatOwner extends Command
{
    protected $signature = 'dar-anawat:make-owner';

    protected $description = 'Create or promote an owner account using interactive credentials';

    public function handle(): int
    {
        $email = mb_strtolower(trim((string) $this->ask('Owner email')));
        $validator = Validator::make(['email' => $email], [
            'email' => ['required', 'email', 'max:254'],
        ]);

        if ($validator->fails()) {
            $this->error($validator->errors()->first('email'));

            return self::FAILURE;
        }

        $user = User::query()->where('email', $email)->first();

        if ($user) {
            if ($user->isOwner()) {
                $this->info('This account is already an owner.');

                return self::SUCCESS;
            }

            if (! $this->confirm('Promote this existing user to owner?')) {
                return self::FAILURE;
            }
        } else {
            $name = trim((string) $this->ask('Owner name'));
            $password = $this->secret('Password (minimum 12 characters)');
            $passwordConfirmation = $this->secret('Confirm password');

            $validator = Validator::make([
                'name' => $name,
                'password' => $password,
                'password_confirmation' => $passwordConfirmation,
            ], [
                'name' => ['required', 'string', 'max:120'],
                'password' => ['required', 'confirmed', Password::min(12)],
            ]);

            if ($validator->fails() || $password !== $passwordConfirmation) {
                $this->error($validator->errors()->first() ?: 'The passwords do not match.');

                return self::FAILURE;
            }

            $user = new User([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make($password),
            ]);
        }

        $user->role = 'owner';
        $user->save();

        $this->info("Owner account ready for {$email}.");

        return self::SUCCESS;
    }
}
