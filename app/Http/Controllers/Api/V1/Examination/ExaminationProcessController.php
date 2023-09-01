<?php

namespace App\Http\Controllers\Api\V1\Examination;

use App\Http\Controllers\Controller;
use App\Http\Resources\ExamResource;
use App\Http\Resources\SubmissionResource;
use App\Models\Examination;
use App\Models\Scopes\OwnerScope;
use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @group Examinations
 */
class ExaminationProcessController extends Controller
{
    /**
     * Handles the submission.
     */
    public function __invoke(Request $request, int $exam_id): JsonResource
    {
        $request->validate([
            'answers' => ['required', 'array'],
            'answers.*' => ['required'],
        ]);

        $examination = Examination::withoutGlobalScope(OwnerScope::class)
            ->findOrFail($exam_id);

        $submission = Submission::create([
            'user_id' => $request->user()->id,
            'examination_id' => $examination->id,
            'answers' => $request->input('answers')
        ]);

        return new SubmissionResource($submission);
    }

}
