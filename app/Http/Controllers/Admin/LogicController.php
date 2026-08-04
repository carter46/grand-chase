<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Agent;
use App\Models\User;
use Illuminate\Http\Request;

/**
 * Agent management previously referenced a missing LogicController.
 * Restored here so admin agent routes remain usable against the live `agents` table.
 */
class LogicController extends Controller
{
    public function addagent(Request $request)
    {
        $request->validate([
            'user' => 'required|integer|exists:users,id',
            'referred_users' => 'nullable|integer|min:0',
        ]);

        $existing = Agent::where('agent', $request->user)->first();
        if ($existing) {
            return redirect()->back()->with('message', 'This user is already an agent.');
        }

        $agent = new Agent();
        $agent->agent = $request->user;
        $agent->total_refered = (string) ($request->referred_users ?? 0);
        $agent->total_activated = '0';
        $agent->earnings = '0';
        $agent->save();

        return redirect()->back()->with('success', 'Agent added successfully.');
    }

    public function delagent($id)
    {
        Agent::where('id', $id)->delete();
        return redirect()->back()->with('success', 'Agent removed successfully.');
    }
}
