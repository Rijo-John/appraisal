<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\InternalUser;
use DataTables;

class AdminController extends Controller
{
    /**
     * Show the Assign Admin page.
     */
    public function showAssignAdmin()
    {
        return view('assign-admin'); // No need to pass users manually
    }

    /**
     * Get users for DataTable (AJAX).
     */
    public function getUsers(Request $request)
    {
        /*$users = InternalUser::where('role', '!=', 2)
                ->orWhereNull('role')
                ->where('status','Active')
                ->where('email','Active')
                ->get();*/
        $users = InternalUser::where(function ($query) {
                $query->where('role', '!=', 2)
                      ->orWhereNull('role');
            })
            ->where('status', 'Active')
            ->whereNotNull('email')  // Ensuring email is not null
            ->get();

        return DataTables::of($users)
            ->addColumn('checkbox', function ($user) {
                return '<input type="checkbox" name="user_ids[]" value="'.$user->id.'" class="user-checkbox">';
            })
            ->rawColumns(['checkbox']) // Allow HTML in checkbox column
            ->make(true);

        
    }

    /**
     * Assign selected users as admins.
     */
    public function assignAdmin(Request $request)
    {
        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:internal_users,id',
        ]);

        //dd($request->user_ids);

        InternalUser::whereIn('id', $request->user_ids)->update(['role' => 2]);

        return response()->json(['success' => 'Users assigned as admin successfully.']);
    }
}
