@extends('layouts.app')

@section('title', 'Manajemen User')
@section('page-heading', 'Manajemen User')

@section('content')
<div class="block block-rounded">
    <div class="block-header block-header-default">
        <h3 class="block-title">Daftar Pengguna</h3>
    </div>
    <div class="block-content block-content-full">
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
                    @foreach($users as $user)
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
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
                            @if($user->role === 'administrator')
                                @if($user->is_approved)
                                    <span class="badge bg-success">Approved</span>
                                @else
                                    <span class="badge bg-warning text-dark">Pending</span>
                                @endif
                            @else
                                <span class="badge bg-secondary">N/A</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="btn-group">
                                @if($user->role === 'administrator' && !$user->is_approved)
                                    <form action="{{ route('superuser.users.approve', $user) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-alt-success" title="Approve">
                                            <i class="fa fa-check"></i>
                                        </button>
                                    </form>
                                @endif
                                
                                <button type="button" class="btn btn-sm btn-alt-primary" data-bs-toggle="modal" data-bs-target="#modal-role-{{ $user->id }}" title="Ubah Role">
                                    <i class="fa fa-user-tag"></i>
                                </button>
                                
                                <button type="button" class="btn btn-sm btn-alt-info" data-bs-toggle="modal" data-bs-target="#modal-password-{{ $user->id }}" title="Reset Password">
                                    <i class="fa fa-key"></i>
                                </button>

                                <form action="{{ route('superuser.users.destroy', $user) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus user ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-alt-danger" title="Hapus User">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </form>
                            </div>
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
                                                    <option value="superuser" {{ $user->role === 'superuser' ? 'selected' : '' }}>Superuser</option>
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
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
