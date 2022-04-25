<?php

namespace App\Http\Controllers\API;

use App\Category;
use App\File;
use App\Http\Controllers\Controller;
use App\Mail\CompletedOrder;
use App\Mail\DoneOrder;
use App\Mail\NewOrder;
use App\Mail\UrgentOrder;
use App\Message;
use App\Messenger;
use App\Order;
use App\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;

class MoreOrdersController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function filesCount($orderId)
    {
        return File::where('order_id', $orderId)->where('isComplete', 0)->count();
    }

    public function getFiles($orderId)
    {
        return File::where('order_id', $orderId)->latest()->where('isComplete', 0)->get();
    }

    public function deadline($orderId)
    {
        return Order::where('id', $orderId)->first();
    }

    public function getCompletedFiles($orderId)
    {
        return File::where('order_id', $orderId)->where('isComplete', 1)->orwhere('isComplete', 4)->get();
    }
    public function getRevisionFiles($orderId)
    {
        return File::where('order_id', $orderId)->where('isComplete', 3)->get();
    }
    public function getEditedFiles($orderId)
    {
        return File::where('order_id', $orderId)->where('isComplete', 2)->get();
    }

    public function downloadFile($id)
    {
        $file = File::where('id', $id)->firstOrFail();
        $pathToFile = storage_path('app/' . $file->path);

        return response()->download($pathToFile);
    }

    public function addFiles(Request $request, $orderId)
    {
        $request->validate([
            'files' => 'required',
        ]);

        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $uploadedFile) {
                $filename = $uploadedFile->storeAs('uploads', time() . $uploadedFile->getClientOriginalName());
                // echo $filename;
                $file = new File();
                $file->order_id = $orderId;
                $file->path = $filename;
                $orderNo = Order::where('id', $orderId)->value('order_number');
                $file->order_number = $orderNo;
                $file->save();
            }
        }
        return response(['status' => 'success'], 200);
    }

    public function uploadCompleted(Request $request, $orderId)
    {
        $request->validate([
            'files' => 'required',
        ]);

        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $uploadedFile) {
                $filename = $uploadedFile->storeAs('uploads', time() . $uploadedFile->getClientOriginalName());
                // echo $filename;
                $file = new File();
                $file->order_id = $orderId;
                $file->path = $filename;
                $file->isComplete = 1;
                $orderNo = Order::where('id', $orderId)->value('order_number');
                $file->order_number = $orderNo;
                $file->type = $request->type;
                $file->save();
            }

            if ($request->type == "complete") {
                $order = Order::findOrFail($orderId);
                $order->status = 3;
                $order->completed_time = Carbon::now();
                $order->update();

                $email = User::where('role', 'editor')->where('status_id', 1)->get()->toArray();
                $data = array(
                    'title' => Order::where('id', $orderId)->value('title'),
                    'pages' => Order::where('id', $orderId)->value('pages'),
                    'subject' => Order::where('id', $orderId)->value('discipline'),
                    'completed' => Carbon::now(),
                    'isComplete' => 0,
                    'orderNo' => $orderNo
                );
                Mail::to($email)->send(new CompletedOrder($data));
                return response(['status' => 'success'], 200);
            }elseif ($request->type == "draft") {

                $email = User::where('role', 'editor')->where('status_id', 1)->get()->toArray();
                $data = array(
                    'title' => Order::where('id', $orderId)->value('title'),
                    'pages' => Order::where('id', $orderId)->value('pages'),
                    'subject' => Order::where('id', $orderId)->value('discipline'),
                    'completed' => Carbon::now(),
                    'isComplete' => 1,
                    'orderNo' => $orderNo
                );
                Mail::to($email)->send(new CompletedOrder($data));
                return response(['status' => 'success'], 200);
            }elseif ($request->type == "revision") {
                $order = Order::findOrFail($orderId);
                $order->status = 3;
                $order->completed_time = Carbon::now();
                $order->update();

                $email = User::where('role', 'editor')->where('status_id', 1)->get()->toArray();
                $data = array(
                    'title' => Order::where('id', $orderId)->value('title'),
                    'pages' => Order::where('id', $orderId)->value('pages'),
                    'subject' => Order::where('id', $orderId)->value('discipline'),
                    'completed' => Carbon::now(),
                    'isComplete' => 4,
                    'orderNo' => $orderNo
                );
                Mail::to($email)->send(new CompletedOrder($data));
                return response(['status' => 'success'], 200);
            }
        }

    }

    public function uploadEdited(Request $request, $orderId)
    {
        $request->validate([
            'files' => 'required',
        ]);

        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $uploadedFile) {
                $filename = $uploadedFile->storeAs('uploads', time() . $uploadedFile->getClientOriginalName());
                // echo $filename;
                $file = new File();
                $file->order_id = $orderId;
                $file->path = $filename;
                $file->isComplete = 2;
                $orderNo = Order::where('id', $orderId)->value('order_number');
                $file->order_number = $orderNo;
                $file->save();
            }

            $order = Order::findOrFail($orderId);
            $order->status = 6;
            $order->update();

            $email = User::where('role', 'admin')->get()->toArray();
            $data = array(
                'title' => Order::where('id', $orderId)->value('title'),
                'pages' => Order::where('id', $orderId)->value('pages'),
                'subject' => Order::where('id', $orderId)->value('discipline'),
                'completed' => Carbon::now(),
                'orderNo' => Order::where('id', $orderId)->value('order_number'),
            );
            Mail::to($email)->send(new DoneOrder($data));
            return response(['status' => 'success'], 200);
        }

    }


    public function getWriters()
    {
        return (object)User::where('role', 'writer')->where('status_id', 1)->get();
    }

    public function getWriter($orderId)
    {
        $user = Order::where('id', $orderId)->value('assigned_user_id');
        return User::where('id', $user)->first();
    }

    public function getCompleted()
    {
        return Order::latest()->where('status', 5)->get();
    }

    public function getRevision()
    {
        return Order::latest()->where('status', 4)->paginate(10);
    }

    public function getEdited()
    {
        return Order::latest()->where('status', 6)->get();
    }

    public function notUploaded($orderId){
        $order = Order::findOrFail($orderId);
        $order->status = 1;
        $order->update();
    }

    public function reassignOrder(Request $request)
    {
        if (auth()->user()->role == "admin") {
            $order = Order::findOrFail($request->orderId);




            if ($order->urgency == 0) {
                $order->assigned_user_id = $request->writer;
                $order->status = 1;
                
                $order->update();


            }

            if ($order->urgency == 1) {
                $order->assigned_user_id = $request->writer;
                $order->status = 1;
                $order->update();

            }


            $email = User::where('id', $request->writer)->value('email');
            $data = array(
                'title' => $order->title,
                'pages' => $order->pages,
                'subject' => $order->discipline,
                'deadline' => $order->deadline,
                'orderNo' => $order->order_number,
                'type' => 1
            );
            Mail::to($email)->send(new NewOrder($data));
        }
    }
}
