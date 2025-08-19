<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Reservation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class ReservationController extends Controller
{
    /**
     * Store a newly created reservation in storage.
     */
    public function store(Request $request)
    {
        try {
            // Validar los datos de entrada
            $validatedData = $request->validate([
                'product_id' => 'required|exists:products,id',
                'quantity' => 'required|integer|min:1',
                'delivery_type' => 'required|in:Retiro en tienda,Entrega a domicilio',
                'delivery_address' => 'nullable|string|max:255',
            ]);

            // Verificar que si es entrega a domicilio, se proporcione la dirección
            if ($validatedData['delivery_type'] === 'Entrega a domicilio' && empty($validatedData['delivery_address'])) {
                return response()->json([
                    'message' => 'La dirección es requerida para entrega a domicilio',
                    'errors' => ['delivery_address' => ['La dirección es requerida para entrega a domicilio']]
                ], 422);
            }

            // Crear la reserva
            $reservation = Reservation::create([
                'user_id' => Auth::id(),
                'product_id' => $validatedData['product_id'],
                'quantity' => $validatedData['quantity'],
                'delivery_type' => $validatedData['delivery_type'],
                'delivery_address' => $validatedData['delivery_address'],
                'status' => 'pending', // Estado por defecto
            ]);

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
            \Log::error('Error al crear reserva: ' . $e->getMessage());
            return response()->json([
                'message' => 'Error interno del servidor',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get reservations for the authenticated user.
     */
    public function userReservations(Request $request)
    {
        try {
            $reservations = Reservation::where('user_id', Auth::id())
                ->with('product')
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json($reservations);
        } catch (\Exception $e) {
            \Log::error('Error al obtener reservas: ' . $e->getMessage());
            return response()->json([
                'message' => 'Error al obtener las reservas',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $reservation = Reservation::with('product', 'user')->findOrFail($id);
            return response()->json($reservation);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Reserva no encontrada'
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $reservation = Reservation::findOrFail($id);
            
            $validatedData = $request->validate([
                'quantity' => 'sometimes|integer|min:1',
                'delivery_type' => 'sometimes|in:Retiro en tienda,Entrega a domicilio',
                'delivery_address' => 'nullable|string|max:255',
                'status' => 'sometimes|in:pending,confirmed,completed,cancelled'
            ]);

            $reservation->update($validatedData);

            return response()->json([
                'message' => 'Reserva actualizada exitosamente',
                'reservation' => $reservation->load('product')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al actualizar la reserva'
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $reservation = Reservation::findOrFail($id);
            $reservation->delete();

            return response()->json([
                'message' => 'Reserva eliminada exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al eliminar la reserva'
            ], 500);
        }
    }

    /**
     * Update reservation status (Admin only).
     */
    public function updateStatus(Request $request, string $id)
    {
        try {
            \Log::info('🔄 Actualizando estado de reserva', [
                'reservation_id' => $id,
                'request_data' => $request->all(),
                'user_id' => Auth::id()
            ]);

            $reservation = Reservation::findOrFail($id);
            
            \Log::info('✅ Reserva encontrada', [
                'reservation' => $reservation->toArray()
            ]);
            
            $validatedData = $request->validate([
                'status' => 'required|in:pending,confirmed,completed,cancelled,apartado'
            ]);

            \Log::info('✅ Datos validados', [
                'validated_data' => $validatedData
            ]);

            $oldStatus = $reservation->status;
            $reservation->update(['status' => $validatedData['status']]);

            \Log::info('✅ Estado actualizado', [
                'old_status' => $oldStatus,
                'new_status' => $validatedData['status'],
                'reservation_id' => $reservation->id
            ]);

            return response()->json([
                'message' => 'Estado de reserva actualizado exitosamente',
                'reservation' => $reservation->load('product', 'user')
            ]);
        } catch (\Exception $e) {
            \Log::error('❌ Error al actualizar estado de reserva', [
                'reservation_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'message' => 'Error al actualizar el estado: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all reservations (Admin only).
     */
    public function index()
    {
        try {
            $reservations = Reservation::with('product', 'user')
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json($reservations);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener las reservas'
            ], 500);
        }
    }
}
