<?php

namespace App\Http\Controllers;

use App\Models\QuestionPackage;
use App\Models\QuestionAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuestionPackageController extends Controller {
    /**
     * Tampilkan semua paket soal yang dibuat oleh guru/admin yang sedang login.
     */
    public function index(Request $request) {
        $user = Auth::user();
        
        $query = QuestionPackage::query();

        // Administrator & Superuser bisa melihat semua, Teacher hanya melihat miliknya sendiri
        if (!$user->isAdministrator() && !$user->isSuperuser()) {
            $query->where('user_id', $user->id);
        }

        // Filter by name
        if ($request->filled('q')) {
            $query->where('name', 'like', '%' . $request->q . '%');
        }

        // Filter by status
        if ($request->filled('status')) {
            $isPublished = $request->status === 'published';
            $query->where('is_published', $isPublished);
        }

        // Filter by question type
        if ($request->filled('type')) {
            $type = $request->query('type');
            
            if ($type === 'multiple_choice') {
                $query->whereDoesntHave('questions', function ($q) {
                    $q->where('question_type', 'essay');
                });
            } elseif ($type === 'essay') {
                $query->whereDoesntHave('questions', function ($q) {
                    $q->where('question_type', 'multiple_choice');
                });
            } elseif ($type === 'mixed') {
                $query->whereHas('questions', function ($q) {
                    $q->where('question_type', 'multiple_choice');
                })->whereHas('questions', function ($q) {
                    $q->where('question_type', 'essay');
                });
            }
        }

        $packages = $query->withCount('questions')->latest()->paginate(10)->withQueryString();

        return view('question_packages.index', compact('packages'));
    }

    /**
     * Tampilkan form pembuatan paket soal baru.
     */
    public function create() {
        return view('question_packages.create');
    }

    /**
     * Simpan paket soal baru ke database.
     */
    public function store(Request $request) {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration_minutes' => 'required|integer|min:1|max:480',
            'passing_score' => 'nullable|integer|min:0|max:100',
            'attempt_limit' => 'nullable|integer|min:1',
            'shuffle_questions' => 'nullable|boolean',
            'shuffle_answers' => 'nullable|boolean',
        ]);

        $validated['user_id'] = Auth::id();
        $validated['shuffle_questions'] = $request->has('shuffle_questions');
        $validated['shuffle_answers'] = $request->has('shuffle_answers');
        $validated['is_published'] = false; // Default draft

        QuestionPackage::create($validated);

        return redirect()->route('question-packages.index')
            ->with('success', 'Paket soal berhasil dibuat! Silakan tambahkan pertanyaan.');
    }

    /**
     * Tampilkan form edit paket soal.
     */
    public function edit(QuestionPackage $questionPackage) {
        // Cek otorisasi
        $this->authorizeAccess($questionPackage);

        return view('question_packages.edit', compact('questionPackage'));
    }

    /**
     * Perbarui paket soal di database.
     */
    public function update(Request $request, QuestionPackage $questionPackage) {
        $this->authorizeAccess($questionPackage);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration_minutes' => 'required|integer|min:1|max:480',
            'passing_score' => 'nullable|integer|min:0|max:100',
            'attempt_limit' => 'nullable|integer|min:1',
            'shuffle_questions' => 'nullable|boolean',
            'shuffle_answers' => 'nullable|boolean',
        ]);

        $validated['shuffle_questions'] = $request->has('shuffle_questions');
        $validated['shuffle_answers'] = $request->has('shuffle_answers');

        $questionPackage->update($validated);

        return redirect()->route('question-packages.index')
            ->with('success', 'Paket soal berhasil diperbarui!');
    }

    /**
     * Hapus paket soal dari database (soft delete).
     */
    public function destroy(QuestionPackage $questionPackage) {
        $this->authorizeAccess($questionPackage);

        $questionPackage->delete();

        return redirect()->route('question-packages.index')
            ->with('success', 'Paket soal berhasil dihapus!');
    }

    /**
     * Toggle status publikasi paket soal.
     */
    public function togglePublish($question_package) {
        if (!($question_package instanceof QuestionPackage)) {
            $question_package = QuestionPackage::findOrFail($question_package);
        }

        $this->authorizeAccess($question_package);

        // Validasi: Minimal 1 soal sebelum bisa publish
        if (!$question_package->is_published && $question_package->activeQuestions()->count() < 1) {
            return redirect()->back()
                ->with('error', 'Gagal mempublikasikan. Paket soal minimal harus memiliki 1 soal yang aktif!');
        }

        $question_package->update([
            'is_published' => !$question_package->is_published
        ]);

        return redirect()->back()
            ->with('success', "Status paket soal berhasil diubah!");
    }

    /**
     * Tampilkan hasil pengerjaan siswa untuk paket soal ini.
     */
    public function results(QuestionPackage $questionPackage) {
        $this->authorizeAccess($questionPackage);

        $attempts = QuestionAttempt::where('question_package_id', $questionPackage->id)
            ->with('user')
            ->latest()
            ->paginate(20);

        return view('question_packages.results', compact('questionPackage', 'attempts'));
    }

     /**
      * Helper untuk membatasi akses edit/delete
      */
    private function authorizeAccess(QuestionPackage $package) {
        $user = Auth::user();
        if (!$user->isAdministrator() && !$user->isSuperuser() && $package->user_id !== $user->id) {
            abort(403, 'Anda tidak memiliki hak akses untuk paket soal ini.');
        }
    }
}
