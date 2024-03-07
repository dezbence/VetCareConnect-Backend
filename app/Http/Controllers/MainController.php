<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cure_type;
use App\Models\Cure;
use App\Models\Opening;
use App\Models\Special_opening;
use App\Models\Owner;
use App\Models\Pet;
use App\Models\Vet;
use App\Models\FAQ;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

use App\Mail\PasswordForgot;

use Laravel\Sanctum\PersonalAccessToken;

class MainController extends BaseController
{
    public function getUserData() {
        return  $this->sendResponse(Auth::user(), 'Sikeres művelet!');
    }

    public function modifyUserData(Request $request) {

        $validatorFields = [
            'name' => 'required',
            'postal_code' => 'required',
            'phone' => 'required'
        ];

        if (PersonalAccessToken::findToken($request->bearerToken())->tokenable_type == "App\\Models\\Vet") {
            $validatorFields['address'] = 'required';
        }

        $validator = Validator::make($request->all(), $validatorFields);

        if ($validator->fails()){
            return $this->sendError('Bad request', $validator->errors(), 400);
        }

        Auth::user()
            ->update($request->all());

        return  $this->sendResponse('', 'Sikeres művelet!');
    }

    public function getAllOwner() {

        $owners = Owner::all();

        return  $this->sendResponse($owners, 'Sikeres művelet!');
    }

    public function deleteOwner($id) {

        $owner = Owner::where('id', '=', $id)
            ->get();

        if (count($owner) == 0) return $this->sendError('Bad request', 'Nincs ilyen orvos', 404);

        $cures = Cure::with([
            'pet' => function ($query) {
                $query->where('owner_id', '=', Auth::user()->id);
            }
        ])
        ->get();

        foreach ($cures as $cure) {
            $cure->delete();
        }

        $pets = Pet::where('owner_id', '=', $id)
        ->get();

        foreach ($pets as $pet) {
            $pet->delete();
        }

        Owner::find($id)
            ->delete();

        return  $this->sendResponse($owner, 'Sikeres művelet!');
    }

    public function getAllVet() {

        $vets = Vet::all();
        // unset($vets['password']);
        return  $this->sendResponse($vets, 'Sikeres művelet!');
    }

    public function deleteVet($id) {

        $vet = Vet::where('id', '=', $id)
            ->get();

        if (count($vet) == 0) return $this->sendError('Bad request', 'Nincs ilyen orvos', 404);

        $cures = Cure::where('vet_id', '=', $id)
        ->get();

        foreach ($cures as $cure) {
            $cure->delete();
        }

        $openings = Opening::where('vet_id', '=', $id)
        ->get();

        foreach ($openings as $opening) {
            $opening->delete();
        }

        $special_openings = Opening::where('vet_id', '=', $id)
        ->get();

        foreach ($special_openings as $opening) {
            $opening->delete();
        }

        Vet::find($id)
            ->delete();

        return  $this->sendResponse($vet, 'Sikeres művelet!');
    }

    public function getAllCureTypes() {
        return  $this->sendResponse(Cure_type::all(), 'Sikeres művelet!');
    }

    public function getAllQuestions() {
        return  $this->sendResponse(FAQ::all(), 'Sikeres művelet!');
    }

    public function searchVets(Request $request) {
        $validatorFields = [
            'name' => 'required',
            'postal_code'=> 'required',
            'address' => 'required'
        ];

        $vets = Vet::where('name', 'like', '%'.$request->all()['name'].'%')
            ->where('postal_code', 'like', '%'.$request->all()['postal_code'].'%')
            ->where('address', 'like', '%'.$request->all()['address'].'%')
            ->get();

        return $this->sendResponse($vets, 'Sikeres művelet!');
    }

    public function bearerTest(Request $request) {

        return $this->sendResponse(2, 'Sikeres művelet!');
    }



}
