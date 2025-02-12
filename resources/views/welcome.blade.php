@extends('components.layouts.app')
@section('content')

   <!-- Livewire Slider Component -->

        <livewire:slider />
        <section class="my-2">
        <div class="container mx-auto px-4">

            <!-- Livewire Featured Products Component -->
            <livewire:featured-products />



        </div>
    </section>
    @endsection


@section('shop')
    <livewire:shop />
@endsection








