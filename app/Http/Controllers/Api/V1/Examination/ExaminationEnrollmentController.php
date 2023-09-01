<?php

namespace App\Http\Controllers\Api\V1\Examination;

use App\Http\Controllers\Controller;
use App\Models\Examination;
use Illuminate\Http\Request;

/**
 * @group Examinations
 */
class ExaminationEnrollmentController extends Controller
{
    /**
     * Enroll student to exam
     */
    public function __invoke(Request $request, Examination $examination)
    {
        $this->authorize('update', $examination);

        $request->validate([
            'student_ids' => ['required', 'array'],
            'student_ids.*' => ['required', 'exists:users,id']
        ]);


        $examination->students()->attach($request->input('student_ids'));

        return response(status:201);
    }
}
