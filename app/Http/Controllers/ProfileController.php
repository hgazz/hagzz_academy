<?php

namespace App\Http\Controllers;

use App\Http\Requests\Profile\ProfileRequest;
use App\Http\Traits\FileUpload;
use App\Models\Academies;

class ProfileController extends Controller
{
    use FileUpload;
    private $academies;
    public function __construct(Academies $academies)
    {
        $this->academies = $academies;
    }

    public function index()
    {
        $user = $this->academies->where('id', auth('academy')->id())->first();
        return view('Academy.pages.profile.profile',compact('user'));
    }

    public function update(Academies $user , ProfileRequest $request)
    {
        $imageName = $request->hasFile('logo') ? $this->upload($request->file('logo') , $this->academies::PATH,  $user->getRawOriginal('logo')) : $user->getRawOriginal('logo');
        dd($imageName);
        $user->update([
            'commercial_name'=>$request->name,
            'logo'=> $imageName,
            'phone'=>$request->phone,
            'email'=>$request->email,
            'facebook'=>$request->facebook,
            'instagram'=>$request->instagram,
            'linkedin'=>$request->linkedin,
            'website'=>$request->website
        ]);
        session()->flash('success',trans('admin.profile.Updated Successfully'));
        return back();
    }
}
