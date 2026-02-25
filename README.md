# GetSMS

Full-stack PHP app for buying or renting mobile numbers for verification with SMS-Man integration and Fapshi payments.

## Setup
1. Create a MySQL database named `getsms`.
2. Import the schema at database/schema.sql.
3. Configure environment variables (optional):
   - `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS`
   - `SMSMAN_TOKEN`
   - `FAPSHI_API_KEY`
4. Point Apache to the public folder: c:\xampp\htdocs\getsms\public

## Default admin
- Email: admin@getsms.local
- Password: Admin123!

## Notes
- The API documentation is available at /api/docs after login.
- Generate an API token from the dashboard before calling API endpoints.
- Fapshi webhook endpoint: /webhooks/fapshi (POST JSON). Configure this in your Fapshi dashboard.
- SMS-Man and Fapshi must be configured with valid tokens for production use.
