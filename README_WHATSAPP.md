# WhatsApp Integration (Twilio)

This allows you to send WhatsApp messages to registration users from the event dashboard.

## Setup

1. **Twilio account**: Sign up at [twilio.com](https://www.twilio.com) and get your Account SID and Auth Token from the [Console](https://www.twilio.com/console).

2. **WhatsApp**: In Twilio Console, go to Messaging → Try it out → Send a WhatsApp message. You can use the **Sandbox** for testing (link your phone to the sandbox) or set up **WhatsApp Business API** for production.

3. **.env**: Add to your `.env`:
   ```
   TWILIO_ACCOUNT_SID=your_account_sid
   TWILIO_AUTH_TOKEN=your_auth_token
   TWILIO_WHATSAPP_FROM=whatsapp:+14155238886
   ```
   Use your Twilio WhatsApp number for `TWILIO_WHATSAPP_FROM` (format: `whatsapp:+1234567890`).

## Usage

1. Go to **Event → Registrations** (or a specific registration form) to see the list of registered users.
2. Select users using the **checkboxes** (or use "Bulk Actions" → **Send WhatsApp**).
3. Click the **Send WhatsApp** button (green) in the header or choose **Send WhatsApp** from the Bulk Actions dropdown.
4. In the modal, write your message. Use **placeholders** (click to insert):
   - `@first_name` – First name  
   - `@last_name` – Last name  
   - `@email` – Email  
   - `@phone` – Phone  
   - `@unique_code` – Registration/ticket code  
   - `@event_title` – Event name  
   - `@registration_name` – Registration form name  
   - `@user_type` – User type  

   Example: `مرحبا @first_name @last_name ندعوكم لحضور @event_title ...`
5. Click **Send to N recipients**. Only users with a valid **phone number** will receive the message; others are skipped.

## Phone numbers

- Numbers are normalized to E.164 (e.g. Saudi: `05xxxxxxxx` → `9665xxxxxxxx`).
- Ensure registration users have a **phone** field filled so they can receive WhatsApp messages.
