<?php

namespace App\Http\Controllers;

use App\Http\Resources\QuestionResource;
use App\Models\Option;
use App\Models\Question;
use Illuminate\Http\Request;

class QuestionOptionController extends Controller
{
    /**
     * Add option to question
     */
    public function store(Request $request, Question $question)
    {
        if(!$request->user()->can('update', $question->examination)){
            abort(403);
        }

        $data = $request->validate([
            'value' => ['required', 'string'],
            'is_correct' => ['required', 'boolean']
        ]);

        $question->options()->create($data);

        return new QuestionResource($question);
    }

    /**
     * Delete Options
     */
    public function destroy(Request $request, Question $question, Option $option)
    {
        if(!$request->user()->can('delete', $question->examination)){
            abort(403);
        }

        $question->options()->where('id', $option->id)->delete();
        return response()->noContent();
    }
}
