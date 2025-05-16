<h1>XKCD Comic Subscription System</h1>
Project Description
The XKCD Comic Subscription System is a PHP-based web application that allows users to subscribe to daily XKCD comics via email. Users register their email address, receive a verification code, and, upon verification, are added to a subscription list. A daily task sends a random XKCD comic to all subscribers. Users can unsubscribe using a similar verification process. The system is designed to run on Windows using XAMPP, PHP 8.3, and MailHog for email testing, with Task Scheduler automating daily comic delivery.
Features

Email Registration: Users submit an email via a web form (index.php), receive a 6-digit verification code, and verify it to subscribe.
Unsubscription: Users can unsubscribe via a web form (unsubscribe.php), receiving a verification code to confirm.
Daily Comic Delivery: A scheduled task (cron.php) sends a random XKCD comic to subscribers daily.
Local Email Testing: MailHog captures emails for testing without sending to real inboxes.
No Database: Registered emails are stored in registered_emails.txt.
HTML Emails: Emails use specified HTML formats with From: no-reply@example.com.

Project Requirements

PHP 8.3 with mail() function.
Web server (XAMPP’s Apache).
Forms always visible with correct name and id attributes.
Emails in specified HTML formats:
Verification: <p>Your verification code is: <strong>code</strong></p>
Unsubscribe: <p>To confirm un-subscription, use this code: <strong>code</strong></p>
Comic: <h2>XKCD Comic: XKCD Comic Subscription System</h2><img src="[URL]" alt="XKCD Comic"><p><a href="[unsubscribe_url]" id="unsubscribe-button">Unsubscribe</a></p>


Daily task automation (Task Scheduler on Windows, replacing CRON).
All code in src/ directory.

File Structure
The project resides in the src/ directory:

index.php: Handles email registration and verification forms. Users submit an email, receive a verification code, and verify it to subscribe.
unsubscribe.php: Manages unsubscription. Users submit an email, receive a verification code, and confirm to unsubscribe.
functions.php: Contains core logic:
generateVerificationCode(): Generates a 6-digit code.
registerEmail(): Adds an email to registered_emails.txt.
unsubscribeEmail(): Removes an email from registered_emails.txt.
sendVerificationEmail(): Sends verification code emails.
verifyCode(): Validates verification codes using sessions.
fetchAndFormatXKCDData(): Fetches and formats XKCD comic data.
sendXKCDUpdatesToSubscribers(): Sends comic emails to subscribers.


cron.php: Executes sendXKCDUpdatesToSubscribers() to send daily comics.
setup_task.ps1: PowerShell script to configure a daily Task Scheduler job for cron.php.
registered_emails.txt: Stores subscribed email addresses (created automatically).

Prerequisites

Windows: Tested on Windows 10/11.
XAMPP: Provides Apache and PHP 8.3.
PHP 8.3: Included in XAMPP or installed manually.
MailHog: Local email server for testing.
Git: For version control and submission.
Internet Access: To fetch XKCD comics.

Setup Instructions
Follow these steps to set up and run the project on Windows.
1. Install XAMPP

Download XAMPP from https://www.apachefriends.org/.
Install to C:\xampp (default path).
Start XAMPP Control Panel and verify Apache runs (port 80).

2. Install PHP 8.3 (if not using XAMPP)

If not using XAMPP, download PHP 8.3 from https://windows.php.net/download/.
Extract to C:\php.
Add C:\php to the system PATH.
Verify: php --version (should show PHP 8.3.x).

3. Install MailHog

Download MailHog for Windows from https://github.com/mailhog/MailHog/releases (e.g., MailHog_windows_amd64.exe).
Save to C:\MailHog\MailHog.exe.
Run MailHog:cd C:\MailHog
MailHog.exe


Access the UI at http://localhost:8025.
Keep MailHog running during testing.

4. Configure PHP for Email Sending

Open C:\xampp\php\php.ini (or C:\php\php.ini if manual PHP).
Update the [mail function] section:[mail function]
SMTP=localhost
smtp_port=1025
sendmail_from=no-reply@example.com
mail.log=C:\xampp\php\logs\mail.log


Enable extension=openssl (uncomment if needed).
Save and restart Apache (XAMPP Control Panel: Stop/Start Apache).

5. Place Project Files

Create C:\xampp\htdocs\project-root\src.
Copy the following files to C:\xampp\htdocs\project-root\src:
index.php
unsubscribe.php
functions.php
cron.php
setup_task.ps1


Ensure files match the provided code (with debugging enabled for testing).
Set permissions:icacls C:\xampp\htdocs\project-root\src /grant Users:F



6. Configure Task Scheduler

Update setup_task.ps1 to use the correct PHP path:$PhpPath = "C:\xampp\php\php.exe"


Run as Administrator:cd C:\xampp\htdocs\project-root\src
.\setup_task.ps1


