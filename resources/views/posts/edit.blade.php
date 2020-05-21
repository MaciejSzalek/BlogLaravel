@extends('layouts.app')
@section('title')
    Edit post
@endsection
@section('content')
    @if(Session::has('message'))
        <div class="alert alert-success">
            {{Session::get('message')}}
        </div>
    @endif
<form  action="{{ url('/update') }}" method="post">
    <input type="hidden" name="_token" value="{{ csrf_token() }}"/>
    <input type="hidden" name="post_id" value="{{ $post->id }}{{ old('post_id') }}"/>
    <div>
        <input class="form-control" required="required" placeholder="Enter title" type="text" name="title"
               value="@if(!old('title')){{$post->title}}@endif{{ old('title') }}" />
    </div>
    <div class="form-group">
    <textarea name='body' class="form-control">
      @if(!old('body'))
            {!! $post->body !!}
        @endif
        {!! old('body') !!}
    </textarea>
    </div>
    @if($post->active == '1')
        <input type="submit" name='publish' class="btn btn-success" value="Update" />
    @else
        <input type="submit" name='publish' class="btn btn-success" value="Publish" />
    @endif
    <input type="submit" name='save' class="btn btn-default" value="Save As Draft" />
    <a href="{{  url('delete/'.$post->id.'?_token='.csrf_token()) }}" class="btn btn-danger">Delete</a>
</form>
@endsection
