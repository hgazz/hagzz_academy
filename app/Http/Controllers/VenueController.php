<?php

namespace App\Http\Controllers;

use App\Models\PartnerUser;
use App\Models\Venue;
use Illuminate\Http\Request;

class VenueController extends Controller
{
    private function getAcademyId(): int
    {
        $user = auth('academy')->user();
        if ($user instanceof PartnerUser) {
            return (int) $user->academy_id;
        }
        return (int) ($user?->id ?? auth('academy')->id());
    }

    public function index()
    {
        $academyId = $this->getAcademyId();
        $venues = Venue::where('academy_id', $academyId)->withCount('spaces')->latest()->paginate(15);
        return view('Academy.pages.venues.index', compact('venues'));
    }

    public function create()
    {
        return view('Academy.pages.venues.form', ['venue' => new Venue()]);
    }

    public function store(Request $request)
    {
        $academyId = $this->getAcademyId();
        $user = auth('academy')->user();
        $academy = $user instanceof PartnerUser ? $user->academy : $user;
        $plan = $academy?->currentSubscription()->with('plan')->first()?->plan;

        if ($plan && (int) $plan->max_venues > 0) {
            $count = Venue::where('academy_id', $academyId)->count();
            if ($count >= (int) $plan->max_venues) {
                return back()->withInput()->withErrors(['limit' => trans('admin.venues.location_limit') ?: 'تم الوصول للحد الأقصى من الفروع/الملاعب المسموح بها في الباقة.']);
            }
        }

        Venue::create($this->data($request) + ['academy_id' => $academyId]);
        return to_route('academy.venues.index')->with('success', trans('admin.venues.saved') ?: 'تم حفظ الفرع/الملعب بنجاح.');
    }

    public function edit(Venue $venue)
    {
        $this->authorizeTenant($venue);
        return view('Academy.pages.venues.form', compact('venue'));
    }

    public function update(Request $request, Venue $venue)
    {
        $this->authorizeTenant($venue);
        $venue->update($this->data($request));
        return to_route('academy.venues.index')->with('success', trans('admin.venues.saved') ?: 'تم تحديث بيانات الفرع/الملعب بنجاح.');
    }

    public function destroy(Venue $venue)
    {
        $this->authorizeTenant($venue);
        if ($venue->spaces()->whereHas('bookings')->exists()) {
            return back()->withErrors(['delete' => trans('admin.venues.cannot_delete_booked') ?: 'لا يمكن حذف هذا المكان لاحتوائه على حجوزات مسجلة مسبقاً.']);
        }
        $venue->delete();
        return back()->with('success', trans('admin.venues.deleted') ?: 'تم الحذف بنجاح.');
    }

    private function data(Request $request): array
    {
        $data = $request->validate([
            'name_ar' => ['required', 'string', 'max:255'],
            'name_en' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['required', 'string', 'max:500'],
            'timezone' => ['required', 'timezone'],
            'currency' => ['required', 'in:EGP,QAR,SAR,AED'],
            'active' => ['nullable', 'boolean'],
        ]);

        return [
            'name' => ['ar' => $data['name_ar'], 'en' => $data['name_en']],
            'phone' => $data['phone'] ?? null,
            'address' => $data['address'],
            'timezone' => $data['timezone'],
            'currency' => $data['currency'],
            'active' => $request->boolean('active'),
        ];
    }

    private function authorizeTenant(Venue $venue): void
    {
        abort_unless($venue->academy_id === $this->getAcademyId(), 404);
    }
}
