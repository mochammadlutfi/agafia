<?php

namespace App\Helpers;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use DB;

class MenuHelper
{
    public static function get()
    {
        $menuData = collect([]);

        $user = auth()->guard('admin')->user();
    
        $menuData->push([
            "icon" => "fa fa-home",
            "name" => "Beranda",
            "to" => "admin.dashboard",
        ]);
        
        if(in_array($user->level, ['operational_manager', 'owner'])){
            $menuData->push([
                "icon" => "fa fa-users",
                "name" => "Staff",
                "to" => "admin.staff.index",
            ]);
        }
    
        $menuData->push([
            "icon" => "fa fa-id-badge",
            "name" => "Talent",
            "to" => "admin.talent.index",
        ]);
    
        if(in_array($user->level, ['operational_manager', 'owner'])){
            $menuData->push([
                "icon" => "fa fa-plane-departure",
                "name" => "Lamaran",
                "to" => "admin.lamaran.index",
            ]);
        }
    
        $menuData->push([
            "icon" => "fa fa-file-medical",
            "name" => "Interview",
            "to" => "admin.interview.index",
        ]);

        $menuData->push([
            "name" => 'Training',
            "icon" => 'fa fa-chalkboard-teacher',
            "subActivePaths" => 'admin.training.*',
            "sub" => [
                [
                    "name" => 'Peserta',
                    "to" => 'admin.training.index',
                ],
                [
                    "name" => 'Program',
                    "to" => 'admin.training.program.index',
                ],
            ]
        ]);
    
        $menuData->push([
            "icon" => "fa fa-stethoscope",
            "name" => "Medical Checkup",
            "to" => "admin.medical.index",
        ]);
        
        $menuData->push([
            "icon" => "fa fa-suitcase",
            "name" => "Lowongan Kerja",
            "to" => "admin.lowongan.index",
        ]);
    
        return $menuData->all();
    }
    

    
    public static function user(){

        $menuData = Collect([]);

        $menuData->push([
            "icon" => "fa fa-home",
            "name" => "Beranda",
            "to" => "user.dashboard",
        ]);
        
    //     $menuData->push([
    //         "icon" => "fa fa-user-friends",
    //         "name" => "Anak",
    //         "to" => "user.anak.index",
    //     ]);

        
    //     $menuData->push([
    //         "icon" => "fa fa-wallet",
    //         "name" => "Invoice",
    //         "to" => "user.invoice.index",
    //     ]);

    //     if(auth()->guard('web')->user()->anak()->where('status', 'Aktif')->count()){

    //         $menuData->push([
    //             "icon" => "fa fa-calendar-alt",
    //             "name" => "Absensi",
    //             "to" => "user.absen.index",
    //         ]);
    
    //         $menuData->push([
    //             "icon" => "fa fa-check-square",
    //             "name" => "Penilaian",
    //             "to" => "user.nilai.index",
    //         ]);
    
    //         $menuData->push([
    //             "icon" => "fa fa-clock-rotate-left",
    //             "name" => "Aktivitas",
    //             "to" => "user.aktivitas.index",
    //         ]);

    //         $menuData->push([
    //             'svg' => true,
    //             "icon" => '<g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
	// 	<path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2" />
	// 	<path d="M9 5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v0a2 2 0 0 1-2 2h-2a2 2 0 0 1-2-2m0 12v-5m3 5v-1m3 1v-3" />
	// </g>',
    //             "name" => "Raport",
    //             "to" => "user.raport.index",
    //         ]);
    //     }
        
    //     if(auth()->guard('web')->user()->anak()->where('status', 'Aktif')->where('isLaundry', 1)->count()){
    //         $menuData->push([
    //             "svg" => true,
    //             "icon" => '	<path fill="currentColor" d="M6 13.975v-3L4.125 12l-3-5.2l6.6-3.8H9q.4 1.2.95 2.1T12 6t2.05-.9T15 3h1.275l6.575 3.825L19.875 12L18 10.975v4.8l-1.575 1.375q-.175.15-.4.238t-.45.087q-.15 0-.35-.1t-.4-.25l-2.65-2.275q-.8-.7-1.8-1.025T8.35 13.5q-.6 0-1.188.113T6 13.975m-1.35 5.4l-1.3-1.525L5.525 16q.575-.5 1.313-.763t1.537-.262t1.525.263t1.3.762l2.9 2.475q.3.25.713.388t.837.137q.45 0 .838-.125t.687-.4L19.35 16.6l1.3 1.55L18.475 20q-.575.5-1.3.75T15.65 21t-1.537-.25T12.8 20l-2.9-2.475q-.3-.25-.687-.387T8.375 17q-.425 0-.837.138t-.713.387z" />',
    //             "name" => "Laundry",
    //             "to" => "user.laundry.index",
    //         ]);
    //     }
        
    //     if(auth()->guard('web')->user()->anak()->where('status', 'Aktif')->where('isAntarJemput', 1)->count()){
    //         $menuData->push([
    //             "icon" => "fa fa-car-side",
    //             "name" => "Antar Jemput",
    //             "to" => "user.antarjemput.index",
    //         ]);
    //     }

        return $menuData->all();
    }

    public static function permission()
    {
        $data = auth()->guard('admin')->user()->getAllPermissions()->toArray();
        $permission = array();
        foreach ($data as $element) {
            $permission[$element['module']][] = $element['name'];
        }

        return $permission;
    }
}
