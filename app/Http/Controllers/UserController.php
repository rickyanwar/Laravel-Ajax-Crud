<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Hash;
use App\Model\User;
use Illuminate\Http\Request;
use DataTables;
use Validator;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        if ($request->ajax()) {
            $data = User::latest()->get();
            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function($row){

                           $btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="'.$row->id.'" data-original-title="Edit" class="edit btn btn-primary btn-sm editUser">Edit</a>';

                           $btn = $btn.' <a href="javascript:void(0)" data-toggle="tooltip"  data-id="'.$row->id.'" data-original-title="Delete" class="btn btn-danger btn-sm deleteUser">Delete</a>';

                            return $btn;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
        }

        return view('users.index');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = new User;
        $user->id = $request->user_id;
          $user->name = $request->name;
          $user->username = $request->username;
          $user->address = $request->address;
          $user->roles = json_encode($request->roles);
          $user->phone = $request->phone;
          $user->email = $request->email;
          $user->password = Hash::make($request->password);
          if ($request->file('avatar')) {
            $file = $request->file('avatar')->store('avatars','public');
            $user->avatar = $file;
         }
          $user->save();
        return response()->json(['success'=>'users saved successfully.']);
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = User::find($id);
        return response()->json($user);
    }


    public function update(Request $request, $id)
    {
        \Validator::make($request->all(), [
            "name" => "required|min:5|max:100",
            "roles" => "required",
            "phone" => "required|digits_between:10,12",
            "address" => "required|min:20|max:200",
        ])->validate();
        $user->id = $request->user_id;
        $user = \App\User::findOrFail($id);
        $user->name = $request->get('name');
        $user->roles = json_encode($request->get('roles'));
        $user->address = $request->get('address');
        $user->phone = $request->get('phone');
        $user->status = $request->get('status');
        if($request->file('avatar')){
            if($user->avatar && file_exists(storage_path('app/public/' . $user->avatar))){
                \Storage::delete('public/'.$user->avatar);
            }
            $file = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $file;
        }
        $user->save();
        return redirect()->route('users.edit', ['id' => $id])->with('status', 'User succesfully updated');
    }


    public function destroy($id)
    {
        User::find($id)->delete();

        return response()->json(['success'=>'User deleted successfully.']);
    }
}
