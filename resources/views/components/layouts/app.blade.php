<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Modern E-Commerce') }}</title>
    @vite('resources/css/app.css')
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css" />
    <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
    <!-- Heroicons CDN -->
    <script src="{{ mix('js/app.js') }}"></script>
  <!-- Include Livewire styles -->
  @livewireStyles


</head>
<body class="font-sans antialiased bg-gray-50 text-gray-900 dark:bg-gray-900 dark:text-gray-100">
    <livewire:header />
    <livewire:toast-notification />

    @yield('content')

    @if(request()->routeIs('shop'))

    <livewire:shop />

@endif
@if(request()->routeIs('cart'))

<livewire:cart />
@endif


@if(request()->routeIs('product.show'))
    <livewire:product :id="request()->route('id')"/>
@endif



    <livewire:footer />


    @livewireScripts

</body>

    <script>
document.addEventListener('DOMContentLoaded', function() {
    new Swiper('.swiper-container', {
        loop: true,
        autoplay: {
            delay: 3000,
            disableOnInteraction: false,
        },
        pagination: {
            el: '.swiper-pagination',
            clickable: true,
        },
        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
        },
    });

    const menuClicked = () => {
        // Mobile menu toggle
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const mobileMenu = document.getElementById('mobile-menu');

        let isClicked = false;

        mobileMenuButton.addEventListener('click', () => {
            console.log('clickeegit')
            isClicked = !isClicked;  // Toggle the value of isClicked
            if (isClicked) {
                mobileMenu.classList.remove('hidden');  // Remove 'hidden' if clicked
            } else {
                mobileMenu.classList.add('hidden');  // Add 'hidden' if not clicked
            }
        });
    }

    // Fix issue: Close menu when Livewire navigates
    document.addEventListener("livewire:navigated", () => {
        // mobileMenu.classList.add('hidden');
        menuClicked();
    });
});







</script>
</html>
