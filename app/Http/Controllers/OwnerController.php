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

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class OwnerController extends BaseController
{

    public function getPets()
    {
        $pets = Pet::where('owner_id', '=', Auth::user()->id)
            ->get();

        return  $this->sendResponse($pets, 'Sikeres művelet!');
    }

    public function addNewPet(Request $request) {

        $validatorFields = [
            'name' => 'required',
            'species'=> 'required',
            'gender' => 'required',
            'weight' => 'required',
            'born_date' => 'required',
            'chip_number' => 'required',
            'pedigree_number'=> 'required'
        ];

        $validator = Validator::make($request->all(), $validatorFields);


        if ($validator->fails()){
            return $this->sendError('Bad request', $validator->errors(), 400);
        }

        $petData = $request->all();
        $petData['owner_id'] = Auth::user()->id;

        $newPet = Pet::create($petData);

        return  $this->sendResponse('', 'Sikeres művelet!');
    }

    public function modifyPet(Request $request) {

        $validatorFields = [
            'id' => 'required',
            'name' => 'required',
            'species'=> 'required',
            'gender' => 'required',
            'weight' => 'required',
            'born_date' => 'required',
            'chip_number' => 'required',
            'pedigree_number'=> 'required'
        ];

        $validator = Validator::make($request->all(), $validatorFields);

        if ($validator->fails()){
            return $this->sendError('Bad request', $validator->errors(), 400);
        }

        $ownerPets = Pet::where('id', '=', $request->id)
            ->where('owner_id', '=', Auth::user()->id)
            ->get();

        if (count($ownerPets) == 0) return $this->sendError('Bad request', 'Nincs ilyen állata', 400);

        Pet::find($request->id)->update($request->all());

        return  $this->sendResponse('Sikeres változtatás', 'Sikeres művelet!');

    }

    public function deletePet($id) {

        $pets = Pet::where('id', '=', $id)
            ->where('owner_id', '=', Auth::user()->id)
            ->get();

        if (count($pets) == 0) return $this->sendError('Bad request', 'Nincs ilyen állata', 404);

        $cures = Cure::with([
            'pet' => function ($query) {
                $query->where('owner_id', '=', Auth::user()->id);
            }
        ])
        ->where('pet_id', '=', $pets[0]->id)
        ->get();

        foreach ($cures as $cure) {
            $cure->delete();
        }

        Pet::find($pets[0]->id)
            ->delete();

        return $this->sendResponse("", 'Sikeres művelet!');
    }

    public function getFreeAppointments($id, $date)
    {
        $vets = Vet::with([
        'special_openings' => function ($query) use($date) {
            $query->where('date', '=', $date);
        },
        'openings' => function ($query) use($date){
            $query->where('day', '=', $this->getDayName(date('l', strtotime($date))));
        }
        ])
            ->where('id', '=', $id)
            ->get();

        $opening_hours = [];

        if (count($vets[0]['special_openings']) == 0) {
            foreach ($vets[0]['openings'] as $opening) {
                array_push($opening_hours, $opening->working_hours);
            }
            } else {
            foreach ($vets[0]['special_openings'] as $special_opening) {
                    array_push($opening_hours, $special_opening->working_hours);
                }
            }

        $appointments = [];

        foreach ($opening_hours as $interval) {

            date_default_timezone_set('Europe/Budapest');

            if ($interval == "zárva") return $this->sendResponse("Zárva!", 'Sikeres művelet!');

            $appointment = strtotime(explode('-', $interval)[0], 0);
            $closeTime = strtotime(explode('-', $interval)[1], 0);

            while($appointment < $closeTime) {
                if ($date == date('Y-m-d')) {
                    if ($appointment > strtotime(date('H:i'), 0)) array_push($appointments, date('H:i', $appointment));

                } else array_push($appointments, date('H:i', $appointment));

                $appointment += (30 * 60);
            }
        }

        $cures = Cure::where('vet_id', '=', $id)
            ->where('date', 'like', $date.'%')
            ->get();

        foreach($cures as $cure) {

            $appointments = array_diff($appointments, [date('H:i', strtotime($cure->date))]);

        }

        if (count($appointments) == 0) return $this->sendResponse("Zárva!", 'Sikeres művelet!');

        return $this->sendResponse($appointments, 'Sikeres művelet!');
    }

    public function addNewAppointment(Request $request){

        $validatorFields = [
            'date' => 'required',
            'pet_id'=> 'required',
            'cure_type_id' => 'required',
            'vet_id' => 'required'
        ];

        $validator = Validator::make($request->all(), $validatorFields);

        if ($validator->fails()){
            return $this->sendError('Bad request', $validator->errors(), 400);
        }

        if (Pet::find($request->pet_id)->owner_id == Auth::user()->id) {
            Cure::create($request->all());
        } else {
            return $this->sendError('Bad request', ['error'=>'nincs ilyen állata'], 400);
        }

        return  $this->sendResponse('', 'Sikeres művelet!');
    }

    public function getOwnerAppointments()
    {
        $appointments = Cure::with('cure_type', 'vet', 'pet.owner')
            ->get();

        $return = [];

        foreach ($appointments as $appointment) {
            if ($appointment->pet->owner->id == Auth::user()->id) {

                $old = false;
                if (strtotime($appointment->date) < time()) $old = true;

                $return[] = [
                    'owner_id' => $appointment->pet->owner->id,
                    'pet_name' => $appointment->pet->name,
                    'cure_type' => $appointment->cure_type->type,
                    'cure_id' => $appointment->id,
                    'vet_name' => $appointment->vet->name,
                    'vet_address' => $appointment->vet->address,
                    'vet_postal_code' => $appointment->vet->postal_code,
                    'cure_date' => $appointment->date,
                    'is_old' => $old
                 ];

            }
        }

        usort($return, fn($a, $b) => $a['cure_date'] <=> $b['cure_date']);

        return $this->sendResponse($return, 'Sikeres művelet!');
    }

    public function deleteAppointment($id) {

        $cures = Cure::with([
        'pet.owner' => function ($query) {
            $query->where('id', '=', Auth::user()->id);
        }
        ])
            ->where('id', '=', $id)
            ->get();

        $validCure = null;

        foreach ($cures as $cure) {
            if ($cure['pet']['owner'] != null) {
                $validCure = $cure;
                break;
            }
        }

        if ($validCure == null) {
            return $this->sendError('Bad request', ['error'=>'nincs ilyen időpontja'], 400);
        } else {
            Cure::find($validCure->id)->delete();
        }

        return $this->sendResponse("", 'Sikeres művelet!');

    }

    private function getDayName($day) {

        $days = [
            'Monday' =>'hétfő',
            'Tuesday' => 'kedd',
            'Wednesday' => 'szerda',
            'Thursday' => 'csütörtök',
            'Friday' => 'péntek',
            'Saturday' => 'szombat',
            'Sunday' => 'vasárnap'
        ];

        return $days[$day];
    }

}
