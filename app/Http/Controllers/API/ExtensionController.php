<?php

namespace App\Http\Controllers\API;

use App\Extension;
use App\Http\Controllers\Controller;
use App\Mail\DeadlineExtension;
use App\Mail\DeclinedDeadline;
use App\Order;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\AcceptExtension;

class ExtensionController extends Controller
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

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (auth()->user()->role == "writer") {
            $ext = new Extension();
            $ext->user_id = auth()->user()->id;
            $ext->order_id = $request->orderId;
            $ext->order_number = Order::where('id', $request->orderId)->value('order_number');
            $ext->deadline = $request->deadline;
            $ext->initial = Order::where('id', $request->orderId)->value('deadline');
            $ext->save();
            $email = User::where('role', 'Admin')->get()->toArray();
            $data = array(
                'title' => Order::where('id', $request->orderId)->value('title'),
                'pages' => Order::where('id', $request->orderId)->value('pages'),
                'subject' => Order::where('id', $request->orderId)->value('discipline'),
                'deadline' => Order::where('id', $request->orderId)->value('deadline'),
                'orderNo' =>Order::where('id', $request->orderId)->value('order_number'),
                'NewDeadline'=>$request->deadline,
            );
            Mail::to($email)->send(new DeadlineExtension($data));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (auth()->user()->role == "writer"){
            return Extension::where('user_id', auth()->user()->id)->where('order_id', $id)->latest()->paginate(10);
        }

        if (auth()->user()->role == "admin"){
            return Extension::where('order_id', $id)->latest()->paginate(10);
        }
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

    public function acceptExtension($extId){
        if (auth()->user()->role == "admin"){
            $req = Extension::findOrFail($extId);
            $req->status = 1;
            $req->update();

            $orderId = $req->order_id;
            $new = $req->deadline;

            $order = Order::findOrFail($orderId);
            $order->deadline = $new;
            $order->update();

            $email = User::where('id', $req->user_id)->value('email');
            $data = array(
                'orderNo' => $req->order_number,
                'deadline' => $req->initial,
                'new' => $req->deadline,
                'name' => User::where('id', $req->user_id)->value('name')
            );
            Mail::to($email)->send(new AcceptExtension($data));

        }
    }

    public function declineExtension($extId){
        if (auth()->user()->role == "admin"){
            $req = Extension::findOrFail($extId);
            $req->status = 2;
            $req->update();

            $orderId = $req->order_id;
            $new = $req->initial;

            $order = Order::findOrFail($orderId);
            $order->deadline = $new;
            $order->update();

            $user_id = Order::where('id', $orderId)->value('assigned_user_id');
            $email = User::where('id', $user_id)->value('email');
            $data = array(
                'title' => Order::where('id', $orderId)->value('title'),
                'pages' => Order::where('id', $orderId)->value('pages'),
                'subject' => Order::where('id', $orderId)->value('discipline'),
                'deadline' => Order::where('id', $orderId)->value('deadline'),
                'orderNo' =>Order::where('id', $orderId)->value('order_number'),

            );
            Mail::to($email)->send(new DeclinedDeadline($data));

        }
    }
}
