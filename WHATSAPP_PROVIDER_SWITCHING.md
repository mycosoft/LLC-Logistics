# Quick Guide: Switching WhatsApp Providers

## Important: Queue Worker Restart Required! ⚠️

When you change `WHATSAPP_PROVIDER` in your `.env` file, you MUST restart the queue worker for the changes to take effect.

### Step-by-Step Process:

1. **Update `.env` file:**
```env
WHATSAPP_PROVIDER=local  # or 'meta' or 'auto'
```

2. **Clear config cache:**
```bash
php artisan config:clear
```

3. **Restart queue worker:**
```bash
php artisan queue:restart
```

4. **Wait a few seconds**, then the queue worker will automatically restart and pick up the new configuration

5. **Test the change** by updating a shipment status

---

## Quick Test Command

To verify which provider is active:
```bash
php artisan whatsapp:test
```

This will show you:
- Current provider configuration
- Provider availability status  
- Option to send a test message

---

## Common Issues

### ❌ "I changed .env but notifications still use old provider"
**Solution:** Run `php artisan queue:restart`

### ❌ "No WhatsApp notification received"
**Checklist:**
1. Did you restart queue worker? (`php artisan queue:restart`)
2. Is Docker container running? (`docker ps`)
3. Is WhatsApp device connected? (Check http://localhost:3000)
4. Does the client have a phone number? (Check client record)
5. Is WhatsApp notification enabled in settings?

### ❌ "Local API not available"
**Solution:**
```bash
docker-compose -f docker-compose.whatsapp.yml up -d
```

---

## Provider Comparison

| Feature | Meta Cloud API | Local API (Docker) |
|---------|----------------|-------------------|
| Templates | ✅ Supported | ❌ Not supported |
| Cost | 💰 Paid per message | 🆓 Free |
| Setup | Complex (Meta approval) | Simple (scan QR) |
| Limitations | Requires templates | Personal WhatsApp |
| Best for | Production | Development/Testing |

---

## Remember:
> **Always restart the queue worker after changing WHATSAPP_PROVIDER!**
> 
> ```bash
> php artisan queue:restart
> ```
