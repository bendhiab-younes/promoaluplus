<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Service;
use App\Models\Testimonial;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function home()
    {
        $services = Service::active()->orderBy('sort_order')->take(3)->get();
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
        return view('pages.contact');
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
