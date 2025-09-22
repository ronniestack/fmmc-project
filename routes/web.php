<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\SummerNoteController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\FrontendController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\DashboardController;

Auth::routes();

// ================== FRONTEND ROUTE ================== //
// Landing page (homepage)
Route::get('/',[FrontendController::class,'index'])->name('home');


// ================== AUTHENTICATED ROUTES ================== //
Route::middleware(['auth'])->group(function () {

    // ----------- Dashboard ----------- //
    // Main dashboard page
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');
    
        
    // ----------- User Management ----------- //
    // CRUD operations for users (with permission checks)
    Route::resource('user',UserController::class)
        ->middleware('permission:users.view|users.create|users.edit|users.delete');  

    // View logged-in user's profile
    Route::get('profile',[ProfileController::class,'index'])
        ->name('profile');  
        
    // Update user profile details
    Route::patch('profile-update/{user}',[ProfileController::class,'profileUpdate'])
        ->name('user.profile.update'); 
        
    // Update user password
    Route::patch('user/password-update/{user}',[UserController::class,'password_update'])
        ->name('user.password.update');  
        
    // Update user profile picture
    Route::put('user/profile-pic/{user}',[UserController::class,'updateProfileImage'])
        ->name('user.profile.image.update');  
        
    // Remove user profile picture
    Route::patch('delete-profile-image/{user}',[UserController::class,'deleteProfileImage'])
        ->name('delete.profile.image');  
        
    // View soft-deleted users
    Route::get('user-trash', [UserController::class, 'trashView'])
        ->name('user.trash');     

    // Restore user from trash
    Route::get('user-restore/{id}', [UserController::class, 'restore'])
        ->name('user.restore');
    
    // Permanently delete user
    Route::delete('user-delete/{id}', [UserController::class, 'force_delete'])
        ->name('user.force.delete');      


    // ----------- Application Settings ----------- //
    // View settings page
    Route::get('settings', [SettingController::class, 'index'])
        ->name('setting')
        ->middleware('permission:setting.update');   
        
    // Update application settings
    Route::post('settings/{setting}', [SettingController::class, 'update'])
        ->name('setting.update');      

    
    // ----------- Category Management ----------- //
    // CRUD operations for categories (with permissions)
    Route::resource('category', CategoryController::class)
        ->middleware('permission:categories.view|categories.create|categories.edit|categories.delete');     
        

    // ----------- Service Management ----------- //
    // CRUD operations for services (with permissions)
    Route::resource('service', ServiceController::class)
        ->middleware('permission:services.view|services.create|services.edit|services.delete');        

    // View soft-deleted services
    Route::get('service-trash', [ServiceController::class, 'trashView'])
        ->name('service.trash'); 
        
    // Restore soft-deleted service
    Route::get('service-restore/{id}', [ServiceController::class, 'restore'])
        ->name('service.restore');         

    // Permanently delete service
    Route::delete('service-delete/{id}', [ServiceController::class, 'force_delete'])
        ->name('service.force.delete');         


    // ----------- Summernote (Rich Text Editor) ----------- //
    // Upload image to Summernote editor
    Route::post('summernote',[SummerNoteController::class,'summerUpload'])
        ->name('summer.upload.image'); 
    
    // Delete image from Summernote editor
    Route::post('summernote/delete',[SummerNoteController::class,'summerDelete'])
        ->name('summer.delete.image');   


    // ----------- Doctor Features ----------- //
    // View all bookings for logged-in doctor
    Route::get('doctor-booking',[UserController::class,'DoctorBookings'])
        ->name('doctor.bookings');    
        
    // View details of a specific booking
    Route::get('my-booking/{id}',[UserController::class,'show'])
        ->name('doctor.booking.detail');         

    // Update doctor profile details
    Route::patch('doctor-profile-update/{doctor}',[ProfileController::class,'doctorProfileUpdate'])
        ->name('doctor.profile.update');           

    // Update doctor biography
    Route::put('doctor-bio/{doctor}',[DoctorController::class,'updateBio'])
        ->name('doctor.bio.update');         

});


    // ================== FRONTEND ROUTES ================== //
    // Show services that belong to a specific category
    Route::get('/categories/{category}/services', [FrontendController::class, 'getServices'])
        ->name('get.services');  

    // Show doctor that provide a specific service
    Route::get('/services/{service}/doctors', [FrontendController::class, 'getDoctors'])
        ->name('get.doctors');            

    // Show doctor availability for a given date (optional parameter {date})
    Route::get('/doctors/{doctor}/availability/{date?}', [FrontendController::class, 'getDoctorAvailability'])              
        ->name('doctor.availability');


    // ================== APPOINTMENT ROUTES ================== //
    // Store (create) a new appointment booking
    Route::post('/bookings', [AppointmentController::class, 'store'])
        ->name('bookings.store');  
        
    // Show all appointments (index page)
    // Protected by permission middleware (user must have at least one of these permissions)
    Route::get('/appointments', [AppointmentController::class, 'index'])    
        ->name('appointments')
        ->middleware('permission:appointments.view|appointments.create|services.appointments|appointments.delete');  
        
    // Update the status of an appointment (e.g., booked, rendered, cancelled)
    Route::post('/appointments/update-status', [AppointmentController::class, 'updateStatus'])
        ->name('appointments.update.status');     
        
    // Update appointment or other records directly from the dashboard
    Route::post('/update-status', [DashboardController::class, 'updateStatus'])
        ->name('dashboard.update.status');              


