<!DOCTYPE html>
       <html lang="en">
       <head>
           <meta charset="UTF-8">
           <meta name="viewport" content="width=device-width, initial-scale=1.0">
           <title>Welcome to Community Management</title>
           @vite(['resources/sass/app.scss', 'resources/js/app.js'])
       </head>
       <body>
           <nav class="navbar navbar-expand-lg">
               <a class="navbar-brand" href="/">Community Management</a>
               <div class="collapse navbar-collapse">
                   <ul class="navbar-nav ms-auto">
                       <li class="nav-item">
                           <a class="nav-link" href="{{ route('login') }}">Login</a>
                       </li>
                       <li class="nav-item">
                           <a class="nav-link" href="{{ route('register') }}">Register</a>
                       </li>
                   </ul>
               </div>
           </nav>
           <div class="container mt-5 text-center">
               <h1 class="mb-4">Welcome to Community Management SaaS</h1>
               <p class="lead mb-4">A scalable platform to manage your communities and events effortlessly.</p>
               <p>Join or create communities to organize events, track RSVPs, and engage with members.</p>
               <div class="row mt-5">
                   <div class="col-md-4">
                       <div class="card mb-4">
                           <div class="card-body">
                               <h5 class="card-title">Create Communities</h5>
                               <p class="card-text">Build and manage your own community spaces.</p>
                           </div>
                       </div>
                   </div>
                   <div class="col-md-4">
                       <div class="card mb-4">
                           <div class="card-body">
                               <h5 class="card-title">Event Management</h5>
                               <p class="card-text">Organize and monitor events with ease.</p>
                           </div>
                       </div>
                   </div>
                   <div class="col-md-4">
                       <div class="card mb-4">
                           <div class="card-body">
                               <h5 class="card-title">Real-Time Updates</h5>
                               <p class="card-text">Stay informed with notifications.</p>
                           </div>
                       </div>
                   </div>
               </div>
               <a href="{{ route('register') }}" class="btn btn-primary btn-lg me-2">Get Started</a>
               <a href="{{ route('login') }}" class="btn btn-outline-primary btn-lg">Login</a>
           </div>
       </body>
       </html>