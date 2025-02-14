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
    public $showModal = false;
    public $selectedProduct;

    public function mount()
    {
        $this->cart = session()->get('cart', []);
    }

    public function openModal($productId)
    {
      $this->selectedProduct = Product::find($productId);
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->selectedProduct = null;
    }

    public function addToCart($productId)
    {
        $product = Product::findOrFail($productId);

        $cart = session()->get('cart', []);

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

        session()->put('cart', $cart);
        session()->save();

        $this->cart = session()->get('cart');
        $this->dispatch('cartUpdated');
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
