<?php

namespace App\Listeners;

use App\Models\ActivityLog;
use Illuminate\Auth\Events\Logout;

class LogUserLogout
{
    /**
     * Handle the event.
     */
    public function handle(Logout $event): void
    {
        ActivityLog::create([
            'user_id' => $event->user?->id,
            'action' => 'logout',
            'subject_type' => 'Illuminate\Auth\Events\Logout',
            'description' => ($event->user ? $event->user->name : 'System') . ' keluar dari sistem',
            'ip_address' => request()?->ip(),
            'user_agent' => request()?->userAgent(),
            'location' => $this->getLocation(request()?->ip()),
        ]);
    }

    protected function getLocation(?string $ip): string
    {
        if (!$ip || $ip === '127.0.0.1' || $ip === '::1') {
            return 'Local';
        }
        try {
            $ctx = stream_context_create(['http' => ['ignore_errors' => true, 'timeout' => 2]]);
            $result = @file_get_contents("https://ipapi.co/{$ip}/json/", false, $ctx);
            if ($result) {
                $data = json_decode($result, true);
                if (isset($data['city']) && isset($data['country_name'])) {
                    return $data['city'] . ', ' . $data['country_name'];
                }
                if (isset($data['country_name'])) {
                    return $data['country_name'];
                }
            }
        } catch (\Exception $e) {
            // fallback
        }
        return 'Unknown';
    }
}
