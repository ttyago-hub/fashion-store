<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Reservation;
use App\Models\Product;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class ReservationController extends Controller
{
    // Listar reservas (usuario solo ve las suyas, admin ve todas)
    public function index(Request $request)
    {
        try {
            $user = $request->user();

            $query = Reservation::with(['product', 'user'])
                ->when($user->role !== 'admin', function($query) use ($user) {
                    return $query->where('user_id', $user->id);
                })
                ->when($request->status, function($query, $status) {
                    return $query->where('status', $status);
                })
                ->latest();

            return response()->json($query->get());
            
        } catch (\Exception $e) {
            Log::error('Error al listar reservas: ' . $e->getMessage());
            return response()->json(['error' => 'Error al obtener reservas'], 500);
        }
    }

    // Ver detalle de una reserva
    public function show(Request $request, $id)
    {
        try {
            $reservation = Reservation::with(['product', 'user'])->findOrFail($id);
            $user = $request->user();

            if ($user->role !== 'admin' && $reservation->user_id !== $user->id) {
                return response()->json(['error' => 'No autorizado'], 403);
            }

            return response()->json($reservation);
            
        } catch (\Exception $e) {
            Log::error('Error al mostrar reserva: ' . $e->getMessage());
            return response()->json(['error' => 'Reserva no encontrada'], 404);
        }
    }

// Crear una reserva
public function store(Request $request)
{
    // 👇 Agrega esto para ver si el usuario está autenticado
    dd($request->user());

    try {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'delivery_type' => 'required|in:Retiro en tienda,Entrega a domicilio',
            'delivery_address' => 'required_if:delivery_type,Entrega a domicilio|string|max:255'
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
            'delivery_address' => $request->delivery_type === 'Entrega a domicilio' ? $request->delivery_address : null,
            'delivery_code' => $request->delivery_type === 'Retiro en tienda' ? strtoupper(Str::random(8)) : null,
            'status' => 'pendiente'
        ]);

        // Descontar stock
        $product->decrement('stock', $request->quantity);

        return response()->json([
            'message' => 'Producto apartado exitosamente',
            'reservation' => $reservation->load('product')
        ], 201);
        
    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json(['error' => $e->errors()], 422);
    } catch (\Exception $e) {
        Log::error('Error al crear reserva: ' . $e->getMessage());
        return response()->json(['error' => 'Error al crear la reserva'], 500);
    }
}


    // Actualizar una reserva (general)
    public function update(Request $request, $id)
    {
        try {
            $reservation = Reservation::findOrFail($id);
            $user = $request->user();

            if ($user->role !== 'admin' && $reservation->user_id !== $user->id) {
                return response()->json(['error' => 'No autorizado'], 403);
            }

            $request->validate([
                'quantity' => 'sometimes|integer|min:1',
                'delivery_type' => 'sometimes|in:Retiro en tienda,Entrega a domicilio',
                'delivery_address' => 'sometimes|string|max:255',
                'status' => 'sometimes|in:pendiente,completado,cancelado'
            ]);

            // Lógica para manejar cambios de cantidad y stock
            if ($request->has('quantity') && $request->quantity != $reservation->quantity) {
                $product = $reservation->product;
                $stockDifference = $reservation->quantity - $request->quantity;
                
                if ($product->stock + $stockDifference < 0) {
                    return response()->json(['error' => 'Stock insuficiente'], 400);
                }
                
                $product->increment('stock', $stockDifference);
            }

            $reservation->update($request->all());

            return response()->json([
                'message' => 'Reserva actualizada exitosamente',
                'reservation' => $reservation->fresh(['product', 'user'])
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error al actualizar reserva: ' . $e->getMessage());
            return response()->json(['error' => 'Error al actualizar la reserva'], 500);
        }
    }

    // Actualizar solo el estado de una reserva
    public function updateStatus(Request $request, $id)
    {
        try {
            $request->validate([
                'status' => 'required|in:pendiente,completado,cancelado'
            ]);

            $reservation = Reservation::findOrFail($id);
            $user = $request->user();

            if ($user->role !== 'admin') {
                return response()->json(['error' => 'No autorizado'], 403);
            }

            // Si se cancela, restaurar stock
            if ($request->status === 'cancelado' && $reservation->status !== 'cancelado') {
                $reservation->product->increment('stock', $reservation->quantity);
            }

            $reservation->update(['status' => $request->status]);

            return response()->json([
                'message' => 'Estado de reserva actualizado',
                'reservation' => $reservation->fresh(['product', 'user'])
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error al actualizar estado de reserva: ' . $e->getMessage());
            return response()->json(['error' => 'Error al actualizar el estado'], 500);
        }
    }

    // Eliminar una reserva
    public function destroy(Request $request, $id)
    {
        try {
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

            return response()->json(['message' => 'Reserva eliminada y stock restaurado']);
            
        } catch (\Exception $e) {
            Log::error('Error al eliminar reserva: ' . $e->getMessage());
            return response()->json(['error' => 'Error al eliminar la reserva'], 500);
        }
    }

    // Listar reservas del usuario actual
    public function userReservations(Request $request)
    {
        try {
            return response()->json(
                $request->user()
                    ->reservations()
                    ->with('product')
                    ->latest()
                    ->get()
            );
        } catch (\Exception $e) {
            Log::error('Error al obtener reservas del usuario: ' . $e->getMessage());
            return response()->json(['error' => 'Error al obtener reservas'], 500);
        }
    }
}