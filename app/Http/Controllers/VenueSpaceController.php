<?php

namespace App\Http\Controllers;

use App\Models\Sport;
use App\Models\Venue;
use App\Models\VenueSpace;
use Illuminate\Http\Request;

class VenueSpaceController extends Controller
{
    public function index()
    {
        $spaces = VenueSpace::whereHas('venue', fn ($q) => $q->where('academy_id', auth('academy')->id()))
            ->with(['venue', 'sport'])->latest()->paginate(15);
        return view('Academy.pages.venue_spaces.index', compact('spaces'));
    }

    public function create()
    {
        return view('Academy.pages.venue_spaces.form', $this->formData(new VenueSpace()));
    }

    public function store(Request $request)
    {
        $plan = auth('academy')->user()->currentSubscription()->with('plan')->first()?->plan;
        $spaceCount = VenueSpace::whereHas('venue', fn ($q) => $q->where('academy_id', auth('academy')->id()))->count();
        abort_if($spaceCount >= ($plan?->max_spaces ?? 0), 422, trans('admin.venues.space_limit'));
        VenueSpace::create($this->data($request));
        return to_route('academy.venue-spaces.index')->with('success', trans('admin.venues.space_saved'));
    }

    public function edit(VenueSpace $venueSpace)
    {
        $this->authorizeTenant($venueSpace);
        return view('Academy.pages.venue_spaces.form', $this->formData($venueSpace));
    }

    public function update(Request $request, VenueSpace $venueSpace)
    {
        $this->authorizeTenant($venueSpace);
        $venueSpace->update($this->data($request));
        return to_route('academy.venue-spaces.index')->with('success', trans('admin.venues.space_saved'));
    }

    public function destroy(VenueSpace $venueSpace)
    {
        $this->authorizeTenant($venueSpace);
        abort_if($venueSpace->bookings()->exists(), 422, trans('admin.venues.cannot_delete_booked'));
        $venueSpace->delete();
        return back()->with('success', trans('admin.venues.deleted'));
    }

    private function formData(VenueSpace $space): array
    {
        return ['venueSpace' => $space, 'venues' => Venue::where('academy_id', auth('academy')->id())->where('active', true)->get(), 'sports' => Sport::get()];
    }

    private function data(Request $request): array
    {
        $data = $request->validate([
            'venue_id' => ['required', 'integer'], 'sport_id' => ['nullable', 'exists:sports,id'],
            'name_ar' => ['required', 'string', 'max:255'], 'name_en' => ['required', 'string', 'max:255'],
            'description_ar' => ['nullable', 'string'], 'description_en' => ['nullable', 'string'],
            'space_type' => ['required', 'in:court,field,hall,pool,other'], 'capacity' => ['nullable', 'integer', 'min:1'],
            'slot_minutes' => ['required', 'integer', 'in:30,45,60,90,120'], 'hourly_price' => ['required', 'numeric', 'min:0'],
            'opens_at' => ['required', 'date_format:H:i'], 'closes_at' => ['required', 'date_format:H:i'], 'active' => ['nullable', 'boolean'],
        ]);
        $venue = Venue::where('academy_id', auth('academy')->id())->findOrFail($data['venue_id']);
        return [
            'venue_id' => $venue->id, 'sport_id' => $data['sport_id'] ?? null,
            'name' => ['ar' => $data['name_ar'], 'en' => $data['name_en']],
            'description' => ['ar' => $data['description_ar'] ?? '', 'en' => $data['description_en'] ?? ''],
            'space_type' => $data['space_type'], 'capacity' => $data['capacity'] ?? null, 'slot_minutes' => $data['slot_minutes'],
            'hourly_price' => $data['hourly_price'], 'opens_at' => $data['opens_at'], 'closes_at' => $data['closes_at'],
            'active' => $request->boolean('active'),
        ];
    }

    private function authorizeTenant(VenueSpace $space): void { abort_unless($space->venue?->academy_id === auth('academy')->id(), 404); }
}
