<?php

namespace App\Http\Controllers\API;

use App\File;
use App\Http\Controllers\Controller;
use App\Mail\RevisonOrder;
use App\Order;
use App\Revision;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class RevisionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function makeRevision(Request $request){
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

        $user_id = Order::where('id', $request->orderId)->value('assigned_user_id');
        $email = User::where('id', $user_id)->value('email');
        $data = array(
            'name' => User::where('id', $user_id)->value('name'),
            'title' => Order::where('id', $request->orderId)->value('title'),
            'orderNo' => Order::where('id', $request->orderId)->value('order_number'),

        );
        Mail::to($email)->send(new RevisonOrder($data));
        return $revision->id;
    }

    public function revisionFiles(Request $request, $revisionId){
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $uploadedFile) {
                $filename = $uploadedFile->storeAs('uploads', time() . $uploadedFile->getClientOriginalName());
                $file = new File();
                $file->order_id = $request->orderId;
                $file->order_number = Order::where('id',$request->orderId)->value('order_number');
                $file->revision_id = $revisionId;
                $file->path = $filename;
                $file->isComplete = 3;
                $file->save();
            }
        }
    }
}
