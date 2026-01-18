<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Kartu Pelajar - {{ $student->nama_lengkap }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @page {
            size: 85.6mm 53.98mm;
            margin: 0;
        }
        body {
            margin: 0;
            padding: 0;
            width: 85.6mm;
            height: 53.98mm;
            overflow: hidden;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        #id-card {
            width: 85.6mm;
            height: 53.98mm;
            position: relative;
            background-color: white;
            page-break-after: avoid;
            page-break-inside: avoid;
        }
        @media print {
            body {
                width: 85.6mm;
                height: 53.98mm;
            }
        }
    </style>
</head>
<body>
    <!-- ID Card Container -->
    <div id="id-card" class="relative w-[85.6mm] h-[53.98mm] bg-white overflow-hidden text-gray-800 font-sans select-none border border-gray-100 print:border-0">
        
        <!-- Background Geometric Design -->
        <div class="absolute inset-0 z-0">
            <div class="absolute top-0 right-0 w-[70%] h-full bg-blue-900 transform -skew-x-12 translate-x-10"></div>
            <div class="absolute top-0 right-0 w-[50%] h-full bg-blue-800 transform -skew-x-12 translate-x-10 opacity-80"></div>
            <div class="absolute bottom-0 left-0 w-32 h-32 bg-yellow-400 rounded-full mix-blend-multiply filter blur-2xl opacity-20 animate-blob"></div>
            <div class="absolute top-0 left-0 w-32 h-32 bg-blue-400 rounded-full mix-blend-multiply filter blur-2xl opacity-20 animate-blob animation-delay-2000"></div>
            <!-- Subtle Pattern -->
            <div class="absolute inset-0 opacity-[0.03]" style="background-image: radial-gradient(#000 1px, transparent 1px); background-size: 4px 4px;"></div>
        </div>

        <!-- Header -->
        <div class="relative z-10 flex items-center pt-4 px-5">
            <!-- Logo -->
            <div class="flex-shrink-0 w-10 h-10 bg-white rounded-full flex items-center justify-center shadow-sm p-1">
                 <img src="{{ asset('images/logo.png') }}" onerror="this.src='https://ui-avatars.com/api/?name=SP&background=0D8ABC&color=fff'" class="w-full h-full object-contain" alt="Logo">
            </div>
            <div class="ml-3 text-white">
                <h1 class="text-[10px] font-bold tracking-[0.2em] uppercase opacity-90">KARTU PELAJAR</h1>
                <h2 class="text-[12px] font-extrabold uppercase tracking-wide leading-tight mt-px">SEKOLAH PENGGERAK</h2>
                <p class="text-[6px] opacity-75 font-light">Jl. Sentani No. 6, Jayapura</p>
            </div>
        </div>

        <!-- Content Body -->
        <div class="relative z-10 flex mt-4 px-6 items-start">
            
            <!-- Text Data -->
            <div class="flex-1 space-y-[2px] pt-1">
                <div class="space-y-[1px]">
                    <p class="text-[7px] text-gray-500 uppercase tracking-wider font-semibold mb-0.5">Nama Siswa</p>
                    <p class="text-[11px] font-bold text-blue-900 uppercase truncate pr-2 leading-none">
                        {{ $student->nama_lengkap }}
                    </p>
                </div>
                
                <div class="grid grid-cols-2 gap-2 mt-2">
                    <div class="space-y-[1px] mt-1">
                        <p class="text-[7px] text-gray-500 uppercase tracking-wider font-semibold mb-0.5">NIS / NISN</p>
                        <p class="text-[9px] font-medium text-gray-800 leading-none font-mono">
                            {{ $student->nis }} / {{ $student->nisn }}
                        </p>
                    </div>
                     <div class="space-y-[1px] mt-1">
                        <p class="text-[7px] text-gray-500 uppercase tracking-wider font-semibold mb-0.5">Berlaku Hingga</p>
                        <p class="text-[9px] font-medium text-gray-800 leading-none">
                            {{ now()->addYears(3)->format('Y') }}
                        </p>
                    </div>
                </div>

                <div class="space-y-[1px] mt-2">
                     <p class="text-[7px] text-gray-500 uppercase tracking-wider font-semibold mb-0.5">Tempat, Tanggal Lahir</p>
                    <p class="text-[9px] font-medium text-gray-800 leading-none uppercase">
                        {{ $student->tempat_lahir }}, {{ $student->tanggal_lahir ? $student->tanggal_lahir->format('d M Y') : '' }}
                    </p>
                </div>
            </div>

            <!-- Photo & Barcode Area -->
            <div class="flex flex-col items-center space-y-2 ml-2">
                 <!-- Photo Frame -->
                <div class="w-[18mm] h-[23mm] bg-gray-200 rounded-lg border-[3px] border-white shadow-sm overflow-hidden relative group">
                    @if($student->foto)
                        <img src="{{ asset('storage/' . $student->foto) }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex flex-col items-center justify-center bg-gray-100 text-gray-400">
                            <!-- SVG Placeholder -->
                            <svg class="w-8 h-8 opacity-50" fill="currentColor" viewBox="0 0 24 24"><path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                        </div>
                    @endif
                    <!-- Overlay effect -->
                    <div class="absolute inset-0 bg-gradient-to-tr from-blue-900/10 to-transparent pointer-events-none"></div>
                </div>
            </div>
        </div>

        <!-- Footer / Barcode -->
        <div class="absolute bottom-3 left-6 z-10">
             <div class="flex flex-col">
                <!-- Simulated Barcode -->
                 <div class="h-6 w-32 flex items-end space-x-px opacity-80">
                    @for($i=0; $i<40; $i++)
                        <div class="bg-black w-px h-{{ rand(3,6) }}"></div>
                        <div class="bg-transparent w-px h-6"></div>
                        <div class="bg-black w-[2px] h-{{ rand(4,6) }}"></div>
                    @endfor
                </div>
                <span class="text-[7px] font-mono tracking-widest text-gray-500 mt-0.5">{{ $student->nis }}</span>
             </div>
        </div>

        <!-- Signature Area -->
        <div class="absolute bottom-3 right-6 z-10 text-right">
            <p class="text-[6px] text-gray-500 mb-1">Mengetahui,</p>
            <p class="text-[6px] text-gray-500 mb-4">Kepala Sekolah</p>
             <div class="relative">
                <p class="text-[8px] font-bold text-blue-900 border-b border-gray-300 pb-0.5 inline-block min-w-[60px]">Dr. H. Budi Santoso</p>
            </div>
            <p class="text-[6px] text-gray-400 mt-0.5">NIP. 19750101 200001 1 001</p>
        </div>

        <!-- Decorative stripe bottom -->
        <div class="absolute bottom-0 left-0 w-full h-1.5 bg-gradient-to-r from-yellow-400 to-blue-600"></div>
    </div>

    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>
