<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Models\PaymentSetting;
use App\Traits\SendsDatabaseNotifications;
use Illuminate\Http\Request;

class PaymentSettingsController extends Controller
{
    use SendsDatabaseNotifications;

    public function index()
    {
        // Get all payment settings (GCash, BDO, BPI, Cash)
        $settings = PaymentSetting::all();

        return view('cashier.settings.index', compact('settings'));
    }

    public function update(Request $request, $id)
    {
        $setting = PaymentSetting::findOrFail($id);

        $validated = $request->validate([
            'account_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:100',
            'branch' => 'nullable|string|max:255',
            'extra_info' => 'nullable|string',
        ]);

        $setting->update([
            'account_name' => $validated['account_name'],
            'account_number' => $validated['account_number'],
            'branch' => $validated['branch'] ?? $setting->branch,
            'extra_info' => $validated['extra_info'] ?? $setting->extra_info,
            'updated_by' => auth()->id(),
        ]);

        // Send notification to cashier
        $message = '✅ Payment settings for ' . ucfirst($setting->method) . ' have been updated.';
        $url = route('cashier.settings.index');
        $this->sendNotificationToCurrentUser($message, $url);
        session()->flash('check_notifications', true);

        return redirect()
            ->route('cashier.settings.index')
            ->with('success', 'Settings updated successfully!');
    }

    public function toggle($id)
    {
        $setting = PaymentSetting::findOrFail($id);
        
        $setting->update([
            'is_active' => !$setting->is_active,
            'updated_by' => auth()->id(),
        ]);

        $status = $setting->is_active ? 'activated' : 'deactivated';
        
        // Send notification to cashier
        $message = '✅ ' . ucfirst($setting->method) . ' payment method has been ' . $status . '.';
        $url = route('cashier.settings.index');
        $this->sendNotificationToCurrentUser($message, $url);
        session()->flash('check_notifications', true);

        return response()->json([
            'success' => true,
            'is_active' => $setting->is_active,
            'message' => ucfirst($setting->method) . ' has been ' . $status
        ]);
    }
}