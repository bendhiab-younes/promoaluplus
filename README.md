# AluminiumCraft Tunisie - Laravel Application

A modern Laravel web application for AluminiumCraft, a premium aluminum joinery company in Tunisia specializing in services for expatriates.

## 🚀 Features

- **Multi-language Support**: French, English, and Arabic with RTL support
- **Quote Request System**: Full quote management with email notifications
- **Portfolio Management**: Filterable project gallery with categories
- **Admin Panel**: Filament-powered admin dashboard for content management
- **Responsive Design**: Modern UI with Tailwind CSS

## 🛠️ Tech Stack

- **Framework**: Laravel 12.x
- **Admin Panel**: Filament 3.x
- **CSS**: Tailwind CSS (CDN)
- **Icons**: Lucide Icons
- **Database**: SQLite (configured and seeded)

## 🚀 Quick Start

```bash
# Start development server
php artisan serve
```

**Application**: http://127.0.0.1:8000  
**Admin Panel**: http://127.0.0.1:8000/admin

### Admin Credentials
- Email: `admin@aluminiumcraft.tn`
- Password: `password`

## 📊 Database

**Current Setup**: SQLite database with demo data seeded

### Main Tables

| Table | Purpose | Multilingual Fields |
|-------|---------|---------------------|
| `quotes` | Customer quote requests | - |
| `projects` | Portfolio projects | title, description (fr/en/ar) |
| `services` | Service offerings | title, short_description (fr/en/ar) |
| `testimonials` | Client reviews | content (fr/en/ar) |
| `users` | Admin users | - |

## 📁 Key Files

```
app/
├── Filament/Resources/     # Admin panel (Quote, Project, Service, Testimonial)
├── Http/Controllers/       # PageController, QuoteController
├── Mail/                   # Email templates
├── Models/                 # Eloquent models
└── Http/Middleware/SetLocale.php  # Language switching

resources/views/
├── layouts/app.blade.php   # Main layout
├── pages/                  # home, services, portfolio, about, contact
└── emails/                 # Quote email templates

lang/                       # Translations (fr/en/ar)
database/seeders/DemoSeeder.php  # Sample data
```

## 🌐 Routes

| URL | Description |
|-----|-------------|
| `/` | Homepage |
| `/services` | Services page |
| `/portfolio` | Portfolio (filterable by category) |
| `/about` | About page |
| `/contact` | Contact & quote form |
| `/locale/{locale}` | Language switcher (fr/en/ar) |
| `/admin` | Admin panel |

## 🛠️ Development

### Content Management
Use the admin panel at `/admin` to:
- Manage quote requests (Demandes → Demandes de devis)
- Add/edit projects (Contenu → Projets)
- Manage services and testimonials

### Customization
- **Pages**: Edit `resources/views/pages/*.blade.php`
- **Translations**: Update `lang/{fr,en,ar}/messages.php`
- **Admin Theme**: Modify `app/Providers/Filament/AdminPanelProvider.php`

### Email Setup
Configure SMTP in `.env`:
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
```

## 🔧 Useful Commands

```bash
# Reset database with demo data
php artisan migrate:fresh --seed

# Clear all caches
php artisan optimize:clear

# Create admin user
php artisan tinker
>>> User::create(['name' => 'Admin', 'email' => 'admin@example.com', 'password' => Hash::make('password')]);
```

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
