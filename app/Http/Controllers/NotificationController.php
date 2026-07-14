<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Services\NotificationService;

class NotificationController extends Controller
{
    /**
     * Get active notifications for the drawer (Critical & Important, inventory only).
     */
    public function getNotifications(Request $request)
    {
        $user = auth()->user();
        if (!$user || !$user->can('view-notifications')) {
            return response()->json(['unread_count' => 0, 'notifications' => []]);
        }

        $branchId = session('branch_id');

        // Throttle background notification checks to once per 60 seconds during page loads to improve navigation performance
        $lastChecked = session('last_notification_check');
        if (!$lastChecked || \Carbon\Carbon::parse($lastChecked)->diffInSeconds(now()) > 60) {
            // Check for expiring batches and update notifications dynamically
            \App\Services\NotificationService::checkExpiries($branchId);
            
            // Check for out of stock and low stock dynamically
            \App\Services\NotificationService::checkStockLevels($branchId);
            
            // Check for warranties dynamically
            \App\Services\NotificationService::checkWarranties($branchId);

            session(['last_notification_check' => now()]);
        }

        // Fetch active (unresolved) notifications for the branch (admins can see all if no branch selected)
        $query = Notification::active()->latest();
        if (!$user->isAdmin() || $branchId) {
            $query->where('branch_id', $branchId);
        }

        // Only display Critical and Important (Warning) notifications
        $query->whereIn('priority', ['Critical', 'Important', 'Activity']);
        $query->whereIn('category', ['Inventory', 'System']);

        $notifications = $query->get()->filter(function ($n) use ($user) {
            return $user->isNotificationEnabled($n->category, $n->type);
        });

        // Unread count ONLY counts Critical notifications!
        $unreadCount = $notifications->where('read_status', false)->where('priority', 'Critical')->count();

        // Group by Date: Today, Yesterday, Last 7 Days, Older
        $grouped = [
            'today' => [],
            'yesterday' => [],
            'last_7_days' => [],
            'older' => []
        ];

        foreach ($notifications as $n) {
            $createdAt = Carbon::parse($n->created_at);
            
            // Format time ago localized
            $timeAgo = $createdAt->diffForHumans();

            $formatted = [
                'id' => $n->id,
                'notification_number' => $n->notification_number,
                'title' => $n->getTranslation('title'),
                'message' => $n->getTranslation('message'),
                'category' => $n->category,
                'priority' => $n->priority,
                'type' => $n->type,
                'read_status' => $n->read_status,
                'time_ago' => $timeAgo,
                'product_id' => $n->product_id,
                'batch_id' => $n->batch_id,
                'reference_type' => $n->reference_type,
                'reference_id' => $n->reference_id,
                'actions' => $this->getNotificationActions($n)
            ];

            if ($createdAt->isToday()) {
                $grouped['today'][] = $formatted;
            } elseif ($createdAt->isYesterday()) {
                $grouped['yesterday'][] = $formatted;
            } elseif ($createdAt->greaterThanOrEqualTo(Carbon::now()->subDays(7)->startOfDay())) {
                $grouped['last_7_days'][] = $formatted;
            } else {
                $grouped['older'][] = $formatted;
            }
        }

        return response()->json([
            'unread_count' => $unreadCount,
            'notifications' => $grouped
        ]);
    }

    /**
     * Mark a single notification or all notifications as read.
     */
    public function markAsRead(Request $request, $id = null)
    {
        $user = auth()->user();
        if (!$user || !$user->can('view-notifications')) {
            return response()->json(['success' => false], 403);
        }

        $branchId = session('branch_id');

        if ($id) {
            $notification = Notification::findOrFail($id);
            $readStatus = $request->has('read_status') ? filter_var($request->input('read_status'), FILTER_VALIDATE_BOOLEAN) : true;
            $notification->update([
                'read_status' => $readStatus,
                'read_date' => $readStatus ? now() : null
            ]);
        } else {
            // Mark all read for active inventory alerts
            $query = Notification::active()->where('read_status', false)->where('category', 'Inventory');
            if (!$user->isAdmin() || $branchId) {
                $query->where('branch_id', $branchId);
            }
            
            $notifications = $query->get()->filter(function ($n) use ($user) {
                return $user->isNotificationEnabled($n->category, $n->type);
            });

            foreach ($notifications as $n) {
                $n->update([
                    'read_status' => true,
                    'read_date' => now()
                ]);
            }
        }

        return response()->json(['success' => true]);
    }

    /**
     * Dismiss (resolve) a single notification.
     */
    public function dismiss(Request $request, $id)
    {
        $user = auth()->user();
        if (!$user || !$user->can('view-notifications')) {
            return response()->json(['success' => false], 403);
        }

        $notification = Notification::findOrFail($id);
        $notification->update([
            'resolved_at' => now()
        ]);

        return response()->json(['success' => true]);
    }

