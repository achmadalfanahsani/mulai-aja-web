<?php

namespace App\Http\Controllers;

use App\Models\QuestionPackage;
use App\Models\QuestionAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class QuestionPackageController extends Controller {
    /**
     * Tampilkan daftar paket soal.
     */
    public function index(Request $request) {
        $user = Auth::user();
        $query = QuestionPackage::query();

        // Admin & Superuser bisa melihat semua, Teacher hanya miliknya
        if ($user->isTeacher()) {
            $query->where('user_id', $user->id);
        } elseif ($user->isAdministrator()) {
            $query->where(function($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->orWhereHas('user', function($u) use ($user) {
                      $u->where('created_by_id', $user->id);
                  });
            });
        }

        // Pencarian Nama
        if ($request->filled('q')) {
            $query->where('name', 'like', '%' . $request->q . '%');
        }

        // Filter Status
        if ($request->filled('status')) {
            $isPublished = $request->status === 'published';
            $query->where('is_published', $isPublished);
        }

        // Filter by package type (from form or legacy URL param)
        $packageType = $request->input('package_type') ?? $request->input('type');
        if ($packageType) {
            $query->where('package_type', $packageType);
        }

        $packages = $query->with('user')->withCount('questions')->latest()->paginate(10)->withQueryString();

        return view('question_packages.index', compact('packages'));
    }

    /**
     * Tampilkan form pembuatan paket soal baru.
     */
    public function create(Request $request) {
        $type = $request->query('type');
        return view('question_packages.create', compact('type'));
    }

    /**
     * Simpan paket soal baru ke database.
     */
    public function store(Request $request) {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'package_type' => 'required|in:multiple_choice,essay,mixed',
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

        $package = QuestionPackage::create($validated);

        return redirect()->route('question-packages.index', ['type' => $package->package_type])
            ->with('success', 'Paket soal berhasil dibuat! Silakan tambahkan pertanyaan.');
    }

    /**
     * Tampilkan form edit paket soal.
     */
    public function edit(QuestionPackage $questionPackage) {
        // Cek otorisasi
        Gate::authorize('update', $questionPackage);

        return view('question_packages.edit', compact('questionPackage'));
    }

    /**
     * Perbarui paket soal di database.
     */
    public function update(Request $request, QuestionPackage $question_package) {
        Gate::authorize('update', $question_package);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'package_type' => 'required|in:multiple_choice,essay,mixed',
            'duration_minutes' => 'required|integer|min:1|max:480',
            'passing_score' => 'nullable|integer|min:0|max:100',
            'attempt_limit' => 'nullable|integer|min:1',
            'shuffle_questions' => 'nullable|boolean',
            'shuffle_answers' => 'nullable|boolean',
        ]);

        $validated['shuffle_questions'] = $request->has('shuffle_questions');
        $validated['shuffle_answers'] = $request->has('shuffle_answers');

        $question_package->update($validated);

        return redirect()->route('question-packages.index', ['type' => $question_package->package_type])
            ->with('success', 'Paket soal berhasil diperbarui!');
    }

    /**
     * Hapus paket soal dari database (soft delete).
     */
    public function destroy(QuestionPackage $questionPackage) {
        Gate::authorize('delete', $questionPackage);

        $type = $questionPackage->package_type;
        $questionPackage->delete();

        return redirect()->route('question-packages.index', ['type' => $type])
            ->with('success', 'Paket soal berhasil dihapus!');
    }

    /**
     * Toggle status publikasi paket soal.
     */
    public function togglePublish($question_package) {
        if (!($question_package instanceof QuestionPackage)) {
            $question_package = QuestionPackage::findOrFail($question_package);
        }

        Gate::authorize('togglePublish', $question_package);

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
        Gate::authorize('viewResults', $questionPackage);

        $attempts = $questionPackage->attempts()
            ->with('user')
            ->whereNotNull('finished_at')
            ->latest()
            ->paginate(20);

        return view('question_packages.results', compact('questionPackage', 'attempts'));
    }


}
