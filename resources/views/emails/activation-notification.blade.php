<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>License Activation Confirmation</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <h1 style="color: #2563eb;">License Activated Successfully</h1>
        
        <p>Your license has been activated successfully.</p>
        
        <h2>Activation Details</h2>
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td style="padding: 8px; border-bottom: 1px solid #ddd;"><strong>Product:</strong></td>
                <td style="padding: 8px; border-bottom: 1px solid #ddd;">{{ $license->product_name }}</td>
            </tr>
            <tr>
                <td style="padding: 8px; border-bottom: 1px solid #ddd;"><strong>Device:</strong></td>
                <td style="padding: 8px; border-bottom: 1px solid #ddd;">{{ $activation->device_name ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td style="padding: 8px; border-bottom: 1px solid #ddd;"><strong>Instance:</strong></td>
                <td style="padding: 8px; border-bottom: 1px solid #ddd;">{{ $activation->instance_identifier ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td style="padding: 8px; border-bottom: 1px solid #ddd;"><strong>Activated At:</strong></td>
                <td style="padding: 8px; border-bottom: 1px solid #ddd;">{{ $activation->activated_at->format('Y-m-d H:i:s') }}</td>
            </tr>
            @if($license->expires_at)
            <tr>
                <td style="padding: 8px; border-bottom: 1px solid #ddd;"><strong>Expires At:</strong></td>
                <td style="padding: 8px; border-bottom: 1px solid #ddd;">{{ $license->expires_at->format('Y-m-d') }}</td>
            </tr>
            @endif
        </table>
        
        <p style="margin-top: 20px; color: #666; font-size: 14px;">
            If you did not initiate this activation, please contact support immediately.
        </p>
    </div>
</body>
</html>

