<?php

namespace App\Console\Commands;

use App\Models\Penjualan;
use App\Models\Pembayaran;
use Illuminate\Console\Command;
use Xendit\Xendit;
Xendit::setApiKey('xnd_development_9OgIWa7nlFShkJIaNgjt0kyJ93rSLWLISVeuO0tzfhKsctEZc5LZMcHKY1nOQ');

class updatePenjualan extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:penjualan';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Penjualan';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $barang = [];
        Penjualan::select('pembayaran.id_pembayaran', 'penjualan.id_penjualan', 'penjualan.status')
        ->join('pembayaran', 'penjualan.id_penjualan', '=', 'pembayaran.id_penjualan')
        ->where('penjualan.status', 'PENDING')
        ->get()
        ->groupBy('id_penjualan')
        ->each(function ($item, $key) use (&$barang) {
            $temp = [];
            $temp['id_penjualan'] = $key;
            foreach($item as $b){
                $status_db = $b['status'];
                $id_pembayaran = $b['id_pembayaran'];
            }
            $getInvoice = \Xendit\Invoice::retrieve($id_pembayaran);
            if($getInvoice['status'] != $status_db){
                $status = $getInvoice['status'];
                Penjualan::where('id_penjualan', $key)->update(['status' => $status]);
            }
            $temp['status'] = $status;
            $temp['id_pembayaran'] = $id_pembayaran;
            $barang[] = $temp;
        });
        \Log::info("Penjualan has been updated!");
        }
    }
