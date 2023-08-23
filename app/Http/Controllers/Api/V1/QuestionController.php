<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\QuestionFormRequest;
use App\Http\Resources\QuestionResource;
use App\Models\Examination;
use App\Models\Question;
use Illuminate\Http\JsonResponse;
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
        $this->authorizeResource(Question::class, 'question');
    }

    /**
     * get all question
     */
    public function index(Examination $examination): JsonResource
    {
        return QuestionResource::collection($examination->questions()->limit(10)->get());
    }

    /**
     * add new question
     */
    public function store(QuestionFormRequest $request, Examination $examination): JsonResource
    {
        $data = $request->validated();

        $question = DB::transaction(function() use($data, $examination){
            $options = $data['options'];
            unset($data['options']);

            $question = $examination->questions()->create($data);
            $question->options()->createMany(
                array_map(
                    fn($option) => [
                        'value' => $option['value'],
                        'is_correct' => $option['correct']
                    ],
                    $options
                )
            );

            return $question;
        });

        return new QuestionResource($question);
    }

    /**
     * update question
     */
    public function update(QuestionFormRequest $request, Examination $examination, Question $question): JsonResource
    {
        $data = $request->validated();

        DB::transaction(function() use ($question, $data){
            $options = $data['options'];
            unset($data['options']);

            $question->options()->delete();
            $question->update($data);

            $question->options()->createMany(
                array_map(
                    fn($option) => [
                        'value' => $option['value'],
                        'is_correct' => $option['correct']
                    ],
                    $options
                )
            );

        });

        return new QuestionResource($question);
    }

    /**
     * delete question
     */
    public function destroy(Examination $examination, Question $question): Response
    {
        if($examination->user_id !== auth()->id()) abort(403);

        $question->delete();

        return response()->noContent();
    }
}
