<?php

namespace App\Http\Controllers\API;

use App\Category;
use App\Http\Controllers\Controller;
use App\Mail\AccountApproval;
use App\Mail\AccountSuspension;
use App\Mail\AwardedOrder;
use App\Order;
use Illuminate\Http\Request;
// use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\User;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        return User::latest()->paginate(10);
        // return response(User::all()->jsonSerialize(), Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request,[
            'name' => 'required|string|max:191',
            'email' => 'required|string|email|max:191|unique:users',
            'password' => 'sometimes|required|min:6',
            'phone_number' => 'required|string|min:10',
            'role' => 'required|string',
            'status_id' => 'required|string',

        ]);

        return User::create([
            'name' => $request['name'],
            'email' => $request['email'],
            'password' => Hash::make($request['password']),
            'phone_number'  => $request['phone_number'],
            'role' => $request['role'],
            'status_id' => $request['status_id'],
            'photo' => $request['photo'],
        ]);
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
    public function profile()
    {
        return auth()->user();
    }
    public function value()
    {
        $value = User::where('id',auth()->user()->id)->value('specialties');
        $valuedecoded = json_decode($value, true);
        return ['value'=>$valuedecoded];
    }
    public function Myvalue($userId)
    {
        $value = User::where('id',$userId)->value('specialties');
        $valuedecoded = json_decode($value, true);
        return ['value'=>$valuedecoded];
    }
    public function profiles()
    {
//        $user = User::all();
        return User::Where('role','writer')->latest()->paginate(9);

    }
    public function updateProfile(Request $request)
    {
        $user =  auth()->user();
        $this->validate($request,[
            'name' => 'required|string|max:191',
            'email' => 'required|string|email|max:191|unique:users,email,'.$user->id,
            'password' => 'sometimes|required|min:6',
            'bank' => 'sometimes|required|string|max:191',
            'bank_number' => 'sometimes|required|string|max:191',
            'value'=> 'sometimes|required',
        ]);
        $currentSpecialties = $user->specialties;
        $currentPhoto = $user->photo;
        $currentBank = $user->Bank;
        $currentAcccount = $user->bank_account;

        if($request->photo != $currentPhoto) {
            $name = time() . '.' . explode('/', explode(':', substr($request->photo, 0, strpos($request->photo, ';')))[1])[1];
            \Image::make($request->photo)->save(public_path('img/profile/') . $name);
            $request->merge(['photo' => $name]);
            $userPhoto = public_path('img/profile/') . $currentPhoto;
            if (file_exists($userPhoto)) {
                @unlink($userPhoto);
            }
        }
        if($request->bank != $currentBank){
            $request->merge(['bank' => $request['bank']]);
        }
        if($request->bank_account !=  $currentAcccount){
            $request->merge(['bank_account' => $request['bank_account']]);
        }
        if($request->value !=   $currentSpecialties){
            $special = $request->value;
            $specialEncoded = json_encode($special);
            $request->merge(['specialties' =>  $specialEncoded]);
        }
        if(!empty($request->password)){
            $request->merge(['password' => Hash::make($request['password'])]);
        }


        $user->update($request->all());
        return ['message' => "Success"];
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
        $user = User::FindOrFail($id);

        $this->validate($request,[
            'name' => 'required|string|max:191',
            'email' => 'required|string|email|max:191|unique:users,email,'.$user->id,
            'password' => 'sometimes|min:6',
            'phone_number' => 'required|string|min:10',
            'role' => 'required|string',
            'status_id' => 'required',
        ]);

        $user->update($request->all());
        if($request->status_id == 1){
            $email = $request->email;
            $data = array(
                'name' => $request->name,
                'email'=>$request->email,
                'role' => $request->role,

            );
            Mail::to($email)->send(new AccountApproval($data));
        }elseif ($request->status_id == 2){
                $email = $request->email;
                $data = array(
                    'name' => $request->name,
                    'email'=>$request->email,
                    'role' => $request->role,

                );
                Mail::to($email)->send(new AccountSuspension($data));
            }

        return ['message' => 'Text'];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::FindOrFail($id);

        $user->delete();

        return ['message' => 'User Deleted'];
    }

    public function getLevel(){
        $levelId = auth()->user()->level_id;

        return Category::where('id', $levelId)->first();
    }

    public function AuthRouteAPI(Request $request){
        return $request->user();
    }
    public function search()
    {
        if ($search = \Request::get('q')) {
            $users = User::where(function ($query) use ($search) {
                $query->where('name', 'LIKE', "%$search%")
                    ->orWhere('email', 'LIKE', "%$search%")
                    ->orWhere('role', 'LIKE', "%$search%");
            })->paginate(20);
        } else {
            $users = User::latest()->paginate(10);
        }
        return $users;
    }
}
