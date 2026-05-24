@extends('layouts.app')

@section('title', 'Kelola Soal | MulaiAja')

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
    </style>
@endpush

@section('content')
    <div class="row">
        <div class="col-md-12">
            @if (session('success'))
                <div class="alert alert-success d-flex align-items-center justify-content-between" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="fa fa-check-circle me-2"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="block block-rounded block-themed">
                <div class="block-header block-header-default bg-primary-dark">
                    <h3 class="block-title">
                        <i class="fa fa-folder me-2"></i> {{ $questionPackage->name }}
                        <span class="badge bg-primary ms-2">{{ $questions->count() }} Soal</span>
                    </h3>
                    <div class="block-options">
                        <a href="{{ route('question-packages.questions.create', [$questionPackage->id, 'type' => request('type')]) }}"
                            class="btn btn-sm btn-alt-secondary">
                            <i class="fa fa-plus me-1"></i> Tambah Soal Baru
                        </a>
                        <a href="{{ route('question-packages.index', ['type' => request('type')]) }}"
                            class="btn btn-sm btn-alt-secondary me-2">
                            <i class="fa fa-arrow-left"></i> Kembali ke Paket
                        </a>
                    </div>
                </div>

                <div class="block-content">
                    @if ($questions->isEmpty())
                        <div class="text-center py-5">
                            <i class="fa fa-list-ol fa-3x text-muted mb-3"></i>
                            <h4 class="text-muted">Belum ada Soal di Paket ini</h4>
                            <p class="text-muted">Mulai isi paket soal dengan menekan tombol di bawah.</p>
                            <a href="{{ route('question-packages.questions.create', [$questionPackage->id, 'type' => request('type')]) }}"
                                class="btn btn-primary">
                                <i class="fa fa-plus"></i> Tambah Soal Pertama
                            </a>
                        </div>
                    @else
                        @foreach ($questions as $index => $question)
                            <div class="block block-rounded block-bordered mb-4">
                                <div
                                    class="block-header block-header-default bg-body-light d-flex justify-content-between align-items-center py-2 px-3">
                                    <h4 class="block-title font-size-sm font-w700 text-uppercase mb-0 text-muted">
                                        Soal Nomor #{{ $index + 1 }}
                                        <span class="badge bg-info-light text-info font-w600 ms-2">
                                            {{ $question->isEssay() ? 'Isian Singkat' : 'Pilihan Ganda' }}
                                        </span>
                                        @if ($question->difficulty_level)
                                            <span
                                                class="badge bg-{{ $question->difficulty_level === 'easy' ? 'success' : ($question->difficulty_level === 'medium' ? 'warning' : 'danger') }}-light text-{{ $question->difficulty_level === 'easy' ? 'success' : ($question->difficulty_level === 'medium' ? 'warning' : 'danger') }} font-w600 ms-2">
                                                {{ ucfirst($question->difficulty_level) }}
                                            </span>
                                        @endif
                                    </h4>
                                    <div class="block-options">
                                        <a href="{{ route('questions.edit', $question->id) }}"
                                            class="btn btn-sm btn-alt-warning me-1" data-toggle="tooltip" title="Edit Soal">
                                            <i class="fa fa-pencil-alt"></i> Edit
                                        </a>
                                        <form action="{{ route('questions.destroy', $question->id) }}" method="POST"
                                            onsubmit="return confirm('Apakah Anda yakin ingin menghapus soal ini?')"
                                            style="display:inline-block;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-alt-danger" data-toggle="tooltip"
                                                title="Hapus Soal">
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
                                            <div class="img-zoom-wrapper border p-2" onclick="toggleZoom(this)">
                                                <img src="{{ $question->getImageUrl() }}"
                                                    alt="Gambar Soal #{{ $index + 1 }}"
                                                    style="max-height: 250px; object-fit: contain;">
                                            </div>
                                            <div class="mt-2 text-muted font-size-xs">
                                                <i class="fa fa-search-plus me-1"></i> Klik gambar untuk memperbesar & geser
                                            </div>
                                        </div>
                                    @endif

                                    @if ($question->isMultipleChoice())
                                        <div class="row">
                                            @foreach ($question->options as $option)
                                                @php
                                                    $isCorrect = $option->option_label === $question->correct_answer;
                                                @endphp
                                                <div class="col-md-6 mb-2">
                                                    <div
                                                        class="border rounded p-2 d-flex align-items-center {{ $isCorrect ? 'border-success bg-success-light text-success font-w700' : 'border-gray-light bg-body-light text-muted' }}">
                                                        <div class="badge rounded-circle me-3 {{ $isCorrect ? 'bg-success text-white' : 'bg-secondary text-white' }}"
                                                            style="width: 28px; height: 28px; display: flex; align-items: center; justify-content: center; font-size: 13px;">
                                                            @if ($isCorrect)
                                                                <i class="fa fa-check"></i>
                                                            @else
                                                                {{ $option->option_label }}
                                                            @endif
                                                        </div>
                                                        <div class="font-size-sm ms-2">
                                                            {{ $option->option_text }}
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="row">
                                            <div class="col-12">
                                                <div
                                                    class="border rounded p-2 mb-0 d-flex align-items-center border-success bg-success-light text-success">
                                                    <div class="badge rounded-circle bg-success text-white me-2"
                                                        style="width: 28px; height: 28px; display: flex; align-items: center; justify-content: center; font-size: 13px;">
                                                        <i class="fa fa-check"></i>
                                                    </div>
                                                    <span>Kunci Jawaban: <strong
                                                            class="font-w700">{{ $question->correct_answer }}</strong></span>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    {{-- Penjelasan --}}
                                    @if ($question->explanation)
                                        <div class="alert alert-info bg-info-light border-0 text-dark font-size-sm mt-3 mb-0"
                                            role="alert">
                                            <strong><i class="fa fa-info-circle me-1"></i> Penjelasan:</strong>
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

@push('scripts')
    <script>
        function toggleZoom(wrapper) {
            const img = wrapper.querySelector('img');

            if (wrapper.classList.contains('zoomed')) {
                // Zoom Out
                wrapper.classList.remove('zoomed');
                img.style.transform = 'scale(1) translate(0, 0)';
                img.dataset.scale = 1;
                img.dataset.translateX = 0;
                img.dataset.translateY = 0;

                // Remove drag events
                wrapper.onmousedown = null;
                window.onmousemove = null;
                window.onmouseup = null;
            } else {
                // Zoom In
                wrapper.classList.add('zoomed');
                img.style.transform = 'scale(2.5)';
                img.dataset.scale = 2.5;
                img.dataset.translateX = 0;
                img.dataset.translateY = 0;

                // Initialize dragging
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

                img.dataset.translateX = translateX;
                img.dataset.translateY = translateY;

                const scale = img.dataset.scale || 2.5;
                img.style.transform = `scale(${scale}) translate(${translateX / scale}px, ${translateY / scale}px)`;
            };

            window.onmouseup = function() {
                isDragging = false;
            };
        }

        // Inisialisasi tooltips bootstrap
        document.addEventListener("DOMContentLoaded", function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            })
        });
    </script>
@endpush
