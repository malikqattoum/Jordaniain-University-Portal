@extends('layouts.app')

@section('title', 'تسجيل الدخول - نظام تسجيل الطلبة - الجامعة الأردنية')

@section('content')
<div class="min-vh-100 d-flex" style="font-family: Arial, sans-serif;" dir="ltr">
    <!-- Background Image Section -->
    <div class="flex-fill position-relative" style="
        background-image: url('https://regapp.ju.edu.jo/regapp/javax.faces.resource/images/bg-login.jpg.xhtml?ln=diamond-layout');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
    ">
        <!-- Overlay for better contrast -->
        <div class="position-absolute w-100 h-100" style="background-color: rgba(0, 0, 0, 0.2);"></div>
    </div>

    <!-- Login Panel -->
    <div class="bg-white shadow-lg d-flex flex-column" style="width: 384px; min-height: 100vh;">
        <!-- Header with logo -->
        <div class="p-4 text-center border-bottom">
            <img
                src="https://regapp.ju.edu.jo/regapp/javax.faces.resource/images/ujlogo.png.xhtml?ln=diamond-layout"
                alt="University of Jordan Logo"
                class="mx-auto mb-3 d-block"
                style="width: 80px; height: 80px; object-fit: contain;"
            />
            <h6 class="text-muted small mb-2">الجامعة الأردنية</h6>
            <h4 class="fw-bold text-dark mb-0">نظام تسجيل الطلبة</h4>
        </div>

        <!-- Login Form -->
        <div class="flex-fill p-4">
            <form method="POST" action="{{ route('student.login') }}">
                @csrf

                <!-- Username Field -->
                <div class="mb-3">
                    <input
                        type="text"
                        name="username"
                        id="username"
                        class="form-control text-end @error('username') is-invalid @enderror"
                        placeholder="اسم المستخدم"
                        value="{{ old('username') }}"
                        required
                        autofocus
                        style="padding: 12px 16px; border-radius: 6px; border: 1px solid #dee2e6;"
                    />
                    @error('username')
                        <div class="invalid-feedback d-block">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Password Field -->
                <div class="mb-3">
                    <input
                        type="password"
                        name="password"
                        id="password"
                        class="form-control text-end @error('password') is-invalid @enderror"
                        placeholder="كلمة السر"
                        required
                        style="padding: 12px 16px; border-radius: 6px; border: 1px solid #dee2e6;"
                    />
                    @error('password')
                        <div class="invalid-feedback d-block">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Login Button -->
                <div class="d-grid mb-4">
                    <button type="submit" class="btn btn-primary py-3 fw-medium" style="
                        background-color: #007bff;
                        border: none;
                        border-radius: 6px;
                        transition: all 0.2s ease;
                    ">
                        دخول
                    </button>
                </div>
            </form>

            <!-- Password Recovery Section -->
            <div class="text-center small text-muted mb-4">
                <div class="mb-2">:استعادة كلمة السر باستخدام</div>
                <div class="d-flex flex-column gap-1">
                    <a href="https://adresetpw.ju.edu.jo/" class="text-primary text-decoration-none">رقم الهاتف</a>
                    <a href="https://passwordreset.microsoftonline.com/" class="text-primary text-decoration-none">حساب مايكروسوفت</a>
                    <a href="#" class="text-primary text-decoration-none">البريد الإلكتروني البديل</a>
                </div>
            </div>

            <!-- Language Switch -->
            <div class="text-center">
                <a href="{{ route('student.login', ['lang' => 'en']) }}" class="text-primary text-decoration-none small">English</a>
            </div>
        </div>
    </div>
</div>

<style>
    /* Form Controls Focus State */
    .form-control:focus {
        border-color: #007bff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }

    /* Button Hover Effect */
    .btn-primary:hover {
        background-color: #0056b3 !important;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0, 123, 255, 0.3);
    }

    /* ltr Text Alignment */
    .text-end {
        text-align: right !important;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .min-vh-100 {
            flex-direction: column;
        }

        .min-vh-100 > div:first-child {
            height: 200px;
        }

        .min-vh-100 > div:last-child {
            width: 100% !important;
            min-height: calc(100vh - 200px);
        }
    }
</style>
@endsection
