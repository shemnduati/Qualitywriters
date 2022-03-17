<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Order;
use App\User;
use App\Wallet;
use App\WalletTransaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\NewPayment;
use App\Mail\AutoFined;

class ApproveTasks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'approve:task';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Appprove tasks automatically 7days after they have been completed';

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
     * @return mixed
     */
    public function handle()
    {
        $uploads =  Order::where('status', 3)->get();
        foreach ($uploads as $upload ){
            $orderId = $upload['id'];
            $updatedAt = $upload['updated_at'];
            $approvalTime = 10080;
            $rangeTime = $updatedAt->diffInMinutes(Carbon::now());
            if ($rangeTime > $approvalTime) {

                $ifVerified = WalletTransaction::where('order_id', $orderId)->where('type', 0)->where('status', 0)->count();

                if ($ifVerified > 0) {
                    // Change status to completed
                    $theOrder = Order::findOrFail($orderId);
                    $theOrder->status = 5;
                    $theOrder->update();
                    return response(['status' => 'success'], 200);
                } elseif ($ifVerified == 0) {
                    // Change status to completed
                    $theOrder = Order::findOrFail($orderId);
                    $theOrder->status = 5;
                    $theOrder->update();
        
                    // get order details
                    $order = Order::where('id', $orderId)->first();
        
                    // Check if Writer has a wallet
                    $ifWalletExists = Wallet::where('user_id', $order['assigned_user_id'])->count();
        
                    if ($ifWalletExists > 0) {
                        // Update wallet
                        $myWallet = Wallet::where('user_id', $order['assigned_user_id'])->first();
                        $wallet = Wallet::findOrFail($myWallet['id']);
                        $wallet->amount = $wallet['amount'] + $order['total_amount'];
                        $wallet->update();
                    } elseif ($ifWalletExists == 0) {
                        // Create wallet as wallet does not exist
                        $wallet = new Wallet();
                        $wallet->user_id = $order['assigned_user_id'];
                        $wallet->amount = $order['total_amount'];
                        $wallet->save();
                    }
        
                    $transaction = new WalletTransaction();
                    $transaction->user_id = $order['assigned_user_id'];
                    $transaction->type = 0;
                    $transaction->order_id = $orderId;
                    $transaction->order_number = $order['order_number'];
                    $transaction->amount = $order['total_amount'];
                    $transaction->balance = User::find($order['assigned_user_id'])->wallet->amount;
                    $transaction->save();
        
                    if ($order['completed_time'] > $order['deadline']) {
                        $myWallet = Wallet::where('user_id', $order['assigned_user_id'])->first();
                        $wallet = Wallet::findOrFail($myWallet['id']);
                        $wallet->amount = $wallet['amount'] - (0.15 * $order['total_amount']);
                        $wallet->update();
        
                        $transaction = new WalletTransaction();
                        $transaction->user_id = $order['assigned_user_id'];
                        $transaction->type = 1;
                        $transaction->percentage = 15;
                        $transaction->order_id = $orderId;
                        $transaction->order_number = $order['order_number'];
                        $transaction->description = "Lateness";
                        $transaction->amount = -0.15 * $order['total_amount'];
                        $transaction->balance = User::find($order['assigned_user_id'])->wallet->amount;
                        $transaction->save();
        
                        $email = User::where('id', $order['assigned_user_id'])->value('email');
                        $data = array(
                            'orderNo' => $order['order_number']
                        );
                        Mail::to($email)->send(new AutoFined($data));
                    }
        
                    return response(['status' => 'success'], 200);
                }
            } 
        }
        \Log::info("Scheduler is working fine!");
    }
}
