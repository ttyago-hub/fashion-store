<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class InventoryController extends Controller
{
    // Listar todo el inventario (admin)
    public function index()
    {
        $products = Product::all();

        return response()->json([
            'message' => 'Inventario obtenido correctamente',
            'inventory' => $products
        ]);
    }
}
