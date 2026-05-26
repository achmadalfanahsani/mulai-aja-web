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
        $this->authorize('viewAny', Classroom::class);
        $user = Auth::user();
        $classrooms = Classroom::withCount('students')
            ->when($user->isTeacher(), function($query) use ($user) {
                return $query->where('teacher_id', $user->id);
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
        $this->authorize('create', Classroom::class);
        return view('classrooms.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Classroom::class);
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        Classroom::create([
            'name' => $request->name,
            'description' => $request->description,
            'teacher_id' => Auth::id(),
        ]);

        return redirect()->route('classrooms.index')
            ->with('success', 'Kelas berhasil dibuat.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Classroom $classroom)
    {
        $this->authorize('view', $classroom);

        $classroom->load(['students', 'questionPackages']);
        
        // Data untuk penambahan (Siswa yang belum ada di kelas ini)
        $availableStudents = User::where('role', User::ROLE_STUDENT)
            ->where('is_approved', true)
            ->whereDoesntHave('classrooms', function($q) use ($classroom) {
                $q->where('classrooms.id', $classroom->id);
            })
            ->get();

        // Data untuk penambahan (Paket soal yang belum ada di kelas ini)
        $availablePackages = QuestionPackage::published()
            ->whereDoesntHave('classrooms', function($q) use ($classroom) {
                $q->where('classrooms.id', $classroom->id);
            })
            ->when(Auth::user()->isTeacher(), function($q) {
                return $q->where('user_id', Auth::id());
            })
            ->get();

        return view('classrooms.show', compact('classroom', 'availableStudents', 'availablePackages'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Classroom $classroom)
    {
        $this->authorize('update', $classroom);

        return view('classrooms.edit', compact('classroom'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Classroom $classroom)
    {
        $this->authorize('update', $classroom);

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
        $this->authorize('delete', $classroom);

        $classroom->delete();

        return redirect()->route('classrooms.index')
            ->with('success', 'Kelas berhasil dihapus.');
    }

    /**
     * Add student to classroom.
     */
    public function addStudent(Request $request, Classroom $classroom)
    {
        $this->authorize('update', $classroom);

        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $classroom->students()->syncWithoutDetaching([$request->user_id]);

        return back()->with('success', 'Siswa berhasil ditambahkan ke kelas.');
    }

    /**
     * Remove student from classroom.
     */
    public function removeStudent(Classroom $classroom, User $user)
    {
        $this->authorize('update', $classroom);

        $classroom->students()->detach($user->id);

        return back()->with('success', 'Siswa berhasil dikeluarkan dari kelas.');
    }

    /**
     * Assign package to classroom.
     */
    public function assignPackage(Request $request, Classroom $classroom)
    {
        $this->authorize('update', $classroom);

        $request->validate([
            'question_package_id' => 'required|exists:question_packages,id',
        ]);

        $classroom->questionPackages()->syncWithoutDetaching([$request->question_package_id]);

        return back()->with('success', 'Paket soal berhasil ditugaskan ke kelas.');
    }

    /**
     * Remove package from classroom.
     */
    public function removePackage(Classroom $classroom, QuestionPackage $questionPackage)
    {
        $this->authorize('update', $classroom);

        $classroom->questionPackages()->detach($questionPackage->id);

        return back()->with('success', 'Paket soal berhasil ditarik dari kelas.');
    }
}
