<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Mock Login | MulaiAja CBT</title>
    
    <link rel="shortcut icon" href="{{ asset('assets/media/favicons/favicon.png') }}">
    <link rel="stylesheet" id="css-main" href="{{ asset('assets/css/codebase.min.css') }}">
    
    <style>
        body {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Inter', sans-serif;
        }
        .login-card {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 20px;
            padding: 40px;
            width: 100%;
            max-width: 480px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
            color: #fff;
        }
        .role-btn {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            color: #fff;
            padding: 16px;
            width: 100%;
            text-align: left;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            margin-bottom: 12px;
            display: flex;
            align-items: center;
        }
        .role-btn:hover {
            background: rgba(66, 153, 225, 0.15);
            border-color: #4299e1;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(66, 153, 225, 0.2);
        }
        .role-icon {
            font-size: 24px;
            margin-right: 16px;
            color: #4299e1;
            background: rgba(66, 153, 225, 0.1);
            width: 48px;
            height: 48px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .role-info h5 {
            margin: 0 0 2px 0;
            font-weight: 600;
            font-size: 16px;
        }
        .role-info p {
            margin: 0;
            font-size: 12px;
            color: #94a3b8;
        }
        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .login-header h2 {
            font-weight: 800;
            margin-bottom: 5px;
            background: linear-gradient(to right, #63b3ed, #4299e1);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
    </style>
</head>
<body>

<div class="login-card animated fadeInDown">
    <div class="login-header">
        <h2>MulaiAja CBT</h2>
        <p class="text-muted">Pilih Role Akun untuk Pengujian Instan</p>
    </div>

    @if (session('success'))
        <div class="alert alert-success bg-success-light border-0 text-white alert-dismissible" role="alert">
            <div class="d-flex align-items-center">
                <i class="fa fa-check-circle mr-2"></i>
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif

    <form action="{{ route('login') }}" method="POST">
        @csrf
        <input type="hidden" name="role" id="selected-role" value="student">
        
        <button type="submit" onclick="setRole('student')" class="role-btn">
            <div class="role-icon">
                <i class="fa fa-user-graduate"></i>
            </div>
            <div class="role-info">
                <h5>Siswa (Student)</h5>
                <p>Mengerjakan paket soal CBT, melihat hasil skor & evaluasi.</p>
            </div>
        </button>

        <button type="submit" onclick="setRole('teacher')" class="role-btn">
            <div class="role-icon" style="color: #ed8936; background: rgba(237, 137, 54, 0.1);">
                <i class="fa fa-chalkboard-teacher" style="color: #ed8936;"></i>
            </div>
            <div class="role-info">
                <h5>Guru (Teacher)</h5>
                <p>Membuat, mengedit paket soal, dan menambah bank soal.</p>
            </div>
        </button>

        <button type="submit" onclick="setRole('admin')" class="role-btn">
            <div class="role-icon" style="color: #f56565; background: rgba(245, 101, 101, 0.1);">
                <i class="fa fa-user-shield" style="color: #f56565;"></i>
            </div>
            <div class="role-info">
                <h5>Administrator</h5>
                <p>Akses kontrol penuh ke semua paket soal & pengerjaan.</p>
            </div>
        </button>
    </form>
    
    <div class="text-center mt-4">
        <p class="text-muted" style="font-size: 11px;">MulaiAja Developer Environment • Built for Junior Developers</p>
    </div>
</div>

<script>
    function setRole(role) {
        document.getElementById('selected-role').value = role;
    }
</script>
</body>
</html>
