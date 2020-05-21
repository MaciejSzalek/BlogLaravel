@extends('layouts.app')
@section('title')
    Add New Post
@endsection
@section('content')
    @if (count($errors) > 0)
        <div class="alert alert-danger">
            {{$errors->first()}}
        </div>
    @endif
    <form action="{{url('/new-post')}}" method="post">
        <input type="hidden" name="_token" value="{{csrf_token()}}">
        <div class="form-group">
            <input required="required" value="{{old('title')}}" placeholder="Enter title" type="text" name="title" class="form-control"/>
            <textarea name="body" class="form-control">{{ old('body') }}</textarea>
        </div>
        <input type="submit" name="publish" class="btn btn-success" value="Publish"/>
        <input type="submit" name="save" class="btn-default" value="Save Draft"/>
    </form>
@endsection

