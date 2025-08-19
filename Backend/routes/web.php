<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
/*
* RUTA CORREGIDA PARA TU web.php
* Reemplaza la ruta anterior con esta versión corregida
*/

// RUTA FUNCIONAL CORREGIDA (sin el /api/ extra)
Route::put('/admin/reservations/{id}/status', function($id) {
    try {
        \Log::info("🔧 FUNCTIONAL ROUTE - Reservation ID: $id");
        
        // Buscar la reserva
        $reservation = \DB::table('reservations')->where('id', $id)->first();
        
        if (!$reservation) {
            \Log::error("❌ FUNCTIONAL ROUTE - Reservation not found: $id");
            return response()->json(['error' => 'Reservation not found'], 404);
        }
        
        // Obtener el nuevo status
        $newStatus = request('status');
        $oldStatus = $reservation->status;
        
        \Log::info("📝 FUNCTIONAL ROUTE - Updating from '$oldStatus' to '$newStatus'");
        
        // Validar que el status sea válido
        $validStatuses = ['apartado', 'pending', 'confirmed', 'completed', 'cancelled'];
        if (!in_array($newStatus, $validStatuses)) {
            \Log::error("❌ FUNCTIONAL ROUTE - Invalid status: $newStatus");
            return response()->json(['error' => 'Invalid status'], 422);
        }
        
        // Actualizar el status
        $updated = \DB::table('reservations')
            ->where('id', $id)
            ->update(['status' => $newStatus]);
        
        if ($updated) {
            \Log::info("✅ FUNCTIONAL ROUTE - Status updated successfully");
            return response()->json([
                'success' => true,
                'message' => "Status updated from '$oldStatus' to '$newStatus'",
                'reservation_id' => $id,
                'old_status' => $oldStatus,
                'new_status' => $newStatus
            ]);
        } else {
            \Log::error("❌ FUNCTIONAL ROUTE - Update failed");
            return response()->json(['error' => 'Failed to update status'], 500);
        }
        
    } catch (\Exception $e) {
        \Log::error("❌ FUNCTIONAL ROUTE - Exception: " . $e->getMessage());
        return response()->json([
            'error' => 'Server error: ' . $e->getMessage(),
            'line' => $e->getLine(),
            'file' => $e->getFile()
        ], 500);
    }
});
