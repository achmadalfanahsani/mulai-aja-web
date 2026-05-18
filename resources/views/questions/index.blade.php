@extends('layouts.app')

@section('title', 'Kelola Soal | MulaiAja')

@section('content')
<div class="row">
    <div class="col-md-12">
        @if (session('success'))
            <div class="alert alert-success d-flex align-items-center justify-content-between" role="alert">
                <div class="d-flex align-items-center">
                    <i class="fa fa-check-circle mr-2"></i>
                    <span>{{ session('success') }}</span>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="block block-rounded block-themed">
            <div class="block-header block-header-default bg-primary-dark">
                <h3 class="block-title">
                    <i class="fa fa-folder mr-2"></i> {{ $questionPackage->name }} 
                    <span class="badge bg-primary ml-2">{{ $questions->count() }} Soal</span>
                </h3>
                <div class="block-options">
                    <a href="{{ route('question-packages.index') }}" class="btn btn-sm btn-alt-secondary mr-2">
                        <i class="fa fa-arrow-left"></i> Kembali ke Paket
                    </a>
                    <a href="{{ route('question-packages.questions.create', $questionPackage->id) }}" class="btn btn-sm btn-alt-secondary">
                        <i class="fa fa-plus mr-1"></i> Tambah Soal Baru
                    </a>
                </div>
            </div>
            
            <div class="block-content">
                @if ($questions->isEmpty())
                    <div class="text-center py-5">
                        <i class="fa fa-list-ol fa-3x text-muted mb-3"></i>
                        <h4 class="text-muted">Belum ada Soal di Paket ini</h4>
                        <p class="text-muted">Mulai isi paket soal dengan menekan tombol di bawah.</p>
                        <a href="{{ route('question-packages.questions.create', $questionPackage->id) }}" class="btn btn-primary">
                            <i class="fa fa-plus"></i> Tambah Soal Pertama
                        </a>
                    </div>
                @else
                    @foreach ($questions as $index => $question)
                        <div class="block block-rounded block-bordered mb-4">
                            <div class="block-header block-header-default bg-body-light d-flex justify-content-between align-items-center py-2 px-3">
                                <h4 class="block-title font-size-sm font-w700 text-uppercase mb-0 text-muted">
                                    Soal Nomor #{{ $index + 1 }} 
                                    @if ($question->difficulty_level)
                                        <span class="badge bg-{{ $question->difficulty_level === 'easy' ? 'success' : ($question->difficulty_level === 'medium' ? 'warning' : 'danger') }}-light text-{{ $question->difficulty_level === 'easy' ? 'success' : ($question->difficulty_level === 'medium' ? 'warning' : 'danger') }} font-w600 ml-2">
                                            {{ ucfirst($question->difficulty_level) }}
                                        </span>
                                    @endif
                                </h4>
                                <div class="block-options">
                                    <a href="{{ route('question-packages.questions.edit', [$questionPackage->id, $question->id]) }}" 
                                       class="btn btn-sm btn-alt-warning mr-1" 
                                       data-toggle="tooltip" 
                                       title="Edit Soal">
                                        <i class="fa fa-pencil-alt"></i> Edit
                                    </a>
                                    <form action="{{ route('question-packages.questions.destroy', [$questionPackage->id, $question->id]) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus soal ini?')" style="display:inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-alt-danger" data-toggle="tooltip" title="Hapus Soal">
                                            <i class="fa fa-trash"></i> Hapus
                                        </button>
                                    </form>
                                </div>
                            </div>
                            
                            <div class="block-content py-3 px-4">
                                {{-- Konten Soal --}}
                                <div class="font-size-md font-w600 text-dark mb-3">
                                    {!! nl2br(e($question->question_text)) !!}
                                </div>

                                {{-- Gambar Soal jika ada --}}
                                @if ($question->hasImage())
                                    <div class="mb-4">
                                        <img src="{{ $question->getImageUrl() }}" alt="Gambar Soal #{{ $index + 1 }}" class="img-fluid rounded border p-2" style="max-height: 250px; object-fit: contain;">
                                    </div>
                                @endif

                                {{-- Opsi Jawaban A-E --}}
                                <div class="row">
                                    @foreach ($question->options as $option)
                                        @php
                                            $isCorrect = $option->option_label === $question->correct_answer;
                                        @endphp
                                        <div class="col-md-6 mb-2">
                                            <div class="border rounded p-2 d-flex align-items-center {{ $isCorrect ? 'border-success bg-success-light text-success font-w700' : 'border-gray-light bg-body-light text-muted' }}">
                                                <div class="badge rounded-circle mr-3 {{ $isCorrect ? 'bg-success text-white' : 'bg-secondary text-white' }}" style="width: 28px; height: 28px; display: flex; align-items: center; justify-content: center; font-size: 13px;">
                                                    {{ $option->option_label }}
                                                </div>
                                                <div class="font-size-sm ml-2">
                                                    {{ $option->option_text }}
                                                </div>
                                                @if ($isCorrect)
                                                    <i class="fa fa-check-circle ml-auto mr-2"></i>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                {{-- Penjelasan --}}
                                @if ($question->explanation)
                                    <div class="alert alert-info bg-info-light border-0 text-dark font-size-sm mt-3 mb-0" role="alert">
                                        <strong><i class="fa fa-info-circle mr-1"></i> Penjelasan/Kunci Jawaban:</strong>
                                        <div class="mt-1">{!! nl2br(e($question->explanation)) !!}</div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
