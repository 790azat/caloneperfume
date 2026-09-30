<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\Setting;
use App\Models\Testimonial;
use App\Models\Work;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SiteController extends Controller
{
    public function home(): View
    {
        $featured = Work::published()
            ->with('media', 'category')
            ->orderByDesc('is_featured')
            ->latest('event_date')
            ->take(8)
            ->get()
            ->filter(fn (Work $work) => $work->cover()?->type === 'image')
            ->values();

        $byCalone = Work::published()
            ->with('media', 'category')
            ->whereHas('category', fn ($q) => $q->where('slug', 'by-calone'))
            ->latest('event_date')
            ->take(4)
            ->get();

        return view('pages.home', [
            'heroImages' => $featured->map(fn (Work $work) => $work->cover()->src())->values(),
            'featured' => $featured,
            'byCalone' => $byCalone,
            'services' => Service::where('is_active', true)->orderBy('position')->get(),
            'testimonials' => Testimonial::where('is_active', true)->latest()->get(),
            'stats' => array_filter([
                'events' => (int) Setting::get('stat_events'),
                'guests' => (int) Setting::get('stat_guests'),
                'years' => (int) Setting::get('stat_years'),
            ]),
        ]);
    }

    public function works(): View
    {
        return view('pages.works');
    }

    public function work(Work $work): View
    {
        abort_unless($work->is_published || auth()->user()?->is_admin, 404);

        $work->increment('views');
        $work->load('media', 'category');

        return view('pages.work', [
            'work' => $work,
            'related' => Work::published()->with('media', 'category')
                ->whereKeyNot($work->id)
                ->when($work->category_id, fn ($q) => $q->orderByRaw('category_id = ? desc', [$work->category_id]))
                ->latest('event_date')
                ->take(4)
                ->get(),
        ]);
    }

    public function videos(): View
    {
        return view('pages.videos');
    }

    public function contact(): View
    {
        return view('pages.contact');
    }

    public function locale(Request $request, string $locale): RedirectResponse
    {
        abort_unless(array_key_exists($locale, config('app.locales')), 404);

        $request->session()->put('locale', $locale);

        return redirect()->to(url()->previous(route('home')))
            ->withCookie(cookie()->forever('locale', $locale));
    }
}
