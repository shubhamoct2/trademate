@extends('frontend::pages.index')
@section('title')
    {{ __('Thank You for Contacting Us') }}
@endsection
@section('page-content')
    <section class="section-style-2">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xl-8">
                    <div class="section-title centered">
                    </div>
                </div>
            </div>
            <div class="row justify-content-center">
                <div class="col-xl-8 col-12">
                    <div class="site-form text-center">
                        <h4 class="mb-5">{{ html_entity_decode(trans('translation.reply_for_customer_contact')) }}</h4>
                        <a href="{{ route('home') }}">
                            <button type="button" class="site-btn primary-btn">{{ __('Home') }}</button>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
