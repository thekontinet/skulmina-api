<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\RoleEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\CourseFormRequest;
use App\Http\Resources\CourseResource;
use App\Models\Course;
use App\Rules\HasRole;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

// TODO: Create a policy for this class
/**
 * @group Courses
 */
class CourseController extends Controller
{
    /**
     * Get all courses.
     *
     * @return JsonResource
     */
    public function index()
    {
        return CourseResource::collection(Course::paginate());
    }

    /**
     * Create new course.
     *
     * @return JsonResource
     */
    public function store(CourseFormRequest $request)
    {
        $request->validated();

        $course = Course::create($request->safe()->only(['title', 'code', 'teacher_id']));

        return new CourseResource($course);
    }

    /**
     * Show a single course.
     *
     * @return JsonResource
     */
    public function show(Course $course)
    {
        return new CourseResource($course);
    }

    /**
     * Update course.
     *
     * @return JsonResource
     */
    public function update(CourseFormRequest $request, Course $course)
    {
        $request->validated();
        $course->fill($request->safe()->only(['title', 'code', 'teacher_id']));
        $course->save();

        return new CourseResource($course);
    }

    /**
     * Delete course.
     *
     * @return JsonResource
     */
    public function destroy(Course $course)
    {
        $course->delete();

        return response()->noContent();
    }

    /**
     * Enroll student to course.
     *
     * @return JsonResource
     */
    public function enroll(Request $request, Course $course)
    {
        $request->validate([
            'student_id' => ['required', 'exists:users,id', new HasRole(RoleEnum::STUDENT),
        ]]);

        $course->students()->attach($request->input('student_id'));

        return response()->noContent();
    }
}
