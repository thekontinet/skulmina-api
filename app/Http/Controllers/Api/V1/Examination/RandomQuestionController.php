<?php

namespace App\Http\Controllers\Api\V1\Examination;

use App\Http\Controllers\Controller;
use App\Http\Resources\QuestionResource;
use App\Models\Examination;
use App\Models\Scopes\OwnerScope;
use Illuminate\Http\Request;

/**
 * @group Examinations
 */
class RandomQuestionController extends Controller
{
    /**
     * Retrieve random questions
     */
    public function __invoke(Request $request, $exam_id)
    {
        $examination = Examination::withoutGlobalScope(OwnerScope::class)
            ->findOrFail($exam_id);

        return QuestionResource::collection($examination->questions()
            ->inRandomOrder()
            ->limit(10)
            ->get());
    }
}
