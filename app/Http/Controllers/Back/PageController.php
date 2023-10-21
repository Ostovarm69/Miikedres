<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\Link;
use App\Models\Menu;
use App\Models\Page;
use Cviebrock\EloquentSluggable\Services\SlugService;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Page::class, 'page');
    }

    public function index()
    {
        $pages = Page::detectLang()->latest()->paginate(10);

        return view('back.pages.index', compact('pages'));
    }

    public function create()
    {
        return view('back.pages.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required|string|max:191',
            'content' => 'required',
            'slug' => 'nullable|unique:pages,slug'
        ]);

        Page::create([
            'title'      => $request->title,
            'content'    => $request->content,
            'slug'       => $request->slug ?: $request->title,
            'published'  => $request->published ? true : false,
            'lang'       => app()->getLocale(),
        ]);

        toastr()->success('صفحه با موفقیت ایجاد شد.');

        return response("success", 200);
    }

    public function edit(Page $page)
    {
        return view('back.pages.edit', compact('page'));
    }

    public function update(Request $request, Page $page)
    {
        $this->validate($request, [
            'title' => 'required|string|max:191',
            'content' => 'required',
        ]);

        $slug = $page->slug;

        $page->update([
            'title'     => $request->title,
            'content'   => $request->content,
            'slug'      => $request->slug ?: $request->title,
            'published' => $request->published ? true : false,
        ]);

        Menu::where('link', '/pages/' . $slug)->update([
            'link' => '/pages/' . $page->slug,
        ]);

        Link::where('link', '/pages/' . $slug)->update([
            'link' => '/pages/' . $page->slug,
        ]);

        toastr()->success('صفحه با موفقیت ویرایش شد.');

        return response("success", 200);
    }

    public function destroy(Page $page)
    {
        $page->delete();

        return response("success", 200);
    }
}
