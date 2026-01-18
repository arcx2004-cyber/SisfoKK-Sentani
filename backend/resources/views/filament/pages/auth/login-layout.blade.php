<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Login - {{ config('app.name') }}</title>
        @filamentStyles
        {{-- @vite(['resources/css/app.css', 'resources/js/app.js']) --}} 
        <script src="https://cdn.tailwindcss.com"></script> 
        <script>
            tailwind.config = {
                theme: {
                    extend: {
                        colors: {
                            danger: '#e11d48',
                            primary: '#2563eb',
                            success: '#059669',
                            warning: '#d97706',
                        }
                    }
                }
            }
        </script>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
        <style>
            body { 
                margin: 0; 
                padding: 0; 
                font-family: 'Plus Jakarta Sans', sans-serif;
                background-color: #f3f4f6; /* Gray-100 */
                display: flex;
                align-items: center;
                justify-content: center;
                min-height: 100vh;
            }
            .login-card {
                background: white;
                width: 100%;
                max-width: 1000px;
                min-height: 600px;
                border-radius: 20px;
                box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
                display: flex;
                overflow: hidden;
                margin: 20px;
                position: relative;
            }
            
            /* Left Side: Form */
            .form-side {
                flex: 1;
                padding: 4rem;
                display: flex;
                flex-direction: column;
                justify-content: center;
                background: white;
                z-index: 10; /* Above the wave if needed */
            }

            /* Right Side: Illustration */
            .illustration-side {
                flex: 1.2;
                background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
                position: relative;
                display: none; /* Hidden on mobile */
                align-items: center;
                justify-content: center;
            }
            @media (min-width: 1024px) {
                .illustration-side { display: flex; }
            }

            /* Wave Divider */
            .wave-divider {
                position: absolute;
                top: 0;
                left: -1px; /* Overlap slightly */
                width: 150px; /* Width of the wave intrusion */
                height: 100%;
                z-index: 20;
                pointer-events: none;
            }
            
            /* Typography & Elements */
            .logo-img { height: 40px; margin-bottom: 2rem; }
            .heading { font-size: 1.875rem; font-weight: 800; color: #111827; margin-bottom: 0.5rem; }
            .subheading { color: #6b7280; font-size: 0.875rem; margin-bottom: 2rem; }
            
            /* Floating Image in Right Side */
            .hero-image {
                max-width: 90%;
                height: auto;
                object-fit: contain;
                position: relative;
                z-index: 5;
                filter: drop-shadow(0 10px 15px rgba(0,0,0,0.1));
            }
            
            /* Filament Form Overrides for specific look */
            .custom-login-actions button {
                background-color: #2563eb !important;
                color: #ffffff !important;
                border-radius: 8px !important;
                padding: 0.75rem 1rem !important;
                width: 100%;
                font-weight: 600;
                box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.3);
                display: flex;
                justify-content: center;
                align-items: center;
            }
            .custom-login-actions button:hover {
                background-color: #1d4ed8 !important;
            }
            /* Fallback for other filament buttons */
            .fi-btn {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 0.5rem;
                white-space: nowrap;
                border-radius: 0.5rem;
                font-weight: 500;
                transition-property: color, background-color, border-color, text-decoration-color, fill, stroke;
                transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
                transition-duration: 150ms;
                outline: 2px solid transparent;
                outline-offset: 2px;
                width: 100%; /* Force full width for login button */
            }
            .fi-btn-primary { 
                background-color: #2563eb !important; 
                color: #ffffff !important;
                border-radius: 8px !important; 
                padding: 0.75rem 1rem !important;
                box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            }
            .fi-btn-primary:hover {
                background-color: #1d4ed8 !important;
            }
            .fi-btn-label {
                font-size: 0.875rem;
                line-height: 1.25rem;
            }
            .fi-input { 
                border-radius: 8px !important; 
                border: 1px solid #d1d5db !important;
                padding: 0.5rem 0.75rem !important;
                width: 100%;
            }
            .fi-fo-field-wrp-label {
                color: #374151 !important;
                font-weight: 500;
                margin-bottom: 0.25rem;
                display: block;
            }
        </style>
    </head>
    <body class="antialiased text-gray-900">
        
        <div class="login-card">
            <!-- Left Side: Form -->
            <div class="form-side">
                <div>
                    <img src="{{ asset('logo.png') }}" alt="Logo" class="logo-img">
                    <h1 class="heading">Login</h1>
                    <p class="subheading">Selamat datang di Sistem Informasi Sekolah</p>
                    
                    {{ $slot }}

                    <div class="mt-8 text-xs text-gray-400 text-center">
                        &copy; {{ date('Y') }} Kalam Kudus Sentani
                    </div>
                </div>
            </div>

            <!-- Right Side: Illustration with Wave -->
            <div class="illustration-side">
                <!-- SVG Wave: White fill to blend with Left Side -->
                <svg class="wave-divider" viewBox="0 0 100 100" preserveAspectRatio="none">
                    <path d="M0 0 C 50 0 80 30 80 50 C 80 70 50 100 0 100 L 0 0 Z" fill="white" />
                </svg>

                <div style="text-align: center; z-index: 10; position: relative;">
                    <img src="{{ asset('images/school_login_illustration.png') }}" class="hero-image" alt="Illustration">
                    <div style="margin-top: 20px; font-weight: bold; color: #1e3a8a; font-size: 1.2rem;">
                        Permudah Interaksi Guru & Siswa
                    </div>
                </div>
            </div>
        </div>

        @livewire('notifications')
        @filamentScripts
        @livewireScripts
    </body>
</html>
