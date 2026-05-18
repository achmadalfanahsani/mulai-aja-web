@extends('layouts.app')

@section('title', 'CBT Lembar Ujian | MulaiAja')

@push('styles')
<style>
    /* Glowing Timer */
    .cbt-timer {
        font-family: 'Courier New', Courier, monospace;
        font-size: 24px;
        font-weight: 800;
        background: linear-gradient(135deg, #2d3748 0%, #1a202c 100%);
        border: 2px solid #4a5568;
        box-shadow: 0 0 15px rgba(66, 153, 225, 0.4);
    }
    
    /* Option container styling */
    .cbt-option {
        cursor: pointer;
        transition: all 0.2s ease-in-out;
        border: 2px solid #e2e8f0 !important;
    }
    
    .cbt-option:hover {
        background-color: rgba(66, 153, 225, 0.05);
        border-color: #63b3ed !important;
        transform: translateX(4px);
    }
    
    .cbt-option-input:checked + .cbt-option-content {
        color: #2b6cb0;
        font-weight: 700;
    }
    
    .cbt-option-input:checked + .cbt-option-content .cbt-badge-label {
        background-color: #3182ce !important;
        color: #fff !important;
    }
    
    .cbt-option-input:checked + .cbt-option-content {
        border-color: #3182ce !important;
    }
    
    /* Active option highlight */
    .cbt-option-wrapper.selected .cbt-option {
        border-color: #3182ce !important;
        background-color: rgba(49, 130, 206, 0.05);
        box-shadow: 0 0 8px rgba(49, 130, 206, 0.15);
    }

    /* Grid navigation numbers */
    .nav-btn {
        width: 42px;
        height: 42px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 14px;
        margin: 4px;
        border-radius: 8px;
        transition: all 0.2s;
        text-decoration: none;
    }

    .nav-btn.answered {
        background-color: #c6f6d5;
        color: #22543d;
        border: 1px solid #81e6d9;
    }

    .nav-btn.unanswered {
        background-color: #edf2f7;
        color: #4a5568;
        border: 1px solid #cbd5e0;
    }

    .nav-btn.active-num {
        background-color: #ebf8ff;
        color: #2b6cb0;
        border: 2px solid #3182ce;
        box-shadow: 0 0 10px rgba(49, 130, 206, 0.4);
        transform: scale(1.1);
    }
    
    .nav-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    /* Status indicator */
    #save-indicator {
        font-size: 13px;
        font-weight: 600;
        transition: opacity 0.3s ease;
    }
</style>
@endpush

