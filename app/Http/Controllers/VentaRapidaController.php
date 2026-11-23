<?php

namespace App\Http\Controllers;

use App\Filament\Support\ClienteFormatting;
use App\Services\VentaRapidaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class VentaRapidaController extends Controller
{
    public function verify(Request $request, VentaRapidaService $ventaRapida): JsonResponse
    {
        $data = $request->validate([
            'pin' => ['required', 'digits:4'],
        ], [
            'pin.required' => 'Escribe el PIN de 4 cifras.',
            'pin.digits' => 'El PIN debe tener 4 cifras.',
        ]);

        if (! $ventaRapida->verifyPin($request, $data['pin'])) {
            return response()->json([
                'message' => 'El PIN no es correcto.',
            ], 422);
        }

        return response()->json([
            'message' => 'PIN correcto.',
        ]);
    }

    public function store(Request $request, VentaRapidaService $ventaRapida): JsonResponse
    {
        if (! $ventaRapida->isVerified($request)) {
            return response()->json([
                'message' => 'Introduce el PIN correcto para continuar.',
            ], 403);
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:30'],
            'email' => ['required', 'email', 'max:255'],
            'required_prog' => ['required', 'string', 'max:255'],
        ], [
            'name.required' => 'Escribe el nombre.',
            'phone.required' => 'Escribe el teléfono.',
            'email.required' => 'Escribe el correo.',
            'email.email' => 'El correo no es válido.',
            'required_prog.required' => 'Escribe el programa solicitado.',
        ], [
            'name' => 'nombre',
            'phone' => 'teléfono',
            'email' => 'correo',
            'required_prog' => 'programa solicitado',
        ]);

        $digits = ClienteFormatting::phoneDigits($data['phone']);

        if (strlen($digits) < 6) {
            throw ValidationException::withMessages([
                'phone' => 'Escribe un teléfono válido.',
            ]);
        }

        $data['phone'] = $digits;

        $cliente = $ventaRapida->register($request, $data);

        return response()->json([
            'message' => 'Datos guardados. El aviso de venta rápida ya se envió.',
            'id' => $cliente->id,
        ]);
    }
}
