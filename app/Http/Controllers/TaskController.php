<?php

namespace App\Http\Controllers;

use App\Models\Lists;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class TaskController extends Controller
{
    /**
     * Store a newly created task in storage.
     */
    public function store(Request $request, Lists $lists): RedirectResponse
    {

        // Buscar a próxima posição da ordem (para ordenação)
        $nextOrder = $lists->tasks()->max('order') + 1;

        $task = $lists->tasks()->create([
            'title' => $request->title,
            'description' => $request->description,
            'due_date' => $request->due_date,
            'status' => 'pending',
            'order' => $nextOrder,
            'user_id' => auth()->id(),
        ]);

        return redirect()->back()->with('success', 'Task criada com sucesso!');
    }

    /**
     * Update the specified task in storage.
     */
    public function update(Request $request, Lists $lists, Task $task): RedirectResponse
    {


        // Verificar se a task pertence à lista
        if ($task->lists_id !== $lists->id) {
            abort(404, 'Task não encontrada nesta lista');
        }

        $task->update([
            'title' => $request->title,
            'description' => $request->description,
            'due_date' => $request->due_date,
        ]);

        return redirect()->back()->with('success', 'Task atualizada com sucesso!');
    }

    /**
     * Toggle task completion status.
     */
    public function toggle(Request $request, Lists $lists, Task $task): RedirectResponse
    {
        // Verificar se a task pertence à lista
        if ($task->lists_id !== $lists->id) {
            abort(404, 'Task não encontrada nesta lista');
        }

        $newStatus = $task->status === 'pending' ? 'completed' : 'pending';

        $task->update([
            'status' => $newStatus,
        ]);

        $message = $newStatus === 'completed' ? 'Task concluída!' : 'Task reaberta!';

        return redirect()->back()->with('success', $message);
    }

    /**
     * Reorder tasks (para drag and drop)
     */
    public function reorder(Request $request, Lists $lists, Task $task): RedirectResponse
    {
        $request->validate([
            'new_order' => 'required|integer|min:1',
        ]);

        // Verificar se a task pertence à lista
        if ($task->lists_id !== $lists->id) {
            abort(404, 'Task não encontrada nesta lista');
        }

        $task->update([
            'order' => $request->new_order,
        ]);

        return redirect()->back()->with('success', 'Ordem das tasks atualizada!');
    }

    /**
     * Remove the specified task from storage.
     */
    public function destroy(Lists $lists, Task $task): RedirectResponse
    {
        // Verificar se a task pertence à lista
        if ($task->lists_id !== $lists->id) {
            abort(404, 'Task não encontrada nesta lista');
        }

        $task->delete();

        return redirect()->back()->with('success', 'Task excluída com sucesso!');
    }
}
