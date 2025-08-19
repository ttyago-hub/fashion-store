<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Reservation;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ReservationController extends Controller
{
    /**
     * Display a listing of reservations.
     * Users only see their own reservations, admins see all.
     */
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
                ->orderBy('created_at', 'desc');

            return response()->json($query->get());
            
        } catch (\Exception $e) {
            Log::error('Error al listar reservas: ' . $e->getMessage());
            return response()->json(['error' => 'Error al obtener reservas'], 500);
        }
    }

    /**
     * Store a newly created reservation in storage.
     */
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'product_id' => 'required|exists:products,id',
                'quantity' => 'required|integer|min:1',
                'delivery_type' => 'required|in:Retiro en tienda,Entrega a domicilio',
                'delivery_address' => 'required_if:delivery_type,Entrega a domicilio|string|max:255'
            ]);
            
            $product = Product::findOrFail($validatedData['product_id']);

            if ($product->stock < $validatedData['quantity']) {
                return response()->json(['error' => 'Stock insuficiente'], 400);
            }

            $reservation = Reservation::create([
                'user_id' => Auth::id(),
                'product_id' => $validatedData['product_id'],
                'quantity' => $validatedData['quantity'],
                'delivery_type' => $validatedData['delivery_type'],
                'delivery_address' => $validatedData['delivery_type'] === 'Entrega a domicilio' ? $validatedData['delivery_address'] : null,
                'delivery_code' => $validatedData['delivery_type'] === 'Retiro en tienda' ? strtoupper(Str::random(8)) : null,
                'status' => 'pending'
            ]);

            // Descontar stock
            $product->decrement('stock', $validatedData['quantity']);

            return response()->json([
                'message' => 'Reserva creada exitosamente',
                'reservation' => $reservation->load('product')
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error al crear reserva: ' . $e->getMessage());
            return response()->json([
                'message' => 'Error interno del servidor'
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
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

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $reservation = Reservation::findOrFail($id);
            $user = $request->user();

            if ($user->role !== 'admin' && $reservation->user_id !== $user->id) {
                return response()->json(['error' => 'No autorizado'], 403);
            }

            $validatedData = $request->validate([
                'quantity' => 'sometimes|integer|min:1',
                'delivery_type' => 'sometimes|in:Retiro en tienda,Entrega a domicilio',
                'delivery_address' => 'sometimes|string|max:255',
                'status' => 'sometimes|in:pending,confirmed,completed,cancelled'
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

            $reservation->update($validatedData);

            return response()->json([
                'message' => 'Reserva actualizada exitosamente',
                'reservation' => $reservation->load('product')
            ]);

        } catch (\Exception $e) {
            Log::error('Error al actualizar reserva: ' . $e->getMessage());
            return response()->json(['error' => 'Error al actualizar la reserva'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $reservation = Reservation::findOrFail($id);
            
            // Restaurar stock si la reserva no está completada
            if ($reservation->status !== 'completed') {
                $reservation->product->increment('stock', $reservation->quantity);
            }
            
            $reservation->delete();

            return response()->json([
                'message' => 'Reserva eliminada exitosamente'
            ]);
        } catch (\Exception $e) {
            Log::error('Error al eliminar reserva: ' . $e->getMessage());
            return response()->json(['error' => 'Error al eliminar la reserva'], 500);
        }
    }

    /**
     * Get reservations for the authenticated user.
     */
    public function userReservations()
    {
        try {
            $reservations = Reservation::where('user_id', Auth::id())
                ->with('product')
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json($reservations);
        } catch (\Exception $e) {
            Log::error('Error al obtener reservas del usuario: ' . $e->getMessage());
            return response()->json(['error' => 'Error al obtener las reservas'], 500);
        }
    }

    /**
     * Update reservation status (Admin only).
     */
    public function updateStatus(Request $request, $id)
    {
        try {
            Log::info('Actualizando estado de reserva', [
                'reservation_id' => $id,
                'request_data' => $request->all(),
                'user_id' => Auth::id()
            ]);

            $reservation = Reservation::findOrFail($id);
            
            $validatedData = $request->validate([
                'status' => 'required|in:pending,confirmed,completed,cancelled'
            ]);

            $oldStatus = $reservation->status;
            $reservation->update(['status' => $validatedData['status']]);

            Log::info('Estado actualizado', [
                'old_status' => $oldStatus,
                'new_status' => $validatedData['status'],
                'reservation_id' => $reservation->id
            ]);

            return response()->json([
                'message' => 'Estado de reserva actualizado exitosamente',
                'reservation' => $reservation->load('product', 'user')
            ]);
        } catch (\Exception $e) {
            Log::error('Error al actualizar estado de reserva', [
                'reservation_id' => $id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'message' => 'Error al actualizar el estado'
            ], 500);
        }
    }
}
