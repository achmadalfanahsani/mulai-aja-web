@extends('layouts.app')

@section('title', 'Buat Paket Soal Baru | MulaiAja')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="block block-rounded block-themed">
                <div class="block-header block-header-default bg-primary-dark">
                    <h3 class="block-title">Buat Paket Soal Baru</h3>
                    <div class="block-options">
                        <a href="{{ route('question-packages.index', ['type' => request('type')]) }}" class="btn btn-sm btn-alt-secondary">
                            <i class="fa fa-arrow-left mr-1"></i> Kembali
                        </a>
                    </div>
                </div>

                <div class="block-content">
                    <form action="{{ route('question-packages.store') }}" method="POST" class="py-3">
                        @csrf

                        {{-- Nama Paket --}}
                        <div class="form-group mb-4">
                            <label class="form-label" for="name">Nama Paket Soal <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                                name="name" value="{{ old('name') }}"
                                placeholder="Contoh: Ujian Tengah Semester Matematika Kelas X" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Deskripsi --}}
                        <div class="form-group mb-4">
                            <label class="form-label" for="description">Deskripsi Singkat</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
                                rows="4" placeholder="Tuliskan petunjuk pengerjaan atau rincian cakupan materi ujian...">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Tipe Paket --}}
                        <div class="form-group mb-4">
                            <label class="form-label" for="package_type">Tipe Paket Soal <span
                                    class="text-danger">*</span></label>
                            @php
                                $requestedType = $type ?? request()->query('type');
                                $isReadonly = !empty($requestedType);
                            @endphp

                            @if ($isReadonly)
                                @php
                                    $packageLabels = [
                                        'multiple_choice' => 'Pilihan Ganda',
                                        'essay' => 'Isian Singkat',
                                        'mixed' => 'Campuran',
                                    ];
                                    $displayValue = $packageLabels[$requestedType] ?? $requestedType;
                                @endphp

                                <!-- Input teks biasa yang hanya bisa dibaca (readonly) -->
                                <input type="text" class="form-control" id="package_type_display" value="{{ $displayValue }}" readonly>

                                <input type="hidden" name="package_type" value="{{ $requestedType }}">
                            @else
                                <select class="form-select @error('package_type') is-invalid @enderror" id="package_type"
                                    name="package_type" onchange="toggleShuffleOptions()" required>
                                    <option value="multiple_choice"
                                        {{ old('package_type') == 'multiple_choice' ? 'selected' : '' }}>Pilihan Ganda
                                    </option>
                                    <option value="essay" {{ old('package_type') == 'essay' ? 'selected' : '' }}>Isian
                                        Singkat (Essay)</option>
                                </select>
                            @endif
                            <small class="text-muted">Pilih tipe soal yang akan ada di dalam paket ini agar terorganisir
                                dengan baik.</small>
                            @error('package_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            {{-- Durasi --}}
                            <div class="col-md-6 form-group mb-4">
                                <label class="form-label" for="duration_minutes">Durasi Pengerjaan (Menit) <span
                                        class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fa fa-clock"></i></span>
                                    <input type="number"
                                        class="form-control @error('duration_minutes') is-invalid @enderror"
                                        id="duration_minutes" name="duration_minutes"
                                        value="{{ old('duration_minutes', 60) }}" min="1" max="480" required>
                                    <span class="input-group-text">Menit</span>
                                </div>
                                <small class="text-muted">Maksimal pengerjaan 8 jam (480 menit).</small>
                                @error('duration_minutes')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Passing Score --}}
                            <div class="col-md-6 form-group mb-4">
                                <label class="form-label" for="passing_score">Nilai Kelulusan minimum (%)</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fa fa-award"></i></span>
                                    <input type="number" class="form-control @error('passing_score') is-invalid @enderror"
                                        id="passing_score" name="passing_score" value="{{ old('passing_score') }}"
                                        placeholder="Contoh: 70" min="0" max="100">
                                    <span class="input-group-text">%</span>
                                </div>
                                <small class="text-muted">Biarkan kosong jika tidak ada batasan nilai kelulusan.</small>
                                @error('passing_score')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Attempt Limit --}}
                            <div class="col-md-6 form-group mb-4">
                                <label class="form-label" for="attempt_limit">Batas Pengerjaan (Attempts)</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fa fa-redo"></i></span>
                                    <input type="number" class="form-control @error('attempt_limit') is-invalid @enderror"
                                        id="attempt_limit" name="attempt_limit" value="{{ old('attempt_limit') }}"
                                        placeholder="Contoh: 1" min="1">
                                    <span class="input-group-text">Kali</span>
                                </div>
                                <small class="text-muted">Berapa kali siswa boleh mengerjakan paket ini. Kosong = Tanpa
                                    batas.</small>
                                @error('attempt_limit')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Konfigurasi Pengacakan --}}
                        <div class="block block-rounded block-bordered bg-body-light mb-4 p-3">
                            <h4 class="font-size-sm font-w700 text-uppercase text-muted mb-3">Pengaturan Keamanan &
                                Pengacakan</h4>

                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" id="shuffle_questions"
                                    name="shuffle_questions" value="1" checked>
                                <label class="form-check-label font-w600" for="shuffle_questions">Acak Urutan Soal
                                    (Shuffle Questions)</label>
                                <div class="text-muted font-size-sm">Setiap siswa akan mendapatkan urutan soal yang
                                    berbeda-beda saat ujian dimulai.</div>
                            </div>

                            <div class="form-check form-switch mt-3" id="shuffle_answers_container">
                                <input class="form-check-input" type="checkbox" id="shuffle_answers"
                                    name="shuffle_answers" value="1" checked>
                                <label class="form-check-label font-w600" for="shuffle_answers">Acak Urutan Opsi Jawaban
                                    (Shuffle Options)</label>
                                <div class="text-muted font-size-sm">Opsi jawaban (A s/d E) akan teracak secara otomatis
                                    untuk menghindari kerjasama antar siswa.</div>
                            </div>
                        </div>

                        {{-- Submit buttons --}}
                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <a href="{{ route('question-packages.index', ['type' => $requestedType]) }}"
                                class="btn btn-alt-secondary">Batal</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-save mr-1"></i> Simpan & Buat
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
        function toggleShuffleOptions() {
            const packageTypeSelect = document.getElementById('package_type');
            const packageTypeDisplay = document.getElementById('package_type_display');
            const container = document.getElementById('shuffle_answers_container');

            let type = '';
            if (packageTypeSelect) {
                type = packageTypeSelect.value;
            } else if (packageTypeDisplay) {
                type = "{{ $requestedType }}";
            }

            if (type === 'essay') {
                container.style.display = 'none';
            } else {
                container.style.display = 'block';
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            toggleShuffleOptions();
        });
    </script>
@endpush
