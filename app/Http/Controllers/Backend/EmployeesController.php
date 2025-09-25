<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\JobsModel;
use App\Models\User;
use Monolog\Handler\RedisHandler;
use Str;

class EmployeesController extends Controller
{
    public function index(Request $request)
    {
        $data['getRecord'] = User::getRecord($request);
        return view('backend.employees.list', $data);
    }

    public function add(Request $request)
    {
        return view('backend.employees.add');
    }

    public function add_post(Request $request)
    {
        // dd($request->all());
        $user = request()->validate([
            'name'           => 'required',
            'email'          => 'required|unique:users',
            'hire_date'      => 'required',
            'job_id'         => 'required',
            'salary'         => 'required',
            'commission_pct' => 'required',
            'manager_id'     => 'required',
            'department_id'  => 'required',
        ]);

        $user                       = new User;
        $user->name                 = trim($request->name);
        $user->last_name            = trim($request->last_name);
        $user->email                = trim($request->email);
        $user->phone_number         = trim($request->phone_number);
        $user->hire_date            = trim($request->hire_date);
        $user->job_id               = trim($request->job_id);
        $user->salary               = trim($request->salary);
        $user->commission_pct       = trim($request->commission_pct);
        $user->manager_id           = trim($request->manager_id);
        $user->department_id        = trim($request->department_id);
        $user->role              = $user->role == 1 ? 1 : 0;
        $user->save();

        return redirect('admin/employees')->with('success', 'Employees Succesfully Register');
    }

    public function view($id)
    {
        $data['getRecord'] = User::find($id);
        return view('backend.employees.view', $data);
    }

    public function edit($id)
    {
        $data['getRecord'] = User::find($id);
        return view('backend.employees.edit', $data);
    }

    public function update($id, Request $request)
    {
        $user = $request->validate([
            'username'      => 'required',
            'nik'           => 'required|numeric:min:3',
            'department'    => 'required',
            'warehouse'     => 'nullable',
            'email'         => 'required|unique:users,email,' . $id,
            'sign'          => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // Validate signature file
        ]);

        $user                   = User::find($id);
        if ($user) {
            $user->username     = trim($request->username);
            $user->fullname     = trim($request->fullname);
            $user->nik          = trim($request->nik);
            $user->department   = trim($request->department);
            $user->warehouse_access           = trim($request->warehouse ?? '');
            $user->level        = trim($request->level);
            $user->email        = trim($request->email);
            if ($request->hasFile('sign')) {
                // Delete old file if exists
                if ($user->sign && file_exists(public_path('assets/images/sign/' . $user->sign))) {
                    unlink(public_path('assets/images/sign/' . $user->sign));
                }

                // Save new file
                $file      = $request->file('sign');
                $username   = Str::slug($user->username, '_'); // Safe for filename
                $timestamp  = now()->setTimezone('Asia/Jakarta')->format('Ymd'); // e.g. 20250801_153045
                $filename  = $timestamp . '_' . $username . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('assets/images/sign/'), $filename);

                $user->sign = $filename;
            }
            // dd($user);

            $user->save();

            return redirect('admin/employees')->with('success', 'Employees succesfully update');
        } else {
            return redirect()->back()->with('error', 'User not found');
        }
    }

    public function delete($id)
    {
        $recordDelete = User::find($id);
        $recordDelete->delete();
        return redirect()->back()->with('error', 'Employees succesfully delete');
    }
}
