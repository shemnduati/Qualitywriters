<?php

namespace App\Http\Controllers\API;

use App\Dispute;
use App\Http\Controllers\Controller;
use App\Mail\NewPayment;
use App\Order;
use App\User;
use App\Wallet;
use App\WalletTransaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\AutoFined;

class WalletTransactionsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index()
    {
        //
    }

    public function walletBalance()
    {
        $user = auth()->user()->id;

        return User::find($user)->wallet;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function isVerified($orderId)
    {
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

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function showTransactions()
    {
    return WalletTransaction::where('user_id', auth()->user()->id)->orderBy('id', 'desc')->latest()->paginate(10);
}

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    public function pay(Request $request)
    {
        $request->validate([
            'amount' => 'required',
            'Paymethod' => 'required',
        ]);
        $myWallet = Wallet::WHERE('user_id', $request->id)->first();
        $wallet = Wallet::findOrFail($myWallet['id']);
        $wallet->amount = $myWallet['amount'] - $request->amount;
        $wallet->save();

        $transaction = new WalletTransaction();
        $transaction->user_id = $request->id;
        $transaction->Payment_method = $request->Paymethod;
        $transaction->amount = $request->amount;
        $transaction->type = 3;
        $transaction->balance = User::find($request->id)->wallet->amount;
        $transaction->save();

        $email = User::where('id', $request->id)->value('email');
        $data = array(
            'amount'=> $request->amount,
        );
        Mail::to($email)->send(new NewPayment($data));

        return response(['status' => 'success'], 200);
    }

    public function fine(Request $request)
    {
        $request->validate([
            'percentage' => 'required',
            'description' => 'required',
        ]);

        $theOrder = Order::findOrFail($request->orderId);
        $totalAmount = $theOrder['total_amount'];
        $fineAmount = ($request->percentage / 100) * $totalAmount;

        $ifWalletExists = Wallet::where('user_id', $theOrder['assigned_user_id'])->count();
        if ($ifWalletExists > 0) {
            // Update wallet
            $myWallet = Wallet::where('user_id', $theOrder['assigned_user_id'])->first();
            $wallet = Wallet::findOrFail($myWallet['id']);
            $wallet->amount = $wallet['amount'] - $fineAmount;
            $wallet->update();
        } elseif ($ifWalletExists == 0) {
            // Create wallet as wallet does not exist
            $wallet = new Wallet();
            $wallet->user_id = $theOrder['assigned_user_id'];
            $wallet->amount = -$fineAmount;
            $wallet->save();
        }

        $transaction = new WalletTransaction();
        $transaction->user_id = $theOrder['assigned_user_id'];
        $transaction->type = 1;
        $transaction->percentage = $request->percentage;
        $transaction->order_id = $request->orderId;
        $transaction->order_number = $theOrder['order_number'];
        $transaction->description = $request->description;
        $transaction->amount = -$fineAmount;
        $transaction->balance = User::find($theOrder['assigned_user_id'])->wallet->amount;
        $transaction->save();

        return response(['status' => 'success'], 200);
    }

    public function disputed(Request $request)
    {
        $request->validate([
            'description' => 'required',
        ]);
        if (auth()->user()->role == "admin") {
            $dispute = new Dispute();
            $dispute->order_number = $request->orderId;
            $dispute->instruction = $request->description;
            $dispute->save();

            $order = Order::findOrFail($request->orderId);
            $order->status = 8;
            $order->update();

            $payment = WalletTransaction::where('order_id', $request->orderId)->where('type', 0)->where('status', 0)->count();
            if ($payment != '') {
                $amount = WalletTransaction::where('order_id', $request->orderId)->where('type', 0)->where('status', 0)->value('amount');
                $user_id = Order::where('id', $request->orderId)->value('assigned_user_id');

                $myWallet = Wallet::where('user_id', $user_id)->first();
                $wallet = Wallet::findOrFail($myWallet['id']);
                $wallet->amount = $wallet['amount'] - $amount;
                $wallet->update();

                $transaction = new WalletTransaction();
                $transaction->user_id = $user_id;
                $transaction->type = 8;
                $transaction->status = 1;
                $transaction->order_id = $request->orderId;
                $transaction->description = $request->description;
                $transaction->order_number = Order::where('id', $request->orderId)->value('order_number');
                $transaction->amount = -$amount;
                $transaction->balance = User::find($user_id)->wallet->amount;
                $transaction->save();

                $trans_id = WalletTransaction::where('order_id', $request->orderId)->where('type', 0)->where('status', 0)->value('id');
                $trans = WalletTransaction::findOrFail($trans_id);
                $trans->status = 1;
                $trans->update();
            }


        }
    }

    public function bonus(Request $request)
    {
        $request->validate([
            'percentage' => 'required',
            'description' => 'required',
        ]);

        $theOrder = Order::findOrFail($request->orderId);
        $totalAmount = $theOrder['total_amount'];
        $bonusAmount = ($request->percentage / 100) * $totalAmount;

        $ifWalletExists = Wallet::where('user_id', $theOrder['assigned_user_id'])->count();
        if ($ifWalletExists > 0) {
            // Update wallet
            $myWallet = Wallet::where('user_id', $theOrder['assigned_user_id'])->first();
            $wallet = Wallet::findOrFail($myWallet['id']);
            $wallet->amount = $wallet['amount'] + $bonusAmount;
            $wallet->update();
        } elseif ($ifWalletExists == 0) {
            // Create wallet as wallet does not exist
            $wallet = new Wallet();
            $wallet->user_id = $theOrder['assigned_user_id'];
            $wallet->amount = +$bonusAmount;
            $wallet->save();
        }

        $transaction = new WalletTransaction();
        $transaction->user_id = $theOrder['assigned_user_id'];
        $transaction->type = 4;
        $transaction->percentage = $request->percentage;
        $transaction->order_id = $request->orderId;
        $transaction->order_number = $theOrder['order_number'];
        $transaction->description = $request->description;
        $transaction->amount = $bonusAmount;
        $transaction->balance = User::find($theOrder['assigned_user_id'])->wallet->amount;
        $transaction->save();

        return response(['status' => 'success'], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function cancelorder($DisputId)
    {
        if (auth()->user()->role == "admin") {
            $order =  WalletTransaction::findOrFail($DisputId);
            $order->type = 7;
            $order->update();
        }
    }

    public function acceptorder($DisputId)
    {
        if (auth()->user()->role == "admin") {
            $dispute = WalletTransaction::findOrFail($DisputId);
            $dispute->type = 9;
            $dispute->update();

            $myWallet = Wallet::where('user_id', $dispute->user_id)->first();
            $wallet = Wallet::findOrFail($myWallet['id']);
            $wallet->amount = $wallet->amount + -($dispute->amount);
            $wallet->update();

            $transaction = new WalletTransaction();
            $transaction->user_id = $dispute->user_id;
            $transaction->type = 9;
            $transaction->amount = -($dispute->amount);
            $transaction->order_id = $dispute->order_id;
            $transaction->order_number = $dispute->order_number;
            $transaction->description = "Your dispute NO:" .$DisputId . " has been resolved";
            $transaction->balance = $wallet->amount;
            $transaction->save();
        }
    }

    public function dropFine($fineId)
    {
        if (auth()->user()->role == "admin") {
            $fine = WalletTransaction::findOrFail($fineId);
            $fine->type = 5;
            $fine->update();

            $myWallet = Wallet::where('user_id', $fine->user_id)->first();
            $wallet = Wallet::findOrFail($myWallet['id']);
            $wallet->amount = $wallet->amount + -($fine->amount);
            $wallet->update();

            $transaction = new WalletTransaction();
            $transaction->user_id = $fine->user_id;
            $transaction->type = 5;
            $transaction->amount = -($fine->amount);
            $transaction->order_id = $fine->order_id;
            $transaction->order_number = $fine->order_number;
            $transaction->description = "Your fine NO:" . $fineId . " has been dropped";
            $transaction->balance = $wallet->amount;
            $transaction->save();
        }
    }
}
