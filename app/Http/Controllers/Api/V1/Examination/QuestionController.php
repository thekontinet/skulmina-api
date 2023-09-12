<?php

namespace App\Http\Controllers\Api\V1\Examination;

use App\Enums\RoleEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\QuestionFormRequest;
use App\Http\Resources\QuestionResource;
use App\Models\Question;
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
        $this->authorizeResource(Question::class, 'question');
    }

    /**
     * get all question
     */
    public function index(): JsonResource
    {
        return QuestionResource::collection(Question::paginate());
    }

    /**
     * get single question
     */
    public function show(Question $question): JsonResource
    {
        return new QuestionResource($question);
    }

    /**
     * add new question
     */
    public function store(QuestionFormRequest $request): JsonResource
    {
        $request->validated(['description', 'answers', 'options']);

        $question = DB::transaction(function() use($request){
            $question = Question::create($request->only('description'));
            $question->options()->createMany($request->getFormattedOptions());
            return $question;
        });

        if($request->input('examination_id')){
            $question->examinations()->sync([$request->input('examination_id')]);
        }

        return new QuestionResource($question);
    }

    /**
     * update question
     */
    public function update(QuestionFormRequest $request, Question $question): JsonResource
    {
        $question = DB::transaction(function() use($question, $request){
            $question->update($request->only('description'));

            // Delete existing options and add new ones
            $question->options()->delete();
            $question->options()->createMany($request->getFormattedOptions());

            return $question;
        });

        return new QuestionResource($question);
    }

    /**
     * delete question
     */
    public function destroy(Question $question): Response
    {
        $question->examinations()->detach();
        $question->delete();
        return response()->noContent();
    }
}