    /**
     * Dismiss (resolve) all active notifications.
     */
    public function dismissAll(Request $request)
    {
        $user = auth()->user();
        if (!$user || !$user->can('view-notifications')) {
            return response()->json(['success' => false], 403);
        }

        $branchId = session('branch_id');

        $query = Notification::active()->where('category', 'Inventory');
        if (!$user->isAdmin() || $branchId) {
            $query->where('branch_id', $branchId);
        }

        $query->update(['resolved_at' => now()]);

        return response()->json(['success' => true]);
    }

    /**
     * Show Settings Form.
     */
    public function showSettings()
    {
        $user = auth()->user();
        if (!$user || !$user->can('view-notifications')) {
            abort(403);
        }
        $settings = $user->notification_settings ?? $this->getDefaultSettings();
        
        return view('settings.notifications', compact('settings'));
    }

    /**
     * Save user notification preferences.
     */
    public function saveSettings(Request $request)
    {
        $user = auth()->user();
        if (!$user || !$user->can('view-notifications')) {
            abort(403);
        }

        $user->notification_settings = $request->input('settings', []);
        if ($user->isDirty('notification_settings')) {
            $user->save();
            return redirect()->back()->with('success', __('pos.settings_updated_successfully') ?? 'Notification settings updated successfully.');
        }

        return redirect()->back()->with('info', __('pos.no_changes_made') ?? 'No changes were made.');
    }

    /**
     * View the Activity Feed page (or return JSON feed).
     */
    public function activityFeed(Request $request)
    {
        $user = auth()->user();
        if (!$user || !$user->can('view-notifications')) {
            abort(403);
        }
        $branchId = session('branch_id');

        $query = Notification::latest();
        if (!$user->isAdmin() || $branchId) {
            $query->where('branch_id', $branchId);
        }

        // Activity feed lists EVERYTHING (Critical, Important, Activity)
        $notifications = $query->get()->filter(function ($n) use ($user) {
            return $user->isNotificationEnabled($n->category, $n->type);
        });

        // Paginate manually for rendering
        $page = $request->input('page', 1);
        $perPage = 25;
        $sliced = array_slice($notifications->all(), ($page - 1) * $perPage, $perPage);
        
        $feedItems = [];
        foreach ($sliced as $n) {
            $feedItems[] = [
                'id' => $n->id,
                'notification_number' => $n->notification_number,
                'title' => $n->getTranslation('title'),
                'message' => $n->getTranslation('message'),
                'category' => $n->category,
                'priority' => $n->priority,
                'created_at' => $n->created_at->format('Y-m-d H:i'),
                'time_ago' => $n->created_at->diffForHumans()
            ];
        }

        if ($request->ajax()) {
            return response()->json($feedItems);
        }

        return view('settings.activity_feed', compact('feedItems'));
    }

    /**
     * Get polymorphic URL links and buttons for notifications.
     */
    private function getNotificationActions(Notification $n)
    {
        $actions = [];
        
        if ($n->product_id) {
            $actions[] = [
                'label_en' => 'View Product',
                'label_ar' => 'عرض المنتج',
                'url' => route('products.index') . '?search=' . $n->product_id,
                'class' => 'btn-outline-primary'
            ];
            
            if ($n->type === 'low_stock' || $n->type === 'out_of_stock') {
                $actions[] = [
                    'label_en' => 'Create Purchase',
                    'label_ar' => 'إنشاء مشتريات',
                    'url' => route('purchases.create') . '?product_id=' . $n->product_id,
                    'class' => 'btn-primary'
                ];
            } else {
                $actions[] = [
                    'label_en' => 'Create Adjustment',
                    'label_ar' => 'عمل تسوية',
                    'url' => route('adjustments.index') . '?product_id=' . $n->product_id,
                    'class' => 'btn-warning text-dark'
                ];
            }
        }

        if ($n->batch_id) {
            $batch = \App\Models\Batch::find($n->batch_id);
            if ($batch) {
                $actions[] = [
                    'label_en' => 'View Batch',
                    'label_ar' => 'عرض الدفعة',
                    'url' => route('products.index') . '?search=' . urlencode($batch->batch_number),
                    'class' => 'btn-outline-secondary'
                ];
            }
        }

        return $actions;
    }

    /**
     * Get default user settings.
     */
    private function getDefaultSettings()
    {
        return [
            'inventory' => [
                'low_stock' => true,
                'out_of_stock' => true,
                'expiring_soon' => true,
                'expired' => true,
                'expiry_warning_period' => 30
            ]
        ];
    }
}
