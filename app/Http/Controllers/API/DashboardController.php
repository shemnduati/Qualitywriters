<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Order;
use App\Wallet;
use Illuminate\Http\Request;

class DashboardController extends Controller
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
        if (auth()->user()->role == "editor") {
            $completed = Order::Where('status', 5)->count();
            $revision = Order::where('status', 4)->count();
            $pending = Order::where('status', 3)->count();
            $data = array(
                'completed' => $completed,
                'revision' => $revision,
                'pending' => $pending,
            );
            return ['data' => $data];
        }

        if (auth()->user()->role == "admin") {
            $completed = Order::Where('status', 5)->count();
            $revision = Order::where('status', 4)->count();
            $pending = Order::where('status', 3)->count();
            $active = Order::where('status', 0)->count();
            $edited = Order::where('status', 6)->count();
            $total = Order::all()->count();
            $totalOwed = Wallet::all()->sum('amount');
            $data = array(
                'completed' => $completed,
                'revision' => $revision,
                'pending' => $pending,
                'active' => $active,
                'edited' => $edited,
                'total' => $total,
                'totalOwed' => $totalOwed
            );
            return ['data' => $data];
        }
    }

    public function myDashboard($id)
    {
        $completed = Order::Where('status', 5)->where('assigned_user_id', $id)->count();
        $revision = Order::where('status', 4)->where('assigned_user_id', $id)->count();
        $active = Order::where('status', 1)->where('assigned_user_id', $id)->count();
        $edited = Order::where('status', 6)->where('assigned_user_id', $id)->count();
        $new = Order::where('status', 0)->count();
        $data = array(
            'completed' => $completed,
            'revision' => $revision,
            'active' => $active,
            'edited' => $edited,
            'new' => $new
        );
        return ['data' => $data];
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
