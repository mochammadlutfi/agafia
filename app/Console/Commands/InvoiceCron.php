<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Invoice;
use App\Models\InvoiceDetail;
use App\Models\Anak;
use App\Helpers\GeneralHelp;
class InvoiceCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoice:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        \Log::info("Cron job Berhasil di jalankan " . date('Y-m-d H:i:s'));
        $today = Carbon::now();
        // $anak
        foreach($anak as $a){
            $inv = new Invoice();
            $inv->nomor = GeneralHelp::squence_invoice();
            $inv->anak_id = $a->id;
            $inv->user_id = $a->user_id;
            $inv->tgl = Carbon::now();
            $inv->tgl_tempo = Carbon::now()->addMonth(1);
            $inv->metode = 'Transfer';
            $inv->status = 'unpaid';
            $inv->total = $total;
    
            $line = new InvoiceDetail();
            $line->invoice_id = $inv->id;
            $line->tipe = 'SPP';
            $line->harga = $a->paket->spp;
            $line->qty = 1;
            $line->save($line);
            
            if($a->is_laundry){
                $line = new InvoiceDetail();
                $line->tipe =                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            'SPP';
                $line->harga = $a->paket->spp;
                $line->qty = 1;
                $inv->detail->save($line);
            }
    
            if($a->is_antarjemput){
                if($a->is_laundry){
                    $line = new InvoiceDetail();
                    $line->tipe =                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            'SPP';
                    $line->harga = $a->paket->spp;
                    $line->qty = 1;
                    $inv->detail->save($line);
                }
            }
        }

    }
}
