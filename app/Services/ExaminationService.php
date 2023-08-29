<?php

namespace App\Services;

use App\Models\Examination;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ExaminationService
{
    private function __construct(private User $user)
    {}

    public static function forUser(User $user){
       return new static($user);
    }

    public function inviteTo(Examination $examination)
    {
        return $this->user->inviteTo($examination);
    }

    public function attempted(Examination $examination)
    {
        return $examination->attendedBy($this->user);
    }

    public function start(Examination $examination)
    {
        return $this->user->attend($examination);
    }

    public function handleSubmit(Examination $examination, array $answers)
    {
        // all questions with correct answers
        $questions = $examination->questions()
            ->with(['options' => fn ($query) => $query->onlyCorrectOptions()])
            ->get();

        $result =  $this->evaluateResult($questions, $answers);

        return DB::transaction(function () use ($examination, $result) {
            $submission = Submission::create([
                'examination_id' => $examination->id,
                'user_id' => $this->user->id,
                'score' => $result['score'],
                'meta_data' => $result
            ]);

            $this->user->leave($examination);
            return $submission;
        });
    }

    private function evaluateResult($questions, $submittedAnswers)
    {
        $results = [
            'score' => 0,
            'questions_count' => count($questions),
            'answer_count' => count($submittedAnswers),
            'summary' => []
        ];

        // Loop through each question and process the results
        foreach ($questions as $question) {
            $correctOptionValue = $question->options->first()->value;
            $submittedAnswer = $submittedAnswers[$question->id] ?? null;
            $isCorrect = $submittedAnswer === $correctOptionValue;

            // Increment the score
            $results['score']++;

            // Build summary for the question
            $results['summary'][$question->id] = [
                'question' => $question->description,
                'submited_answer' => $submittedAnswer,
                'correct_answer' => $correctOptionValue,
                'is_correct' => $isCorrect,
            ];
        }

        return $results;
    }
}
