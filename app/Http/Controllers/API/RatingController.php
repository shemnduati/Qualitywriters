<?php

namespace App\Http\Controllers\API;

use App\Category;
use App\Http\Controllers\Controller;
use App\Order;
use App\Rating;
use Illuminate\Http\Request;
use App\User;

class RatingController extends Controller
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
        $ratings = User::where('role','writer')->get();
        $rates = array();
        foreach ($ratings as $rating){
            $id  = $rating['id'];
            $name = $rating['name'];
            $email = $rating['email'];
            $photo = $rating['photo'];
            $bank = $rating['bank'];
            $bank_account = $rating['bank_account'];
            $level = $rating['level_id'];
            $phone = $rating['phone_number'];
            $mylevel = Category::where('id',$level)->value('title');
            $review = Rating::where('user_id',$rating['id'])->count();
            $finished = Order::where('assigned_user_id',$rating['id'])->where('completed_time', '!=', '')->count();
            $uncompleted = Order::where('assigned_user_id',$rating['id'])->where('completed_time', Null)->count();
            $myratings = Rating::where('user_id',$rating['id'])->get();
            $total_rating = collect($myratings)->avg('rating');
            $rate = array(
                'id'=> $id,
                'name'=> $name,
                'email'=>$email,
                'phone'=>$phone,
                'bank'=>$bank,
                'bank_account'=>$bank_account,
                'photo'=>$photo,
                'level'=>$mylevel,
                'review' => $review,
                'finished' => $finished,
                'uncompleted' => $uncompleted,
                'total_rating' => $total_rating,
            );
            array_push($rates,$rate);
    }
        return ['rate'=>$rates];
    }
    public function writer($userId)
    {
        return User::Where('id',$userId)->first();
//        $ratings = User::where('id',$userId)->get();
//        $rates = array();
//        foreach ($ratings as $rating){
//            $id  = $rating['id'];
//            $name = $rating['name'];
//            $email = $rating['email'];
//            $photo = $rating['photo'];
//            $bank = $rating['bank'];
//            $bank_account = $rating['bank_account'];
//            $level = $rating['level_id'];
//            $phone = $rating['phone_number'];
//            $mylevel = Category::where('id',$level)->value('title');
//            $rate = array(
//                'id'=> $id,
//                'name'=> $name,
//                'email'=>$email,
//                'phone'=>$phone,
//                'bank'=>$bank,
//                'bank_account'=>$bank_account,
//                'photo'=>$photo,
//                'level'=>$mylevel,
//            );
//            array_push($rates,$rate);
//        }
//        return ['rate'=>$rates];
    }

    public function getRate($orderId)
    {
        return Rating::where('order_id',$orderId)->count();

    }
    public function reviews($userId)
    {
        $reviews = Rating::where('user_id',$userId)->get();
        $revs = array();
        foreach ($reviews as $review){
            $order = $review['order_id'];
            $rate = $review['rating'];
            $topic = Order::where('id',$order)->value('title');
            $orderNumber = Order::where('id',$order)->value('order_number');
            $rev = array(
                'id'=>$order,
                'title'=>$topic,
                'orderId'=>$orderNumber,
                'rate'=>$rate,
            );
            array_push($revs,$rev);

        }
        return ['review'=>$revs];

    }
    public function getMyRate($orderId)
    {
        $rate = Rating::where('order_id',$orderId)->count();
        if($rate != 0){
            return Rating::where('order_id', $orderId)->value('rating');
        }else{
            return 0;
        }

    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
       $rating = Rating::create([
           'order_id' => $request->OrderId,
           'user_id' => $request->UserId,
           'rating' => $request ->Rating,
       ]);
        return response()->json($rating);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