Verify in Task Scheduler (taskschd.msc):
Task: XKCDDailyComic
Trigger: Daily at 12:00 AM
Action: Run C:\xampp\php\php.exe with C:\xampp\htdocs\project-root\src\cron.php



7. Verify Session Storage

In php.ini, ensure:session.save_path="C:\xampp\tmp"


Grant permissions:icacls C:\xampp\tmp /grant Users:F



How to Run the Project
Follow these steps to run and test the application.
1. Start Services

Apache: Start in XAMPP Control Panel.
MailHog:cd C:\MailHog
MailHog.exe



2. Test Email Registration

Go to http://localhost/project-root/src/index.php.
Verify forms:
Email: <input name="email" id="email">, <button id="submit-email">
Verification: <input name="verification_code" id="verification_code">, <button id="submit-verification">


Enter an email (e.g., test@example.com), click “Submit.”
Check MailHog (http://localhost:8025):
Subject: Your Verification Code
Body: <p>Your verification code is: <strong>123456</strong></p>


Enter the code, click “Verify.”
Confirm: “Email test@example.com successfully registered!”
Check registered_emails.txt contains the email.

3. Test Unsubscription

Go to http://localhost/project-root/src/unsubscribe.php.
Verify forms:
Unsubscribe: <input name="unsubscribe_email" id="unsubscribe_email">, <button id="submit-unsubscribe">
Verification: <input name="verification_code" id="verification_code">, <button id="submit-verification">


Enter the registered email, click “Unsubscribe.”
Check MailHog:
Subject: Confirm Un-subscription
Body: <p>To confirm un-subscription, use this code: <strong>654321</strong></p>


Enter the code, click “Verify.”
Confirm: “Email test@example.com successfully unsubscribed!”
Check registered_emails.txt is empty or excludes the email.

4. Test Comic Sending

Register an email.
Run cron.php manually:cd C:\xampp\htdocs\project-root\src
C:\xampp\php\php.exe cron.php


Check MailHog:
Subject: Your XKCD Comic
Body: <h2>XKCD Comic: [Title]</h2><img src="[URL]" alt="XKCD Comic"><p><a href="http://localhost/project-root/src/unsubscribe.php" id="unsubscribe-button">Unsubscribe</a></p>


Click the unsubscribe link to confirm it opens unsubscribe.php.

5. Test Task Scheduler

Open Task Scheduler (taskschd.msc).
Right-click “XKCDDailyComic,” select “Run.”
Check MailHog for a comic email.
The task runs automatically at midnight daily.

Troubleshooting

No Emails in MailHog:
Verify MailHog is running (http://localhost:8025).
Check php.ini settings (smtp_port=1025).
Review C:\xampp\php\logs\php_error_log and mail.log.
Test with test_mail.php:<?php
$to = "test@example.com";
$subject = "Test Email";
$body = "<p>Test email from PHP</p>";
$headers = "From: no-reply@example.com\r\nContent-Type: text/html; charset=UTF-8\r\n";
if (mail($to, $subject, $body, $headers)) {
    echo "Email sent!";
} else {
    echo "Email failed.";
}
?>




Session Issues:
Ensure C:\xampp\tmp is writable.
Clear sessions: del C:\xampp\tmp\sess_*.


File Permissions:
Reapply: icacls C:\xampp\htdocs\project-root\src /grant Users:F.


Task Scheduler Fails:
Verify $PhpPath in setup_task.ps1.
Check Task Scheduler’s “History” tab.


Comic Fetch Fails:
Ensure allow_url_fopen=On in php.ini.
Test: file_get_contents('https://xkcd.com/1/info.0.json').



Notes

Windows Adaptation: Task Scheduler (setup_task.ps1) replaces CRON due to the Windows environment. This is equivalent to the required setup_cron.sh for Linux.
MailHog: Used for local testing to capture emails without sending to real inboxes (e.g., @gmail.com).
Debugging: Files include error_log statements for troubleshooting. Remove before production if needed.
Submission: Only src/ files are submitted. No modifications outside src/.

Submission Instructions

Clone the repository:cd C:\Users\YourUsername\Documents
git clone https://github.com/your-repo/xkcd-project.git
cd xkcd-project


Create a branch:git checkout -b feature/xkcd-windows


Copy src/:xcopy C:\xampp\htdocs\project-root\src C:\Users\YourUsername\Documents\xkcd-project\src /E /H /C /I


Commit and push:git add src/
git commit -m "Implemented XKCD system for Windows with Task Scheduler"
git push origin feature/xkcd-windows


Raise a pull request (PR) against main with description:Implemented XKCD comic subscription system on Windows using PHP 8.3, XAMPP, and Task Scheduler (setup_task.ps1) due to lack of CRON. Emails tested with MailHog (localhost:1025). All requirements met:
- Forms always visible with correct attributes.
- HTML email formats as specified.
- Uses registered_emails.txt.
- Daily task via Task Scheduler.



License
This project is for educational purposes and follows the guidelines of the XKCD comic subscription challenge.
