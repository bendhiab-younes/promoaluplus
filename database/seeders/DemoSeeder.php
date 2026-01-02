<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Service;
use App\Models\Testimonial;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin user
        User::create([
            'name' => 'Admin',
            'email' => 'admin@aluminiumcraft.tn',
            'password' => Hash::make('password'),
        ]);

        // Create services
        Service::create([
            'slug' => 'windows',
            'title' => ['fr' => 'Fenêtres Aluminium', 'en' => 'Aluminum Windows', 'ar' => 'نوافذ ألمنيوم'],
            'short_description' => [
                'fr' => 'Fenêtres sur mesure avec double vitrage, isolation thermique et acoustique optimale.',
                'en' => 'Custom windows with double glazing, optimal thermal and acoustic insulation.',
                'ar' => 'نوافذ مخصصة مع زجاج مزدوج وعزل حراري وصوتي مثالي.',
            ],
            'icon' => 'home',
            'color' => 'blue',
            'features' => ['Double vitrage', 'Isolation thermique', 'Sur mesure', 'Garantie 10 ans'],
            'is_active' => true,
            'sort_order' => 1,
        ]);

        Service::create([
            'slug' => 'doors',
            'title' => ['fr' => 'Portes & Baies Vitrées', 'en' => 'Doors & Sliding Doors', 'ar' => 'أبواب ونوافذ منزلقة'],
            'short_description' => [
                'fr' => 'Portes d\'entrée, coulissantes et baies vitrées sécurisées et esthétiques.',
                'en' => 'Secure and aesthetic entrance doors, sliding doors and bay windows.',
                'ar' => 'أبواب مدخل آمنة وأبواب منزلقة ونوافذ كبيرة أنيقة.',
            ],
            'icon' => 'door-open',
            'color' => 'orange',
            'features' => ['Sécurité renforcée', 'Design moderne', 'Étanchéité parfaite', 'Multi-points'],
            'is_active' => true,
            'sort_order' => 2,
        ]);

        Service::create([
            'slug' => 'facades',
            'title' => ['fr' => 'Façades & Vérandas', 'en' => 'Facades & Verandas', 'ar' => 'واجهات وشرفات'],
            'short_description' => [
                'fr' => 'Murs-rideaux, façades ventilées et vérandas pour agrandir votre espace de vie.',
                'en' => 'Curtain walls, ventilated facades and verandas to expand your living space.',
                'ar' => 'جدران ستائرية وواجهات مهواة وشرفات لتوسيع مساحة معيشتك.',
            ],
            'icon' => 'building',
            'color' => 'green',
            'features' => ['Murs-rideaux', 'Vérandas sur mesure', 'Architecture moderne', 'Haute performance'],
            'is_active' => true,
            'sort_order' => 3,
        ]);

        // Create projects
        Project::create([
            'title' => ['fr' => 'Villa Moderne - La Marsa', 'en' => 'Modern Villa - La Marsa', 'ar' => 'فيلا حديثة - المرسى'],
            'description' => ['fr' => 'Installation complète de fenêtres et baies vitrées pour une villa moderne.', 'en' => 'Complete installation of windows and bay windows for a modern villa.', 'ar' => 'تركيب كامل للنوافذ والنوافذ الكبيرة لفيلا حديثة.'],
            'category' => 'windows',
            'location' => 'La Marsa, Tunis',
            'is_featured' => true,
            'is_active' => true,
            'sort_order' => 1,
        ]);

        Project::create([
            'title' => ['fr' => 'Résidence Carthage', 'en' => 'Carthage Residence', 'ar' => 'إقامة قرطاج'],
            'description' => ['fr' => 'Portes d\'entrée et baies coulissantes haut de gamme.', 'en' => 'High-end entrance doors and sliding doors.', 'ar' => 'أبواب مدخل وأبواب منزلقة فاخرة.'],
            'category' => 'doors',
            'location' => 'Carthage, Tunis',
            'is_featured' => true,
            'is_active' => true,
            'sort_order' => 2,
        ]);

        Project::create([
            'title' => ['fr' => 'Immeuble Commercial CUN', 'en' => 'CUN Commercial Building', 'ar' => 'مبنى تجاري'],
            'description' => ['fr' => 'Façade rideau en aluminium pour immeuble de bureaux.', 'en' => 'Aluminum curtain wall for office building.', 'ar' => 'واجهة ستائرية من الألمنيوم لمبنى مكاتب.'],
            'category' => 'facades',
            'location' => 'Centre Urbain Nord',
            'is_featured' => true,
            'is_active' => true,
            'sort_order' => 3,
        ]);

        // Create testimonials
        Testimonial::create([
            'client_name' => 'Mohamed B.',
            'client_location' => 'Paris, France',
            'content' => [
                'fr' => 'Excellent travail! J\'ai géré tout le projet depuis Paris et AluminiumCraft a parfaitement respecté mes attentes. Communication impeccable et qualité au rendez-vous.',
                'en' => 'Excellent work! I managed the entire project from Paris and AluminiumCraft perfectly met my expectations. Impeccable communication and quality delivery.',
                'ar' => 'عمل ممتاز! أدرت المشروع بأكمله من باريس وAluminiumCraft حققت توقعاتي بالكامل. تواصل لا تشوبه شائبة وجودة عالية.',
            ],
            'rating' => 5,
            'project_type' => 'windows',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        Testimonial::create([
            'client_name' => 'Sonia K.',
            'client_location' => 'Montréal, Canada',
            'content' => [
                'fr' => 'Qualité européenne à prix tunisien. Communication impeccable malgré le décalage horaire. Je recommande vivement!',
                'en' => 'European quality at Tunisian prices. Impeccable communication despite the time difference. Highly recommend!',
                'ar' => 'جودة أوروبية بأسعار تونسية. تواصل لا تشوبه شائبة رغم فرق التوقيت. أوصي بشدة!',
            ],
            'rating' => 5,
            'project_type' => 'doors',
            'is_active' => true,
            'sort_order' => 2,
        ]);

        Testimonial::create([
            'client_name' => 'Ahmed T.',
            'client_location' => 'Berlin, Allemagne',
            'content' => [
                'fr' => 'Professionnels et à l\'écoute. Les photos et vidéos envoyées régulièrement m\'ont permis de suivre l\'avancement à distance.',
                'en' => 'Professional and attentive. The photos and videos sent regularly allowed me to follow the progress remotely.',
                'ar' => 'محترفون ومنتبهون. الصور ومقاطع الفيديو المرسلة بانتظام سمحت لي بمتابعة التقدم عن بُعد.',
            ],
            'rating' => 5,
            'project_type' => 'facades',
            'is_active' => true,
            'sort_order' => 3,
        ]);
    }
}
