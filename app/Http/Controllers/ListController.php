<?php

namespace App\Http\Controllers;

use App\Models\Lists;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ListController extends Controller
{
    public function index()
    {
        return Inertia::render('Lists/Index', [
            'lists' => auth()->user()->lists()->latest()->get(),
        ]);
    }

    public function store(Request $request){
        $data = $request->validate([
            'title' => 'required|string|max:255',
        ]);

        $request->user()->lists()->create($data);

        return redirect()->route('lists.index')->with('success', 'List created successfully.');
    }

    public function update(Request $request, Lists $lists)
    {
        $this->authorize('update', $lists);
        $lists->update($request->only(['title']));
        return redirect()->route('lists.index')->with('success', 'List updated successfully.');
    }

    public function destroy(Lists $lists)
    {
        $this->authorize('delete', $lists);
        $lists->delete();
        return redirect()->route('lists.index')->with('success', 'List deleted successfully.');
    }


}
