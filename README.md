# WordPress Site Health Monitor

This is a Must-Use Plugin (MU-plugin) for WordPress that automatically monitors the site for critical errors and sends weekly status emails.

## Features

- 🚨 Instant email alerts for fatal PHP errors
- ✅ Weekly "all good" status reports every Monday
- ✉️ Uses a custom email (not tied to `admin_email`)
- 🛡️ Hidden from WordPress plugin UI — runs silently
- 💾 No dependencies, no plugin activation required

## Installation

1. Place `site-health-monitor.php` in your `wp-content/mu-plugins/` directory.
2. Update the email address inside the file to your desired recipient.
3. Ensure your server can send emails (via `wp_mail()` or SMTP plugin).

## License

MIT
