<?php

namespace App\Http\Controllers;

use App\Models\Slider;
use App\Models\News;
use App\Models\Event;

class HomeController extends Controller
{
    public function index()
    {
        $sliders = Slider::all();
        $news = News::latest()->get();
        $events = Event::latest()->get(); // fetch events

        return view('home', compact('sliders', 'news', 'events'));
    }
}
