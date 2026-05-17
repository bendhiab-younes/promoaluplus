# Promo Alu Plus - Laravel Application

A comprehensive Laravel web application for **Promo Alu Plus**, a premium aluminum joinery company in Tunisia specializing in high-quality aluminum products and services for residential and commercial clients.

## ✨ Key Features

### 🌐 Public Website
- **Multi-language Support**: French, English, and Arabic with full RTL support
- **Modern Responsive Design**: Beautiful UI with Tailwind CSS, smooth animations, and mobile-first approach
- **Service Showcase**: Dynamic services page with detailed descriptions
- **Portfolio Gallery**: Filterable project gallery by category (windows, doors, facades, etc.)
- **Quote Request System**: Comprehensive quote form with email notifications
- **AI Chatbot**: Interactive chatbot with predefined conversation flows and FAQ integration
- **WhatsApp Integration**: Quick contact via WhatsApp floating button

### 🛠️ Admin Panel (Filament)
- **Dashboard**: Overview of business metrics
- **Quote Management**: Full workflow for handling quote requests
  - Status tracking (New → Contacted → Quoted → Accepted/Rejected → Completed)
  - Line items management with automatic total calculations
  - PDF generation for professional quotes
  - One-click invoice creation from accepted quotes
- **Invoice Management**: Complete invoicing system
  - Auto-generated invoice numbers (FAC-YYYY-XXXX)
  - Status tracking (Draft → Sent → Paid/Overdue)
  - PDF generation for professional invoices
  - Link to original quote
- **Content Management**:
  - Projects with multilingual titles/descriptions
  - Services with icons, colors, and features
  - Testimonials from clients
  - FAQ management for chatbot
  - Chatbot conversation flows
- **Site Settings**: Customizable company info, hero content, statistics

## 🛠️ Tech Stack

| Component | Technology |
|-----------|------------|
| Framework | Laravel 12.x |
| Admin Panel | Filament 3.x |
| CSS | Tailwind CSS |
| Icons | Lucide Icons |
| PDF Generation | DomPDF |
| Database | SQLite |
| Frontend | Blade + Alpine.js |

## 🚀 Quick Start

```bash
# Install dependencies
composer install

# Run migrations and seed data
php artisan migrate --seed

# Start development server
php artisan serve
```

**Application**: http://127.0.0.1:8000  
**Admin Panel**: http://127.0.0.1:8000/admin

### Admin Credentials
- **Email**: `admin@aluminiumcraft.tn`
- **Password**: `password`

## 📊 Database Schema

### Core Tables

| Table | Purpose |
|-------|---------|
| `quotes` | Customer quote requests with workflow status |
| `quote_items` | Line items for quotes |
| `invoices` | Generated invoices |
| `invoice_items` | Line items for invoices |
| `projects` | Portfolio projects (multilingual) |
| `services` | Service offerings (multilingual) |
| `testimonials` | Client testimonials (multilingual) |
| `faqs` | Frequently asked questions |
| `chatbot_flows` | Chatbot conversation trees |
| `site_settings` | Dynamic site configuration |

## 📁 Project Structure

```
app/
├── Filament/
│   ├── Resources/          # Admin CRUD resources
│   │   ├── QuoteResource   # Quote management with workflow
│   │   ├── InvoiceResource # Invoice management
│   │   ├── ProjectResource # Portfolio projects
│   │   ├── ServiceResource # Services
│   │   └── ...
│   └── Pages/
│       └── SiteSettings    # Site configuration page
├── Http/Controllers/
│   ├── PageController      # Public pages
│   ├── QuoteController     # Quote form submission
│   ├── PdfController       # PDF generation
│   └── ChatbotController   # Chatbot API
├── Models/                 # Eloquent models
└── Mail/                   # Email notifications

resources/views/
├── layouts/app.blade.php   # Main layout with nav, footer, chatbot
├── pages/                  # home, services, portfolio, about, contact
├── components/chatbot.blade.php  # Chatbot widget
├── pdf/                    # PDF templates (quote, invoice)
└── emails/                 # Email templates

lang/{fr,en,ar}/            # Translation files
```

## 🌐 Routes

### Public Routes
| URL | Description |
|-----|-------------|
| `/` | Homepage with hero, services, projects, testimonials |
| `/services` | All services with details |
| `/portfolio` | Project gallery with category filter |
| `/about` | Company information |
| `/contact` | Contact info & quote request form |
| `/locale/{locale}` | Language switcher (fr/en/ar) |

### API Routes
| URL | Method | Description |
|-----|--------|-------------|
| `/quote` | POST | Submit quote request |
| `/chatbot/welcome` | GET | Get chatbot welcome message |
| `/chatbot/message` | POST | Send message to chatbot |

### Admin Routes
| URL | Description |
|-----|-------------|
| `/admin` | Dashboard |
| `/admin/quotes` | Quote management |
| `/admin/invoices` | Invoice management |
| `/admin/projects` | Project management |
| `/admin/services` | Service management |
| `/admin/site-settings` | Site configuration |

## 📋 Quote/Invoice Workflow

```
┌─────────────┐     ┌───────────┐     ┌─────────────┐     ┌──────────┐
│  New Quote  │ ──► │ Contacted │ ──► │ Quote Sent  │ ──► │ Accepted │
│   Request   │     │           │     │ (with PDF)  │     │          │
└─────────────┘     └───────────┘     └─────────────┘     └────┬─────┘
                                              │                 │
                                              ▼                 ▼
                                        ┌──────────┐    ┌─────────────┐
                                        │ Rejected │    │ Create      │
                                        │          │    │ Invoice     │
                                        └──────────┘    └─────────────┘
```

## 🔧 Configuration

### Email Setup
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_FROM_ADDRESS=promoaluplus@gmail.com
```

### WhatsApp
Update the WhatsApp number in `resources/views/layouts/app.blade.php`:
```javascript
function openWhatsApp() {
    window.open('https://wa.me/21626192898', '_blank');
}
```

## 🔧 Useful Commands

```bash
# Reset database with fresh data
php artisan migrate:fresh --seed

# Clear all caches
php artisan optimize:clear

# Generate new admin user
php artisan tinker
>>> User::create(['name' => 'Admin', 'email' => 'admin@example.com', 'password' => Hash::make('password')]);

# Run chatbot seeder
php artisan db:seed --class=ChatbotFlowSeeder
```

## 📄 License

This project is proprietary software developed for Promo Alu Plus.