<?php

namespace App\Http\Controllers;

use App\Models\Owner;
use App\Models\Vet;
use App\Models\Password_reset;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Mail\PasswordReset as PasswordResetMail;
use Illuminate\Support\Facades\Mail;


class PasswordController extends BaseController
{

    public function forgotPassword(Request $request) {

        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if($validator->fails()){
            return $this->sendError('Bad request', $validator->errors(), 400);
        }

        $user = Vet::where('email', $request->email)->first();
        if ($user == null) {
            $user = Owner::where('email', $request->email)->first();
        }

        if ($user == null) {
                return $this->sendError('User not found','Nincs fiók ezzel az email címmel!', 404);
        }

        $token = Str::random(60);

        Mail::to($user)->send(new PasswordResetMail($token));

        $passwordResetExist = Password_reset::find($request->email);

        if ($passwordResetExist == null) {
            $passwordReset = new Password_reset();
            $passwordReset->email = $request->email;
            $passwordReset->token = $token;
            $passwordReset->created_at = Carbon::now('GMT+1')->timestamp;
            $passwordReset->save();
        } else {
            $passwordResetExist->token = $token;
            $passwordResetExist->created_at = Carbon::now('GMT+1')->timestamp;
            $passwordResetExist->save();
        }

        // dd($key);
        // $passwordReset = Password_reset::create(['email' => $request->email,'token' => $token, 'created_at' => Carbon::now('GMT+1')->timestamp]);

        return $this->sendResponse('','Az email sikeresen elküldve!', 200);
    }

}
