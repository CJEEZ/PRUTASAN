<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        $orders = Order::latest()->take(10)->get();
        $sellers = User::where('role','seller')->get();
        return view('admin.dashboard', compact('orders','sellers'));
    }

    public function approveSeller(User $seller)
    {
        $seller->update(['is_approved'=>true]);
        return back()->with('success','Seller approved');
    }
}
