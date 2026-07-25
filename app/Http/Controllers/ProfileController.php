<?php

namespace App\Http\Controllers;

use App\Http\Requests\Profile\ProfileRequest;
use App\Http\Traits\FileUpload;
use App\Models\Academies;
use App\Models\PartnerUser;

class ProfileController extends Controller
{
    use FileUpload;

    public function index()
    {
        $userAuth = auth('academy')->user();
        $academyId = ($userAuth instanceof PartnerUser) ? $userAuth->academy_id : $userAuth->id;

        $user = Academies::with([
            'country',
            'currentSubscription.plan',
            'currentSubscription.planPrice',
            'sports',
            'branches'
        ])->findOrFail($academyId);

        $saasSubscription = $user->currentSubscription;

        return view('Academy.pages.profile.profile', compact('user', 'saasSubscription', 'userAuth'));
    }

    public function update(Academies $user, ProfileRequest $request)
    {
        $userAuth = auth('academy')->user();
        $academyId = ($userAuth instanceof PartnerUser) ? $userAuth->academy_id : $userAuth->id;

        // Ensure user can only update their own academy
        $academy = Academies::findOrFail($academyId);

        $imageName = $request->hasFile('logo') 
            ? $this->upload($request->file('logo'), Academies::PATH, $academy->getRawOriginal('logo')) 
            : $academy->getRawOriginal('logo');

        $academy->update([
            'commercial_name' => $request->name,
            'logo' => $imageName,
            'phone' => $request->phone,
            'email' => $request->email,
            'facebook' => $request->facebook,
            'instagram' => $request->instagram,
            'linkedin' => $request->linkedin,
            'website' => $request->website,
        ]);

        session()->flash('success', trans('admin.profile.Updated Successfully'));
        return back();
    }
}
