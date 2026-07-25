<div style="font-family: sans-serif; padding: 20px; color: #334155; max-width: 600px; border: 1px solid #e2e8f0; border-radius: 12px;">
    <h2 style="color: #1e3a8a; border-bottom: 2px solid #e2e8f0; padding-bottom: 10px;">New Portfolio Inquiry</h2>

    <p style="margin-top: 20px;"><strong>Name:</strong> {{ $senderName }}</p>
    <p><strong>Email:</strong> <a href="mailto:{{ $senderEmail }}">{{ $senderEmail }}</a></p>

    <div style="margin-top: 20px; padding: 15px; border-left: 4px solid #3b82f6; background: #f8fafc;">
        <p style="margin: 0; font-weight: bold; color: #475569; margin-bottom: 5px;">Message:</p>
        <p style="margin: 0; white-space: pre-wrap; line-height: 1.6;">{{ $body }}</p>
    </div>

    <p style="font-size: 11px; color: #94a3b8; margin-top: 30px; border-top: 1px solid #e2e8f0; padding-top: 10px;">
        Sent automatically from your portfolio website.
    </p>
</div>
