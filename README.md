# RDFFinance

RDFFinance is a Laravel 8.1 application that provides secure authentication, OTP-based Aadhar login, document-aware registration, and rich loan servicing workflows with smart EMI tools.

## Features

- Email/password and Aadhar/OTP login flows with SweetAlert feedback.
- Automated Aadhar number extraction from uploaded PDFs or images using OCR.
- User registration with document uploads and duplicate Aadhar validation.
- Loan origination with configurable penalties, EMI schedule generation, and reminders.
- Smart EMI calculator accessible without authentication.
- AJAX-powered forms and Bootstrap 5 UI for a seamless experience.

## Getting Started

1. **Clone and install dependencies**
   ```bash
   composer install
   npm install
   ```

2. **Environment setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

   Configure database credentials and mailer settings in `.env`. The project defaults to SMTP via `sendmail()`. For development you can use Mailtrap.

3. **Additional requirements**
   - PHP 8.0+ with required extensions.
   - MySQL 5.7+/MariaDB 10.3+.
   - Node.js 16+.
   - [Tesseract OCR](https://github.com/tesseract-ocr/tesseract) and Poppler (`pdftotext`) binaries for document parsing.

4. **Database**
   ```bash
   php artisan migrate
   ```

5. **Assets**
   ```bash
   npm run dev   # or npm run production
   php artisan storage:link
   ```

6. **Run the application**
   ```bash
   php artisan serve
   ```

Visit `http://localhost:8000` to access the portal.

## Testing Guest EMI Calculator

The smart EMI calculator is accessible at `/emi-calculator` without logging in. Authenticated users can open it from the dashboard or navbar at any time.

## Mail & OTP

SMTP defaults are provided in `.env.example`. For production configure your own SMTP host. OTPs are sent via `Mail::raw` and stored in the `otps` table with expiry management.

## Loan Reminders & Penalties

- Reminders are shown on the dashboard and update automatically.
- Penalties default to ₹100 but can be customised per loan or adjusted during EMI payment.
- Overdue EMIs automatically flip to an `overdue` status whenever reminders are generated.

## Project Structure Highlights

- Controllers live under `app/Http/Controllers`.
- Service layer in `app/Services` encapsulates OTP, Aadhar extraction, and loan logic.
- Front-end scripts reside in `resources/js/pages` and are compiled with Laravel Mix.
- Blade templates are organised under `resources/views`.

## Scripts

- `npm run dev` – compile assets for development.
- `npm run prod` – compile and version assets for production.
- `php artisan queue:work` – optional if you offload mail sending or reminders.

## Notes

- Ensure OCR binaries are installed and available in the system path.
- Large Aadhar files may take longer to parse; make sure PHP max upload size exceeds 5MB if needed.
- SweetAlert is used for all AJAX responses; gracefully handle errors by inspecting browser console logs.

Enjoy building with RDFFinance!

