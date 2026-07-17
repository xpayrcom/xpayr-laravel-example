# XPayr Laravel Integration Example

[![CI](https://github.com/xpayrcom/xpayr-laravel-example/actions/workflows/ci.yml/badge.svg)](https://github.com/xpayrcom/xpayr-laravel-example/actions/workflows/ci.yml)
[![License: MIT](https://img.shields.io/badge/License-MIT-0f766e.svg)](LICENSE)

Runnable Laravel 12 application for creating XPayr checkout sessions, persisting payment state, and verifying webhook events safely.

> **Status:** Standalone testable application

## Purpose

Provide a production-minded Laravel application without hiding payment-state or webhook-security decisions behind generated code.

## Included

- Server-side checkout controller and XPayr API service
- Payment persistence migration and model
- Raw-body webhook verification with idempotent updates
- SQLite-ready local setup and full feature tests

## Quick start

```bash
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate
php artisan serve
```

## Installation

1. Set `XPAYR_SECRET_KEY` to an `sk_test_*` key in `.env`.
2. Set `XPAYR_WEBHOOK_SECRET` to the secret configured in the XPayr merchant dashboard.
3. Run the SQLite migrations with `php artisan migrate`.
4. Create sessions through authenticated `POST /api/checkout/xpayr` requests.
5. Register `https://your-domain.example/api/webhooks/xpayr` in the XPayr merchant dashboard.

Run the complete local suite with:

```bash
php artisan test
```

The checkout endpoint requires your application authentication middleware. The webhook endpoint verifies the untouched body, stores every event under a unique event ID, and applies the payment update in the same database transaction.

Checkout creation is intentionally not retried automatically because repeating a `POST /payments` request without an idempotency contract could create duplicate sessions. Applications should persist the returned session ID and resolve uncertain requests through order-level reconciliation.

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
