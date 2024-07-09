<?php

namespace App\Http\Controllers;

use App\Models\AdAccount;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Hash;
use App\Models\SystemNotification;

class UserController extends Controller
{

    public function indexClients()
    {
        if (auth()->user()->role !== 'admin' && auth()->user()->role !== 'manager' &&  auth()->user()->role !== 'employee') {
            return redirect('/');
        }

        $users = User::where('role', 'customer')
            ->orderBy('created_at', 'desc')
            ->paginate(50);
        return view('template.home.users.client.index', compact('users'));
    }

    public function loadMoreClients(Request $request)
    {
        if ($request->ajax()) {
            $page = $request->page;
            $users = User::where('role', 'customer')->orderBy('created_at', 'desc')->paginate(50, ['*'], 'page', $page);
            return view('template.home.users.client.load_more', compact('users'))->render();
        }
        return response()->json(['message' => 'Bad Request'], 400);
    }

    public function showClient($id)
    {
        if (auth()->user()->role !== 'admin' && auth()->user()->role !== 'manager' &&  auth()->user()->role !== 'employee') {
            return redirect('/');
        }
        $user = User::findOrFail($id);
        $adAccounts = AdAccount::where('client_id', $id)->get();
        return view('template.home.users.client.show', compact('user', 'adAccounts'));
    }

    public function editClient($id)
    {
        if (auth()->user()->role !== 'admin' && auth()->user()->role !== 'manager') {
            return redirect('/');
        }
        $client = User::findOrFail($id);
        return view('template.home.users.client.edit', compact('client'));
    }
    public function updateClient(Request $request, $id)
    {
        if (auth()->user()->role !== 'admin' && auth()->user()->role !== 'manager') {
            return redirect('/');
        }

        $client = User::findOrFail($id);
        $client->update([
            'name' => $request->name,
            'username' => $request->username,
            'phone' => $request->phone,
            'email' => $request->email,
            'business_type' => $request->business_type,
            'business_name' => $request->business_name,
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('user.client')->with('success', 'Client information updated successfully.');
    }
    public function destroyClient($id)
    {
        if (auth()->user()->role !== 'admin') {
            return redirect('/');
        }
        $client = User::findOrFail($id);
        $client->delete();

        return redirect()->route('user.client')->with('success', 'Client deleted successfully.');
    }



    public function indexManagers()
    {
        if (auth()->user()->role !== 'admin') {
            return redirect('/');
        }
        $users = User::where('role', 'manager')
            ->orderby('created_at', 'desc')
            ->get();
        return view('template.home.users.manager.index', compact('users'));
    }
    public function editManager($id)
    {
        if (auth()->user()->role !== 'admin') {
            return redirect('/');
        }
        $manager = User::findOrFail($id);
        return view('template.home.users.manager.edit', compact('manager'));
    }
    public function updateManager(Request $request, $id)
    {
        if (auth()->user()->role !== 'admin') {
            return redirect('/');
        }

        $manager = User::findOrFail($id);
        $manager->update([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('user.manager')->with('success', 'Manager information updated successfully.');
    }
    public function destroyManager($id)
    {
        if (auth()->user()->role !== 'admin') {
            return redirect('/');
        }
        $manager = User::findOrFail($id);
        $manager->delete();

        return redirect()->route('user.manager')->with('success', 'Manager deleted successfully.');
    }


    public function indexEmployees()
    {
        if (auth()->user()->role !== 'admin' && auth()->user()->role !== 'manager') {
            return redirect('/');
        }
        $users = User::where('role', 'employee')
            ->orderby('created_at', 'desc')
            ->get();
        return view('template.home.users.employee.index', compact('users'));
    }
    public function editEmployee($id)
    {
        if (auth()->user()->role !== 'admin' && auth()->user()->role !== 'manager') {
            return redirect('/');
        }
        $employee = User::findOrFail($id);
        return view('template.home.users.employee.edit', compact('employee'));
    }
    public function updateEmployee(Request $request, $id)
    {
        if (auth()->user()->role !== 'admin' && auth()->user()->role !== 'manager') {
            return redirect('/');
        }

        $employee = User::findOrFail($id);
        $employee->update([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('user.employee')->with('success', 'Employee information updated successfully.');
    }
    public function destroyEmployee($id)
    {
        if (auth()->user()->role !== 'admin') {
            return redirect('/');
        }
        $employee = User::findOrFail($id);
        $employee->delete();

        return redirect()->route('user.employee')->with('success', 'Employee deleted successfully.');
    }



    public function indexAdmins()
    {
        if (auth()->user()->role !== 'admin') {
            return redirect('/');
        }
        $users = User::where('role', 'admin')
            ->orderby('created_at', 'desc')
            ->get();
        return view('template.home.users.admin.index', compact('users'));
    }
    public function editAdmin($id)
    {
        if (auth()->user()->role !== 'admin') {
            return redirect('/');
        }
        $admin = User::findOrFail($id);
        return view('template.home.users.admin.edit', compact('admin'));
    }
    public function updateAdmin(Request $request, $id)
    {
        if (auth()->user()->role !== 'admin') {
            return redirect('/');
        }

        $admin = User::findOrFail($id);
        $admin->update([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('user.admin')->with('success', 'Admin information updated successfully.');
    }
    public function destroyAdmin($id)
    {
        if (auth()->user()->role !== 'admin') {
            return redirect('/');
        }
        $admin = User::findOrFail($id);
        $admin->delete();

        return redirect()->route('user.admin')->with('success', 'Admin deleted successfully.');
    }


    public function updateRole(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user->update([
            'role' => $request->role,
        ]);

        switch ($request->role) {
            case 'admin':
                return redirect()->route('user.admin')->with('success', "Role for {$user->name} changed successfully to {$request->role}.");
            case 'employee':
                return redirect()->route('user.employee')->with('success', "Role for {$user->name} changed successfully to {$request->role}.");
            case 'manager':
                return redirect()->route('user.manager')->with('success', "Role for {$user->name} changed successfully to {$request->role}.");
            default:
                return redirect()->back()->with('error', 'Invalid role selected.');
        }
    }
}
