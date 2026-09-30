<?php

namespace App\Http\Middleware;

use App\Support\FacilityTerminology;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApplyFacilityTerminology
{
    /**
     * Handle an incoming request.
     *
     * Ensures that gym / health center / academy terminology and global view
     * variables are dynamically applied based on the authenticated partner's business type.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth('academy')->user();
        if ($user) {
            $type = ($user instanceof \App\Models\Academies)
                ? ($user->business_type ?? 'academy')
                : ($user instanceof \App\Models\PartnerUser ? ($user->academy?->business_type ?? 'academy') : 'academy');

            FacilityTerminology::applyDynamicTranslations($type);

            $isGym = in_array($type, ['gym', 'health_center', 'hybrid']);
            $isHealth = in_array($type, ['health_center', 'hybrid']);

            view()->share([
                'facilityType'          => $type,
                'isGymFacility'         => $isGym,
                'isHealthFacility'      => $isHealth,
                'hasCampsModule'        => in_array($type, ['academy', 'hybrid']),
                'termStudent'           => facility_term('student', $type),
                'termStudentsPlural'    => facility_term('students_list', $type),
                'termAddStudent'        => facility_term('add_student', $type),
                'termCoaches'           => facility_term('coaches_list', $type),
                'termTraining'          => facility_term('training', $type, true),
                'termAttendance'        => facility_term('attendance', $type),
                'termSubscriptions'     => facility_term('subscription', $type, true),
                'termGroups'            => facility_term('group', $type, true),
            ]);
        }

        return $next($request);
    }
}
