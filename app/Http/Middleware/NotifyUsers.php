<?php

     namespace App\Http\Middleware;

     use Closure;
     use Illuminate\Http\Request;
     use Illuminate\Support\Facades\Auth;

     class NotifyUsers
     {
         public function handle(Request $request, Closure $next)
         {
             if (Auth::check()) {
                 // Example: Add notifications to session or database
                 $notifications = session()->get('notifications', []);
                 $notifications[] = ['message' => 'Welcome back, ' . Auth::user()->name . '!'];
                 session(['notifications' => $notifications]);
             }
             return $next($request);
         }
     }