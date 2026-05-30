@extends('layouts.app')

@section('title', 'Hasil Evaluasi Ujian | MulaiAja')

@section('content')
<div class="row">
    {{-- Panel Hasil Skor Utama --}}
    <div class="col-md-12 mb-4">
        @if (session('success'))
            <div class="alert alert-success d-flex align-items-center justify-content-between" role="alert">
                <div class="d-flex align-items-center">
                    <i class="fa fa-check-circle me-2"></i>
                    <span>{{ session('success') }}</span>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('warning'))
            <div class="alert alert-warning d-flex align-items-center justify-content-between" role="alert">
                <div class="d-flex align-items-center">
                    <i class="fa fa-exclamation-triangle me-2"></i>
                    <span>{{ session('warning') }}</span>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="block block-rounded block-themed">
            <div class="block-header block-header-default bg-primary-dark">
                <h3 class="block-title"><i class="fa fa-chart-bar me-2"></i> Hasil Evaluasi Ujian CBT</h3>
                <div class="block-options">
                    @if(request('from') === 'package_results')
                        <a href="{{ route('question-packages.results', [$package->id, 'from_classroom' => request('from_classroom'), 'type' => request('type')]) }}" class="btn btn-sm btn-alt-secondary">
                            <i class="fa fa-arrow-left"></i> Kembali ke Hasil Paket
                        </a>
                    @else
                        <a href="{{ route('exams.index') }}" class="btn btn-sm btn-alt-secondary">
                            <i class="fa fa-arrow-left"></i> Kembali ke Dashboard
                        </a>
                    @endif
                </div>
            </div>

            <div class="block-content block-content-full p-4">
                <div class="row align-items-center">
                    {{-- Badge Skor Besar --}}
                    <div class="col-lg-5 text-center border-end py-3">
                        <div class="font-size-sm font-w700 text-uppercase text-muted mb-2">Nilai Akhir</div>
                        
                        @php
                            $isPassed = $questionAttempt->isPassed();
                        @endphp
                        
                        <div class="d-inline-flex align-items-center justify-content-center rounded-circle border-0 p-4 mb-3 {{ $isPassed === true ? 'bg-gd-emerald' : ($isPassed === false ? 'bg-gd-cherry' : 'bg-gd-primary') }}" 
                             style="width: 140px; height: 140px; box-shadow: 0 10px 20px rgba(0,0,0,0.15);">
                            <span class="font-size-h2 font-w800 text-white" style="font-size: 38px;">{{ $questionAttempt->total_score }}</span>
                        </div>

                        {{-- Status Kelulusan --}}
                        <div class="mt-2">
                            @if ($isPassed === true)
                                <span class="badge bg-success font-size-md font-w700 rounded-pill px-4 py-2 text-white">
                                    <i class="fa fa-check-circle me-1"></i> LULUS UJIAN
                                </span>
                                <div class="text-success font-size-sm font-w600 mt-2">Selamat! Anda berhasil melampaui batas nilai kelulusan {{ $package->passing_score }}%.</div>
                            @elseif ($isPassed === false)
                                <span class="badge bg-danger font-size-md font-w700 rounded-pill px-4 py-2 text-white">
                                    <i class="fa fa-times-circle me-1"></i> TIDAK LULUS
                                </span>
                                <div class="text-danger font-size-sm font-w600 mt-2">Maaf, nilai Anda berada di bawah batas minimum kelulusan {{ $package->passing_score }}%. Tetap semangat belajar!</div>
                            @else
                                <span class="badge bg-primary font-size-md font-w700 rounded-pill px-4 py-2 text-white">
                                    <i class="fa fa-info-circle me-1"></i> SELESAI
                                </span>
                                <div class="text-muted font-size-sm mt-2">Ujian berhasil diselesaikan tanpa parameter batas nilai kelulusan.</div>
                            @endif
                        </div>
                    </div>

                    {{-- Analisis Statistik Rinci --}}
                    <div class="col-lg-7 py-3 ps-lg-4">
                        <h4 class="font-w700 text-dark mb-3">Statistik Pengerjaan</h4>
                        <div class="row">
                            <div class="col-6 mb-3">
                                <div class="text-muted font-size-xs text-uppercase font-w700 mb-1">Nama Paket Ujian</div>
                                <div class="font-size-sm font-w600 text-dark text-truncate">{{ $package->name }}</div>
                            </div>
                            <div class="col-6 mb-3">
                                <div class="text-muted font-size-xs text-uppercase font-w700 mb-1">Durasi Pengerjaan</div>
                                <div class="font-size-sm font-w600 text-dark">
                                    <i class="fa fa-clock me-1 text-muted"></i> {{ $questionAttempt->getFormattedDuration() }} / {{ $package->getFormattedDuration() }}
                                </div>
                            </div>
                            <div class="col-6 mb-3">
                                <div class="text-muted font-size-xs text-uppercase font-w700 mb-1">Benar / Total Soal</div>
                                <div class="font-size-sm font-w600 text-success">
                                    <i class="fa fa-check me-1"></i> {{ $stats['correct_count'] }} dari {{ $stats['total_questions'] }} Soal
                                </div>
                            </div>
                            <div class="col-6 mb-3">
                                <div class="text-muted font-size-xs text-uppercase font-w700 mb-1">Salah / Kosong</div>
                                <div class="font-size-sm font-w600 text-danger">
                                    <i class="fa fa-times me-1"></i> {{ $stats['wrong_count'] }} Salah / {{ $stats['unanswered_count'] }} Kosong
                                </div>
                            </div>
                            <div class="col-12 border-top pt-2">
                                <span class="text-muted font-size-xs">Metode Penyerahan: </span>
                                <span class="badge bg-secondary font-size-xs">
                                    {{ $questionAttempt->is_auto_submitted ? 'Auto-Submit (Waktu Habis)' : 'Manual (Diserahkan Mandiri)' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Review Lembar Jawaban & Pembahasan Ujian --}}
    <div class="col-md-12">
        <h2 class="content-heading d-flex align-items-center">
            <i class="fa fa-list-ol me-2 text-primary"></i> Evaluasi Lembar Jawaban & Pembahasan
        </h2>

        @foreach ($responses as $index => $resp)
            @php
                $question = $resp->question;
                $isCorrect = $resp->is_correct;
                $selected = $resp->selected_answer;
                $isAnswered = !$resp->isUnanswered();
                $key = $question->correct_answer;
            @endphp
            
            <div class="block block-rounded block-bordered mb-4">
                {{-- Header Soal --}}
                <div class="block-header block-header-default d-flex justify-content-between align-items-center py-2 px-3 {{ $isAnswered ? ($isCorrect ? 'bg-success-light text-success' : 'bg-danger-light text-danger') : 'bg-body-light text-muted' }}">
                    <h4 class="block-title font-size-sm font-w700 text-uppercase mb-0">
                        Soal Nomor #{{ $index + 1 }}
                        @if ($isAnswered)
                            @if ($isCorrect)
                                <span class="badge bg-success font-w700 font-size-xs ms-2 text-white">
                                    <i class="fa fa-check me-1"></i> BENAR
                                </span>
                            @else
                                <span class="badge bg-danger font-w700 font-size-xs ms-2 text-white">
                                    <i class="fa fa-times me-1"></i> SALAH
                                </span>
                            @endif
                        @else
                            <span class="badge bg-warning font-w700 font-size-xs ms-2 text-white">
                                <i class="fa fa-exclamation-triangle me-1"></i> TIDAK DIJAWAB
                            </span>
                        @endif
                    </h4>
                </div>

                {{-- Konten Pertanyaan --}}
                <div class="block-content py-3 px-4">
                    <div class="font-size-md font-w600 text-dark mb-3">
                        {!! nl2br(e($question->question_text)) !!}
                    </div>

                    {{-- Gambar penjelas jika ada --}}
                    @if ($question->hasImage())
                        <div class="mb-4">
                            <img src="{{ $question->getImageUrl() }}" alt="Gambar Soal #{{ $index + 1 }}" class="img-fluid rounded border p-2" style="max-height: 200px; object-fit: contain;">
                        </div>
                    @endif

                    @if($question->isMultipleChoice())
                        {{-- 5 Opsi Pilihan Jawaban A-E --}}
                        <div class="row">
                            @foreach ($question->options as $option)
                                @php
                                    $optLabel = $option->option_label;
                                    $isOptCorrect = $optLabel === $key;
                                    $isOptSelected = $optLabel === $selected;
                                    
                                    // Set style warna per opsi
                                    $borderColor = 'border-gray-light';
                                    $bgClass = 'bg-body-light';
                                    $badgeClass = 'bg-secondary';
                                    $textColor = 'text-muted';
                                    $icon = '';

                                    if ($isOptCorrect) {
                                        $borderColor = 'border-success';
                                        $bgClass = 'bg-success-light';
                                        $badgeClass = 'bg-success';
                                        $textColor = 'text-success font-w700';
                                        $icon = '<i class="fa fa-check-circle ms-auto text-success me-2"></i>';
                                    } elseif ($isOptSelected && !$isCorrect) {
                                        $borderColor = 'border-danger';
                                        $bgClass = 'bg-danger-light';
                                        $badgeClass = 'bg-danger';
                                        $textColor = 'text-danger font-w700';
                                        $icon = '<i class="fa fa-times-circle ms-auto text-danger me-2"></i>';
                                    }
                                @endphp
                                
                                <div class="col-md-6 mb-2">
                                    <div class="border rounded p-2 d-flex align-items-center {{ $borderColor }} {{ $bgClass }} {{ $textColor }}">
                                        <div class="badge rounded-circle me-3 {{ $badgeClass }} text-white" style="width: 26px; height: 26px; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 700;">
                                            @if ($isOptCorrect)
                                                <i class="fa fa-check"></i>
                                            @elseif ($isOptSelected)
                                                <i class="fa fa-times"></i>
                                            @else
                                                <i class="fa fa-circle-o text-white-50"></i>
                                            @endif
                                        </div>
                                        <div class="font-size-sm ms-2">
                                            {{ $option->option_text }}
                                        </div>
                                        {!! $icon !!}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        {{-- Essay/Short Answer Review --}}
                        <div class="row">
                            <div class="col-12">
                                <div class="border rounded p-3 bg-body-light">
                                    <div class="mb-3">
                                        <span class="text-muted font-size-xs text-uppercase font-w700"><i class="fa fa-user me-1"></i> Jawaban Anda:</span>
                                        <div class="font-size-md mt-1 p-2 border rounded {{ $isCorrect ? 'bg-success-light text-success border-success' : 'bg-danger-light text-danger border-danger' }} font-w600">
                                            {{ $resp->essay_answer ?: '(Tidak ada jawaban)' }}
                                        </div>
                                    </div>
                                    <div>
                                        <span class="text-muted font-size-xs text-uppercase font-w700"><i class="fa fa-check-circle me-1"></i> Kunci Jawaban:</span>
                                        <div class="font-size-md mt-1 p-2 border border-success bg-success-light text-success rounded font-w600">
                                            {{ $question->correct_answer }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Detail Pembahasan (Ditampilkan wajib jika JAWABAN SALAH / KOSONG agar siswa belajar) --}}
                    @php
                        $correctText = $resp->getCorrectAnswerText();
                        $selectedText = $resp->getSelectedAnswerText();
                    @endphp
                    @if (!$isCorrect)
                        @if ($question->explanation)
                            <div class="alert alert-danger alert-permanent bg-danger-light border-0 text-dark font-size-sm mt-3 mb-0" role="alert">
                                <strong class="text-danger"><i class="fa fa-chalkboard me-1"></i> Pembahasan:</strong>
                                <div class="mt-2 border-top border-danger-light pt-2 font-size-sm">
                                    {!! nl2br(e($question->explanation)) !!}
                                </div>
                            </div>
                        @else
                            @if($question->isMultipleChoice())
                                <div class="alert alert-danger alert-permanent bg-danger-light border-0 text-dark font-size-sm mt-3 mb-0" role="alert">
                                    <strong><i class="fa fa-info-circle me-1"></i> Kunci Jawaban:</strong>
                                    <div class="mt-1 font-w600 text-dark">Kunci jawaban yang benar adalah: <strong class="text-success">"{{ $correctText }}"</strong>. (Maaf, tidak ada pembahasan untuk soal ini).</div>
                                </div>
                            @endif
                        @endif
                    @else
                        {{-- Jika Benar, bisa buka penjelasan secara opsional --}}
                        <div class="mt-2 text-end">
                            <button class="btn btn-sm btn-link text-success p-0 font-w600 font-size-sm" type="button" data-bs-toggle="collapse" data-bs-target="#explanationCollapse-{{ $question->id }}" aria-expanded="false" aria-controls="explanationCollapse-{{ $question->id }}">
                                <i class="fa fa-search-plus me-1"></i> Lihat Pembahasan Soal
                            </button>
                            <div class="collapse text-start mt-2" id="explanationCollapse-{{ $question->id }}">
                                <div class="alert alert-success alert-permanent bg-success-light border-0 text-dark font-size-sm mb-0" role="alert">
                                    @if($question->isMultipleChoice())
                                        <strong><i class="fa fa-info-circle me-1"></i> Kunci Jawaban:</strong>
                                        <div class="mt-1 mb-2 font-w600 text-dark">Kunci jawaban yang benar adalah: <strong class="text-success">"{{ $correctText }}"</strong>.</div>
                                    @endif

                                    @if($question->explanation)
                                        <strong class="text-success"><i class="fa fa-check me-1"></i> Pembahasan:</strong>
                                        <div class="mt-1">{!! nl2br(e($question->explanation)) !!}</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
