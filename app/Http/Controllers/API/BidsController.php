<?php

namespace App\Http\Controllers\API;

use App\Bid;
use App\Category;
use App\Http\Controllers\Controller;
use App\Mail\AwardedOrder;
use App\Mail\CompletedOrder;
use App\Order;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class BidsController extends Controller
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

    public function makeBid($orderId)
    {
        $userId = auth()->user()->id;
        $userPending = Order::where('assigned_user_id', $userId)->where('status', 1)->count();
        
        if($userPending > 0){
            return response()->json([
                'status' => 'error',
                'msg' => 'You have a pending order kindly complete it first',
            ], 422);
        }else {
            $ifBid = Bid::where('order_id', $orderId)->count();
            if ($ifBid == 0) {
                $bid = new Bid();
                $bid->order_id = $orderId;
                $user = auth()->user()->id;
                $bid->user_id = $user;
                $bid->status = 0;
                $bid->save();
    
                $words = Order::where('id', $orderId)->value('pages');
                $order = Order::findOrFail($orderId);
                $order->assigned_user_id = $user;
                $order->status = 1;
                $amount = 1;
                $order->amount = $amount;
                $order->total_amount = $amount * $words;
                $order->update();
                return response(['status' => 'success'], 200);
            } else {
                return response()->json([
                    'status' => 'error',
                    'msg' => 'The Order has been Taken',
                ], 422);
            }
        }
    }

    public function checkBid($orderId)
    {
        return Bid::where('order_id', $orderId)->count();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($orderId)
    {
        $bids = Bid::where('order_id', $orderId)->latest()->get();
        $parent = array();

        foreach ($bids as $bid) {
            $name = User::where('id', $bid['user_id'])->value('name');
            $email = User::where('id', $bid['user_id'])->value('email');
            $phone = User::where('id', $bid['user_id'])->value('phone_number');
            $level_id = User::where('id', $bid['user_id'])->value('level_id');
            $level = Category::where('id', $level_id)->value('title');

            $child = array(
                'name' => $name,
                'email' => $email,
                'phone' => $phone,
                'id' => $bid['id'],
                'level' => $level,
                'status' => $bid['status']
            );

            array_push($parent, $child);
        }

        return $parent;
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
        // Change bid status from pending to accepted
        $bid = Bid::findOrFail($id);
        $bid->status = 1;
        $bid->update();

        // Update order details/assign user to order
        $user = User::where('id', $bid->user_id)->first();
        $orderId = Bid::where('id', $id)->value('order_id');
        $amount = Category::where('id', $user['level_id'])->value('amount');

        $order = Order::findOrFail($orderId);

        // Check if urgent or not
        if ($order->urgency == 0) {
            $order->amount = $amount;
            if ($order->spacing == "single") {
                $order->total_amount = $order->pages * $amount * 2;
            }elseif ($order->spacing == "double") {
                $order->total_amount = $order->pages * $amount;
            }
        }

        $order->status = 1;
        $order->assigned_user_id = $user['id'];
        $order->update();

        $email = User::where('id',$bid->user_id)->value('email');
        $data = array(
            'title' => Order::where('id',$orderId)->value('title'),
            'pages' => Order::where('id',$orderId)->value('pages'),
            'subject'=>Order::where('id',$orderId)->value('discipline'),
            'deadline' => Order::where('id',$orderId)->value('deadline'),
            'orderNo' =>Order::where('id',$orderId)->value('order_number'),
        );
        Mail::to($email)->send(new AwardedOrder($data));

        $others = Bid::where('order_id', $orderId)->where('status', 0)->get();

        // Reject all the other bids
        if (count($others) > 0) {
            foreach ($others as $other) {
                $each = Bid::findOrFail($other['id']);
                $each->status = 2;
                $each->update();
            }
        }

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
}
