// Versión limpia del método store (usar después de que funcione):

public function store(Request $request)
{
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
