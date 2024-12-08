<?php

namespace App\Http\Controllers;

use App\Models\Productos;
use App\Models\Ventas;
use Illuminate\Http\Request;

class VentasController extends Controller
{
    public function index(){
        return view('Ventas');
    }
    //funcion para agregar venta
    public function addVenta(Request $request){
        // Validar los datos de entrada
        $request->validate([
            'cantidad' => 'required|integer|min:1',  // Asegurarse de que la cantidad sea positiva
            'total' => 'required|numeric|min:0',     // Asegurarse de que el total sea un número positivo
            'producto_id' => 'required|exists:productos,id', // Validar que el producto exista en la base de datos
        ]);
    
        // Obtener el producto
        $producto = Productos::find($request->producto_id);
    
        // Verificar si hay suficiente stock
        if ($producto->stock < $request->cantidad) {
            return back()->with('error', 'No hay suficiente stock para realizar la venta.');
        }
    
        // Crear la venta
        Ventas::create([
            'cantidad' => $request->cantidad,
            'total' => $request->total,
            'producto_id' => $request->producto_id,
        ]);
    
        // Reducir el stock del producto
        $producto->stock -= $request->cantidad;
        $producto->save();
    
        // Retornar a la página anterior con un mensaje de éxito
        return back()->with('message', 'Venta realizada con éxito.');
    }
    

    public function editarVenta(Request $request){
        $venta = Ventas::find($request->id);
        $producto = Productos::find($request->producto_id);

        $total = $request->cantidad * $producto->precio;

        $venta->fill($request->all());
        $venta->total = $total;
        $venta->save();

        return back()->with('message', 'Venta actualizada correctamente.');
    }

}
