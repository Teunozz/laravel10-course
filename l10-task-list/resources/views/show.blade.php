@extends('layouts.app')

@section('title', $task->title)

@section('content')
    <nav class="mb-4">
        <a href="{{ route('task.index') }}" class="link">Back</a>
    </nav>

    <p class="mb-4 text-slate-700">{{ $task->description }}</p>

    @if ($task->long_description)
        <p class="mb-4 text-slate-700">{{ $task->long_description }}</p> 
    @endif

    <p class="mb-4 text-sm text-slate-500">Created {{ $task->created_at->diffForHumans() }} - Updated {{ $task->updated_at->diffForHumans() }}</p>

    <p @class(['mb-4 font-medium', 'text-green-500' => $task->completed, 'text-red-500' => !$task->completed])>{{ $task->completed ? 'Completed' : 'Not completed' }}

    <div class="flex gap-2">
        <a href="{{ route('task.edit', ['task' => $task]) }}" class="btn">Edit</a>

        <form method="POST" action="{{ route('task.toggle-completed', ['task' => $task]) }}">
            @csrf
            @method('PUT')

            <button type="submit" class="btn">Toggle task {{ $task->completed ? 'not completed' : 'completed' }}</button>
        </form>

        <form method="POST" action="{{ route('task.destroy', ['task' => $task]) }}">
            @csrf
            @method('DELETE')

            <button type="submit" class="btn">Delete</button>
        </form>
    </div>
@endsection