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

    public function test_invalid_signed_codes_cannot_fall_back_to_legacy_lookup(): void
    {
        $student = new AcademyStudent();
        $student->id = 41;
        $student->academy_id = 7;
        $code = MembershipCode::make($student);
        $last = substr($code, -1);
        foreach ([substr($code, 0, -1).($last === 'A' ? 'B' : 'A'), substr($code, 0, -1), $code.'extra', 'HZ-7-41-invalid'] as $invalid) {
            $this->assertNull(MembershipCode::studentId($invalid, 7));
        }
    }

    public function test_signed_codes_accept_scanner_whitespace_and_lowercase(): void
    {
        $student = new AcademyStudent();
        $student->id = 41;
        $student->academy_id = 7;
        $this->assertSame(41, MembershipCode::studentId(' '.strtolower(MembershipCode::make($student))."\r\n", 7));
    }
}
