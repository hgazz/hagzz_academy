<?php

namespace App\Http\Controllers;

use App\Models\Academies;
use App\Models\PartnerPermission;
use App\Models\PartnerRole;
use App\Models\PartnerUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class PartnerUserController extends Controller
{
    public function index()
    {
        /** @var PartnerUser $authUser */
        $authUser = auth('academy')->user();
        $academyId = $authUser->academy_id;

        $users = PartnerUser::with(['roles', 'assignedBranches'])
            ->where('academy_id', $academyId)
            ->latest()
            ->paginate(15);

        return view('Academy.pages.team.index', compact('users', 'authUser'));
    }

    public function create()
    {
        /** @var PartnerUser $authUser */
        $authUser = auth('academy')->user();
        $academyId = $authUser->academy_id;

        $roles = PartnerRole::whereNull('academy_id')
            ->orWhere('academy_id', $academyId)
            ->get();

        $branches = Academies::where('branch_to', $academyId)->get();

        return view('Academy.pages.team.create', compact('roles', 'branches'));
    }

    public function store(Request $request)
    {
        /** @var PartnerUser $authUser */
        $authUser = auth('academy')->user();
        $academyId = $authUser->academy_id;

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:partner_users,email',
            'phone' => 'nullable|string|max:50',
            'password' => 'required|string|min:6',
            'role_id' => 'required|exists:partner_roles,id',
            'access_all_branches' => 'nullable|boolean',
            'branch_ids' => 'nullable|array',
            'branch_ids.*' => 'exists:academies,id',
        ]);

        $accessAll = $request->boolean('access_all_branches');

        $user = PartnerUser::create([
            'academy_id' => $academyId,
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'is_owner' => false,
            'access_all_branches' => $accessAll,
            'status' => 'active',
        ]);

        $user->roles()->sync([$request->role_id]);

        if (!$accessAll && $request->filled('branch_ids')) {
            $user->assignedBranches()->sync($request->branch_ids);
        }

        session()->flash('success', trans('admin.users.created_successfully') ?? 'تم إضافة المستخدم بنجاح');
        return redirect()->route('academy.team.index');
    }

    public function edit(PartnerUser $team)
    {
        /** @var PartnerUser $authUser */
        $authUser = auth('academy')->user();
        if ($team->academy_id !== $authUser->academy_id) {
            abort(403);
        }

        $roles = PartnerRole::whereNull('academy_id')
            ->orWhere('academy_id', $authUser->academy_id)
            ->get();

        $branches = Academies::where('branch_to', $authUser->academy_id)->get();
        $selectedBranches = $team->assignedBranches->pluck('id')->toArray();
        $selectedRole = $team->roles->first()?->id;

        return view('Academy.pages.team.edit', compact('team', 'roles', 'branches', 'selectedBranches', 'selectedRole'));
    }

    public function update(Request $request, PartnerUser $team)
    {
        /** @var PartnerUser $authUser */
        $authUser = auth('academy')->user();
        if ($team->academy_id !== $authUser->academy_id) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('partner_users', 'email')->ignore($team->id)],
            'phone' => 'nullable|string|max:50',
            'password' => 'nullable|string|min:6',
            'role_id' => 'required|exists:partner_roles,id',
            'access_all_branches' => 'nullable|boolean',
            'branch_ids' => 'nullable|array',
            'branch_ids.*' => 'exists:academies,id',
        ]);

        $accessAll = $request->boolean('access_all_branches');

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'access_all_branches' => $accessAll,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $team->update($data);
        $team->roles()->sync([$request->role_id]);

        if (!$accessAll && $request->filled('branch_ids')) {
            $team->assignedBranches()->sync($request->branch_ids);
        } else {
            $team->assignedBranches()->detach();
        }

        session()->flash('success', trans('admin.users.updated_successfully') ?? 'تم تحديث بيانات المستخدم بنجاح');
        return redirect()->route('academy.team.index');
    }

    public function destroy(PartnerUser $team)
    {
        /** @var PartnerUser $authUser */
        $authUser = auth('academy')->user();
        if ($team->academy_id !== $authUser->academy_id || $team->is_owner) {
            session()->flash('error', 'لا يمكن حذف المالك الرئيسي للحساب');
            return back();
        }

        $team->delete();
        session()->flash('success', 'تم حذف المستخدم بنجاح');
        return redirect()->route('academy.team.index');
    }

    public function updateStatus(PartnerUser $team)
    {
        /** @var PartnerUser $authUser */
        $authUser = auth('academy')->user();
        if ($team->academy_id !== $authUser->academy_id || $team->is_owner) {
            session()->flash('error', 'لا يمكن تجميد المالك الرئيسي');
            return back();
        }

        $newStatus = $team->status === 'active' ? 'inactive' : 'active';
        $team->update(['status' => $newStatus]);

        session()->flash('success', 'تم تغيير حالة المستخدم بنجاح');
        return redirect()->route('academy.team.index');
    }
}
