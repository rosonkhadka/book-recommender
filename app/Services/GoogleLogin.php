<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use Exception;
use Illuminate\Support\Str;
use Google_Client;

class GoogleLogin
{
    public function handle($data): ?User
    {
        $client = new Google_Client(['client_id' => config('services.google.client_id')]);
        try{
            $payload = $client->verifyIdToken($data['token']);
            if ($payload) {
                $user = User::whereEmail($payload['email'])->first();
                if ($user) {
                    return $this->syncGoogleUser($user, $payload['sub']);
                }
                return $this->registerGoogleUser($payload);
            }
        }catch(Exception $e){
            abort(401, 'Invalid token');
        }
        return null;
    }


    protected function registerGoogleUser($payload): User
    {
        return User::updateOrCreate(
            [
                'social_provider' => 'google',
                'social_provider_id' => $payload['sub'],
            ],
            [
                'email' => $payload['email'],
                'name' => $payload['name'],
                'password' => bcrypt(Str::random(12)),
                'email_verified_at' => now(),
            ]
        );
    }

    protected function syncGoogleUser(User $user, $providerID): User
    {
        $user->social_provider = 'google';
        $user->social_provider_id = $providerID;
        $user->update();
        return $user->fresh();
    }
}
