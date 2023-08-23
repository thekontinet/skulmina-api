<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\RoleEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\ExaminationFormRequest;
use App\Http\Resources\ExamResource;
use App\Models\Examination;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @group Examinations
 */
class ExaminationController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Examination::class, 'examination');
    }

    /**
     * Get all examinations
     *
     * Get all examinations based on authenticated user role
     * <aside class='info'>
     * The examination retrieval is determined by the authenticated user's role:
     * <strong>Student:</strong> Retrieve all examinations assigned to students. <br>
     * <strong>Teacher:</strong> Retrieve all examinations authored by teachers.
     * </aside>
     *
     * @return JsonResource
     */
    public function index(): JsonResource
    {
        /** @var User */
        $user = auth()->user();

        if($user->hasRole(RoleEnum::STUDENT->value)){
            $examinations = Examination::whereHas('seats', function($query) use ($user){
                $query->where('user_id', $user->id);
            })->paginate();
        }
        else{
            $examinations = Examination::whereUserId($user->id)->paginate();
        }

        return ExamResource::collection($examinations);
    }

    /**
     * Create examination
     *
     * @return JsonResource
     */
    public function store(ExaminationFormRequest $request): JsonResource
    {
        $data = $request->validated();
        $data['user_id'] = auth()->id();
        return new ExamResource(Examination::create($data));
    }

    /**
     * Update an examinations
     *
     * @return JsonResource
     */
    public function update(ExaminationFormRequest $request, Examination $examination)
    {
        $data = $request->validated();
        $examination->update($data);

        return new ExamResource($examination);
    }

    /**
     * Show single examination
     *
     * @return JsonResource
     */
    public function show(Examination $examination)
    {
        return new ExamResource($examination);
    }

    /**
     * Destroy examinations
     *
     * @return JsonResource
     */
    public function destroy(Examination $examination)
    {
        $examination->delete();
        return response()->noContent();
    }
}
