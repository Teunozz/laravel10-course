<?php

use App\Http\Requests\TaskRequest;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect()->route('task.index');
});

Route::get('/tasks', function () {
    return view('index', [
        'tasks' =>  Task::latest()->paginate(),
    ]);
})->name('task.index');

Route::view('/tasks/create', 'create')
    ->name('task.create');

Route::get('/tasks/{task}/edit', function (Task $task) {
    return view('edit', ['task' => $task]);
})->name('task.edit');

Route::get('/tasks/{task}', function (Task $task) {
    return view('show', ['task' => $task]);
})->name('task.show');

Route::post('/tasks', function (TaskRequest $request) {
    $task = Task::create($request->validated());
    return redirect()->route('task.show', ['task' => $task])
        ->with('success', 'Task created successfully!');
})->name('task.store');

Route::put('/tasks/{task}', function (TaskRequest $request, Task $task) {
    $task->update($request->validated());
    return redirect()->route('task.show', ['task' => $task])
        ->with('success', 'Task updated successfully!');
})->name('task.update');

Route::delete('/tasks/{task}', function (Task $task) {
    $task->delete();
    return redirect()->route('task.index')
        ->with('success', 'Task deleted successfully!');
})->name('task.destroy');

Route::put('/tasks/{task}/toggle-completed', function (Task $task) {
    $task->toggleCompleted();
    return redirect()->back()
        ->with('success', 'Task updated successfully!');
})->name('task.toggle-completed');
