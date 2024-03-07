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

class VetController extends BaseController
{

    public function getVetAppointments($date) {
        $appointments = Cure::with('cure_type', 'vet', 'pet.owner')
            ->where('date', 'like', $date.'%')
            ->where('vet_id', '=', Auth::user()->id)
            ->get();

        if (count($appointments) == 0) return $this->sendResponse('Nincs időpont', 'Sikeres művelet!');

        $return = [];

        foreach ($appointments as $appointment) {
            if ($appointment->vet->id == Auth::user()->id) {
                if (strtotime($appointment->date) > strtotime(date('Y-m-d')))
                {
                    $return[] = [
                        'pet_species' => $appointment->pet->species,
                        'cure_type' => $appointment->cure_type->type,
                        'cure_date' => $appointment->date
                    ];
                }
            }
        }

        usort($return, fn($a, $b) => $a['cure_date'] <=> $b['cure_date']);

        return $this->sendResponse($return, 'Sikeres művelet!');
    }

    public function getOpenings(){
        $order = ["hétfő", "kedd", "szerda", "csütörtök", "péntek", "szombat", "vasárnap"];

        $openings = Opening::where('vet_id', '=', Auth::user()->id)
                ->get();

        $openingsSorted = $openings->sortBy(function ($item) use ($order){
            return array_search($item["day"], $order);
        });

        $openingsSortedArray = array();
        foreach ($openingsSorted as $item) {
            array_push($openingsSortedArray, $item);
        }

        return $this->sendResponse($openingsSortedArray, 'Sikeres művelet!');
    }

    public function addOpenings(Request $request) {

        $validatorFields = [
            'working_hours'=> 'required',
            'day'=> 'required'
        ];

        foreach ($request->all() as $opening) {
            $validator = Validator::make($opening, $validatorFields);
        }

        if ($validator->fails()){
            return $this->sendError('Bad request', $validator->errors(), 400);
        }

        foreach ($request->all() as $opening) {
            $opening['vet_id'] = Auth::user()->id;
            Opening::create($opening);
        }

        return $this->sendResponse('', 'Sikeres művelet!');
    }

    public function modifyOpening(Request $request) {

        $validatorFields = [
            'id' => 'required',
            'working_hours'=> 'required',
            'day'=> 'required'
        ];

        $validator = Validator::make($request->all(), $validatorFields);

        if ($validator->fails()){
            return $this->sendError('Bad request', $validator->errors(), 400);
        }

        $opening = $request->all();
        $opening['vet_id'] = Auth::user()->id;

        $vetOpenings = Opening::where('id', '=', $request->id)
            ->where('vet_id', '=', Auth::user()->id)
            ->get();

        if (count($vetOpenings) == 0) return $this->sendError('Bad request', 'Nincs ilyen nyitvatartása', 400);

        Opening::find($request->id)->update($opening);

        return $this->sendResponse(Opening::find($request->id), 'Sikeres művelet!');
    }

    public function deleteOpening($day) {
        $days = ["hétfő", "kedd", "szerda", "csütörtök", "péntek", "szombat", "vasárnap", "hétköznapok", "minden nap"];

        if (!in_array($day, $days)) return $this->sendError('Bad request', 'Nincs ilyen nap', 400);

        if ($day == $days[8]) {
            $vetOpenings = Opening::where('vet_id', '=', Auth::user()->id)->get();
        } else if ($day == $days[7]) {
            $vetOpenings = Opening::where([
                    ['vet_id', '=', Auth::user()->id],
                    ['day', '!=', 'szombat'],
                    ['day', '!=', 'vasárnap']
                ])
                ->get();
        } else {
            $vetOpenings = Opening::where('day', '=', $day)
            ->where('vet_id', '=', Auth::user()->id)
            ->get();
        }
        if (count($vetOpenings) == 0) return $this->sendError('Bad request', 'Nincs ilyen nyitvatartása', 400);

        foreach ($vetOpenings as $item) {
            $item->delete();
        }

        return $this->sendResponse($vetOpenings, 'Sikeres művelet!');
    }

    public function getSpecialOpenings(){
        $oldOpenings = Special_opening::where('vet_id', '=', Auth::user()->id)
            ->where('date', '<', date("Y-m-d"))
            ->delete();

        $openings = Special_opening::where('vet_id', '=', Auth::user()->id)
            ->orderBy('date')
            ->get();

        return $this->sendResponse($openings, 'Sikeres művelet!');
    }

    public function addSpecialOpenings(Request $request) {

        $validatorFields = [
            'working_hours' => 'required',
            'date' => 'required'
        ];

        foreach ($request->all() as $specialOpening) {
            $validator = Validator::make($specialOpening, $validatorFields);
        }

        if ($validator->fails()){
            return $this->sendError('Bad request', $validator->errors(), 400);
        }

        $test = [];

        foreach ($request->all() as $specialOpening) {
            $specialOpening['vet_id'] = Auth::user()->id;
            Special_opening::create($specialOpening);
        }

        return $this->sendResponse('', 'Sikeres művelet!');
    }

    public function modifySpecialOpening(Request $request) {

        $validatorFields = [
            'id' => 'required',
            'working_hours'=> 'required',
            'date'=> 'required'
        ];

        $validator = Validator::make($request->all(), $validatorFields);

        if ($validator->fails()){
            return $this->sendError('Bad request', $validator->errors(), 400);
        }

        $specialOpening = $request->all();
        $specialOpening['vet_id'] = Auth::user()->id;

        $vetSpecialOpenings = Special_opening::where('id', '=', $request->id)
            ->where('vet_id', '=', Auth::user()->id)
            ->get();

        if (count($vetSpecialOpenings) == 0) return $this->sendError('Bad request', 'Nincs ilyen nyitvatartása', 400);

        Special_opening::find($request->id)->update($specialOpening);

        return $this->sendResponse(Special_opening::find($request->id), 'Sikeres művelet!');
    }

    public function deleteSpecialOpening($id) {

        $vetSpecialOpenings = Special_opening::where('id', '=', $id)
            ->where('vet_id', '=', Auth::user()->id)
            ->get();

        if (count($vetSpecialOpenings) == 0) return $this->sendError('Bad request', 'Nincs ilyen nyitvatartása', 400);

        Special_opening::find($id)->delete();

        return $this->sendResponse('', 'Sikeres művelet!');
    }

}