@section('content')
<div class="row">
    {{-- Header Soal & Timer --}}
    <div class="col-md-12 mb-3">
        <div class="block block-rounded block-themed mb-2">
            <div class="block-content block-content-full bg-primary-dark d-flex flex-column flex-sm-row justify-content-between align-items-center py-3">
                <div class="text-center text-sm-left mb-2 mb-sm-0">
                    <h3 class="font-w700 text-white mb-0">{{ $questionAttempt->questionPackage->name }}</h3>
                    <p class="text-white-50 font-size-sm mb-0">Ujian Berlangsung • Student: {{ Auth::user()->name }}</p>
                </div>
                <div class="text-center">
                    <span class="text-white-50 font-size-xs text-uppercase font-w700 d-block mb-1"><i class="fa fa-hourglass-half mr-1"></i> Sisa Waktu Ujian</span>
                    <div id="countdown" class="cbt-timer text-white rounded px-4 py-2">
                        00:00:00
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Board Ujian --}}
    <div class="col-md-8">
        {{-- Card Soal --}}
        <div class="block block-rounded block-themed">
            <div class="block-header block-header-default bg-primary d-flex justify-content-between align-items-center">
                <h3 class="block-title">Pertanyaan {{ $currentNumber }} dari {{ count($questionIds) }}</h3>
                <div class="block-options">
                    {{-- Auto save status --}}
                    <span id="save-indicator" class="text-white-75 mr-2">
                        <i class="fa fa-check-circle"></i> Sinkron
                    </span>
                </div>
            </div>

            <div class="block-content block-content-full p-4">
                {{-- Pertanyaan --}}
                <div class="font-size-md font-w600 text-dark mb-4" style="line-height: 1.6;">
                    {!! nl2br(e($question->question_text)) !!}
                </div>

                {{-- Gambar jika ada --}}
                @if ($question->hasImage())
                    <div class="mb-4 text-center text-sm-left">
                        <img src="{{ $question->getImageUrl() }}" alt="Gambar Soal #{{ $currentNumber }}" class="img-fluid rounded border p-2" style="max-height: 300px; object-fit: contain;">
                    </div>
                @endif

                {{-- 5 Opsi Pilihan Jawaban A-E --}}
                <div class="mb-4">
                    <form id="draft-form" action="{{ route('exams.save-response', $questionAttempt->id) }}" method="POST">
                        @csrf
                        <input type="hidden" name="question_id" value="{{ $question->id }}">
                        
                        <div class="cbt-options-list">
                            @foreach ($options as $option)
                                @php
                                    $isSelected = $currentResponse && $currentResponse->selected_answer === $option->option_label;
                                @endphp
                                <label class="cbt-option-wrapper d-block mb-3 {{ $isSelected ? 'selected' : '' }}" style="cursor:pointer;">
                                    <input type="radio" name="selected_answer" value="{{ $option->option_label }}" 
                                           class="cbt-option-input d-none" 
                                           {{ $isSelected ? 'checked' : '' }}
                                           onchange="autoSaveAnswer('{{ $option->option_label }}', this)">
                                    
                                    <div class="cbt-option border rounded p-3 d-flex align-items-center">
                                        <div class="cbt-badge-label badge rounded-circle bg-secondary text-white mr-3" style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center; font-size: 14px; font-weight: 700;">
                                            {{ $option->option_label }}
                                        </div>
                                        <div class="font-size-sm flex-grow-1 text-dark ml-2">
                                            {{ $option->option_text }}
                                        </div>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </form>
                </div>
            </div>

            {{-- Footer Navigasi Soal Sebelumnya/Selanjutnya --}}
            <div class="block-content block-content-full block-content-sm bg-body-light border-top d-flex justify-content-between align-items-center px-4">
                @if ($currentNumber > 1)
                    <a href="{{ route('exams.attempt', [$questionAttempt->id, 'page' => $currentNumber - 1]) }}" class="btn btn-alt-secondary">
                        <i class="fa fa-arrow-left mr-1"></i> Soal Sebelumnya
                    </a>
                @else
                    <button class="btn btn-alt-secondary" disabled>
                        <i class="fa fa-arrow-left mr-1"></i> Soal Sebelumnya
                    </button>
                @endif

                @if ($currentNumber < count($questionIds))
                    <a href="{{ route('exams.attempt', [$questionAttempt->id, 'page' => $currentNumber + 1]) }}" class="btn btn-primary">
                        Soal Selanjutnya <i class="fa fa-arrow-right ml-1"></i>
                    </a>
                @else
                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#confirmSubmitModal">
                        <i class="fa fa-check-circle mr-1"></i> Selesai Ujian
                    </button>
                @endif
            </div>
        </div>
    </div>

    {{-- Sidebar Navigasi Nomor Soal --}}
    <div class="col-md-4">
        <div class="block block-rounded block-themed">
            <div class="block-header block-header-default bg-primary-dark">
                <h3 class="block-title"><i class="fa fa-th-large mr-2"></i> Nomor Pertanyaan</h3>
            </div>
            
            <div class="block-content block-content-full text-center">
                {{-- Panel navigasi grid nomor --}}
                <div class="d-flex flex-wrap justify-content-center mb-4">
                    @foreach ($navigation as $nav)
                        <a href="{{ route('exams.attempt', [$questionAttempt->id, 'page' => $nav['number']]) }}" 
                           class="nav-btn {{ $nav['is_active'] ? 'active-num' : ($nav['is_answered'] ? 'answered' : 'unanswered') }}"
                           id="nav-btn-{{ $nav['question_id'] }}">
                            {{ $nav['number'] }}
                        </a>
                    @endforeach
                </div>

                <div class="border-top pt-3 text-left">
                    <div class="row font-size-xs text-muted mb-3">
                        <div class="col-6 mb-1">
                            <span class="badge bg-success mr-1" style="width:12px; height:12px; display:inline-block;">&nbsp;</span> Sudah Dijawab
                        </div>
                        <div class="col-6 mb-1">
                            <span class="badge bg-secondary mr-1" style="width:12px; height:12px; display:inline-block;">&nbsp;</span> Belum Dijawab
                        </div>
                    </div>
                </div>

                {{-- Tombol selesai --}}
                <button type="button" class="btn btn-danger w-100 font-w700 mt-2 py-2" data-bs-toggle="modal" data-bs-target="#confirmSubmitModal">
                    <i class="fa fa-paper-plane mr-1"></i> Serahkan Ujian (Submit)
                </button>
            </div>
        </div>
    </div>
</div>

