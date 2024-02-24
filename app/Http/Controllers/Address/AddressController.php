<?php

namespace App\Http\Controllers\Address;

use App\DataTables\AddressDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Address\AddressRequest;
use App\Http\Traits\AcademyTrait;
use App\Http\Traits\CityAndAreaTrait;
use App\Models\Address;
use App\Models\Area;
use App\Models\City;
use App\Services\TranslatableService;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    use CityAndAreaTrait,AcademyTrait;
    private $addressModel;
    public function __construct(Address $address)
    {
        $this->addressModel = $address;
    }

    public function index(AddressDataTable $dataTable)
    {
        return $dataTable->render('Academy.pages.address.index');
    }

    public function create()
    {
        $cities = $this->getCities();
        $areas = $this->getAreas();
        return view('Academy.pages.address.create',compact('cities', 'areas'));
        return response()->json($areas);
    }

    public function getAreaByCity($city)
    {
//        dd($city);
        $city = City::findOrFail($city);
        $areas = Area::where('city_id', $city->id)->get();
        return response()->json($areas);
    }

    public function editArea($city)
    {
        dd(12);
        $city = City::findOrFail($city);
        $areas = Area::where('city_id', $city->id)->get();
        return response()->json($areas);
    }
    public function store(AddressRequest  $request)
    {
      $active = ($request->active == "on") ? true : false;
     $transactions = TranslatableService::generateTranslatableFields($this->addressModel::getTranslatableFields() , $request->validated());
     $this->addressModel->create(array_merge($transactions , [
         'active'=>$request->has('active') ? 1 : 0,
         'academy_id'=> auth()->id(),
         'city_id'=>$request->city_id,
         'area_id'=>$request->area_id,
         'longitude'=>$request->longitude,
         'latitude'=>$request->latitude,
     ]));

     session()->flash('success',trans('admin.address.address successfully created'));
     return to_route('academy.address.index');
    }

    public function edit(Address $address)
    {
        $cities = $this->getCities();
        $areas = $this->getAreas();
        return view('Academy.pages.address.edit',compact('address', 'cities', 'areas'));
    }

    public function update(Address $address , AddressRequest $request)
    {
        $active = ($request->active == "on") ? true : false;
        $transactions = TranslatableService::generateTranslatableFields($this->addressModel::getTranslatableFields() , $request->validated());
        $address->update(array_merge($transactions , [
            'active'=>$request->has('active') ? 1 : 0,
            'city_id'=>$request->city_id,
            'area_id'=>$request->area_id,
            'longitude'=>$request->longitude,
            'latitude'=>$request->latitude,
        ]));
        session()->flash('success',trans('admin.address.address successfully update'));
        return to_route('academy.address.index');
    }

    public function delete(Address $address)
    {
        $address->delete();
        session()->flash('success',trans('admin.address.address successfully deleted'));
        return to_route('academy.address.index');
    }

}
