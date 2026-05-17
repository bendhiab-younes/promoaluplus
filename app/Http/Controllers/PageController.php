<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use App\Models\Project;
use App\Models\Service;
use App\Models\Testimonial;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function home()
    {
        $services = Service::active()->orderBy('sort_order')->get();
        $featuredProjects = Project::active()->featured()->orderBy('sort_order')->take(6)->get();
        $testimonials = Testimonial::active()->orderBy('sort_order')->take(3)->get();

        return view('pages.home', compact('services', 'featuredProjects', 'testimonials'));
    }

    public function services()
    {
        $services = Service::active()->orderBy('sort_order')->get();

        return view('pages.services', compact('services'));
    }

    public function portfolio(Request $request)
    {
        $category = $request->get('category', 'all');

        $query = Project::active()->orderBy('sort_order');

        if ($category !== 'all') {
            $query->byCategory($category);
        }

        $projects = $query->get();
        $testimonials = Testimonial::active()->orderBy('sort_order')->get();

        return view('pages.portfolio', compact('projects', 'testimonials', 'category'));
    }

    public function about()
    {
        return view('pages.about');
    }

    public function contact()
    {
        $translator = app('translator');
        $faqs = Faq::active()->ordered()->get()->map(function (Faq $faq) use ($translator) {
            if ((int) $faq->sort_order === 2) {
                $faq->question = [
                    'fr' => $translator->get('messages.faq_q2', [], 'fr'),
                    'en' => $translator->get('messages.faq_q2', [], 'en'),
                    'ar' => $translator->get('messages.faq_q2', [], 'ar'),
                ];
                $faq->answer = [
                    'fr' => $translator->get('messages.faq_a2', [], 'fr'),
                    'en' => $translator->get('messages.faq_a2', [], 'en'),
                    'ar' => $translator->get('messages.faq_a2', [], 'ar'),
                ];
            }

            return $faq;
        });

        return view('pages.contact', compact('faqs'));
    }

    public function setLocale(string $locale)
    {
        if (in_array($locale, ['fr', 'ar', 'en'])) {
            session(['locale' => $locale]);
            app()->setLocale($locale);
        }

        return back();
    }
}
