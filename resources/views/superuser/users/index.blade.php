@extends('layouts.app')

@section('title', 'Manajemen User')
@section('page-heading', 'Manajemen User')

@section('content')
<div class="block block-rounded">
    <div class="block-header block-header-default">
        <h3 class="block-title">Daftar Pengguna</h3>
    </div>
    <div class="block-content">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Filter Form -->
        <form action="{{ route('superuser.users.index') }}" method="GET" class="mb-4">
            <div class="row g-3">
                <div class="col-md-4">
                    <input type="text" name="q" class="form-control" placeholder="Cari Nama atau Email..." value="{{ request('q') }}">
                </div>
                <div class="col-md-3">
                    <select name="role" class="form-select">
                        <option value="">Semua Role</option>
                        <option value="student" {{ request('role') == 'student' ? 'selected' : '' }}>Student</option>
                        <option value="teacher" {{ request('role') == 'teacher' ? 'selected' : '' }}>Teacher</option>
                        <option value="administrator" {{ request('role') == 'administrator' ? 'selected' : '' }}>Administrator</option>

                    </select>
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fa fa-filter me-1"></i> Filter
                    </button>
                </div>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-bordered table-striped table-vcenter">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 50px;">#</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th style="width: 15%;">Role</th>
                        <th class="text-center" style="width: 15%;">Status</th>
                        <th class="text-center" style="width: 150px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr>
                        <td class="text-center">{{ ($users->currentPage() - 1) * $users->perPage() + $loop->iteration }}</td>
                        <td class="fw-semibold">{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>
                            <span class="badge bg-{{ 
                                $user->role === 'superuser' ? 'danger' : 
                                ($user->role === 'administrator' ? 'warning' : 
                                ($user->role === 'teacher' ? 'info' : 'success')) 
                            }}">
                                {{ ucfirst($user->role) }}
                            </span>
                        </td>
                        <td class="text-center">
                            @if($user->is_approved)
                                <span class="badge bg-success">Approved</span>
                            @else
                                <span class="badge bg-warning">Pending</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="btn-group" role="group">
                                @if(!$user->is_approved)
                                    <button type="submit" form="approve-form-{{ $user->id }}" class="btn btn-sm btn-alt-success" title="Approve">
                                        <i class="fa fa-check"></i>
                                    </button>
                                @else
                                    <button type="submit" form="reject-form-{{ $user->id }}" class="btn btn-sm btn-alt-warning" title="Reject/Unapprove">
                                        <i class="fa fa-times"></i>
                                    </button>
                                @endif
                                
                                <button type="button" class="btn btn-sm btn-alt-primary" data-bs-toggle="modal" data-bs-target="#modal-role-{{ $user->id }}" title="Ubah Role">
                                    <i class="fa fa-user-tag"></i>
                                </button>
                                
                                <button type="button" class="btn btn-sm btn-alt-info" data-bs-toggle="modal" data-bs-target="#modal-password-{{ $user->id }}" title="Reset Password">
                                    <i class="fa fa-key"></i>
                                </button>

                                <button type="button" class="btn btn-sm btn-alt-danger" data-bs-toggle="modal" data-bs-target="#modal-delete-{{ $user->id }}" title="Hapus User">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </div>

                            {{-- Hidden Forms for Approval --}}
                            @if(!$user->is_approved)
                                <form id="approve-form-{{ $user->id }}" action="{{ route('superuser.users.approve', $user) }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            @else
                                <form id="reject-form-{{ $user->id }}" action="{{ route('superuser.users.reject', $user) }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            @endif
                        </td>
                    </tr>

                    <!-- Role Modal -->
                    <div class="modal fade" id="modal-role-{{ $user->id }}" tabindex="-1" role="dialog" aria-labelledby="modal-role-{{ $user->id }}" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <form action="{{ route('superuser.users.update-role', $user) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <div class="block block-rounded shadow-none mb-0">
                                        <div class="block-header block-header-default">
                                            <h3 class="block-title">Ubah Role: {{ $user->name }}</h3>
                                            <div class="block-options">
                                                <button type="button" class="btn-block-option" data-bs-dismiss="modal" aria-label="Close">
                                                    <i class="fa fa-times"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="block-content fs-sm">
                                            <div class="mb-4">
                                                <label class="form-label" for="role-{{ $user->id }}">Pilih Role Baru</label>
                                                <select class="form-select" id="role-{{ $user->id }}" name="role">
                                                    <option value="student" {{ $user->role === 'student' ? 'selected' : '' }}>Student</option>
                                                    <option value="teacher" {{ $user->role === 'teacher' ? 'selected' : '' }}>Teacher</option>
                                                    <option value="administrator" {{ $user->role === 'administrator' ? 'selected' : '' }}>Administrator</option>

                                                </select>
                                            </div>
                                        </div>
                                        <div class="block-content block-content-full block-content-sm text-end border-top">
                                            <button type="button" class="btn btn-alt-secondary" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-alt-primary">Simpan Perubahan</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Password Modal -->
                    <div class="modal fade" id="modal-password-{{ $user->id }}" tabindex="-1" role="dialog" aria-labelledby="modal-password-{{ $user->id }}" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <form action="{{ route('superuser.users.update-password', $user) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <div class="block block-rounded shadow-none mb-0">
                                        <div class="block-header block-header-default">
                                            <h3 class="block-title">Reset Password: {{ $user->name }}</h3>
                                            <div class="block-options">
                                                <button type="button" class="btn-block-option" data-bs-dismiss="modal" aria-label="Close">
                                                    <i class="fa fa-times"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="block-content fs-sm">
                                            <div class="mb-4">
                                                <label class="form-label" for="password-{{ $user->id }}">Password Baru</label>
                                                <input type="password" class="form-control" id="password-{{ $user->id }}" name="password" required>
                                            </div>
                                            <div class="mb-4">
                                                <label class="form-label" for="password-confirm-{{ $user->id }}">Konfirmasi Password Baru</label>
                                                <input type="password" class="form-control" id="password-confirm-{{ $user->id }}" name="password_confirmation" required>
                                            </div>
                                        </div>
                                        <div class="block-content block-content-full block-content-sm text-end border-top">
                                            <button type="button" class="btn btn-alt-secondary" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-alt-primary">Update Password</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Delete Modal -->
                    <div class="modal fade" id="modal-delete-{{ $user->id }}" tabindex="-1" role="dialog" aria-labelledby="modal-delete-{{ $user->id }}" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <form action="{{ route('superuser.users.destroy', $user) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <div class="block block-rounded shadow-none mb-0">
                                        <div class="block-header block-header-default">
                                            <h3 class="block-title">Hapus User: {{ $user->name }}</h3>
                                            <div class="block-options">
                                                <button type="button" class="btn-block-option" data-bs-dismiss="modal" aria-label="Close">
                                                    <i class="fa fa-times"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="block-content fs-sm py-4">
                                            <p class="mb-0">Apakah Anda yakin ingin menghapus user ini? Tindakan ini tidak dapat dibatalkan.</p>
                                        </div>
                                        <div class="block-content block-content-full block-content-sm text-end border-top">
                                            <button type="button" class="btn btn-alt-secondary" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-alt-danger">Ya, Hapus User</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4">Data tidak ditemukan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $users->links() }}
        </div>
    </div>
</div>
@endsection
