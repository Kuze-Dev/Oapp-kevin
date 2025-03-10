<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Product;

class FeaturedProducts extends Component
{
    public function render()
    {
        // Paginate the featured products with 6 per page (you can adjust the number)
        $featuredProducts = Product::where('featured', true)
            ->orderBy('created_at', 'desc')
            ->paginate(6);

        return view('livewire.featured-products', compact('featuredProducts'));
    }
}
