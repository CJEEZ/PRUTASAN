<?php
namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    public function index()
    {
        $products = Auth::user()->products()->paginate(12);
        return view('seller.products.index', compact('products'));
    }

    public function create()
    {
        return view('seller.products.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'=>'required|string',
            'price'=>'required|numeric',
            'stock'=>'required|integer',
            'thumbnail'=>'nullable|image|max:2048'
        ]);

        $data = $request->only(['title','description','price','stock','discount_percent','category_id']);
        $data['seller_id'] = Auth::id();
        $data['slug'] = Str::slug($request->title).'-'.uniqid();

        if ($request->hasFile('thumbnail')){
            $path = $request->file('thumbnail')->store('uploads/products','public');
            $data['thumbnail'] = $path;
        }

        Product::create($data);

        return redirect()->route('seller.products.index')->with('success','Product added');
    }

    // edit, update, destroy methods similar...
}
