<?php

namespace App\Providers;

use App\Models\AccessToken;
use App\Models\Contact;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider;

class ContactAuthProvider implements UserProvider
{
    public function retrieveById($identifier)
    {
        $contact = Contact::find($identifier);

        return $contact;
    }

    public function retrieveByToken($identifier, $token)
    {
        /*...*/
    }

    public function updateRememberToken(Authenticatable $contact, $token)
    {
        /*...*/
    }

    public function retrieveByCredentials(array $credentials)
    {
        $contact = Contact::whereId($credentials['contact_id'])->first();

        if (! $contact) {
            return null;
        }

        $token = AccessToken::where([
            ['contact_id', '=', $contact->id],
            ['token', '=', $credentials['token']],
        ])->first();

        if (! $token) {
            return null;
        }

        return $contact;
    }

    public function validateCredentials(Authenticatable $contact, array $credentials)
    {
        $token = AccessToken::where([
            ['contact_id', '=', $contact->getAuthIdentifier()],
            ['token', '=', $credentials['token']],
        ])->first();

        return $token !== null;
    }
}
