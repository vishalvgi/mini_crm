<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use Log;
use Session;
use Illuminate\Support\Facades\Redirect;
use File;
use Exception;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Company;
use App\Employee;

class EmployeeController extends Controller {

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index($company_id) {
        $view_data['company'] = Company::find($company_id);
        $view_data['data'] = Employee::where('company_id', $company_id)->paginate(10);
        return view('employee.index', ['view_data' => $view_data]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create($company_id) {
        $view_data['company'] = Company::find($company_id);
        return view('employee.create', ['view_data' => $view_data]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store($company_id, Request $request) {
        try {
            DB::beginTransaction();
            $rules = array(
                'first_name' => 'required|max:255',
                'last_name' => 'required|max:255',
                'email' => 'nullable|email|max:255',
                'phone' => 'nullable|min:12|numeric'
            );
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                DB::rollBack();
                return Redirect::to('company/' . $company_id . '/employee/create')
                                ->withErrors($validator)
                                ->withInput();
            } else {
                $exist = Employee::where('company_id', $company_id)
                        ->where('first_name', 'like', $request->input('first_name'))
                        ->where('last_name', 'like', $request->input('last_name'))
                        ->count();
                if ($exist) {
                    DB::rollBack();
                    return Redirect::to('company/' . $company_id . '/employee/create')
                                    ->withErrors(['first_name' => 'Employee already exist with same name',
                                        'last_name' => 'Employee already exist with same name'])
                                    ->withInput();
                }
                $empObj = new Employee();
                $empObj->first_name = $request->input('first_name');
                $empObj->last_name = $request->input('last_name');
                $empObj->email = $request->input('email');
                $empObj->phone = $request->input('phone');
                $empObj->company_id = $company_id;
                $empObj->save();
            }
            Session::flash('status', 'Successfully employee saved!');
            DB::commit();
            return Redirect::to('company/' . $company_id . '/employee');
        } catch (Exception $ex) {
            Log::debug($ex);
            Session::flash('status', 'Internal Error!');
            DB::rollBack();
            return Redirect::to('company/' . $company_id . '/employee/create')->withErrors(['status', 'Internal Error!'])
                            ->withInput();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($company_id, $id) {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($company_id, $id) {

        $view_data = Employee::find($id);
        $view_data['company'] = Company::find($company_id);
        return view('employee.edit')
                        ->with('view_data', $view_data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update($company_id, $id, Request $request) {
        try {
            DB::beginTransaction();
            $rules = array(
                'first_name' => 'required|max:255',
                'last_name' => 'required|max:255',
                'email' => 'nullable|email|max:255',
                'phone' => 'nullable|min:12|numeric'
            );
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                DB::rollBack();
                return Redirect::to('company/' . $company_id . '/employee/' . $id . '/edit')
                                ->withErrors($validator)
                                ->withInput();
            } else {
                $exist = Employee::where('company_id', $company_id)
                        ->where('id', '!=', $id)
                        ->where('first_name', 'like', $request->input('first_name'))
                        ->where('last_name', 'like', $request->input('last_name'))
                        ->count();
                if ($exist) {
                    DB::rollBack();
                    return Redirect::to('company/' . $company_id . '/employee/' . $id . '/edit')
                                    ->withErrors(['first_name' => 'Employee already exist with same name',
                                        'last_name' => 'Employee already exist with same name'])
                                    ->withInput();
                }
                $empObj = Employee::find($id);
                if ($empObj->company_id != $company_id) {
                    DB::rollBack();
                    Session::flash('status', 'Invalid Employee\'s Company');
                    return rRedirect::to('home');
                }
                $empObj->first_name = $request->input('first_name');
                $empObj->last_name = $request->input('last_name');
                $empObj->email = $request->input('email');
                $empObj->phone = $request->input('phone');
                $empObj->save();
            }
            Session::flash('status', 'Successfully employee saved!');
            DB::commit();
            return Redirect::to('company/' . $company_id . '/employee');
        } catch (Exception $ex) {
            Log::debug($ex);
            Session::flash('status', 'Internal Error!');
            DB::rollBack();
            return Redirect::to('company/' . $company_id . '/employee/' . $id . '/edit')
                            ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($company_id, $id) {
        try {
            DB::beginTransaction();
            Employee::find($id)->delete();
            Session::flash('status', 'Successfully employee deleted!');
            DB::commit();
            return Redirect::to('company/' . $company_id . '/employee');
        } catch (Exception $ex) {
            Log::debug($ex);
            Session::flash('status', 'Internal Error!');
            DB::rollBack();
            return Redirect::to('company/' . $company_id . '/employee');
        }
    }

}
