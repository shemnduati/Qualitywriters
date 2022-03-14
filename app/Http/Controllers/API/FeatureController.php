<?php

namespace App\Http\Controllers\API;

use App\Dispute;
use App\File;
use App\Http\Controllers\Controller;
use App\Mail\RevisonOrder;
use App\Order;
use App\Revision;
use App\User;
use App\Wallet;
use App\WalletTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class FeatureController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function adminSort($id)
    {
        if (auth()->user()->role == "admin") {
            return Order::where('status', $id)->latest()->paginate(10);
        }
    }

    public function writerSort($id)
    {
        if (auth()->user()->role == "writer") {
            return Order::where('status', $id)->where('assigned_user_id', auth()->user()->id)->latest()->paginate(10);
        }
    }

    public function editorSort($id)
    {
        if (auth()->user()->role == "editor") {
            return Order::where('status', $id)->latest()->paginate(10);
        }
    }

    public function makeRevision(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'instruction' => 'required',
        ]);

        $revision = new Revision();
        $revision->order_number = $request->orderId;
        $revision->title = $request->title;
        $revision->instruction = $request->instruction;
        $revision->save();

        $order = Order::findOrFail($request->orderId);
        $order->status = 4;
        $order->update();

        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $uploadedFile) {
                $filename = $uploadedFile->storeAs('uploads', time() . $uploadedFile->getClientOriginalName());
                // echo $filename;
                $file = new File();
                $file->order_id = $request->orderId;
                $file->order_number = Order::where('id', $request->orderId)->value('order_number');
                $file->path = $filename;
                $file->isComplete = 3;
                $file->save();
            }
        }

        $user_id = Order::where('id', $request->orderId)->value('assigned_user_id');
        $email = User::where('id', $user_id)->value('email');
        $data = array(
            'name' => User::where('id', $user_id)->value('name'),
            'title' => Order::where('id', $request->orderId)->value('title'),
            'orderNo' => Order::where('id', $request->orderId)->value('order_number'),

        );
        Mail::to($email)->send(new RevisonOrder($data));
        return response(['status' => 'success'], 200);
    }

    public function Revision($orderId)
    {
        return Revision::where('order_number', $orderId)->latest()->paginate(10);
    }

    public function disputes($orderId)
    {
        return Dispute::where('order_number', $orderId)->latest()->paginate(10);
    }

    public function editDeadline(Request $request, $orderId)
    {
        if (auth()->user()->role == "admin") {
            $order = Order::findOrFail($orderId);
            $order->deadline = $request->deadline;
            $order->update();
        }
    }

    public function deleteOrder($orderId)
    {
        if (auth()->user()->role == "admin") {
            $order = Order::findOrFail($orderId);
            $order->delete();

            $files = File::where('order_id', $orderId)->get();

            foreach ($files as $file) {
                $filey = File::findOrFail($file['id']);
                $filey->delete();
                Storage::delete($file['path']);
            }
        }
    }

    public function deleteFile(File $file)
    {
        if (auth()->user()->role == "writer") {
            $file->delete();
            Storage::delete($file['path']);

            $isCompleted = File::where('order_id', $file['order_id'])->where('type', 'completed')->count();

            if ($isCompleted < 1) {
                $order = Order::findOrFail($file['order_id']);
                $order->status = 1;
                $order->update();
            }
        }
    }

    public function cancelOrder($orderId)
    {
        if (auth()->user()->role == "admin") {
            $order = Order::findOrFail($orderId);
            $order->status = 7;
            $order->update();


            $payment = WalletTransaction::where('order_id', $orderId)->where('type', 0)->count();
            if ($payment != '') {
                $amount = WalletTransaction::where('order_id', $orderId)->where('type', 0)->value('amount');
                $user_id = Order::where('id', $orderId)->value('assigned_user_id');

                $myWallet = Wallet::where('user_id', $user_id)->first();
                $wallet = Wallet::findOrFail($myWallet['id']);
                $wallet->amount = $wallet['amount'] - $amount;
                $wallet->update();

                $transaction = new WalletTransaction();
                $transaction->user_id = $user_id;
                $transaction->type = 6;
                $transaction->order_id = $orderId;
                $transaction->order_number = Order::where('id', $orderId)->value('order_number');
                $transaction->amount = -$amount;
                $transaction->balance = User::find($user_id)->wallet->amount;
                $transaction->save();
            }


        }
    }
}
