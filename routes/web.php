<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;


Route::namespace('Frontend')->group(function(){
    
    Route::middleware('guest')->group(function () {
        Route::get('/','HomeController@index')->name('home');
        Route::get('/profil','HomeController@profil')->name('profil');
        Route::get('/sitemap.xml','HomeController@sitemap')->name('sitemap');
        
        // Route::prefix('/video')->name('video.')->group(function () {
        //     Route::get('/', 'VideoController@index')->name('index');
        //     Route::get('/data', 'VideoController@data')->name('data');
        //     Route::get('/{slug}', 'VideoController@show')->name('show');
        // });

        
        Route::namespace('Auth')->group(function () {
            Route::get('/login','LoginController@showLoginForm')->name('login');
            Route::post('/login','LoginController@login');

            
            Route::prefix('/register')->group(function () {
                Route::get('/','RegisterController@showRegisterForm')
                ->middleware('guest:web')->name('register');
                Route::post('/','RegisterController@register');
            });
        });

    });
    
    Route::middleware(['auth:web'])->group(function () {

        Route::namespace('Auth')->group(function () {
            Route::post('/logout','LoginController@logout')->name('logout');

            Route::get('/verify-email','EmailVerificationPromptController@__invoke')
            ->middleware('auth')
            ->name('verification.notice');
            
            Route::get('/verify-email/{id}/{hash}','VerifyEmailController@__invoke')
            ->middleware(['auth', 'signed', 'throttle:6,1'])
            ->name('verification.verify');

            Route::post('/email/verification-notification','EmailVerificationNotificationController@store')
            ->middleware(['throttle:6,1'])
            ->name('verification.send');

            Route::middleware(['verified'])
            ->group(function () {
                Route::get('/pendaftaran/detail','RegisterController@detail')->name('register.detail');
                Route::post('/pendaftaran/detail','RegisterController@detailStore');
                Route::get('/pendaftaran/revisi/{id}','RegisterController@revisi')->name('register.revisi');
                Route::post('/pendaftaran/revisi/{id}','RegisterController@revisiStore');
            });
        });

        Route::prefix('/user')->name('user.')
        ->middleware(['verified'])
        ->group(function () {
            route::get('/', function () {    
                return redirect()->route('user.dashboard');
            });

            Route::get('/dashboard','DashboardController@index')
            ->name('dashboard');

            Route::get('/interview','DashboardController@interview')
            ->name('interview');

            // Route::prefix('/profile')->name('profile.')->group(function () {
            //     Route::get('/', 'BiodataController@index')->name('index');
            //     Route::get('/form', 'BiodataController@form')->name('form');
            //     Route::post('/store','BiodataController@store')->name('store');
            // });
            
            Route::prefix('/password')->group(function () {
                Route::get('/', 'UserController@password')->name('password');
                Route::post('/','UserController@passwordUpdate');
            });

            Route::prefix('/profile')->name('biodata.')->group(function () {
                Route::get('/', 'BiodataController@index')->name('index');
                Route::get('/form', 'BiodataController@form')->name('form');
                Route::post('/store','BiodataController@store')->name('store');
            });

            Route::prefix('/medical')->name('medical.')->group(function () {
                Route::get('/', 'MedicalController@index')->name('index');
                Route::post('/store', 'MedicalController@store')->name('store');
                Route::get('/{id}', 'MedicalController@show')->name('show');
            });
            

            Route::prefix('/training')->name('training.')->group(function () {
                Route::get('/', 'TrainingController@index')->name('index');
                Route::get('/{id}', 'TrainingController@show')->name('show');
            });
        });

    });

});

