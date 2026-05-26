@extends('layouts.app')

@section('title', 'Detail Kelas: ' . $classroom->name)
@section('page-heading', 'Kelola Kelas: ' . $classroom->name)

@section('content')
@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/js/plugins/select2/css/select2.min.css') }}">
@endpush
<div class="row">
    <div class="col-md-12">
        <div class="block block-rounded">
            <ul class="nav nav-tabs nav-tabs-block" role="tablist">
                <li class="nav-item">
                    <button class="nav-link active" id="students-tab" data-bs-toggle="tab" data-bs-target="#students" role="tab">
                        <i class="fa fa-users me-1"></i> Daftar Siswa
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" id="packages-tab" data-bs-toggle="tab" data-bs-target="#packages" role="tab">
                        <i class="fa fa-boxes me-1"></i> Paket Soal
                    </button>
                </li>
                <li class="nav-item ms-auto">
                    <a href="{{ route('classrooms.index') }}" class="btn btn-sm btn-alt-secondary m-2">
                        <i class="fa fa-arrow-left me-1"></i> Kembali
                    </a>
                </li>
            </ul>
            <div class="block-content tab-content">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                {{-- Tab: Siswa --}}
                <div class="tab-pane active" id="students" role="tabpanel">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5>Daftar Siswa ({{ $classroom->students->count() }})</h5>
                        </div>
                        <div class="col-md-6 text-end">
                            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modal-add-student">
                                <i class="fa fa-user-plus me-1"></i> Tambah Siswa
                            </button>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-vcenter">
                            <thead>
                                <tr>
                                    <th class="text-center" style="width: 50px;">#</th>
                                    <th>Nama Siswa</th>
                                    <th>Email</th>
                                    <th class="text-center" style="width: 100px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($classroom->students as $student)
                                <tr>
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td class="fw-semibold">{{ $student->name }}</td>
                                    <td>{{ $student->email }}</td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-alt-danger" title="Keluarkan dari kelas"
                                            onclick="if(confirm('Apakah Anda yakin ingin mengeluarkan siswa ini dari kelas?')) { document.getElementById('remove-student-{{ $student->id }}').submit(); }">
                                            <i class="fa fa-times"></i>
                                        </button>
                                        <form id="remove-student-{{ $student->id }}" action="{{ route('classrooms.students.remove', [$classroom->id, $student->id]) }}" method="POST" style="display: none;">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">Belum ada siswa di kelas ini.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Tab: Paket Soal --}}
                <div class="tab-pane" id="packages" role="tabpanel">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5>Paket Soal yang Ditugaskan ({{ $classroom->questionPackages->count() }})</h5>
                        </div>
                        <div class="col-md-6 text-end">
                            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modal-assign-package">
                                <i class="fa fa-plus me-1"></i> Tugaskan Paket Soal
                            </button>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-vcenter">
                            <thead>
                                <tr>
                                    <th class="text-center" style="width: 50px;">#</th>
                                    <th>Nama Paket Soal</th>
                                    <th class="text-center">Tipe</th>
                                    <th class="text-center" style="width: 100px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($classroom->questionPackages as $package)
                                <tr>
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td class="fw-semibold">{{ $package->name }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-secondary">{{ ucfirst(str_replace('_', ' ', $package->package_type)) }}</span>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-alt-danger" title="Tarik dari kelas"
                                            onclick="if(confirm('Apakah Anda yakin ingin menarik paket soal ini dari kelas?')) { document.getElementById('remove-package-{{ $package->id }}').submit(); }">
                                            <i class="fa fa-times"></i>
                                        </button>
                                        <form id="remove-package-{{ $package->id }}" action="{{ route('classrooms.packages.remove', [$classroom->id, $package->id]) }}" method="POST" style="display: none;">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">Belum ada paket soal yang ditugaskan ke kelas ini.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modals --}}

<!-- Modal: Add Student -->
<div class="modal fade" id="modal-add-student" tabindex="-1" role="dialog" aria-labelledby="modal-add-student" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('classrooms.students.add', $classroom->id) }}" method="POST">
                @csrf
                <div class="block block-rounded block-transparent mb-0">
                    <div class="block-header block-header-default">
                        <h3 class="block-title">Tambah Siswa ke Kelas</h3>
                        <div class="block-options">
                            <button type="button" class="btn-block-option" data-bs-dismiss="modal" aria-label="Close">
                                <i class="fa fa-fw fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <div class="block-content fs-sm">
                        <div class="mb-4">
                            <label class="form-label" for="user_id">Pilih Siswa</label>
                            <select class="js-select2 form-select" id="user_id" name="user_id" style="width: 100%;" data-placeholder="Cari nama atau email siswa.." required>
                                <option></option><!-- Required for data-placeholder -->
                                @foreach($availableStudents as $student)
                                    <option value="{{ $student->id }}">{{ $student->name }} ({{ $student->email }})</option>
                                @endforeach
                            </select>
                            <div class="form-text text-muted">Hanya menampilkan siswa yang belum bergabung di kelas ini.</div>
                        </div>
                    </div>
                    <div class="block-content block-content-full text-end bg-body">
                        <button type="button" class="btn btn-sm btn-alt-secondary me-1" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-sm btn-primary">Tambahkan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Assign Package -->
<div class="modal fade" id="modal-assign-package" tabindex="-1" role="dialog" aria-labelledby="modal-assign-package" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('classrooms.packages.assign', $classroom->id) }}" method="POST">
                @csrf
                <div class="block block-rounded block-transparent mb-0">
                    <div class="block-header block-header-default">
                        <h3 class="block-title">Tugaskan Paket Soal</h3>
                        <div class="block-options">
                            <button type="button" class="btn-block-option" data-bs-dismiss="modal" aria-label="Close">
                                <i class="fa fa-fw fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <div class="block-content fs-sm">
                        <div class="mb-4">
                            <label class="form-label" for="question_package_id">Pilih Paket Soal</label>
                            <select class="js-select2 form-select" id="question_package_id" name="question_package_id" style="width: 100%;" data-placeholder="Cari paket soal.." required>
                                <option></option><!-- Required for data-placeholder -->
                                @foreach($availablePackages as $package)
                                    <option value="{{ $package->id }}">{{ $package->name }} ({{ ucfirst(str_replace('_', ' ', $package->package_type)) }})</option>
                                @endforeach
                            </select>
                            <div class="form-text text-muted">Hanya menampilkan paket soal yang sudah dipublikasi.</div>
                        </div>
                    </div>
                    <div class="block-content block-content-full text-end bg-body">
                        <button type="button" class="btn btn-sm btn-alt-secondary me-1" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-sm btn-primary">Tugaskan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@push('scripts')
    <script src="{{ asset('assets/js/lib/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/select2/js/select2.full.min.js') }}"></script>
    <script>
        jQuery(function() {
            // Inisialisasi Select2
            jQuery('.js-select2').each(function() {
                let el = jQuery(this);
                el.select2({
                    dropdownParent: el.closest('.modal'),
                    placeholder: el.data('placeholder'),
                    allowClear: true
                });
            });
        });
    </script>
@endpush
@endsection
