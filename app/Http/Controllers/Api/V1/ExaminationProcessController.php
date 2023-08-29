<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ExamResource;
use App\Http\Resources\SubmissionResource;
use App\Models\Examination;
use App\Models\Question;
use App\Models\Submission;
use App\Services\AttendanceService;
use App\Services\ExaminationService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;

/**
 * @group Examinations
 */
class ExaminationProcessController extends Controller
{
    /**
     * Start examination
     */
    public function start(Request $request, Examination $examination)
    {
        $this->authorize('submit', $examination);
        $request->user()->attend($examination);
        return new ExamResource($examination);
    }

    /**
     * Handles the submission.
     */
    public function submit(Request $request, Examination $examination): JsonResource
    {
        $this->authorize('submit', $examination);
        $request->validate([
            'answers' => ['required', 'array'],
            'answers.*' => ['required'],
        ]);

        $submission = ExaminationService::forUser($request->user())
            ->handleSubmit($examination, $request->input('answers'));

        return new SubmissionResource($submission);
    }

}
