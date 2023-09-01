<?php

namespace App\Http\Controllers;

use App\Enums\RoleEnum;
use App\Http\Resources\ExamResource;
use App\Http\Resources\StudentExamResource;
use App\Models\Examination;
use App\Models\Scopes\OwnerScope;
use App\Models\User;
use Illuminate\Http\Request;

/**
 * @group Examinations
 */
class StudentExaminationController extends Controller
{
    /**
     * Get all student exams
     */
    public function __invoke(Request $request, User $user)
    {
        if($request->user()->hasRole(RoleEnum::STUDENT->value)){
            $user = $request->user();
        }

        $exams = $user->examinations()->paginate();
        return StudentExamResource::collection($exams);
    }
}
