<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use App\Models\User;
use App\Models\QuestionPackage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class ClassroomController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        Gate::authorize('viewAny', Classroom::class);
        $user = Auth::user();
        $classrooms = Classroom::withCount('students')
            ->when($user->isTeacher(), function($query) use ($user) {
                return $query->whereHas('teachers', function($q) use ($user) {
                    $q->where('users.id', $user->id);
                });
            })
            ->latest()
            ->paginate(10);

        return view('classrooms.index', compact('classrooms'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        Gate::authorize('create', Classroom::class);
        return view('classrooms.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Gate::authorize('create', Classroom::class);
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        Classroom::create([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return redirect()->route('classrooms.index')
            ->with('success', 'Kelas berhasil dibuat.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Classroom $classroom)
    {
        Gate::authorize('view', $classroom);

        $user = Auth::user();
        $classroom->load([
            'students' => function($q) use ($user) {
                if (!$user->isSuperuser() && $user->isAdministrator()) {
                    $q->where('users.created_by_id', $user->id);
                }
            },
            'teachers' => function($q) use ($user) {
                if (!$user->isSuperuser() && $user->isAdministrator()) {
                    $q->where('users.created_by_id', $user->id);
                }
            },
            'questionPackages'
        ]);
        
        // Data untuk penambahan (Siswa yang belum ada di kelas ini)
        $availableStudents = User::where('role', User::ROLE_STUDENT)
            ->where('is_approved', true)
            ->whereDoesntHave('classrooms', function($q) use ($classroom) {
                $q->where('classrooms.id', $classroom->id);
            })
            ->when(!$user->isSuperuser(), function($q) use ($user) {
                $creatorId = $user->isTeacher() ? $user->created_by_id : $user->id;
                return $q->where('created_by_id', $creatorId);
            })
            ->get();

        // Data untuk penambahan (Paket soal yang belum ada di kelas ini)
        $availablePackages = QuestionPackage::published()
            ->whereDoesntHave('classrooms', function($q) use ($classroom) {
                $q->where('classrooms.id', $classroom->id);
            })
            ->when(!Auth::user()->isSuperuser(), function($q) {
                if (Auth::user()->isTeacher()) {
                    return $q->where('user_id', Auth::id());
                } elseif (Auth::user()->isAdministrator()) {
                    return $q->where(function($query) {
                        $query->where('user_id', Auth::id())
                              ->orWhereHas('user', function($u) {
                                  $u->where('created_by_id', Auth::id());
                              });
                    });
                }
            })
            ->get();

        // Data untuk penambahan (Guru yang belum ada di kelas ini) - Hanya untuk Admin
        $availableTeachers = collect();
        if (Auth::user()->isAdministrator() || Auth::user()->isSuperuser()) {
            $availableTeachers = User::where('role', User::ROLE_TEACHER)
                ->where('is_approved', true)
                ->whereDoesntHave('managedClassrooms', function($q) use ($classroom) {
                    $q->where('classrooms.id', $classroom->id);
                })
                ->when(!Auth::user()->isSuperuser(), function($q) {
                    return $q->where('created_by_id', Auth::id());
                })
                ->get();
        }

        return view('classrooms.show', compact('classroom', 'availableStudents', 'availablePackages', 'availableTeachers'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Classroom $classroom)
    {
        Gate::authorize('update', $classroom);

        return view('classrooms.edit', compact('classroom'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Classroom $classroom)
    {
        Gate::authorize('update', $classroom);

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $classroom->update($request->only('name', 'description'));

        return redirect()->route('classrooms.index')
            ->with('success', 'Data kelas berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Classroom $classroom)
    {
        Gate::authorize('delete', $classroom);

        $classroom->delete();

        return redirect()->route('classrooms.index')
            ->with('success', 'Kelas berhasil dihapus.');
    }

    /**
     * Add teacher to classroom (Admin only).
     */
    public function addTeacher(Request $request, Classroom $classroom)
    {
        if (!Auth::user()->isAdministrator() && !Auth::user()->isSuperuser()) {
            abort(403);
        }

        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $userToAdd = User::findOrFail($request->user_id);
        if (!Auth::user()->isSuperuser() && $userToAdd->created_by_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses untuk menambahkan user ini.');
        }

        $classroom->teachers()->syncWithoutDetaching([$request->user_id]);

        return back()->with('success', 'Guru berhasil ditambahkan ke kelas.');
    }

    /**
     * Remove teacher from classroom (Admin only).
     */
    public function removeTeacher(Classroom $classroom, User $user)
    {
        if (!Auth::user()->isAdministrator() && !Auth::user()->isSuperuser()) {
            abort(403);
        }

        if (!Auth::user()->isSuperuser() && $user->created_by_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses untuk mengeluarkan user ini.');
        }

        $classroom->teachers()->detach($user->id);

        return back()->with('success', 'Guru berhasil dikeluarkan dari kelas.');
    }

    /**
     * Add student to classroom.
     */
    public function addStudent(Request $request, Classroom $classroom)
    {
        Gate::authorize('update', $classroom);

        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $userToAdd = User::findOrFail($request->user_id);
        $creatorId = Auth::user()->isTeacher() ? Auth::user()->created_by_id : Auth::id();
        if (!Auth::user()->isSuperuser() && $userToAdd->created_by_id !== $creatorId) {
            abort(403, 'Anda tidak memiliki akses untuk menambahkan user ini.');
        }

        $classroom->students()->syncWithoutDetaching([$request->user_id]);

        return back()->with('success', 'Siswa berhasil ditambahkan ke kelas.');
    }

    /**
     * Remove student from classroom.
     */
    public function removeStudent(Classroom $classroom, User $user)
    {
        Gate::authorize('update', $classroom);

        if (!Auth::user()->isSuperuser() && Auth::user()->isAdministrator() && $user->created_by_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses untuk mengeluarkan user ini.');
        }

        $classroom->students()->detach($user->id);

        return back()->with('success', 'Siswa berhasil dikeluarkan dari kelas.');
    }

    /**
     * Assign package to classroom.
     */
    public function assignPackage(Request $request, Classroom $classroom)
    {
        Gate::authorize('update', $classroom);

        $request->validate([
            'question_package_id' => 'required|exists:question_packages,id',
        ]);

        $package = QuestionPackage::findOrFail($request->question_package_id);
        
        if (!Auth::user()->isSuperuser()) {
            if (Auth::user()->isTeacher() && $package->user_id !== Auth::id()) {
                abort(403, 'Anda tidak memiliki akses untuk menugaskan paket soal ini.');
            } elseif (Auth::user()->isAdministrator()) {
                $isOwn = $package->user_id === Auth::id();
                $isFromSubordinate = $package->user && $package->user->created_by_id === Auth::id();
                if (!$isOwn && !$isFromSubordinate) {
                    abort(403, 'Anda tidak memiliki akses untuk menugaskan paket soal ini.');
                }
            }
        }

        $classroom->questionPackages()->syncWithoutDetaching([$request->question_package_id]);

        return back()->with('success', 'Paket soal berhasil ditugaskan ke kelas.');
    }

    /**
     * Remove package from classroom.
     */
    public function removePackage(Classroom $classroom, QuestionPackage $questionPackage)
    {
        Gate::authorize('update', $classroom);

        $classroom->questionPackages()->detach($questionPackage->id);

        return back()->with('success', 'Paket soal berhasil ditarik dari kelas.');
    }
}
