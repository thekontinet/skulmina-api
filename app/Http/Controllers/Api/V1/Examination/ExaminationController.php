<?php

namespace App\Http\Controllers\Api\V1\Examination;

use App\Enums\RoleEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\ExaminationFormRequest;
use App\Http\Resources\ExamResource;
use App\Models\Examination;
use App\Models\Scopes\OwnerScope;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @group Examinations
 */
class ExaminationController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Examination::class, 'examination');
        Examination::addGlobalScope(new OwnerScope);
    }

    /**
     * Get all examinations
     *
     * @return JsonResource
     */
    public function index(): JsonResource
    {
        $examinations = Examination::paginate();
        return ExamResource::collection($examinations);
    }

    /**
     * Create examination
     *
     * Allows teachers to create examinations
     *
     * @return JsonResource
     */
    public function store(ExaminationFormRequest $request): JsonResource
    {
        $data = $request->validated();
        $data['user_id'] = auth()->id();
        unset($data['question_ids']);
        $examination = Examination::create($data);

        // Add questions to exam if questions was sent
        if($request->input('question_ids')){
            $examination->questions()->sync($request->input('question_ids'));
        }

        return new ExamResource($examination);
    }

    /**
     * Update an examination
     *
     * Allows teachers update their examinations and questions
     *
     * @return JsonResource
     */
    public function update(ExaminationFormRequest $request, Examination $examination)
    {
        $data = $request->validated();
        unset($data['question_ids']);
        $examination->update($data);

        // Add questions to exam if questions was sent
        if($request->input('question_ids')){
            $examination->questions()->sync($request->input('question_ids'));
        }

        return new ExamResource($examination);
    }

    /**
     * Show single examination
     *
     * Allows teachers get single examinations
     *
     * @return JsonResource
     */
    public function show(Examination $examination)
    {
        return new ExamResource($examination);
    }

    /**
     * Delete an examination
     *
     * Allows teachers delete their examinations
     *
     * @return JsonResource
     */
    public function destroy(Examination $examination)
    {
        $examination->delete();
        return response()->noContent();
    }
}
