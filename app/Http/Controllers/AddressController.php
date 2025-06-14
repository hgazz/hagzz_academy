<?php

namespace App\Http\Controllers;

use App\DataTables\AddressDataTable;
use App\Exports\AddressExport;
use App\Http\Requests\Address\AddressRequest;
use App\Http\Traits\AcademyTrait;
use App\Http\Traits\CityAndAreaTrait;
use App\Models\Address;
use App\Models\Area;
use App\Models\City;
use App\Models\Country;
use App\Services\TranslatableService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class AddressController extends Controller
{
    use CityAndAreaTrait,AcademyTrait;
    private Address $addressModel;
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
        $countries = $this->getCountry();
        return view('Academy.pages.address.create',compact('cities', 'areas','countries'));
    }


    public function getAreaByCity($city)
    {
        $city = City::findOrFail($city);
        $areas = Area::where('city_id', $city->id)->get();
        return response()->json($areas);
    }

    public function editArea($city)
    {
        $city = City::findOrFail($city);
        $areas = Area::where('city_id', $city->id)->get();
        return response()->json($areas);
    }
    public function getAllCountry($country)
    {
        $country = Country::findOrFail($country);
        $cities = City::where('country_id', $country->id)->get();
        return response()->json($cities);
    }
    public function store(AddressRequest  $request)
    {
         $transactions = TranslatableService::generateTranslatableFields($this->addressModel::getTranslatableFields() , $request->validated());
         $this->addressModel->create(array_merge($transactions , [
             'active'=> $request->has('active') ? 1 : 0,
             'academy_id'=> auth()->id(),
             'city_id'=> $request->city_id,
             'area_id'=> $request->area_id,
             'longitude'=> $request->longitude,
             'latitude'=> $request->latitude,
             'country_id'=> $request->country_id,
             'location_owned'=> (bool)$request->location_owned
         ]));

     session()->flash('success',trans('admin.address.address successfully created'));
     return to_route('academy.address.index');
    }

    public function edit(Address $address)
    {
        $cities = $this->getCities();
        $areas = $this->getAreas();
        $countries = $this->getCountry();
        return view('Academy.pages.address.edit',compact('address', 'cities', 'areas','countries'));
    }

    public function update(Address $address , AddressRequest $request)
    {
        $transactions = TranslatableService::generateTranslatableFields($this->addressModel::getTranslatableFields() , $request->validated());
        $address->update(array_merge($transactions , [
            'active'=> $request->has('active') ? 1 : 0,
            'country_id'=> $request->country_id,
            'city_id'=> $request->city_id,
            'area_id'=> $request->area_id,
            'longitude'=> $request->longitude,
            'latitude'=> $request->latitude,
            'location_owned'=> (bool)$request->location_owned
        ]));
        session()->flash('success',trans('admin.address.address successfully update'));
        return to_route('academy.address.index');
    }

    public function delete(Request $request)
    {
        $address = $this->addressModel->findOrFail($request->id);
        $address->delete();
        return response()->json(['data' => [
            'status' => 'success',
            'model'   => trans('admin.address.address'),
            'message' => trans('admin.address.address successfully deleted'),
        ]]);
    }

    public function export()
    {
        return Excel::download(new AddressExport() , 'address.xlsx');
    }

}
