<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Owner;
use App\Models\Vet;

class VerificationController extends BaseController
{

    public function verify($user_id, Request $request) {
        if (!$request->hasValidSignature()) {
            return $this->sendError('','Hibás vagy lejárt megerősítő link!');
        }

        $user = Vet::find($user_id);
        if ($user == null) {
            $user = Owner::find($user_id);
        }

        if ($user == null) {
            return $this->sendError('','Hibás verifikáció!');
        }

        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }

        return $this->sendResponse('','Sikeres megerősítés!');
    }

    public function resend($email) {
        $user = Vet::where('email', $email)->first();
        if ($user == null) {
            $user = Owner::where('email', $email)->first();
        }

        if ($user === null) {
            return $this->sendError('','Nincs ilyen felhasználó!');
        }

        if ($user->hasVerifiedEmail()) {
            return $this->sendError('','Az email már meg lett erősítve!');
        }

        $user->sendEmailVerificationNotification();

        return $this->sendResponse('','Új megerősítő email sikeresen elküldve!');
    }


}
