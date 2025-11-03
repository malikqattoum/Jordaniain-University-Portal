<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'بوابة طلاب الجامعة الأردنية')</title>

    <!-- Bootstrap RTL CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
        }

        .layout-wrapper {
            min-height: 100vh;
            display: flex;
        }

        .layout-sidebar {
            width: 280px;
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            color: white;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            z-index: 1000;
        }

        .layout-content-wrapper {
            margin-right: 280px;
            width: calc(100% - 280px);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .layout-topbar {
            background: white;
            padding: 1rem 2rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .layout-content {
            flex: 1;
            padding: 2rem;
        }

        .layout-footer {
            background: #343a40;
            color: white;
            text-align: center;
            padding: 1rem;
            margin-top: auto;
        }

        .logo {
            display: flex;
            align-items: center;
            padding: 1.5rem;
            text-decoration: none;
            color: white;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .logo-image {
            width: 40px;
            height: 40px;
            margin-left: 10px;
        }

        .layout-menu {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .layout-menu li {
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .layout-menu a {
            display: flex;
            align-items: center;
            padding: 1rem 1.5rem;
            color: white;
            text-decoration: none;
            transition: background-color 0.3s;
        }

        .layout-menu a:hover {
            background-color: rgba(255,255,255,0.1);
            color: white;
        }

        .layout-menu .active {
            background-color: rgba(255,255,255,0.2);
        }

        .layout-menuitem-icon {
            margin-left: 10px;
            width: 20px;
        }

        .card {
            border: none;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            border-radius: 10px;
            margin-bottom: 2rem;
        }

        .card-header {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            color: white;
            border-radius: 10px 10px 0 0 !important;
            padding: 1rem 1.5rem;
        }

        .btn-primary {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            border: none;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #2a5298 0%, #1e3c72 100%);
        }

        .table th {
            background-color: #f8f9fa;
            border-top: none;
        }

        .profile-item {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .topbar-menu {
            display: flex;
            list-style: none;
            margin: 0;
            padding: 0;
            gap: 10px;
            align-items: center;
        }

        .academic-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
        }

        .stat-value {
            font-size: 2rem;
            font-weight: bold;
            color: #1e3c72;
            margin-bottom: 0.5rem;
        }

        .stat-label {
            color: #6c757d;
            font-size: 0.9rem;
        }

        @media (max-width: 768px) {
            .layout-sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s;
            }

            .layout-sidebar.show {
                transform: translateX(0);
            }

            .layout-content-wrapper {
                margin-right: 0;
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="layout-wrapper">
        @auth('student')
        <!-- Sidebar -->
        <div class="layout-sidebar">
            <a href="{{ route('student.dashboard') }}" class="logo">
                {{-- <i class="fas fa-university logo-image"></i> --}}
                <img src="https://regapp.ju.edu.jo/regapp/javax.faces.resource/images/ujlogo.png.xhtml?ln=diamond-layout" alt="u-logo"
                    style="width: 80px; height: 80px; object-fit: contain;"
                >
                <span>الجامعة الأردنية</span>
            </a>

            <ul class="layout-menu">
                <li>
                    <a href="{{ route('student.dashboard') }}" class="{{ request()->routeIs('student.dashboard') ? 'active' : '' }}">
                        <i class="fas fa-home layout-menuitem-icon"></i>
                        <span>الصفحة الرئيسية</span>
                    </a>
                </li>
                <!-- التسجيل وبيانات الطالب -->
                <li class="layout-root-menuitem">
                    <a href="#" data-bs-toggle="collapse" data-bs-target="#regMenu" aria-expanded="false" aria-controls="regMenu">
                        <i class="fas fa-book layout-menuitem-icon"></i>
                        <span class="layout-menuitem-text">التسجيل وبيانات الطالب</span>&nbsp;
                        <i class="fas fa-chevron-down layout-submenu-toggler"></i>
                    </a>
                    <ul id="regMenu" class="collapse" role="menu">
                        <li><a href="#"><i class="fas fa-calendar-alt layout-menuitem-icon"></i><span class="layout-menuitem-text">حجز الوقت الحر وموعد التسجيل</span></a></li>
                        <li><a href="#"><i class="fas fa-table layout-menuitem-icon"></i><span class="layout-menuitem-text">تسجيل المواد</span></a></li>
                        <li><a href="#"><i class="fas fa-times layout-menuitem-icon"></i><span class="layout-menuitem-text">إلغاء طلبات فتح الشعب</span></a></li>
                        <li><a href="#"><i class="fas fa-minus layout-menuitem-icon"></i><span class="layout-menuitem-text">الانسحاب بدون ترصيد</span></a></li>
                        <li><a class="rotated-icon" href="#"><i class="fas fa-file-alt layout-menuitem-icon"></i><span class="layout-menuitem-text">العبء الدراسي الآلي</span></a></li>
                        <li><a href="{{ route('student.fee-payment') }}" class="{{ request()->routeIs('student.fee-payment') ? 'active' : '' }}"><i class="fas fa-money-bill layout-menuitem-icon"></i><span class="layout-menuitem-text">تسديد الرسوم الجامعية</span></a></li>
                        <li><a href="{{ route('student.payment-history') }}" class="{{ request()->routeIs('student.payment-history') ? 'active' : '' }}"><i class="fas fa-history layout-menuitem-icon"></i><span class="layout-menuitem-text">تاريخ المدفوعات</span></a></li>
                        <li><a href="#"><i class="fas fa-phone layout-menuitem-icon"></i><span class="layout-menuitem-text">بيانات الطالب الشخصية</span></a></li>
                    </ul>
                </li>
                <!-- الطلبات الإلكترونية -->
                <li class="layout-root-menuitem">
                    <a href="#" data-bs-toggle="collapse" data-bs-target="#onlineFormsMenu" aria-expanded="false" aria-controls="onlineFormsMenu">
                        <i class="fas fa-envelope layout-menuitem-icon"></i>
                        <span class="layout-menuitem-text">الطلبات الإلكترونية</span>&nbsp;
                        <i class="fas fa-chevron-down layout-submenu-toggler"></i>
                    </a>
                    <ul id="onlineFormsMenu" class="collapse" role="menu">
                        <li><a href="#"><i class="fas fa-link layout-menuitem-icon"></i><span class="layout-menuitem-text">تقديم عذر الامتحان التكميلي</span></a></li>
                        <li><a href="#"><i class="fas fa-sync layout-menuitem-icon"></i><span class="layout-menuitem-text">تقديم طلب انتقال</span></a></li>
                        <li><a href="#"><i class="fas fa-question layout-menuitem-icon"></i><span class="layout-menuitem-text">اسأل المرشد</span></a></li>
                        <li><a href="#"><i class="fas fa-check layout-menuitem-icon"></i><span class="layout-menuitem-text">براءة الذمة للطلبة</span></a></li>
                        <li><a href="#"><i class="fas fa-file layout-menuitem-icon"></i><span class="layout-menuitem-text">نموذج اعتماد مكان التدريب</span></a></li>
                    </ul>
                </li>
                <!-- الاستفسارات -->
                <li class="layout-root-menuitem">
                    <a href="#" data-bs-toggle="collapse" data-bs-target="#inqMenu" aria-expanded="false" aria-controls="inqMenu">
                        <i class="fas fa-search layout-menuitem-icon"></i>
                        <span class="layout-menuitem-text">الاستفسارات</span>&nbsp;
                        <i class="fas fa-chevron-down layout-submenu-toggler"></i>
                    </a>
                    <ul id="inqMenu" class="collapse fade-in-right" role="menu">
                        <li><a href="#"><i class="fas fa-print layout-menuitem-icon"></i><span class="layout-menuitem-text">طباعة الجدول والقسيمة</span></a></li>
                        <li><a href="#"><i class="fas fa-percentage layout-menuitem-icon"></i><span class="layout-menuitem-text">نتائج منتصف الفصل</span></a></li>
                        <li><a href="{{ route('student.academic-results') }}" class="{{ request()->routeIs('student.academic-results') ? 'active' : '' }}"><i class="fas fa-chart-bar layout-menuitem-icon"></i><span class="layout-menuitem-text">نتائج الطالب النهائية</span></a></li>
                        <li><a href="#"><i class="fas fa-table layout-menuitem-icon"></i><span class="layout-menuitem-text">الجدول الدراسي</span></a></li>
                        <li><a href="#"><i class="fas fa-search layout-menuitem-icon"></i><span class="layout-menuitem-text">الاستفسار عن المواد المطروحة</span></a></li>
                        <li><a href="#"><i class="fas fa-list layout-menuitem-icon"></i><span class="layout-menuitem-text">الخطة الدراسية</span></a></li>
                        <li><a href="#"><i class="fas fa-sync layout-menuitem-icon"></i><span class="layout-menuitem-text">التخصصات المسموح الانتقال إليها</span></a></li>
                    </ul>
                </li>
                <li>
                    <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="fas fa-sign-out-alt layout-menuitem-icon"></i>
                        <span>خروج</span>
                    </a>
                </li>
            </ul>
        </div>

        <!-- Content Wrapper -->
        <div class="layout-content-wrapper">
            <!-- Topbar -->
            <div class="layout-topbar">
                <div class="topbar-left">
                    <h5 class="mb-0">@yield('page-title', 'بوابة طلاب الجامعة الأردنية')</h5>
                </div>
                <div class="topbar-right">
                    <ul class="topbar-menu">
                        <li class="profile-item">
                            <i class="fas fa-user"></i>
                            <span>{{ Auth::guard('student')->user()->name }}</span>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Main Content -->
            <div class="layout-content">
                @yield('content')
            </div>

            <!-- Footer -->
            <div class="layout-footer">
                <span>برمجة وإعداد - فريق معلومات الطلبة مركز تكنولوجيا المعلومات الجامعة الأردنية © {{ date('Y') }}</span>
            </div>
        </div>
        @endauth

        @guest('student')
        <div class="container-fluid">
            @yield('content')
        </div>
        @endguest
    </div>

    <!-- Logout Form -->
    @auth('student')
    <form id="logout-form" action="{{ route('student.logout') }}" method="POST" style="display: none;">
        @csrf
    </form>
    @endauth

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @yield('scripts')
</body>
</html>
