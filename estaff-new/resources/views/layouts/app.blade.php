<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>eStaff Portal</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 text-gray-800">

    {{-- Navbar --}}
    <nav class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
            <h1 class="text-xl font-bold text-blue-600">eStaff Portal</h1>

            <div class="space-x-4">
                <a href="/" class="font-medium">Home</a>
                <a href="/news" class="font-medium">News</a>
                <a href="/events" class="font-medium">Events</a>

                @auth
                    <a href="/dashboard" class="text-blue-600 font-semibold">Dashboard</a>
                @else
                    <a href="/auth/google" class="bg-blue-600 text-white px-4 py-2 rounded">
                        Login with Google
                    </a>
                @endauth
            </div>
        </div>
    </nav>

    {{-- Page Content --}}
    <main>
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="bg-white border-t mt-16">
        <div class="max-w-7xl mx-auto px-6 py-6 text-center text-sm text-gray-500">
            © {{ date('Y') }} eStaff Portal. All rights reserved.
        </div>
    </footer>

</body>
</html>
