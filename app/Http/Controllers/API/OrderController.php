<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Mail\NewOrder;
use App\Mail\UrgentOrder;
use App\Order;
use App\File;
use App\User;
use App\Category;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;


class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index()
    {
        if (auth()->user()->role == "admin") {
            return Order::latest()->paginate(10);
        } elseif (auth()->user()->role == "writer") {
            return Order::latest()->where('status', 0)->paginate(10);
        } elseif (auth()->user()->role == "editor") {
            return Order::latest()->where('status', 3)->paginate(10);
        }
    }

    public function user($orderId)
    {
        return Order::where('id', $orderId)->value('assigned_user_id');
    }

    public function admin()
    {
        return User::where('role', 'admin')->value('id');
    }

    public function getMyOrders()
    {
        return Order::latest()->where('assigned_user_id', auth()->user()->id)->paginate(10);
    }
    public function wrtorders($userId)
    {

        return  Order::where('assigned_user_id', $userId)->whereIn('status',[3 ,6,5])->paginate(50);

    }
    public function unorders($userId)
    {

        return  Order::where('assigned_user_id', $userId)->whereIn('status',[1 ,4,7,8])->paginate(50);

    }
    public function store(Request $request)
    {
        $request->validate([
            'level' => 'required',
            'discipline' => 'required',
            'title' => 'required',
            'words' => 'required',
            'deadline' => 'required',
            'spacing' => 'required',
            'paper_format' => 'required',
            'description' => 'required',
            'urgent' => 'required',
            'sources' => 'required',
        ]);

        $ifExist = Order::where('order_number', $request->order_number)->count();

        if ($ifExist > 0) {
            return response()->json([
                'status' => 'error',
                'msg' => 'The order number already exists',
            ], 422);
        } else {
            //Not Urgent
            if ($request->urgent == 0) {
                $order = new Order();
                if ($request->writer) {
                    $order->assigned_user_id = $request->writer;
                    $order->status = 1;
                    $level_id = User::where('id', $request->writer)->value('level_id'); 
                    $amount = 1;
                    $order->amount = $amount;
                    $order->total_amount = $amount * $request->words;

                    if ($request->writer) {
                        $email = User::where('id', $request->writer)->value('email');
                        $data = array(
                            'title' => $request->title,
                            'pages' => $request->pages,
                            'subject' => $request->discipline,
                            'deadline' => $request->deadline,
                            'orderNo' => $request->order_number,
                            'type' => 1,
                            'sources' => $request->sources,
                        );
                       // Mail::to($email)->send(new NewOrder($data));
                    }
                } else {
                    $order->status = 0;
                }
                $order->order_number = $request->order_number;
                $order->title = $request->title;
                $order->description = $request->description;
                $order->deadline = $request->deadline;
                $order->pages = $request->words;
                $order->sources = $request->sources;
                $order->academic_level = $request->level;
                $order->discipline = $request->discipline;
                $order->paper_format = $request->paper_format;
                $order->spacing = $request->spacing;
                $order->save();
                $order_id = $order->id;
                if ($request->writer == '') {
                    $email = User::where('role', 'writer')->where('status_id', 1)->get()->toArray();
                    $data = array(
                        'title' => $request->title,
                        'pages' => $request->words,
                        'subject' => $request->discipline,
                        'deadline' => $request->deadline,
                        'orderNo' => $request->order_number,
                        'type' => 2,
                        'sources' => $request->sources,
                    );
                    //Mail::to($email)->send(new NewOrder($data));
                }
                if ($request->hasFile('files')) {
                    foreach ($request->file('files') as $uploadedFile) {
                        $filename = $uploadedFile->storeAs('uploads', time() . $uploadedFile->getClientOriginalName());
                        // echo $filename;
                        $file = new File();
                        $file->order_id = $order_id;
                        $file->path = $filename;
                        $file->order_number = $request->order_number;
                        $file->save();
                    }
                }
                return response(['status' => 'success'], 200);

                // When Urgent
            } elseif ($request->urgent == 1) {
                $order = new Order();

                if ($request->writer) {
                    $order->assigned_user_id = $request->writer;
                    $order->status = 1;
                    if ($request->writer) {
                        $email = User::where('id', $request->writer)->value('email');
                        $data = array(
                            'title' => $request->title,
                            'pages' => $request->words,
                            'subject' => $request->discipline,
                            'deadline' => $request->deadline,
                            'orderNo' => $request->order_number,
                            'sources' => $request->sources,
                        );
                        //Mail::to($email)->send(new UrgentOrder($data));
                    }
                } else {
                    $order->status = 0;
                }
                $order->order_number = $request->order_number;
                $order->title = $request->title;
                $order->description = $request->description;
                $order->deadline = $request->deadline;
                $order->pages = $request->words;
                $order->amount = $request->amount;
                $order->total_amount = $request->amount * $request->words;
                $order->sources = $request->sources;
                $order->academic_level = $request->level;
                $order->discipline = $request->discipline;
                $order->paper_format = $request->paper_format;
                $order->spacing = $request->spacing;
                $order->urgency = $request->urgent;
                $order->save();
                $order_id = $order->id;
                if ($request->writer == '') {
                    $email = User::where('role', 'writer')->where('status_id', 1)->get()->toArray();
                    $data = array(
                        'title' => $request->title,
                        'pages' => $request->words,
                        'subject' => $request->discipline,
                        'deadline' => $request->deadline,
                        'orderNo' => $request->order_number,
                        'sources' => $request->sources,
                    );
                    //Mail::to($email)->send(new UrgentOrder($data));
                }
                if ($request->hasFile('files')) {
                    foreach ($request->file('files') as $uploadedFile) {
                        $filename = $uploadedFile->storeAs('uploads', time() . $uploadedFile->getClientOriginalName());
                        // echo $filename;
                        $file = new File();
                        $file->order_id = $order_id;
                        $file->path = $filename;
                        $file->order_number = $request->order_number;
                        $file->save();
                    }
                }

                return response(['status' => 'success'], 200);
            }
        }
    }

    public function findMyOrder()
    {
        if ($search = \Request::get('q')) {
            $order = Order::where('assigned_user_id', auth()->user()->id)->where(function ($query) use ($search) {
                $query->where('title', 'LIKE', "%$search%")
                    ->orWhere('order_number', 'LIKE', "%$search%");
            })->paginate(20);
        } else {
            $order = Order::latest()->paginate(10);
        }
        return $order;
    }

    public function show($id)
    {
        return Order::where('id', $id)->first();
    }

    public function search()
    {
        if ($search = \Request::get('q')) {
            $order = Order::where(function ($query) use ($search) {
                $query->where('title', 'LIKE', "%$search%")
                    ->orWhere('order_number', 'LIKE', "%$search%");
            })->paginate(20);
        } else {
            $order = Order::latest()->paginate(10);
        }
        return $order;
    }
}
