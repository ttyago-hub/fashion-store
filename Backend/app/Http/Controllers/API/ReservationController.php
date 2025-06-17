<?php

namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Reservation;
use App\Models\Product;
use Illuminate\Support\Str;

class ReservationController extends Controller
{
    // Listar reservas (usuario solo ve las suyas, admin ve todas)
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->role === 'admin') {
            return Reservation::with('user', 'product')->get();
        }

        return $user->reservations()->with('product')->get();
    }

    // Ver detalle de una reserva
    public function show(Request $request, $id)
    {
        $reservation = Reservation::with('product', 'user')->findOrFail($id);
        $user = $request->user();

        if ($user->role !== 'admin' && $reservation->user_id !== $user->id) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        return response()->json($reservation);
    }

    // Crear una reserva
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'delivery_type' => 'required|in:retiro,domicilio',
            'delivery_address' => 'nullable|string'
        ]);
        
        $product = Product::findOrFail($request->product_id);

        if ($product->stock < $request->quantity) {
            return response()->json(['error' => 'Stock insuficiente'], 400);
        }

        $reservation = Reservation::create([
            'user_id' => $request->user()->id,
            'product_id' => $product->id,
            'quantity' => $request->quantity,
            'delivery_type' => $request->delivery_type,
            'delivery_address' => $request->delivery_type === 'domicilio' ? $request->delivery_address : null,
            'delivery_code' => $request->delivery_type === 'retiro' ? strtoupper(Str::random(8)) : null,
            'pickup_code' => null
        ]);

        // Descontar stock
        $product->stock -= $request->quantity;
        $product->save();

        return response()->json([
            'message' => 'Producto apartado exitosamente',
            'reservation' => $reservation
        ], 201);
    }
    public function destroy(Request $request, $id)
{
    $reservation = Reservation::findOrFail($id);
    $user = $request->user();

    // Solo el dueño de la reserva o un admin puede cancelarla
    if ($user->id !== $reservation->user_id && $user->role !== 'admin') {
        return response()->json(['error' => 'No autorizado'], 403);
    }

    // Restaurar stock del producto
    $product = $reservation->product;
    $product->stock += $reservation->quantity;
    $product->save();

    // Eliminar reserva
    $reservation->delete();

    return response()->json(['message' => 'Reserva cancelada y stock restaurado.']);
}

}
