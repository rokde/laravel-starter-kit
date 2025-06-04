# Passkeys Module

This module provides passkey authentication support for the Laravel Starter Kit, allowing users to register and use passkeys (WebAuthn credentials) for secure, passwordless authentication.

## Features

- Passwordless authentication using passkeys (WebAuthn)
- Management of multiple passkeys per user
- Integration with Laravel's authentication system
- User-friendly interface for registering and managing passkeys

## Requirements

- PHP 8.2 or higher
- Laravel 10.x or higher
- Browser with WebAuthn support (most modern browsers)
- Secure context (HTTPS or localhost)

## Installation

This module is included in the Laravel Starter Kit by default. If you're using the starter kit, no additional installation steps are required.

## Usage

### User Registration of Passkeys

1. Navigate to the passkey settings page at `/settings/passkeys`
2. Enter a name for your passkey
3. Follow the browser prompts to register your passkey (typically involves biometric authentication or security key)
4. Your passkey will be saved and can be used for future logins

### Authentication with Passkeys

1. On the login page, click the "Sign in with passkey" option
2. Follow the browser prompts to authenticate with your passkey
3. You'll be logged in without needing to enter a password

### Managing Passkeys

1. Navigate to the passkey settings page at `/settings/passkeys`
2. View all your registered passkeys
3. Delete passkeys you no longer need

## Implementation Details

This module is built on top of the [Spatie Laravel Passkeys](https://github.com/spatie/laravel-passkeys) package and provides:

- A controller for managing passkeys (`PasskeysController`)
- Routes for passkey management and authentication
- Vue.js components for the frontend interface
- Integration with the Laravel authentication system

## Security Considerations

Passkeys provide several security benefits:

- Phishing-resistant authentication
- No shared secrets stored on servers
- Biometric or hardware-based authentication
- Protection against credential stuffing attacks

## Troubleshooting

Common issues:

- Passkeys require a secure context (HTTPS or localhost)
- Not all browsers support WebAuthn (check [browser compatibility](https://caniuse.com/webauthn))
- Hardware security keys may require drivers or additional setup

## License

This module is part of the Laravel Starter Kit and is licensed under the same terms.
