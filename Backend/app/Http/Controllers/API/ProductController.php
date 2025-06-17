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
            'name' => 'required|string',
            'description' => 'nullable|string',
            'category' => 'required|string',
            'stock' => 'required|integer',
            'price' => 'required|numeric',
        ]);

        $product = Product::create($request->all());

        return response()->json($product, 201);
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

