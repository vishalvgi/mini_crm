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

class CompanyController extends Controller {

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth');
    }

    /**
     * Show the company list.
     *
     * @return view
     */
    public function index() {
        $view_data = Company::paginate(10);
        return view('company.index', ['view_data' => $view_data]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create() {
        return view('company.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request) {
        try {
            DB::beginTransaction();
            $rules = array(
                'name' => 'required|max:255|unique:companies',
                'email' => 'nullable|email|max:255',
                'logo' => 'nullable|mimes:jpeg,gif,png|dimensions:min_width=100,min_height=100|max:2048',
                'webiste' => 'nullable|max:255'
            );
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                DB::rollBack();
                return Redirect::to('home/create')
                                ->withErrors($validator)
                                ->withInput();
            } else {
                $compObj = new Company();
                $compObj->name = $request->input('name');
                $compObj->email = $request->input('email');
                $compObj->website = $request->input('website');
                $compObj->save();
                if ($files = $request->file('logo')) {
                    $destinationPath = 'storage/public'; // upload path
                    $profilefile = date('YmdHis') . "_" . rand(100, 999) . "_" . $compObj->id . "." . $files->getClientOriginalExtension();
                    $files->move($destinationPath, $profilefile);
                    $compObj->logo = $profilefile;
                    $compObj->save();
                }
            }
            Session::flash('status', 'Successfully company saved!');
            DB::commit();
            return Redirect::to('home');
        } catch (Exception $ex) {
            Log::debug($ex);
            Session::flash('status', 'Internal Error!');
            DB::rollBack();
            return Redirect::to('home/create')->withErrors(['status', 'Internal Error!'])
                            ->withInput();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id) {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id) {
        $view_data = Company::find($id);
        return view('company.edit')
                        ->with('view_data', $view_data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update($id, Request $request) {
        try {
            DB::beginTransaction();
            $rules = array(
                'name' => 'required|max:255|unique:companies,name,' . $id,
                'email' => 'nullable|email|max:255',
                'logo' => 'nullable|mimes:jpeg,gif,png|dimensions:min_width=100,min_height=100|max:2048',
                'webiste' => 'nullable|max:255'
            );
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                DB::rollBack();
                return Redirect::to('home/' . $id . '/edit')
                                ->withErrors($validator)
                                ->withInput();
            } else {
                $compObj = Company::find($id);
                $compObj->name = $request->input('name');
                $compObj->email = $request->input('email');
                $compObj->website = $request->input('website');
                $compObj->save();
                if ($files = $request->file('logo')) {
                    if ($compObj->logo) {
                        Storage::delete('storage/public/' . $compObj->logo);
                    }
                    $destinationPath = 'storage/public'; // upload path
                    $profilefile = date('YmdHis') . "_" . rand(100, 999) . "_" . $compObj->id . "." . $files->getClientOriginalExtension();
                    $files->move($destinationPath, $profilefile);
                    $compObj->logo = $profilefile;
                    $compObj->save();
                }
            }
            Session::flash('status', 'Successfully company saved!');
            DB::commit();
            return Redirect::to('home');
        } catch (Exception $ex) {
            Log::debug($ex);
            Session::flash('status', 'Internal Error!');
            DB::rollBack();
            return Redirect::to('home/' . $id . '/edit')
                            ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id) {
        try {
            DB::beginTransaction();
            Employee::where('company_id', $id)->delete();
            $company = Company::find($id);
            $logo = $company->logo;
            $company = $company->delete();
            Storage::delete('storage/public/' . $logo);
            Session::flash('status', 'Successfully company deleted!');
            DB::commit();
            return Redirect::to('home');
        } catch (Exception $ex) {
            Log::debug($ex);
            Session::flash('status', 'Internal Error!');
            DB::rollBack();
            return Redirect::to('home');
        }
    }

}
