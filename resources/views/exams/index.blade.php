@extends('layouts.app')

@section('title', 'Daftar Ujian CBT | MulaiAja')

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

        @if (session('error'))
            <div class="alert alert-danger d-flex align-items-center justify-content-between" role="alert">
                <div class="d-flex align-items-center">
                    <i class="fa fa-exclamation-circle mr-2"></i>
                    <span>{{ session('error') }}</span>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Ujian CBT yang Tersedia --}}
        <h2 class="content-heading d-flex align-items-center">
            <i class="fa fa-pen-nib me-2 text-primary"></i> Ujian CBT yang Tersedia
        </h2>

        {{-- Filter Form --}}
        <div class="block block-rounded block-bordered mb-4">
            <div class="block-content block-content-full">
                <form action="{{ route('exams.index') }}" method="GET">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-text bg-white border-right-0">
                                    <i class="fa fa-search text-muted"></i>
                                </span>
                                <input type="text" name="q" class="form-control border-left-0" 
                                       placeholder="Cari nama paket soal..." value="{{ request('q') }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <select name="type" class="form-select">
                                <option value="">Semua Tipe Soal</option>
                                <option value="multiple_choice" {{ request('type') == 'multiple_choice' ? 'selected' : '' }}>Pilihan Ganda</option>
                                <option value="essay" {{ request('type') == 'essay' ? 'selected' : '' }}>Isian Singkat</option>
                                <option value="mixed" {{ request('type') == 'mixed' ? 'selected' : '' }}>Campuran</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fa fa-filter mr-1"></i> Filter
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        
        @if ($packages->isEmpty())
            <div class="block block-rounded block-bordered p-5 text-center">
                <i class="fa fa-calendar-times fa-3x text-muted mb-3"></i>
                <h4 class="text-muted">Tidak ada Ujian CBT yang Aktif saat ini</h4>
                <p class="text-muted mb-0">Hubungi guru Anda jika Anda merasa ada kesalahan penjadwalan ujian.</p>
            </div>
        @else
            <div class="row">
                @foreach ($packages as $package)
                    <div class="col-md-4 mb-4">
                        <div class="block block-rounded block-bordered h-100 d-flex flex-column mb-0">
                            <div class="block-content block-content-full bg-primary-dark text-center py-4">
                                <i class="fa fa-file-invoice fa-3x text-white-50 mb-3"></i>
                                <h4 class="font-w700 text-white mb-0 text-truncate px-2">{{ $package->name }}</h4>
                                <div class="text-white-75 font-size-sm mt-2">
                                    <i class="fa fa-user-edit me-1"></i> {{ $package->user->name ?? 'Sistem' }}
                                    <span class="badge {{ $package->type_badge_class }} ms-1">
                                        {{ $package->type_label }}
                                    </span>
                                </div>
                            </div>
                            
                            <div class="block-content flex-grow-1 py-3">
                                @if($package->classrooms->isNotEmpty())
                                    <div class="mb-3">
                                        @foreach($package->classrooms as $classroom)
                                            <span class="badge bg-info-light text-info border border-info-lighter mb-1">
                                                <i class="fa fa-users me-1"></i> {{ $classroom->name }}
                                            </span>
                                        @endforeach
                                    </div>
                                @endif

                                <p class="text-muted font-size-sm mb-3 text-break-word">
                                    {{ $package->description ?? 'Tidak ada deskripsi rincian paket soal.' }}
                                </p>
                                
                                <div class="row text-center font-size-sm">
                                    <div class="col-6 border-right">
                                        <div class="font-size-h5 font-w700 text-dark">{{ $package->active_questions_count }}</div>
                                        <div class="text-muted text-uppercase font-size-xs font-w600">Pertanyaan</div>
                                    </div>
                                    <div class="col-6">
                                        <div class="font-size-h5 font-w700 text-dark">{{ $package->duration_minutes }}</div>
                                        <div class="text-muted text-uppercase font-size-xs font-w600">Menit</div>
                                    </div>
                                </div>
                                
                                @if($package->passing_score)
                                    <div class="text-center font-size-xs text-warning font-w700 mt-3">
                                        <i class="fa fa-award mr-1"></i> Nilai Kelulusan minimum: {{ $package->passing_score }}%
                                    </div>
                                @endif
                            </div>

                            <div class="block-content block-content-full block-content-sm bg-body-light border-top">
                                <button type="button" class="btn btn-primary w-100 font-w600" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#modal-start-exam"
                                        data-package-id="{{ $package->id }}"
                                        data-package-name="{{ $package->name }}"
                                        data-package-duration="{{ $package->duration_minutes }}"
                                        data-package-questions="{{ $package->active_questions_count }}"
                                        data-package-type="{{ $package->type_label }}"
                                        data-package-type-class="{{ $package->type_badge_class }}"
                                        data-package-classrooms="{{ $package->classrooms->pluck('name')->join(', ') }}">
                                    <i class="fa fa-play-circle mr-1"></i> Mulai Ujian
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="d-flex justify-content-end mt-2 mb-4">
                {{ $packages->links() }}
            </div>

            <!-- Modal Konfirmasi Mulai Ujian -->
            <div class="modal fade" id="modal-start-exam" tabindex="-1" role="dialog" aria-labelledby="modal-start-exam" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <form id="form-start-exam" action="" method="POST">
                            @csrf
                            <div class="block block-rounded shadow-none mb-0">
                                <div class="block-header block-header-default bg-primary">
                                    <h3 class="block-title text-white">Konfirmasi Mulai Ujian</h3>
                                    <div class="block-options">
                                        <button type="button" class="btn-block-option text-white" data-bs-dismiss="modal" aria-label="Close">
                                            <i class="fa fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="block-content fs-sm py-4">
                                    <div class="text-center mb-4">
                                        <i class="fa fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                                        <h4 class="mb-2">Anda yakin ingin memulai ujian ini?</h4>
                                        <p class="text-muted mb-1" id="modal-package-name-display"></p>
                                        <div id="modal-package-classrooms" class="mb-2"></div>
                                        <span class="badge" id="modal-package-type"></span>
                                    </div>
                                    <div class="row g-2 text-center bg-body-light p-3 rounded">
                                        <div class="col-6">
                                            <div class="font-size-h5 font-w700 text-dark" id="modal-package-questions">0</div>
                                            <div class="text-muted text-uppercase font-size-xs font-w600">Pertanyaan</div>
                                        </div>
                                        <div class="col-6 border-start">
                                            <div class="font-size-h5 font-w700 text-dark" id="modal-package-duration">0</div>
                                            <div class="text-muted text-uppercase font-size-xs font-w600">Menit</div>
                                        </div>
                                    </div>
                                    <div class="alert alert-info alert-permanent d-flex align-items-center mt-4 mb-0" role="alert">
                                        <i class="fa fa-info-circle me-2"></i>
                                        <p class="mb-0 fs-xs">
                                            Waktu pengerjaan akan dimulai setelah Anda menekan tombol "Ya, Mulai Sekarang". Pastikan koneksi internet Anda stabil.
                                        </p>
                                    </div>
                                </div>
                                <div class="block-content block-content-full block-content-sm text-end border-top">
                                    <button type="button" class="btn btn-alt-secondary" data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fa fa-play-circle me-1"></i> Ya, Mulai Sekarang
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif

        {{-- Riwayat Ujian --}}
        <h2 class="content-heading d-flex align-items-center mt-4">
            <i class="fa fa-history me-2 text-primary"></i> Riwayat Ujian Anda
        </h2>

        <div class="block block-rounded block-themed">
            <div class="block-header block-header-default bg-primary-dark">
                <h3 class="block-title">Riwayat Pengerjaan</h3>
            </div>
            
            <div class="block-content block-content-full" id="history-container">
                @include('exams._history_table')
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const container = document.getElementById('history-container');
        const startExamModal = document.getElementById('modal-start-exam');

        if (startExamModal) {
            startExamModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const packageId = button.getAttribute('data-package-id');
                const packageName = button.getAttribute('data-package-name');
                const packageDuration = button.getAttribute('data-package-duration');
                const packageQuestions = button.getAttribute('data-package-questions');
                const packageType = button.getAttribute('data-package-type');
                const packageTypeClass = button.getAttribute('data-package-type-class');
                const packageClassrooms = button.getAttribute('data-package-classrooms');

                const form = document.getElementById('form-start-exam');
                const nameDisplay = document.getElementById('modal-package-name-display');
                const questionsDisplay = document.getElementById('modal-package-questions');
                const durationDisplay = document.getElementById('modal-package-duration');
                const typeDisplay = document.getElementById('modal-package-type');
                const classroomsDisplay = document.getElementById('modal-package-classrooms');

                form.action = `{{ url('exams/packages') }}/${packageId}/start`;
                nameDisplay.textContent = packageName;
                questionsDisplay.textContent = packageQuestions;
                durationDisplay.textContent = packageDuration;
                
                typeDisplay.textContent = packageType;
                typeDisplay.className = `badge ${packageTypeClass}`;

                classroomsDisplay.innerHTML = '';
                if (packageClassrooms) {
                    packageClassrooms.split(', ').forEach(cls => {
                        const span = document.createElement('span');
                        span.className = 'badge bg-info-light text-info border border-info-lighter me-1';
                        span.innerHTML = `<i class="fa fa-users me-1"></i> ${cls}`;
                        classroomsDisplay.appendChild(span);
                    });
                }
            });
        }

        container.addEventListener('click', function(e) {
            // Find the closest link inside pagination-ajax
            const link = e.target.closest('.pagination-ajax a');
            
            if (link) {
                e.preventDefault();
                const url = link.getAttribute('href');
                
                // Show loading state (optional)
                container.style.opacity = '0.5';
                
                fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.text())
                .then(html => {
                    container.innerHTML = html;
                    container.style.opacity = '1';
                    
                    // Scroll back to container top if needed
                    // container.scrollIntoView({ behavior: 'smooth', block: 'start' });
                })
                .catch(error => {
                    console.error('Error fetching history:', error);
                    container.style.opacity = '1';
                });
            }
        });
    });
</script>
@endpush
