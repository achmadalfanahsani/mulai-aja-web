@extends('layouts.app')

@section('title', 'Edit Soal | MulaiAja')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-9">
        <div class="block block-rounded block-themed">
            <div class="block-header block-header-default bg-primary-dark">
                <h3 class="block-title">Edit Soal</h3>
                <div class="block-options">
                    <a href="{{ route('question-packages.questions.index', $questionPackage->id) }}" class="btn btn-sm btn-alt-secondary">
                        <i class="fa fa-arrow-left me-1"></i> Kembali
                    </a>
                </div>
            </div>
            
            <div class="block-content">
                <form action="{{ route('questions.update', $question->id) }}" method="POST" enctype="multipart/form-data" class="py-3">
                    @csrf
                    @method('PUT')
                    
                    {{-- Detail Paket --}}
                    <div class="alert alert-secondary bg-body-light border-0 text-dark py-2 px-3 mb-4 d-flex align-items-center">
                        <i class="fa fa-folder-open me-2 text-primary"></i>
                        <span class="font-size-sm">Mengedit soal pada paket: <strong>{{ $questionPackage->name }}</strong></span>
                    </div>

                    {{-- Isi Soal --}}
                    <div class="form-group mb-4">
                        <label class="form-label" for="question_text">Teks Pertanyaan (Soal) <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('question_text') is-invalid @enderror" 
                                  id="question_text" name="question_text" rows="5" 
                                  placeholder="Tuliskan pertanyaan di sini..." required>{{ old('question_text', $question->question_text) }}</textarea>
                        @error('question_text')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        {{-- Difficulty --}}
                        <div class="col-md-6 form-group mb-4">
                            <label class="form-label" for="difficulty_level">Tingkat Kesulitan</label>
                            <select class="form-select @error('difficulty_level') is-invalid @enderror" id="difficulty_level" name="difficulty_level">
                                <option value="easy" {{ old('difficulty_level', $question->difficulty_level) == 'easy' ? 'selected' : '' }}>Mudah (Easy)</option>
                                <option value="medium" {{ old('difficulty_level', $question->difficulty_level) == 'medium' ? 'selected' : '' }}>Sedang (Medium)</option>
                                <option value="hard" {{ old('difficulty_level', $question->difficulty_level) == 'hard' ? 'selected' : '' }}>Sulit (Hard)</option>
                            </select>
                            @error('difficulty_level')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Gambar Pendukung --}}
                        <div class="col-md-6 form-group mb-4">
                            <label class="form-label" for="question_image">Gambar Penjelas (Opsional)</label>
                            <input class="form-control @error('question_image') is-invalid @enderror" type="file" id="question_image" name="question_image" accept="image/*">
                            <small class="text-muted">Biarkan kosong jika tidak ingin mengubah gambar.</small>
                            
                            {{-- Preview Gambar Sekarang jika ada --}}
                            @if ($question->hasImage())
                                <div class="mt-2">
                                    <div class="text-muted font-size-xs mb-1">Gambar saat ini:</div>
                                    <img src="{{ $question->getImageUrl() }}" alt="Gambar Soal" class="img-fluid rounded border p-1" style="max-height: 100px; object-fit: contain;">
                                </div>
                            @endif
                            
                            @error('question_image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <hr class="my-4">

                    {{-- 5 Opsi Jawaban --}}
                    <h4 class="font-size-md font-w700 text-uppercase text-muted mb-4"><i class="fa fa-list me-1"></i> Opsi Jawaban & Kunci Jawaban</h4>
                    
                    @foreach (['A' => 1, 'B' => 2, 'C' => 3, 'D' => 4, 'E' => 5] as $label => $index)
                        <div class="row mb-3 align-items-center">
                            <div class="col-auto">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="correct_answer" id="correct_{{ $label }}" value="{{ $label }}" 
                                        {{ old('correct_answer', $question->correct_answer) == $label ? 'checked' : '' }} required>
                                    <label class="form-check-label font-w700 text-primary font-size-lg" for="correct_{{ $label }}" data-toggle="tooltip" title="Pilih sebagai kunci jawaban yang benar">
                                    </label>
                                </div>
                            </div>
                            <div class="col">
                                <input type="text" class="form-control @error('options.' . $label) is-invalid @enderror" 
                                       name="options[{{ $label }}]" value="{{ old('options.' . $label, $options[$label] ?? '') }}" 
                                       placeholder="Ketikkan teks untuk Opsi {{ $index }}..." required>
                                @error('options.' . $label)
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    @endforeach
                    <small class="text-muted d-block mb-4"><i class="fa fa-info-circle me-1"></i> Centang *Radio Button* di sebelah kiri opsi untuk memilih **kunci jawaban yang benar**.</small>

                    <hr class="my-4">

                    {{-- Penjelasan --}}
                    <div class="form-group mb-4">
                        <label class="form-label" for="explanation">Penjelasan Kunci Jawaban (Opsional)</label>
                        <textarea class="form-control @error('explanation') is-invalid @enderror" 
                                  id="explanation" name="explanation" rows="4" 
                                  placeholder="Berikan penjelasan mengapa kunci jawaban tersebut benar. Penjelasan akan ditampilkan kepada siswa saat review hasil ujian...">{{ old('explanation', $question->explanation) }}</textarea>
                        @error('explanation')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Submit --}}
                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <a href="{{ route('question-packages.questions.index', $questionPackage->id) }}" class="btn btn-alt-secondary">Batal</a>
                        <button type="submit" class="btn btn-warning">
                            <i class="fa fa-save me-1"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Inisialisasi tooltips bootstrap
    document.addEventListener("DOMContentLoaded", function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    });
</script>
@endpush
