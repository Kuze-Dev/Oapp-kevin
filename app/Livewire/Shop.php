<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Product;
use App\Models\Brand;
use App\Models\Category;

class Shop extends Component
{
    public $categoryId;
    public $brandId;
    public $search;
    public $cart = [];

    public function mount()
    {
        $this->cart = session()->get('cart', []);
    }



    public function render()
    {
        $categories = Category::all();
        $brands = Brand::all();

        $products = Product::query()
            ->with(['category', 'brand'])
            ->when(!empty($this->categoryId), fn($query) => $query->where('category_id', $this->categoryId))
            ->when(!empty($this->brandId), fn($query) => $query->where('brand_id', $this->brandId))
            ->when(!empty($this->search), fn($query) => $query->where('name', 'like', '%' . $this->search . '%'))
            ->get();

        return view('livewire.shop', compact('products', 'categories', 'brands'));
    }
}

