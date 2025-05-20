<?php

namespace App\Http\Controllers\Intranet;

use App\Http\Controllers\Controller;
use App\Models\{Module, Element};
use App\Models\Page;
use App\Traits\AutenticationTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ModuleController extends Controller
{
    use AutenticationTrait;

    public function index(Request $request)
    {
        $user = $this->getAuthenticate();

        $modules = Module::with(['default_page', 'access_rules'])
            ->get()
            ->filter(fn($module) => $module->isVisibleToUser($user))
            ->values();

        return Inertia::render("Intranet/Modules/index", compact("modules"));
    }

    public function show(Module $module)
{
    $pageId = request()->get('page');

    $page = $pageId
        ? Page::with(['sections.elements.access_rules', 'access_rules', 'children', 'parent','parentRecursive'])
            ->where('module_id', $module->id)
            ->findOrFail($pageId)
        : $module->default_page()
            ->with(['sections.elements.access_rules', 'access_rules', 'children', 'parent','parentRecursive'])
            ->first();

    foreach ($page->sections as $section) {
        foreach ($section->elements as $element) {
            if ($element->type === 'link') {
                $content = $element->content;
                $content['url'] = route('intranets.elements.redirect', $element->id);
                $element->content = $content;
            }
        }
    }

    return Inertia::render('Intranet/Modules/Show', [
        'module' => $module,
        'page' => $page,
        'child_pages' => $page->children,
        'parent_page' => $page->parent,
        'default_page_id' => $module->default_page_id,
    ]);
}

}
