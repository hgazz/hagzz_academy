<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Models\Academies;
use App\Models\PartnerRole;
use App\Models\PartnerUser;
use Illuminate\Http\Request;
use App\Models\PartnerActivityLog;

class AuthController extends Controller
{
    public function loginPage()
    {
        return view('Academy.pages.auth.login');
    }

    public function login(LoginRequest $request)
    {
        $remember_me = $request->has('remember') ? true : false;
        $credentials = $request->only('email', 'password');

        if (auth()->guard('academy')->attempt($credentials, $remember_me)) {
            PartnerActivityLog::log('login', 'تسجيل دخول ناجح إلى لوحة تحكم الشريك');
            return to_route('academy.index');
        }

        // Fallback for legacy academy accounts
        $academy = Academies::where('email', $request->email)->first();
        if ($academy && Hash::check($request->password, $academy->password)) {
            $rootAcademyId = $academy->branch_to ? $academy->branch_to : $academy->id;
            $partnerUser = PartnerUser::updateOrCreate(
                ['email' => $academy->email],
                [
                    'academy_id' => $rootAcademyId,
                    'name' => trim(($academy->first_name ?? '') . ' ' . ($academy->last_name ?? '')) ?: ($academy->name ?? 'Partner Owner'),
                    'phone' => $academy->phone,
                    'password' => $academy->password,
                    'is_owner' => ($academy->branch_to === null),
                    'access_all_branches' => true,
                    'status' => 'active',
                ]
            );

            $ownerRole = PartnerRole::where('name', 'owner')->whereNull('academy_id')->first();
            if ($ownerRole) {
                $partnerUser->roles()->syncWithoutDetaching([$ownerRole->id]);
            }

            auth()->guard('academy')->login($partnerUser, $remember_me);
            return to_route('academy.index');
        }

        return redirect()->back()->with(['error' => trans('admin.auth.invalid_email_or_password')])->withInput($request->only('email'));
    }

    public function logout()
    {
        auth()->guard('academy')->logout();
        session()->invalidate();
        return to_route('academy.loginPage');
    }
}
