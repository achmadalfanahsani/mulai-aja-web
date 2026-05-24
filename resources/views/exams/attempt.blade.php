@extends('layouts.app')

@section('title', 'CBT Lembar Ujian | MulaiAja')
@push('styles')
<style>
    .img-zoom-wrapper {
        position: relative;
        overflow: hidden;
        cursor: zoom-in;
        border-radius: 0.375rem;
        background-color: #f8f9fa;
        display: inline-block;
        max-width: 100%;
    }
    .img-zoom-wrapper.zoomed {
        cursor: grab;
    }
    .img-zoom-wrapper.zoomed:active {
        cursor: grabbing;
    }
    .img-zoom-wrapper img {
        transition: transform 0.3s ease;
        transform-origin: center center;
        display: block;
        max-width: 100%;
        height: auto;
        user-select: none;
        -webkit-user-drag: none;
    }

    /* Glowing Timer */
    .cbt-timer {
...
        font-family: 'Courier New', Courier, monospace;
        font-size: 24px;
        font-weight: 800;
        background: linear-gradient(135deg, #2d3748 0%, #1a202c 100%);
        border: 2px solid #4a5568;
        box-shadow: 0 0 15px rgba(var(--bs-primary-rgb), 0.4);
    }
    
    /* Option container styling */
    .cbt-option {
        cursor: pointer;
        transition: all 0.2s ease-in-out;
        border: 2px solid #e2e8f0 !important;
    }
    
    .cbt-option:hover {
        background-color: rgba(var(--bs-primary-rgb), 0.05);
        border-color: var(--bs-primary-border-subtle) !important;
        transform: translateX(4px);
    }
    

 /* Light Theme - Active option highlight */
.cbt-option-wrapper.selected .cbt-option {
    border-color: rgba(var(--bs-primary-rgb), 0.45) !important;
    background-color: rgba(var(--bs-primary-rgb), 0.2) !important;
    box-shadow:
        0 1px 3px rgba(var(--bs-primary-rgb), 0.10),
        0 0 0 1px rgba(var(--bs-primary-rgb), 0.08) !important;
    transition: all 0.2s ease;
}

.cbt-option-wrapper.selected .cbt-option .cbt-option-text {
    color: #2b3a50ff !important;
    font-weight: 600;
}

/* Dark Theme - Active option highlight */
.dark .cbt-option-wrapper.selected .cbt-option {
    border-color: rgba(var(--bs-primary-rgb), 0.55) !important;
    background-color: rgba(var(--bs-primary-rgb), 0.12) !important;
    box-shadow: 0 0 6px rgba(var(--bs-primary-rgb), 0.25) !important;
}

.dark .cbt-option-wrapper.selected .cbt-option .cbt-option-text {
    color: #c3c3c3ff !important;
    font-weight: 600;
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
        background-color: var(--bs-primary-bg-subtle);
        color: var(--bs-primary-text-emphasis);
        border: 2px solid var(--bs-primary);
        box-shadow: 0 0 10px rgba(var(--bs-primary-rgb), 0.4);
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
                        <div class="img-zoom-wrapper border p-2" onclick="toggleZoom(this)">
                            <img src="{{ $question->getImageUrl() }}" alt="Gambar Soal #{{ $currentNumber }}" style="max-height: 300px; object-fit: contain;">
                        </div>
                        <div class="mt-2 text-muted font-size-xs">
                            <i class="fa fa-search-plus me-1"></i> Klik gambar untuk memperbesar & geser
                        </div>
                    </div>
                @endif

                {{-- Pilihan Jawaban (Multiple Choice or Essay) --}}
                <div class="mb-4">
                    <form id="draft-form" action="{{ route('exams.save-response', $questionAttempt->id) }}" method="POST">
                        @csrf
                        <input type="hidden" name="question_id" value="{{ $question->id }}">
                        
                        @if($question->isMultipleChoice())
                            <div class="cbt-options-list">
                                @foreach ($options as $option)
                                    @php
                                        $isSelected = $currentResponse && $currentResponse->selected_answer === $option->option_label;
                                    @endphp
                                    <label class="cbt-option-wrapper d-block mb-3 {{ $isSelected ? 'selected' : '' }}" style="cursor:pointer;">
                                        <input type="radio" name="selected_answer" value="{{ $option->option_label }}" 
                                               class="cbt-option-input d-none" 
                                               {{ $isSelected ? 'checked' : '' }}
                                               onchange="autoSaveAnswer('{{ $option->option_label }}', 'multiple_choice')">
                                        
                                        <div class="cbt-option border rounded p-3 d-flex align-items-center">
                                            <div class="font-size-sm flex-grow-1 cbt-option-text">
                                                {{ $option->option_text }}
                                            </div>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        @else
                            {{-- Essay Input --}}
                            <div class="essay-container">
                                <label class="form-label" for="essay_answer">Ketikkan Jawaban Anda:</label>
                                <input type="text" class="form-control form-control-lg" id="essay_answer" name="essay_answer" 
                                       placeholder="Ketikkan jawaban Anda di sini..."
                                       value="{{ $currentResponse->essay_answer ?? '' }}"
                                       onblur="autoSaveAnswer(this.value, 'essay')"
                                       autocomplete="off">
                                <div class="mt-2 text-muted font-size-xs">
                                    <i class="fa fa-info-circle me-1"></i> Jawaban otomatis tersimpan saat Anda berpindah ke kolom lain atau soal lain.
                                </div>
                            </div>
                        @endif
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
                    <button type="button" class="btn btn-success" onclick="prepareSubmit()">
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
                           id="nav-btn-{{ $nav['question_id'] }}"
                           data-answered="{{ $nav['is_answered'] ? 'true' : 'false' }}">
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
                <button type="button" class="btn btn-danger w-100 font-w700 mt-2 py-2" onclick="prepareSubmit()">
                    <i class="fa fa-paper-plane mr-1"></i> Serahkan Ujian (Submit)
                </button>
            </div>
        </div>
    </div>
</div>

{{-- MODAL KONFIRMASI SUBMIT (OPSI 1) --}}
<div class="modal fade" id="confirmSubmitModal" tabindex="-1" aria-labelledby="confirmSubmitModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-body border-0 shadow rounded-4 overflow-hidden">
            
            <div class="modal-header bg-primary border-0 py-3 px-4">
                <h5 class="modal-title text-white font-w700" id="confirmSubmitModalLabel">
                    <i class="fa fa-paper-plane me-2"></i> Konfirmasi Selesai Ujian
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body py-4 px-4 text-center">
                <i class="fa fa-question-circle fa-4x text-primary-light mb-3 opacity-50"></i>
                <h4 class="font-w700 text-dark">Apakah Anda yakin ingin mengakhiri ujian ini?</h4>
                <p class="text-muted mb-0">Setelah diserahkan, Anda tidak dapat mengubah jawaban Anda lagi.</p>
                
                {{-- Rangkuman Soal Terisi --}}
                <div class="alert alert-secondary bg-body-light border-0 py-2 px-3 mt-4 d-inline-block rounded-3">
                    @php
                        $stats = $questionAttempt->getAnswerStatistics();
                        $answeredCount = $stats['answered_count'];
                        $unansweredCount = $stats['unanswered_count'];
                    @endphp
                    <span class="font-w600 text-dark fs-sm">
                        <i class="fa fa-info-circle text-primary me-1"></i> 
                        Sudah dijawab: <strong class="text-success" id="modal-answered-count">{{ $answeredCount }}</strong> &nbsp;•&nbsp; Belum dijawab: <strong class="text-warning" id="modal-unanswered-count">{{ $unansweredCount }}</strong>
                    </span>
                </div>
            </div>
            
            <div class="modal-footer bg-body-light border-0 py-3 px-4 d-flex justify-content-between">
                <button type="button" class="btn btn-secondary font-w600 rounded-3 px-3 py-2" data-bs-dismiss="modal">Lanjutkan Mengerjakan</button>
                <form action="{{ route('exams.submit', $questionAttempt->id) }}" method="POST" class="m-0">
                    @csrf
                    <button type="submit" class="btn btn-primary font-w700 rounded-3 px-3 py-2 shadow-sm">
                        <i class="fa fa-check-circle me-1"></i> Ya, Selesai & Kirim
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
    function toggleZoom(wrapper) {
        const img = wrapper.querySelector('img');
        
        if (wrapper.classList.contains('zoomed')) {
            wrapper.classList.remove('zoomed');
            img.style.transform = 'scale(1) translate(0, 0)';
            img.dataset.scale = 1;
            img.dataset.translateX = 0;
            img.dataset.translateY = 0;
            wrapper.onmousedown = null;
            window.onmousemove = null;
            window.onmouseup = null;
        } else {
            wrapper.classList.add('zoomed');
            img.style.transform = 'scale(2.5)';
            img.dataset.scale = 2.5;
            img.dataset.translateX = 0;
            img.dataset.translateY = 0;
            initDragging(wrapper, img);
        }
    }

    function initDragging(wrapper, img) {
        let isDragging = false;
        let startX, startY;
        let translateX = 0;
        let translateY = 0;

        wrapper.onmousedown = function(e) {
            if (!wrapper.classList.contains('zoomed')) return;
            e.preventDefault();
            isDragging = true;
            startX = e.clientX - translateX;
            startY = e.clientY - translateY;
        };

        window.onmousemove = function(e) {
            if (!isDragging) return;
            translateX = e.clientX - startX;
            translateY = e.clientY - startY;
            const scale = img.dataset.scale || 2.5;
            img.style.transform = `scale(${scale}) translate(${translateX / scale}px, ${translateY / scale}px)`;
        };

        window.onmouseup = function() {
            isDragging = false;
        };
    }
</script>
<script>
    // Global variable to track pending auto-save promises
    let pendingSave = null;

    // 1. DRAFT ANSWER AUTO-SAVE VIA AJAX FETCH
    function autoSaveAnswer(answer, type) {
        if (type === 'multiple_choice') {
            // Highlight active option wrapper
            document.querySelectorAll('.cbt-option-wrapper').forEach(function(el) {
                el.classList.remove('selected');
            });
            event.target.closest('.cbt-option-wrapper').classList.add('selected');
        }

        const indicator = document.getElementById('save-indicator');
        indicator.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Menyimpan...';
        indicator.className = 'text-warning font-size-sm mr-2';

        const formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('question_id', '{{ $question->id }}');
        
        if (type === 'multiple_choice') {
            formData.append('selected_answer', answer);
        } else {
            formData.append('essay_answer', answer);
        }

        // Assign the promise to pendingSave
        pendingSave = fetch('{{ route("exams.save-response", $questionAttempt->id) }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                indicator.innerHTML = '<i class="fa fa-check-circle text-white-75"></i> Tersimpan';
                indicator.className = 'text-white-75 font-size-sm mr-2';
                
                // Update live color of sidebar button to green (answered)
                const navBtn = document.getElementById('nav-btn-{{ $question->id }}');
                if (navBtn) {
                    const isAnswered = (type === 'multiple_choice' && answer) || (type === 'essay' && answer.trim() !== '');
                    
                    if (isAnswered) {
                        navBtn.classList.remove('unanswered');
                        navBtn.classList.add('answered');
                        navBtn.setAttribute('data-answered', 'true');
                    } else {
                        navBtn.classList.remove('answered');
                        navBtn.classList.add('unanswered');
                        navBtn.setAttribute('data-answered', 'false');
                    }
                }
            } else {
                indicator.innerHTML = '<i class="fa fa-times-circle text-danger"></i> Gagal Menyimpan';
                indicator.className = 'text-danger font-size-sm mr-2';
            }
            pendingSave = null; // Clear pending save
        })
        .catch(error => {
            console.error('Error:', error);
            indicator.innerHTML = '<i class="fa fa-times-circle text-danger"></i> Offline / Error';
            indicator.className = 'text-danger font-size-sm mr-2';
            pendingSave = null; // Clear pending save
        });
    }

    function prepareSubmit() {
        const essayInput = document.getElementById('essay_answer');
        
        // If it's an essay question, trigger a manual save if not already saved
        if (essayInput) {
            autoSaveAnswer(essayInput.value, 'essay');
        }

        // Wait for any pending save to finish before showing the modal
        if (pendingSave) {
            pendingSave.then(() => {
                const modal = new bootstrap.Modal(document.getElementById('confirmSubmitModal'));
                modal.show();
            });
        } else {
            const modal = new bootstrap.Modal(document.getElementById('confirmSubmitModal'));
            modal.show();
        }
    }

    // 2. CLIENT-SIDE COUNTDOWN TIMER WITH SAFE AUTO-SUBMIT LOGICS
    (function() {
        const endTimeMs = parseInt('{{ $endTime }}');
        const countdownEl = document.getElementById('countdown');
        const autoSubmitForm = document.getElementById('auto-submit-form');

        function updateTimer() {
            const now = new Date().getTime();
            let timeRemaining = Math.floor((endTimeMs - now) / 1000);

            if (timeRemaining <= 0) {
                countdownEl.innerHTML = "00:00:00";
                countdownEl.style.boxShadow = "0 0 15px rgba(245, 101, 101, 0.8)";
                countdownEl.style.borderColor = "#e53e3e";
                
                // Kunci interaksi form
                document.querySelectorAll('.cbt-option-input').forEach(input => input.disabled = true);
                
                // Auto-submit ke server tanpa alert yang memblokir proses
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

            setTimeout(updateTimer, 1000);
        }

        // Jalankan timer saat halaman dimuat
        updateTimer();
    })();

    // 3. DYNAMICALLY RE-CALCULATE ANSWER COUNTS WHEN MODAL POP-UP SHOWS
    function updateModalCounts() {
        const total = {{ count($questionIds) }};
        let answered = 0;
        document.querySelectorAll('.nav-btn').forEach(function(btn) {
            if (btn.getAttribute('data-answered') === 'true') {
                answered++;
            }
        });
        
        const unanswered = total - answered;
        
        const answeredEl = document.getElementById('modal-answered-count');
        const unansweredEl = document.getElementById('modal-unanswered-count');
        if (answeredEl) answeredEl.innerText = answered;
        if (unansweredEl) unansweredEl.innerText = unanswered;
    }

    document.addEventListener("DOMContentLoaded", function() {
        const confirmSubmitModal = document.getElementById('confirmSubmitModal');
        if (confirmSubmitModal) {
            confirmSubmitModal.addEventListener('show.bs.modal', function () {
                updateModalCounts();
            });
        }
    });
</script>
@endpush
