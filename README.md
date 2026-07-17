# XPayr Laravel Integration Example

[![CI](https://github.com/xpayrcom/xpayr-laravel-example/actions/workflows/ci.yml/badge.svg)](https://github.com/xpayrcom/xpayr-laravel-example/actions/workflows/ci.yml)
[![License: MIT](https://img.shields.io/badge/License-MIT-0f766e.svg)](LICENSE)

Reference Laravel integration for creating XPayr checkout sessions, persisting payment state, and verifying webhook events safely.

> **Status:** Framework integration reference

## Purpose

Provide a production-minded Laravel recipe without hiding payment-state or webhook-security decisions behind generated code.

## Included

- Server-side checkout controller and XPayr API service
- Payment persistence migration and model
- Raw-body webhook verification with idempotent updates

## Quick start

```bash
Review README installation steps, copy the integration files into a Laravel 11 or 12 application, and configure a test key.
```

## Installation

1. Copy `app/`, `config/xpayr.php`, the migration, and `routes/xpayr.php` into a Laravel 11 or 12 application.
2. Load `routes/xpayr.php` from `bootstrap/app.php` or merge the routes into your API routes.
3. Copy the XPayr variables from `.env.example`; begin with an `sk_test_*` key.
4. Run `php artisan migrate`.
5. Register `https://your-domain.example/webhooks/xpayr` in the XPayr merchant dashboard.

The checkout endpoint requires your application authentication middleware. The webhook endpoint verifies the untouched body, stores every event under a unique event ID, and applies the payment update in the same database transaction.

Do not grant products from the checkout response. Fulfillment starts only after verified webhook/API state confirms payment completion.

Use an XPayr test key before live credentials. Never expose `sk_test_*`, `sk_live_*`, agent keys, webhook secrets, or wallet private keys in browser code or commits.

## Documentation

- [Developer Hub](https://xpayr.com/developers)
- [Merchant API documentation](https://xpayr.com/doc-api)
- [Testnet checkout guide](https://xpayr.com/developers/testnet-checkout-api)
- [Webhook signature guide](https://xpayr.com/developers/webhook-signature-guide)

## Security

Read [SECURITY.md](SECURITY.md) before reporting a vulnerability. Payment completion must be based on verified XPayr webhook/API state and canonical on-chain evidence, not browser callbacks alone.

## License

MIT. See [LICENSE](LICENSE).
