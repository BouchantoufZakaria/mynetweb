<?php

namespace App\Services;

use Random\RandomException;

class  GameService
{

    /**
     * @throws RandomException
     * @return string the access_token for the user
     *
     */
    public function createNewAccessTokenAndUpdateOrCreateUser(string $uuid, string $phoneNumber, string $username): string
    {
        // Generate a new access token
        $accessToken = bin2hex(random_bytes(32));

        // Create or update the user with the provided information
        $user = \App\Models\User::updateOrCreate(
            ['uuid' => $uuid],
            [
                'phone_number' => $phoneNumber,
                'username' => $username,
                'access_token' => $accessToken,
            ]
        );

        return $user->access_token;
    }
}
