<?php

namespace App\Livewire;

use Log;
use session;
use App\Models\Brand;
use App\Models\Product;
use Livewire\Component;
use App\Models\Category;


class Shop extends Component
{
    public $categoryId;
    public $brandId;
    public $search;
    public $cart = [];

    public function mount()
    {
        // Load cart from session if available
        $this->cart = session()->get('cart', []);
    }

    public function addToCart($productId)
    {
        $product = Product::findOrFail($productId);

        // Retrieve the existing cart from session
        $cart = session()->get('cart', []);

        // Add product to cart if not already added
        if (!isset($cart[$productId])) {
            $cart[$productId] = [
                'id' => $product->id,
                'name' => $product->name,
                'description' => $product->description,
                'product_image' => $product->product_image,
                'status' => $product->status,
                'category_id' => $product->category_id,
                'brand_id' => $product->brand_id,
            ];
        }

        // Store the updated cart in the session
        session()->put('cart', $cart);
        session()->save();

        // Reload the cart variable
        $this->cart = session()->get('cart');

        // Emit event to update Cart component
        $this->dispatch('cartUpdated');

        // Debugging
        Log::info('Session After Adding:', ['cart' => session()->get('cart')]);
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
