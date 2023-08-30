<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\RoleEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\QuestionFormRequest;
use App\Http\Resources\QuestionResource;
use App\Models\Examination;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

/**
 * @group Examinations
 * @subgroup Manage Questions
 */
class QuestionController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Examination::class, 'examination');
    }

    /**
     * get all question
     */
    public function index(Request $request, Examination $examination): JsonResource
    {
        return QuestionResource::collection($examination->questions()->limit(10)->get());
    }

    /**
     * add new question
     */
    public function store(QuestionFormRequest $request, Examination $examination): JsonResource
    {
        $data = $request->validated();
        $question = $examination->questions()->create($data);
        return new QuestionResource($question);
    }

    /**
     * update question
     */
    public function update(QuestionFormRequest $request, Examination $examination, Question $question): JsonResource
    {
        $data = $request->validated();
        $question->update($data);

        return new QuestionResource($question);
    }

    /**
     * delete question
     */
    public function destroy(Request $request, Examination $examination, Question $question): Response
    {
        $question->delete();
        return response()->noContent();
    }
}
