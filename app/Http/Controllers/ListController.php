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
            'lists' => auth()
                ->user()
                ->lists()
                ->first()
                ->withCount('tasks')
                ->get(),

        ]);
    }

    public function store(StoreListRequest $request): RedirectResponse
    {

        $validatedData = $request->validated();
        $request->user()->lists()->create($validatedData);

        return redirect()->route('lists.index')->with('success', 'List created successfully.');
    }

    public function show(Lists $lists)
    {
        // Carregar tasks da lista ordenadas por 'order' e depois por data de criação
        $lists->load([
            'tasks' => function ($query) {
                $query->orderBy('order', 'asc')
                    ->orderBy('created_at', 'asc');
            }
        ]);

        // Separar tasks por status para melhor organização no frontend
        $tasks = $lists->tasks->groupBy('status');
        $pendingTasks = $tasks->get('pending', collect());
        $completedTasks = $tasks->get('completed', collect());

        return Inertia::render('Lists/Show', [
            'list' => $lists,
            'pendingTasks' => $pendingTasks,
            'completedTasks' => $completedTasks,
            'stats' => [
                'total' => $lists->tasks->count(),
                'completed' => $completedTasks->count(),
                'pending' => $pendingTasks->count(),
                'completion_percentage' => $lists->tasks->count() > 0
                    ? round(($completedTasks->count() / $lists->tasks->count()) * 100, 1)
                    : 0
            ]
        ]);
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
