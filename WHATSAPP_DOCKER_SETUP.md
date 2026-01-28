# WhatsApp API Docker Setup Guide

## Overview
This guide will help you set up the go-multidevice WhatsApp API in Docker for testing with your Bryanz Logistics application.

## Prerequisites
- Docker Desktop installed and running on Windows
- Your Laravel application running (php artisan serve)

## Setup Steps

### 1. Start the WhatsApp API Container

```powershell
# Navigate to your project directory
cd "c:\Users\MYCO KAGAME\Desktop\Bryanz Logistics"

# Start the WhatsApp API service
docker-compose -f docker-compose.whatsapp.yml up -d
```

### 2. Access the Web Interface

Open your browser and go to: **http://localhost:3000**

You should see the go-multidevice web interface.

### 3. Link Your WhatsApp Account

1. Click on **"Login"** or **"Scan QR"**
2. Open WhatsApp on your phone
3. Go to **Settings > Linked Devices > Link a Device**
4. Scan the QR code displayed in the browser
5. Wait for the connection to establish

### 4. Update Your Laravel .env File

Update the WhatsApp API URL in your `.env` file:

```env
WHATSAPP_API_URL=http://localhost:3000
WHATSAPP_PHONE_NUMBER_ID=your-phone-number
```

### 5. Test the API

You can test if it's working by sending a test message:

```powershell
# Using PowerShell
$body = @{
    phone = "2567xxxxxxxx"  # Replace with test number (include country code)
    message = "Hello from Bryanz Logistics!"
} | ConvertTo-Json

Invoke-RestMethod -Uri "http://localhost:3000/send-message" -Method Post -Body $body -ContentType "application/json"
```

## API Endpoints

The go-multidevice API provides these endpoints:

- `GET /app/devices` - List linked devices
- `POST /send-message` - Send a text message
- `POST /send-image` - Send an image
- `POST /send-file` - Send a file
- `GET /app/logout` - Logout/disconnect

## Updating Your WhatsAppService

You'll need to update your `WhatsAppService.php` to use the new API endpoints:

```php
// Example for sending a message
public function sendMessage($to, $message)
{
    $response = Http::timeout(60)
        ->retry(3, 1000)
        ->post(config('services.whatsapp.api_url') . '/send-message', [
            'phone' => $to,
            'message' => $message,
        ]);

    return $response->successful();
}
```

## Troubleshooting

### Container won't start
```powershell
# Check container logs
docker-compose -f docker-compose.whatsapp.yml logs -f
```

### QR Code not showing
- Make sure port 3000 is not being used by another application
- Try restarting the container: `docker-compose -f docker-compose.whatsapp.yml restart`

### Session keeps disconnecting
- The session data is stored in a Docker volume named `whatsapp_data`
- To reset: `docker-compose -f docker-compose.whatsapp.yml down -v` (this will delete the session)

### Can't send messages
- Verify your phone number format includes country code (e.g., 256774222619)
- Check that WhatsApp is still linked on your phone
- View logs: `docker-compose -f docker-compose.whatsapp.yml logs whatsapp-api`

## Stopping the Service

```powershell
# Stop the container
docker-compose -f docker-compose.whatsapp.yml down

# Stop and remove volumes (will require re-scanning QR code)
docker-compose -f docker-compose.whatsapp.yml down -v
```

## Important Notes

1. **Session Persistence**: The WhatsApp session is stored in a Docker volume, so you won't need to re-scan the QR code every time you restart the container.

2. **Network Access**: The container can communicate with your Laravel app running on `localhost` via `host.docker.internal`.

3. **Production Use**: For production, you should:
   - Use environment variables for sensitive data
   - Set up proper webhook handling
   - Consider using a reverse proxy (nginx)
   - Implement rate limiting

4. **WhatsApp Limitations**: 
   - You can only link one device at a time with this setup
   - WhatsApp may ban accounts that send too many messages
   - Always test with your own number first

## Next Steps

After setting this up, you can integrate it with your Laravel application by updating the `WhatsAppService` class to use the new API endpoints instead of the Meta WhatsApp Cloud API.
