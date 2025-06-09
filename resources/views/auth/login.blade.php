@extends('layouts.app')

@section('title', 'تسجيل الدخول - بوابة طلاب الجامعة الأردنية')

@section('content')
<div class="min-vh-100 d-flex align-items-center justify-content-center" style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card shadow-lg">
                    <div class="card-header text-center">
                        <div class="mb-3">
                            <i class="fas fa-university fa-3x text-white"></i>
                        </div>
                        <h4 class="mb-0">بوابة طلاب الجامعة الأردنية</h4>
                    </div>
                    <div class="card-body p-4">
                        <form method="POST" action="{{ route('login') }}">
                            @csrf
                            
                            <div class="mb-3">
                                <label for="username" class="form-label">اسم المستخدم</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-user"></i>
                                    </span>
                                    <input type="text" 
                                           class="form-control @error('username') is-invalid @enderror" 
                                           id="username" 
                                           name="username" 
                                           value="{{ old('username') }}" 
                                           required 
                                           autofocus
                                           placeholder="أدخل اسم المستخدم (مثال: jml20210001)">
                                </div>
                                @error('username')
                                    <div class="invalid-feedback d-block">
                                        {{ $message }}
                                    </div>
                                @enderror
                                <small class="form-text text-muted">
                                    اسم المستخدم = اختصار الاسم الأول + رقم الطالب
                                </small>
                            </div>
                            
                            <div class="mb-4">
                                <label for="password" class="form-label">كلمة المرور</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-lock"></i>
                                    </span>
                                    <input type="password" 
                                           class="form-control @error('password') is-invalid @enderror" 
                                           id="password" 
                                           name="password" 
                                           required
                                           placeholder="أدخل كلمة المرور">
                                </div>
                                @error('password')
                                    <div class="invalid-feedback d-block">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-sign-in-alt me-2"></i>
                                    تسجيل الدخول
                                </button>
                            </div>
                        </form>
                    </div>
                    <div class="card-footer text-center text-muted">
                        <small>© {{ date('Y') }} الجامعة الأردنية - جميع الحقوق محفوظة</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .card {
        border: none;
        border-radius: 15px;
        overflow: hidden;
    }
    
    .card-header {
        background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
        border: none;
        padding: 2rem;
    }
    
    .form-control {
        border-radius: 8px;
        padding: 12px;
        border: 2px solid #e9ecef;
        transition: all 0.3s;
    }
    
    .form-control:focus {
        border-color: #2a5298;
        box-shadow: 0 0 0 0.2rem rgba(42, 82, 152, 0.25);
    }
    
    .input-group-text {
        background-color: #f8f9fa;
        border: 2px solid #e9ecef;
        border-left: none;
    }
    
    .btn-primary {
        background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
        border: none;
        padding: 12px;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.3s;
    }
    
    .btn-primary:hover {
        background: linear-gradient(135deg, #2a5298 0%, #1e3c72 100%);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(42, 82, 152, 0.3);
    }
</style>
@endsection