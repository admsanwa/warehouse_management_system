<?php

namespace App\Http\Controllers\Backend;
use App\Models\JobsModel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\JobsExport;

class JobsController extends Controller
{
    public function index(Request $request) {
        $data['getRecord'] = JobsModel::getRecord($request);
        return view('backend.jobs.list', $data);
    }

    public function jobs_export(Request $request) {
        return Excel::download(new JobsExport, 'jobs.xlsx');
    }

    public function add() {
        return view('backend.jobs.add');
    }

    public function add_post(Request $request){
        $user = request()->validate([
            'job_title' => 'required',
            'min_salary'=> 'required',
            'max_salary'=> 'required'
        ]);

        $user               = new JobsModel;
        $user->job_title    = trim(request()->job_title);
        $user->min_salary   = trim(request()->min_salary);
        $user->max_salary   = trim(request()->max_salary);

        $user->save();
        return redirect('admin/jobs')->with('success', 'Jobs succesfully register');
    }

    public function view($id, Request $request){
        $data['getRecord'] = JobsModel::find($id);
        return view('backend.jobs.view', $data);
    }

    public function edit($id, Request $request) {
        $data['getRecord'] = JobsModel::find($id);
        return view('backend.jobs.edit', $data);
    }

    public function update($id, Request $request){
        $user = request()->validate([
            'job_title'     => 'required',
            'min_salary'    => 'required',
            'max_salary'    => 'required'
        ]);
        $user               = JobsModel::find($id);
        $user->job_title    = trim(request()->job_title);
        $user->min_salary   = trim(request()->min_salary);
        $user->max_salary   = trim(request()->max_salary);
        $user->save();

        return redirect('admin/jobs')->with('success', 'Jobs succesfully updated');
    }

    public function delete($id){
        $recordDelete = JobsModel::find($id);
        $recordDelete->delete();
        return redirect()->back()->with('error', 'Jobs succesfully delete');
    }
}
