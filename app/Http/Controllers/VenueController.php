<?php

namespace App\Http\Controllers;

use App\Models\Venue;
use Illuminate\Http\Request;

class VenueController extends Controller
{
    public function index()
    {
        $venues = Venue::where('academy_id', auth('academy')->id())->withCount('spaces')->latest()->paginate(15);
        return view('Academy.pages.venues.index', compact('venues'));
    }

    public function create() { return view('Academy.pages.venues.form', ['venue' => new Venue()]); }

    public function store(Request $request)
    {
        $plan = auth('academy')->user()->currentSubscription()->with('plan')->first()?->plan;
        abort_if(Venue::where('academy_id', auth('academy')->id())->count() >= ($plan?->max_venues ?? 0), 422, trans('admin.venues.location_limit'));
        Venue::create($this->data($request) + ['academy_id' => auth('academy')->id()]);
        return to_route('academy.venues.index')->with('success', trans('admin.venues.saved'));
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
        return to_route('academy.venues.index')->with('success', trans('admin.venues.saved'));
    }

    public function destroy(Venue $venue)
    {
        $this->authorizeTenant($venue);
        abort_if($venue->spaces()->whereHas('bookings')->exists(), 422, trans('admin.venues.cannot_delete_booked'));
        $venue->delete();
        return back()->with('success', trans('admin.venues.deleted'));
    }

    private function data(Request $request): array
    {
        $data = $request->validate([
            'name_ar' => ['required', 'string', 'max:255'], 'name_en' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'], 'address' => ['required', 'string', 'max:500'],
            'timezone' => ['required', 'timezone'], 'currency' => ['required', 'in:EGP,QAR,SAR,AED'],
            'active' => ['nullable', 'boolean'],
        ]);
        return [
            'name' => ['ar' => $data['name_ar'], 'en' => $data['name_en']], 'phone' => $data['phone'] ?? null,
            'address' => $data['address'], 'timezone' => $data['timezone'], 'currency' => $data['currency'],
            'active' => $request->boolean('active'),
        ];
    }

    private function authorizeTenant(Venue $venue): void { abort_unless($venue->academy_id === auth('academy')->id(), 404); }
}
