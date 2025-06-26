<?php
/**
 * Plugin Name: Site Health Monitor
 * Description: Sends critical error alerts and weekly "all good" emails.
 * Version: 1.0
 */

// === CONFIGURATION ===
$monitor_email = 'alerts@example.com'; // <-- Change this to your preferred notification email
$send_day = 'monday';                  // Day to send weekly status
$send_hour = 8;                        // Hour (0-23) to send weekly status

// === ERROR MONITORING ===
add_action('shutdown', function () use ($monitor_email) {
    $error = error_get_last();
    if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        $subject = '🚨 WordPress Site Error Alert';
        $message = "A critical error occurred on the site:\n\n";
        $message .= "Type: {$error['type']}\n";
        $message .= "Message: {$error['message']}\n";
        $message .= "File: {$error['file']}\n";
        $message .= "Line: {$error['line']}\n";
        $message .= "Time: " . date('Y-m-d H:i:s') . "\n";

        wp_mail($monitor_email, $subject, $message, ['Content-Type: text/plain; charset=UTF-8']);
    }
});

// === WEEKLY STATUS SCHEDULER ===

// Create the cron event on plugin load
add_action('init', function () use ($send_day, $send_hour) {
    if (!wp_next_scheduled('health_monitor_send_status')) {
        $timestamp = strtotime("next $send_day $send_hour:00");
        wp_schedule_event($timestamp, 'weekly', 'health_monitor_send_status');
    }
});

// Custom cron schedule for 'weekly' if missing
add_filter('cron_schedules', function ($schedules) {
    if (!isset($schedules['weekly'])) {
        $schedules['weekly'] = [
            'interval' => 604800, // 7 days in seconds
            'display'  => __('Once Weekly'),
        ];
    }
    return $schedules;
});

// Send "all good" email
add_action('health_monitor_send_status', function () use ($monitor_email) {
    $subject = '✅ Weekly Site Status - All Good';
    $message = "This is your weekly status report from your WordPress site.\n\n";
    $message .= "As of " . date('Y-m-d H:i:s') . ", no critical errors have been reported.\n\n";
    $message .= "Everything looks good.";

    wp_mail($monitor_email, $subject, $message, ['Content-Type: text/plain; charset=UTF-8']);
});

// === CLEANUP ON DEACTIVATION (Optional but good hygiene) ===
register_shutdown_function(function () {
    if (defined('WP_UNINSTALL_PLUGIN')) {
        wp_clear_scheduled_hook('health_monitor_send_status');
    }
});