Route::prefix('admin')->namespace('Admin')->name('admin.')->group(function(){
    
    Route::namespace('Auth')->group(function () {
        Route::middleware('guest')->group(function () {
            Route::get('/','LoginController@showLoginForm')->name('login');
            Route::post('/','LoginController@login');
        });
        Route::middleware('auth:admin')->group(function () {
            Route::post('/logout','LoginController@logout')->name('logout');
        });
    });

    Route::middleware('auth:admin')->group(function () {

        Route::get('/beranda','DashboardController@index')->name('dashboard');

        // Route::prefix('/pendaftaran')->name('register.')->group(function () {
        //     Route::get('/', 'PendaftaranController@index')->name('index');
        //     Route::get('/pending', 'PendaftaranController@pending')->name('pending');
        //     Route::get('/create', 'PendaftaranController@create')->name('create');
        //     Route::post('/store','PendaftaranController@store')->name('store');
        //     Route::get('/data', 'PendaftaranController@data')->name('data');
        //     Route::get('/slug', 'PendaftaranController@check_slug')->name('slug');
        //     Route::get('/{id}', 'PendaftaranController@show')->name('show');
        //     Route::get('/{id}/edit','PendaftaranController@edit')->name('edit');
        //     Route::post('/{id}/update','PendaftaranController@update')->name('update');
        //     Route::delete('/{id}/hapus','PendaftaranController@destroy')->name('delete');
        // });

        Route::prefix('/talent')->name('talent.')->group(function () {
            Route::get('/', 'TalentController@index')->name('index');
            Route::get('/data', 'TalentController@data')->name('data');
            Route::get('/{id}', 'TalentController@show')->name('show');
            Route::get('/{id}/edit','TalentController@edit')->name('edit');
            Route::post('/{id}/update','TalentController@update')->name('update');
            Route::post('/{id}/state','TalentController@state')->name('state');
            Route::delete('/{id}/hapus','TalentController@destroy')->name('delete');
        });

        Route::prefix('/staff')->name('staff.')->group(function () {
            Route::get('/', 'StaffController@index')->name('index');
            Route::get('/create', 'StaffController@create')->name('create');
            Route::post('/store','StaffController@store')->name('store');
            Route::get('/data', 'StaffController@data')->name('data');
            Route::get('/{id}', 'StaffController@show')->name('show');
            Route::get('/{id}/edit','StaffController@edit')->name('edit');
            Route::post('/{id}/update','StaffController@update')->name('update');
            Route::delete('/{id}/hapus','StaffController@destroy')->name('delete');
        });

        
        Route::prefix('/medical')->name('medical.')->group(function () {
            Route::get('/', 'MedicalController@index')->name('index');
            Route::get('/data', 'MedicalController@data')->name('data');
            Route::get('/{id}', 'MedicalController@show')->name('show');
            Route::get('/{id}/edit','MedicalController@edit')->name('edit');
            Route::post('/{id}/update','MedicalController@update')->name('update');
            Route::post('/{id}/state','MedicalController@state')->name('state');
            Route::delete('/{id}/hapus','MedicalController@destroy')->name('delete');
        });

        Route::prefix('/interview')->name('interview.')->group(function () {
            
            Route::prefix('/jadwal')->name('jadwal.')->group(function () {
                Route::get('/', 'JadwalInterviewController@index')->name('index');
                Route::get('/create', 'JadwalInterviewController@create')->name('create');
                Route::post('/store', 'JadwalInterviewController@store')->name('store');
                Route::get('/data', 'JadwalInterviewController@data')->name('data');
                Route::get('/{id}', 'JadwalInterviewController@show')->name('show');
                Route::get('/{id}/edit','JadwalInterviewController@edit')->name('edit');
                Route::post('/{id}/update','JadwalInterviewController@update')->name('update');
                Route::delete('/{id}/hapus','JadwalInterviewController@destroy')->name('delete');
            });
            
            Route::prefix('/hasil')->name('hasil.')->group(function () {
                Route::get('/', 'HasilInterviewController@index')->name('index');
                Route::get('/create', 'HasilInterviewController@create')->name('create');
                Route::post('/store', 'HasilInterviewController@store')->name('store');
                Route::get('/data', 'HasilInterviewController@data')->name('data');
                Route::get('/{id}', 'HasilInterviewController@show')->name('show');
                Route::get('/{id}/edit','HasilInterviewController@edit')->name('edit');
                Route::post('/{id}/update','HasilInterviewController@update')->name('update');
                Route::delete('/{id}/hapus','HasilInterviewController@destroy')->name('delete');
            });

        });

        
        Route::prefix('/training')->name('training.')->group(function () {
            
            Route::prefix('/program')->name('program.')->group(function () {
                Route::get('/', 'ProgramTrainingController@index')->name('index');
                Route::get('/create', 'ProgramTrainingController@create')->name('create');
                Route::post('/store', 'ProgramTrainingController@store')->name('store');
                Route::get('/data', 'ProgramTrainingController@data')->name('data');
                Route::get('/{id}', 'ProgramTrainingController@show')->name('show');
                Route::get('/{id}/edit','ProgramTrainingController@edit')->name('edit');
                Route::post('/{id}/update','ProgramTrainingController@update')->name('update');
                Route::delete('/{id}/hapus','ProgramTrainingController@destroy')->name('delete');
            });
            
            Route::get('/', 'TrainingController@index')->name('index');
            Route::get('/data', 'TrainingController@data')->name('data');
            Route::post('/store', 'TrainingController@store')->name('store');
            Route::get('/{id}', 'TrainingController@show')->name('show');
            Route::get('/{id}/edit','TrainingController@edit')->name('edit');
            Route::post('/{id}/update','TrainingController@update')->name('update');
            Route::delete('/{id}/hapus','TrainingController@destroy')->name('delete');

        });

        Route::prefix('/departure')->name('departure.')->group(function () {
            Route::get('/', 'DepartureController@index')->name('index');
            Route::get('/data', 'DepartureController@data')->name('data');
            Route::get('/{id}', 'DepartureController@show')->name('show');
            Route::get('/{id}/edit','DepartureController@edit')->name('edit');
            Route::post('/{id}/update','DepartureController@update')->name('update');
            Route::delete('/{id}/hapus','DepartureController@destroy')->name('delete');
        });

        Route::prefix('/lowongan')->name('lowongan.')->group(function () {
            Route::get('/', 'LowonganController@index')->name('index');
            Route::get('/create', 'LowonganController@create')->name('create');
            Route::post('/store', 'LowonganController@store')->name('store');
            Route::get('/data', 'LowonganController@data')->name('data');
            Route::get('/{id}', 'LowonganController@show')->name('show');
            Route::get('/{id}/edit','LowonganController@edit')->name('edit');
            Route::post('/{id}/update','LowonganController@update')->name('update');
            Route::delete('/{id}/hapus','LowonganController@destroy')->name('delete');
        });
    });
});