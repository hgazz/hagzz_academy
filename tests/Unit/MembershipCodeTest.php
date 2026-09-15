<?php

namespace Tests\Unit;

use App\Models\AcademyStudent;
use App\Support\MembershipCode;
use Tests\TestCase;

class MembershipCodeTest extends TestCase
{
    public function test_membership_code_resolves_for_its_own_academy(): void
    {
        $student = new AcademyStudent();
        $student->id = 41;
        $student->academy_id = 7;

        $code = MembershipCode::make($student);

        $this->assertSame(41, MembershipCode::studentId($code, 7));
    }

    public function test_membership_code_is_rejected_by_another_academy(): void
    {
        $student = new AcademyStudent();
        $student->id = 41;
        $student->academy_id = 7;

        $this->assertNull(MembershipCode::studentId(MembershipCode::make($student), 8));
    }

    public function test_tampered_membership_code_is_rejected(): void
    {
        $student = new AcademyStudent();
        $student->id = 41;
        $student->academy_id = 7;
        $code = MembershipCode::make($student);

        $this->assertNull(MembershipCode::studentId(str_replace('-41-', '-42-', $code), 7));
    }
}
