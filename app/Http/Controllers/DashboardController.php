<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\QuestionPackage;
use App\Models\QuestionAttempt;
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

        if ($user->isSuperuser()) {
            $stats = [
                'total_users' => User::whereNot('role', User::ROLE_SUPERUSER)->count(),
                'total_students' => User::where('role', User::ROLE_STUDENT)->count(),
                'total_teachers' => User::where('role', User::ROLE_TEACHER)->count(),
                'total_administrators' => User::where('role', User::ROLE_ADMINISTRATOR)->count(),
                'pending_approvals' => User::pendingApproval()->count(),
            ];
        } elseif ($user->isAdministrator()) {
            $stats = [
                'total_users' => User::where('created_by_id', $user->id)->count(),
                'total_students' => User::where('role', User::ROLE_STUDENT)->where('created_by_id', $user->id)->count(),
                'total_teachers' => User::where('role', User::ROLE_TEACHER)->where('created_by_id', $user->id)->count(),
                'total_administrators' => 0, // Admin cannot create/see other admins
                'pending_approvals' => User::where('created_by_id', $user->id)->where('is_approved', false)->count(),
            ];
        } elseif ($user->isTeacher()) {
            $stats = [
                'total_packages' => QuestionPackage::where('user_id', $user->id)->count(),
                'total_attempts' => QuestionAttempt::whereHas('questionPackage', function($q) use ($user) {
                    $q->where('user_id', $user->id);
                })->count(),
            ];
        } elseif ($user->isStudent()) {
            $stats = [
                'total_attempts' => QuestionAttempt::where('user_id', $user->id)->count(),
                'completed_exams' => QuestionAttempt::where('user_id', $user->id)->where('is_completed', true)->count(),
                'average_score' => QuestionAttempt::where('user_id', $user->id)->where('is_completed', true)->avg('total_score') ?? 0,
            ];
        }

        return view('pages.dashboard', compact('stats'));
    }
}
