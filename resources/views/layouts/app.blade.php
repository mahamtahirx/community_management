<!DOCTYPE html>
     <html lang="en">
     <head>
         <meta charset="UTF-8">
         <meta name="viewport" content="width=device-width, initial-scale=1.0">
         <title>Community Management</title>
         @vite(['resources/sass/app.scss', 'resources/js/app.js'])
     </head>
     <body>
        @if(session('success'))
    <div class="toast-container position-fixed bottom-0 end-0 p-3">
        <div class="toast align-items-center text-white bg-success border-0 show" role="alert">
            <div class="d-flex">
                <div class="toast-body">
                    {{ session('success') }}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    </div>
@endif

@if(session('error'))
    <div class="toast-container position-fixed bottom-0 end-0 p-3">
        <div class="toast align-items-center text-white bg-danger border-0 show" role="alert">
            <div class="d-flex">
                <div class="toast-body">
                    {{ session('error') }}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    </div>
@endif

         <nav class="navbar navbar-expand-lg">
             <a class="navbar-brand" href="{{ route('dashboard') }}">Community Management</a>
             <div class="collapse navbar-collapse">
                 <ul class="navbar-nav ms-auto">
                     @auth
                         <li class="nav-item">
                             <a class="nav-link" href="{{ route('communities.index') }}">Communities</a>
                         </li>
                         <li class="nav-item">
                             <form action="{{ route('logout') }}" method="POST">
                                 @csrf
                                 <button type="submit" class="nav-link btn btn-link">Logout</button>
                             </form>
                         </li>
                     @else
                         <li class="nav-item">
                             <a class="nav-link" href="{{ route('login') }}">Login</a>
                         </li>
                         <li class="nav-item">
                             <a class="nav-link" href="{{ route('register') }}">Register</a>
                         </li>
                     @endauth
                 </ul>
             </div>
         </nav>
         <main class="py-4">
             @yield('content')
         </main>
     </body>
     </html>
     <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
