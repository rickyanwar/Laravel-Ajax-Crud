<?php

namespace App\Http\Controllers;
use Yajra\DataTables\DataTables;
use App\Model\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Validator;
use View;
class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getall()
    {
       $user = User::orderBy('id', 'desc');
       return Datatables::of($user)
         //  ->setRowAttr(['align' => 'center'])
        //  ->addColumn('status', function ($user) {
        //     return $user->status ? 'Active' : 'Inactive';
        //  })

         ->addColumn('roles',function(User $user){
             return json_decode($user->roles);
         })
         ->addColumn('action', 'users.action')
         ->setRowClass(function ($user) {
            return $user->status ? '' : '';
         })->make(true);
    }

    public function index()
    {
        return view('users.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
      $view = View::make('users.create')->render();
      return response()->json(['html' => $view]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
         $request->validate([
            'name' => 'required',
            'username' => 'required',
            'email' => 'required',
            'phone' => 'required',
            'avatar'=> 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
                      ]);
          $user = new User;
          $user->name = $request->name;
          $user->username = $request->username;
          $user->address = $request->address;
          $user->roles = json_encode($request->roles);
          $user->phone = $request->phone;
          $user->email = $request->email;
          $user->password = Hash::make($request->password);
          if ($request->file('avatar')) {
            $file = $request->file('avatar')->store('avatars','public');
            $new_user->avatar = $file;
         }
          $user->save();
          return response()->json(['html' => 'Successfully Inserted']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        $view = View::make('users.show  ', compact('user'))->render();
        return response()->json(['html' => $view]);
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {

        $view = View::make('users.edit', compact('user'))->render();
        return response()->json(['html' => $view]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        \Validator::make($request->all(), [
            "name" => "required|min:5|max:100",

        ])->validate();
          $user->name = $request->name;
          $user->username = $request->username;
          $user->address = $request->address;
          $user->roles = json_encode($request->roles);
          $user->phone = $request->phone;
          $user->email = $request->email;
          if ($request->password){
            $user->password = Hash::make($request->password);
          }
          $user->password = Hash::needsRehash($request->password);
          if ($request->file('avatar')) {
            if($user->avatar && file_exists(storage_path('app/public/' . $user->avatar))){
                \Storage::delete('public/'.$user->avatar);
            }
            $file = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $file;
          }
          $user->save();

    	return response()->json(['html' => 'Successfully Inserted']);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $user->delete();
        return response()->json(['type' => 'success', 'message' => 'Successfully Deleted']);
    }
}
