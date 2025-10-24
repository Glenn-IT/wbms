# Email Notification Setup Guide

## Gmail SMTP Configuration

This system uses Gmail SMTP to send email notifications to clients about their overdue bills.

### Prerequisites

1. **PHPMailer Library** - Required for sending emails
2. **Gmail Account** - waterbillingsystem00@gmail.com
3. **App Password** - Already configured: `xoqu lcpm pprm bwbu`

### Installation Steps

#### Step 1: Install PHPMailer

If you're using Composer:

```bash
composer require phpmailer/phpmailer
```

Or manually download PHPMailer and place it in: `c:\xampp\htdocs\wbms\libs\PHPMailer\`

The directory structure should be:

```
c:\xampp\htdocs\wbms\libs\PHPMailer\
├── src/
│   ├── Exception.php
│   ├── PHPMailer.php
│   └── SMTP.php
```

#### Step 2: Add Email Column to Database

Run the SQL migration script:

```sql
-- Open phpMyAdmin and run this query:
ALTER TABLE `client_list`
ADD COLUMN `email` VARCHAR(255) NULL DEFAULT NULL AFTER `contact`;
```

Or use the provided file: `database/add_client_email.sql`

#### Step 3: Update Client Records with Email Addresses

You need to add email addresses to your clients:

1. Go to Admin Panel → Clients
2. Edit each client and add their email address
3. Save the changes

### Features

#### 1. Send Individual Email

- From the "List of Due Bills" page
- Click on a client's Action dropdown
- Select "Send Email" (only visible if client has an email)
- Confirmation dialog will appear
- Email will be sent immediately

#### 2. Send Bulk Emails

- Click the "Send Email Notifications" button at the top
- Confirmation dialog will appear
- System will send emails to all clients with:
  - Overdue bills (past due date)
  - Valid email addresses
  - Active status
- Success message will show how many emails were sent

### Email Template

The email includes:

- Client name and contact details
- Billing statement with:
  - Client Code and Meter ID
  - Reading Date and Due Date
  - Current and Previous Reading
  - Consumption and Rate
  - Total Amount Due
  - Days Overdue (if applicable)
- Payment instructions
- Professional formatting (HTML and Plain Text versions)

### Configuration Files

1. **MailConfig.php** - SMTP configuration

   - Gmail SMTP settings
   - Email credentials
   - Sender information

2. **MailNotification.php** - Email sending logic
   - PHPMailer integration
   - Email templates
   - Batch sending functionality

### Troubleshooting

#### Email not sending?

1. Check if PHPMailer is installed correctly
2. Verify Gmail credentials in `MailConfig.php`
3. Ensure client has a valid email address
4. Check server error logs

#### Gmail blocking emails?

1. Verify the App Password is correct
2. Check if "Less secure app access" is enabled (if using regular password)
3. Try generating a new App Password from Google Account settings

#### Emails going to spam?

1. This is normal for automated emails
2. Ask clients to whitelist: waterbillingsystem00@gmail.com
3. Consider using a custom domain email for better deliverability

### Security Notes

- App Password is stored in `MailConfig.php`
- Never commit this file to public repositories
- Consider using environment variables for production
- Gmail has a sending limit of ~500 emails per day

### Testing

1. Add your personal email to a test client
2. Use "Send Email" from the Actions menu
3. Check your inbox (and spam folder)
4. Verify email formatting and content

### Support

For issues or questions:

- Check the browser console for JavaScript errors
- Check PHP error logs in xampp/apache/logs/
- Verify database connection
- Test with a single email first before bulk sending
