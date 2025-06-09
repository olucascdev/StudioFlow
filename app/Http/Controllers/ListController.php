<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreListRequest;
use App\Models\Lists;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ListController extends Controller
{
    public function index()
    {
        return Inertia::render('Lists/Index', [
            'lists' => auth()->user()->lists()->latest()->get(),
            'tasks' => auth()->user()->tasks()->latest()->get(),
        ]);
    }

    public function store(StoreListRequest $request): RedirectResponse
    {

        $validatedData = $request->validated();
        $request->user()->lists()->create($validatedData);

        return redirect()->route('lists.index')->with('success', 'List created successfully.');
    }

    public function update(Request $request, Lists $lists)
    {

        $lists->update($request->only(['title']));
        return redirect()->route('lists.index')->with('success', 'List updated successfully.');
    }

    public function destroy(Lists $lists)
    {
        $lists->delete();
        return redirect()->route('lists.index')->with('success', 'List deleted successfully.');
    }


}