{{-- MODAL KONFIRMASI SUBMIT --}}
<div class="modal fade" id="confirmSubmitModal" tabindex="-1" aria-labelledby="confirmSubmitModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title text-white" id="confirmSubmitModalLabel"><i class="fa fa-exclamation-triangle mr-2"></i> Konfirmasi Selesai Ujian</h5>
                <button type="button" class="btn-close text-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-4 text-center">
                <i class="fa fa-question-circle fa-4x text-danger mb-3"></i>
                <h4>Apakah Anda yakin ingin mengakhiri ujian ini?</h4>
                <p class="text-muted">Setelah diserahkan, Anda tidak dapat mengubah jawaban Anda lagi.</p>
                
                {{-- Rangkuman Soal Terisi --}}
                <div class="alert alert-secondary bg-body-light border-0 py-2 px-3 mt-3 d-inline-block">
                    @php
                        $answeredCount = $questionAttempt->responses()->whereNotNull('selected_answer')->count();
                        $unansweredCount = count($questionIds) - $answeredCount;
                    @endphp
                    <span class="font-w600 text-dark">
                        <i class="fa fa-info-circle text-primary"></i> 
                        Sudah dijawab: <strong class="text-success">{{ $answeredCount }}</strong> • Belum dijawab: <strong class="text-danger">{{ $unansweredCount }}</strong>
                    </span>
                </div>
            </div>
            <div class="modal-footer bg-body-light d-flex justify-content-between">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Lanjutkan Mengerjakan</button>
                <form action="{{ route('exams.submit', $questionAttempt->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-danger font-w700">
                        <i class="fa fa-check-circle mr-1"></i> Ya, Selesai & Kirim
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- FORM HIDDEN AUTO SUBMIT KETIKA TIMER HABIS --}}
<form id="auto-submit-form" action="{{ route('exams.submit', $questionAttempt->id) }}" method="POST" class="d-none">
    @csrf
    <input type="hidden" name="auto_submitted" value="1">
</form>

@endsection

@push('scripts')
<script>
    // 1. DRAFT ANSWER AUTO-SAVE VIA AJAX FETCH
    function autoSaveAnswer(answer, element) {
        // Highlight active option wrapper
        document.querySelectorAll('.cbt-option-wrapper').forEach(function(el) {
            el.classList.remove('selected');
        });
        element.closest('.cbt-option-wrapper').classList.add('selected');

        const indicator = document.getElementById('save-indicator');
        indicator.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Menyimpan...';
        indicator.className = 'text-warning font-size-sm mr-2';

        const formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('question_id', '{{ $question->id }}');
        formData.append('selected_answer', answer);

        fetch('{{ route("exams.save-response", $questionAttempt->id) }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                indicator.innerHTML = '<i class="fa fa-check-circle text-success"></i> Tersimpan';
                indicator.className = 'text-success font-size-sm mr-2';
                
                // Update live color of sidebar button to green (answered)
                const navBtn = document.getElementById('nav-btn-{{ $question->id }}');
                if (navBtn) {
                    navBtn.classList.remove('unanswered');
                    navBtn.classList.add('answered');
                }
            } else {
                indicator.innerHTML = '<i class="fa fa-times-circle text-danger"></i> Gagal Menyimpan';
                indicator.className = 'text-danger font-size-sm mr-2';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            indicator.innerHTML = '<i class="fa fa-times-circle text-danger"></i> Offline / Error';
            indicator.className = 'text-danger font-size-sm mr-2';
        });
    }

    // 2. CLIENT-SIDE COUNTDOWN TIMER WITH SAFE AUTO-SUBMIT LOGICS
    (function() {
        let timeRemaining = parseInt('{{ $timeRemaining }}'); // Sisa detik
        const countdownEl = document.getElementById('countdown');
        const autoSubmitForm = document.getElementById('auto-submit-form');

        function updateTimer() {
            if (timeRemaining <= 0) {
                countdownEl.innerHTML = "00:00:00";
                countdownEl.style.boxShadow = "0 0 15px rgba(245, 101, 101, 0.8)";
                countdownEl.style.borderColor = "#e53e3e";
                
                // Kunci interaksi form
                document.querySelectorAll('.cbt-option-input').forEach(input => input.disabled = true);
                
                // Tampilkan pesan & Auto-submit ke server
                alert('Waktu pengerjaan Ujian Anda telah habis! Jawaban akan otomatis diserahkan.');
                autoSubmitForm.submit();
                return;
            }

            // Hitung Jam, Menit, Detik
            const hours = Math.floor(timeRemaining / 3600);
            const minutes = Math.floor((timeRemaining % 3600) / 60);
            const seconds = timeRemaining % 60;

            // Format pad (leading zero)
            const formatted = 
                String(hours).padStart(2, '0') + ':' + 
                String(minutes).padStart(2, '0') + ':' + 
                String(seconds).padStart(2, '0');

            countdownEl.innerHTML = formatted;

            // Beri visual peringatan jika sisa waktu < 5 menit (300 detik)
            if (timeRemaining < 300) {
                countdownEl.style.boxShadow = "0 0 15px rgba(237, 137, 54, 0.7)";
                countdownEl.style.borderColor = "#dd6b20";
                countdownEl.classList.add('text-warning');
            }

            timeRemaining--;
            setTimeout(updateTimer, 1000);
        }

        // Jalankan timer saat halaman dimuat
        updateTimer();
    })();
</script>
@endpush
