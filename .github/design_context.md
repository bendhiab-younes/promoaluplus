## Design Context

### Users
Primary users are Tunisians in Tunisia, Tunisian expats abroad, and European residents managing projects in Tunisia.
Their context is often remote decision-making with high trust requirements, limited onsite presence, and a need for clear project communication.
Primary jobs to be done are:
- Discover available aluminum services quickly.
- Evaluate trust, quality, and professionalism.
- Contact the company and request a quote with low friction.

### Brand Personality
Voice and tone should feel clear, reassuring, professional, and premium without being flashy.
3-word personality anchor:
- Qualite
- Transparence
- Ponctualite

Emotional goals:
- Trust and reassurance first.
- Premium quality perception second.

### Aesthetic Direction
Use light mode as default and only mode for now.
Keep and refine the current visual direction already established in the project.

Color palette (CSS variables in `resources/views/layouts/app.blade.php`):
- Blue (trust) — primary `#1e3a8a`, secondary `#3b82f6`, light `#60a5fa`
- Orange (action/CTA) — primary `#f97316`, dark `#ea580c`
- Gold accent — `#d4af37` (premium cue)
- Hero uses a dark-blue gradient (`#0f172a → #1e3a8a → #3b82f6`)

Typography:
- Display/headlines — `Playfair Display` (serif), via `.font-display`
- Body/UI — `Manrope` (sans-serif), the default family
- Arabic — `Noto Sans Arabic` (Tajawal/Cairo/Amiri fallbacks), via `.font-arabic`

Other:
- Clean, modern layouts with subtle premium cues (gradients, soft shadows, glass effects where useful).
- Multilingual readiness with full Arabic RTL support (`dir` flips on `<html>` for `ar`); keep spacing/alignment RTL-safe.
- Motion should be meaningful and restrained; prioritize clarity and usability over animation density.

No external reference sites are defined yet.
No anti-reference list is defined yet.

### Design Principles
1. Trust First, Always
Every page should quickly communicate reliability, professionalism, and project safety through clear hierarchy, strong social proof, and transparent content.

2. Quote Conversion Without Friction
Reduce effort from discovery to contact: visible CTAs, short paths to quote request, and straightforward form UX.

3. Premium, Practical Visual Language
Maintain a modern premium feel while staying practical and readable for broad audiences, including non-technical users.

4. Consistent Multilingual Experience
Ensure parity across French, Arabic, and English with RTL-aware layout behavior and culturally clear wording.

5. Accessibility Baseline by Default
Apply basic accessibility consistently: readable contrast, clear focus states, keyboard-friendly interactions, meaningful alt text, and sensible motion defaults.