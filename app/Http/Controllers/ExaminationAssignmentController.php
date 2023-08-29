<?php

namespace App\Http\Controllers;

use App\Models\Examination;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ExaminationAssignmentController extends Controller
{
    /**
     * Assign students to exam
     */
    public function store(Request $request, Examination $examination)
    {
        $this->authorize('update', $examination);
        $request->validate([
            'student_ids' => ['required', 'array'],
            'student_ids.*' => ['required', 'exists:users,id']
        ]);

        $examination->seats()->createMany(array_map(function ($studentId) {
            return ['user_id' => $studentId];
        }, $request->input('student_ids')));

        return response(status:201);
    }

    /**
     * Assign Student to exam
     */
    public function destroy(Request $request, Examination $examination)
    {
        $this->authorize('update', $examination);
        $request->validate([
            'student_ids' => ['required', 'array'],
            'student_ids.*' => ['required', 'exists:users,id']
        ]);

        $examination->seats()->whereIn('user_id', $request->input('student_ids'))->delete();

        return response()->noContent();
    }
}
