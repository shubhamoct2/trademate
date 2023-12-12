@extends('frontend::pages.index')
@section('title')
    {{ $data['title'] }}
@endsection
@section('meta_keywords')
    {{ $data['meta_keywords'] }}
@endsection
@section('meta_description')
    {{ $data['meta_description'] }}
@endsection
@section('page-content')
    <section class="section-style">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xl-12">
                    <div class="frontend-editor-data">
                        @if ($locale != "en")
                            {!! html_entity_decode(base64_decode($data['content'])) !!}
                        @else
                            {!! html_entity_decode($data['content']) !!}
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
