<?php

namespace App\Http\Controllers\Intranet;

use App\Http\Controllers\Controller;
use App\Models\Element;
use App\Traits\AutenticationTrait;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ElementController extends Controller
{
    use AutenticationTrait;

    public function show(Element $element)
    {
        $user = $this->getAuthenticate();

        if (! $element->isVisibleToUser($user)) {
            abort(403);
        }

        return Inertia::render('Intranet/Elements/Show', [
            'element' => [
                'id' => $element->id,
                'name' => $element->content['name'],
                'url' => $element->content['url'],
            ],
        ]);
    }

    public function redirectToView(Element $element)
    {
        if (! $element->isVisibleToUser(Auth::user())) {
            abort(403);
        }

        $url = $element->content['url'] ?? null;

        if (!$url) {
            abort(404, 'No URL found');
        }

        return redirect()->away($url);
    }



    public function downloadVideo(Element $element)
    {
        if (!$element->isDownloadableByUser(Auth::user())) {
            abort(403);
        }

        return redirect()->away($element->content['url']);
    }
}
