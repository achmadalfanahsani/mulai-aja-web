<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\QuestionPackage;
use App\Models\QuestionAttempt;
use App\Models\Question;
use App\Models\QuestionResponse;
use App\Models\Classroom;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function index()
    {
        $user = Auth::user();
        $stats = [];
        $recent_attempts = [];

        if ($user->isSuperuser()) {
            $stats = [
                'total_users' => User::whereNot('role', User::ROLE_SUPERUSER)->count(),
                'total_students' => User::where('role', User::ROLE_STUDENT)->count(),
                'total_teachers' => User::where('role', User::ROLE_TEACHER)->count(),
                'total_administrators' => User::where('role', User::ROLE_ADMINISTRATOR)->count(),
                'total_packages' => QuestionPackage::count(),
                'total_questions' => Question::count(),
                'total_classrooms' => Classroom::count(),
                'pending_approvals' => User::pendingApproval()->count(),
                'average_score' => QuestionAttempt::where('is_completed', true)->avg('total_score') ?? 0,
            ];
            $recent_attempts = QuestionAttempt::with(['user', 'questionPackage'])
                ->latest()
                ->take(5)
                ->get();
        } elseif ($user->isAdministrator()) {
            $stats = [
                'total_users' => User::where('created_by_id', $user->id)->count(),
                'total_students' => User::where('role', User::ROLE_STUDENT)->where('created_by_id', $user->id)->count(),
                'total_teachers' => User::where('role', User::ROLE_TEACHER)->where('created_by_id', $user->id)->count(),
                'total_administrators' => 0,
                'total_packages' => QuestionPackage::whereHas('user', function ($q) use ($user) {
                    $q->where('created_by_id', $user->id);
                })->count(),
                'total_classrooms' => Classroom::whereHas('teachers', function ($q) use ($user) {
                    $q->where('created_by_id', $user->id);
                })->count(),
                'pending_approvals' => User::where('created_by_id', $user->id)
                    ->where('is_approved', false)
                    ->count(),
                'average_score' => QuestionAttempt::whereHas('user', function ($q) use ($user) {
                    $q->where('created_by_id', $user->id);
                })->where('is_completed', true)->avg('total_score') ?? 0,
            ];
            $recent_attempts = QuestionAttempt::whereHas('questionPackage.user', function ($q) use ($user) {
                $q->where('created_by_id', $user->id);
            })
                ->with(['user', 'questionPackage'])
                ->latest()
                ->take(5)
                ->get();
        } elseif ($user->isTeacher()) {
            $stats = [
                'total_packages' => QuestionPackage::where('user_id', $user->id)->count(),
                'total_attempts' => QuestionAttempt::whereHas('questionPackage', function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                })->count(),
                'total_students' => User::whereHas('questionAttempts.questionPackage', function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                })->distinct()->count(),
                'total_classrooms' => Classroom::whereHas('teachers', function ($q) use ($user) {
                    $q->where('users.id', $user->id);
                })->count(),
                'pending_essays' => QuestionResponse::whereHas('questionAttempt.questionPackage', function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                })->whereHas('question', function ($q) {
                    $q->where('question_type', Question::TYPE_ESSAY);
                })->whereNull('is_correct')->count(),
                'average_score' => QuestionAttempt::whereHas('questionPackage', function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                })->where('is_completed', true)->avg('total_score') ?? 0,
            ];
            $recent_attempts = QuestionAttempt::whereHas('questionPackage', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
                ->with(['user', 'questionPackage'])
                ->latest()
                ->take(5)
                ->get();
        } elseif ($user->isStudent()) {
            $stats = [
                'total_attempts' => QuestionAttempt::where('user_id', $user->id)->count(),
                'completed_exams' => QuestionAttempt::where('user_id', $user->id)->where('is_completed', true)->count(),
                'average_score' => QuestionAttempt::where('user_id', $user->id)->where('is_completed', true)->avg('total_score') ?? 0,
                'highest_score' => QuestionAttempt::where('user_id', $user->id)->where('is_completed', true)->max('total_score') ?? 0,
                'lowest_score' => QuestionAttempt::where('user_id', $user->id)->where('is_completed', true)->min('total_score') ?? 0,
                'total_time_spent' => QuestionAttempt::where('user_id', $user->id)->sum('time_spent_seconds') ?? 0,
                'available_exams' => QuestionPackage::published()
                    ->whereHas('classrooms.students', function ($q) use ($user) {
                        $q->where('users.id', $user->id);
                    })->count(),
            ];
            $recent_attempts = QuestionAttempt::where('user_id', $user->id)
                ->with('questionPackage')
                ->latest()
                ->take(5)
                ->get();
        }

        return view('pages.dashboard', compact('stats', 'recent_attempts'));
    }
}
