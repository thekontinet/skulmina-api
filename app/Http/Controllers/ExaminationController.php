<?php

namespace App\Http\Controllers;

use App\Enums\RoleEnum;
use App\Http\Requests\ExaminationFormRequest;
use App\Http\Resources\ExamResource;
use App\Models\Examination;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExaminationController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Examination::class, 'examination');
    }

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

    public function store(ExaminationFormRequest $request): JsonResource
    {
        $data = $request->validated();
        $data['user_id'] = auth()->id();
        return new ExamResource(Examination::create($data));
    }

    public function update(ExaminationFormRequest $request, Examination $examination)
    {
        $data = $request->validated();
        $examination->update($data);

        return new ExamResource($examination);
    }

    public function show(Examination $examination)
    {
        return new ExamResource($examination);
    }

    public function destroy(Examination $examination)
    {
        $examination->delete();
        return response()->noContent();
    }
}
