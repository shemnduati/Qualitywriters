<?php

namespace App\Http\Controllers\API;

use App\File;
use App\Http\Controllers\Controller;
use App\Order;
use App\User;
use App\Wallet;
use App\WalletTransaction;
use Illuminate\Http\Request;

class PaymentController extends Controller
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
        $earnings = Wallet::all();
        $earn = array();
        foreach ($earnings as $earning) {
            $writer_name = User::where('id', $earning['user_id'])->value('name');
            $amount = $earning['amount'];
            $user_id = $earning['user_id'];
            $earns = array(
                'writer_name' => $writer_name,
                'amount' => $amount,
                'user_id' => $user_id,
            );
            array_push($earn, $earns);
        }
        return ['earning' => $earn];
    }

    public function myEarnings($userId)
    {
        $data = WalletTransaction::where('user_id', $userId)->latest('id')->get();

        return collect($data)->paginate(10);
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
    public function show($id)
    {
        //
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
}
