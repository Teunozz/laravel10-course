@extends('layouts.app')

@section('content')
  <h1 class="mb-10 text-2xl">Add review {{ $book->title }}</h1>

  <form method="POST" action="{{ route('books.reviews.store', $book) }}">
    @csrf

    <label for="review">Review</label>
    <textarea id="review" name="review" required class="input mb-4">{{ old('review') }}</textarea>

    <label for="rating">Rating</label>
    <select id="rating" name="rating" required class="input mb-4">
      <option value="">Select a rating</option>
      @for ($i = 1; $i <= 5; $i++)
        <option value="{{ $i }}" @selected(old('rating') == $i)>{{ $i }}</option>
      @endfor
    </select>
    
    <button type="submit" class="btn">Add Review</button>
  </form>
@endsection