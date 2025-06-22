<?php

namespace App\Helpers;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use DB;
use App\Models\Caleg;
class GeneralHelp
{
    public static function currency($value){
        return number_format($value,0,',','.');
    }

    public static function squence_invoice()
    {
        
    }
}