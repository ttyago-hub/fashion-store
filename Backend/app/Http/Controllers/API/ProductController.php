<?php

namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends Controller
{
    // Módulo 1: Mostrar todos los productos
    public function index()
    {
        return Product::all();
    }

    // Mostrar un solo producto
    public function show($id)
    {
        return Product::findOrFail($id);
    }

    // Módulo 2: Crear producto
public function store(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'category' => 'required|string|max:255',
        'description' => 'nullable|string',
        'price' => 'required|numeric',
        'stock' => 'required|integer',
        'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
    ]);

    try {
        $product = new Product();
        $product->name = $request->name;
        $product->category = $request->category;
        $product->description = $request->description;
        $product->price = $request->price;
        $product->stock = $request->stock;

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->storeAs('public/products', $imageName);
            $product->image = $imageName;
        }

        $product->save();

        return response()->json(['message' => 'Producto guardado con éxito'], 201);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Error al guardar el producto', 'details' => $e->getMessage()], 500);
    }
}

    // Actualizar producto
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $product->update($request->all());

        return response()->json($product);
    }

    // Eliminar producto
    public function destroy($id)
    {
        Product::destroy($id);

        return response()->json(['message' => 'Producto eliminado']);
    }

    // Buscar y filtrar
    public function search(Request $request)
    {
        $query = Product::query();

        if ($request->has('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        return $query->get();
    }
}

