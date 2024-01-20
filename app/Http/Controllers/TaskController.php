<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index()
    {
        $tasks = auth()->user()->tasks;
        return response()->json(['tasks' => $tasks]);
    }

    public function store(Request $request)
    {
        // Validação dos dados da requisição
        $validatedData = $request->validate([
            'title' => 'required|max:255',
            'description' => 'required',
        ]);

        // Cria uma nova instância de Task com os dados validados
        $task = new Task([
            'title' => $validatedData['title'],
            'description' => $validatedData['description'],
            'user_id' => auth()->user()->id, // Associando a tarefa ao usuário autenticado
        ]);

        // Salva a tarefa no banco de dados
        $task->save();

        // Retorna a resposta de sucesso
        return response()->json(['task' => $task], 201);
    }

    public function update(Request $request, Task $task)
    {
        // Validação dos dados da requisição
        $validatedData = $request->validate([
            'title' => 'sometimes|max:255',
            'description' => 'sometimes',
        ]);
    
        // Atualiza os campos da tarefa com os dados validados
        $task->update($validatedData);
    
        // Retorna a resposta de sucesso
        return response()->json(['task' => $task], 200);
    }
    
    public function destroy(Task $task)
    {
        // Excluir a tarefa somente se pertencer ao usuário autenticado
        if (!$task->canDelete(auth()->user())) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $task->delete();
        return response()->json(['message' => 'Task deleted']);
    }
}
