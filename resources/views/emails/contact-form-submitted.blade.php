<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Contact Form Submission</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #FCD34D;
            color: #1E40AF;
            padding: 20px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        .content {
            background-color: #f9fafb;
            padding: 30px;
            border: 1px solid #e5e7eb;
            border-top: none;
            border-radius: 0 0 8px 8px;
        }
        .field {
            margin-bottom: 20px;
        }
        .label {
            font-weight: 600;
            color: #1E40AF;
            display: block;
            margin-bottom: 5px;
        }
        .value {
            background-color: white;
            padding: 12px;
            border-radius: 4px;
            border: 1px solid #d1d5db;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #e5e7eb;
            text-align: center;
            color: #6b7280;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>New Contact Form Submission</h1>
    </div>
    
    <div class="content">
        <p>You have received a new contact form submission from the Kidical Mass Belgium website.</p>
        
        <div class="field">
            <span class="label">Name:</span>
            <div class="value">{{ $contactForm->name }}</div>
        </div>
        
        <div class="field">
            <span class="label">Email:</span>
            <div class="value">
                <a href="mailto:{{ $contactForm->email }}">{{ $contactForm->email }}</a>
            </div>
        </div>
        
        @if($contactForm->phone)
        <div class="field">
            <span class="label">Phone:</span>
            <div class="value">{{ $contactForm->phone }}</div>
        </div>
        @endif
        
        <div class="field">
            <span class="label">Message:</span>
            <div class="value">{{ $contactForm->message }}</div>
        </div>
        
        <div class="field">
            <span class="label">Submitted From:</span>
            <div class="value">
                <a href="{{ $contactForm->page_url }}">{{ $contactForm->page_url }}</a>
            </div>
        </div>
        
        <div class="field">
            <span class="label">Submitted At:</span>
            <div class="value">{{ $contactForm->created_at->format('F j, Y - g:i A') }}</div>
        </div>
    </div>
    
    <div class="footer">
        <p>This is an automated message from Kidical Mass Belgium</p>
        <p>Reply to this email to respond directly to {{ $contactForm->name }}</p>
    </div>
</body>
</html>
