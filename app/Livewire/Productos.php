<?php

namespace App\Livewire;

use App\Models\Productos as ModelsProductos;
use Livewire\Component;

class Productos extends Component
{
    public $productos;
    public $categorias;
    public $buscado = '';
    public $productoSelec;
    public $categoria_producto;
    public $precio;
    public $total;
    public $cantidad = 0;
    public $id;

    public function mount(){
        $this->productos = ModelsProductos::orderBy('created_at', 'desc')->get();
        $this->categorias = ModelsProductos::select('categoria')->distinct()->pluck('categoria');
    }
    public function todos(){
        $this->productos = ModelsProductos::orderBy('created_at', 'desc')->get();
        $this->categorias = ModelsProductos::select('categoria')->distinct()->pluck('categoria');
    }
    public function buscador(){
        $this->productos = collect(ModelsProductos::orderBy('created_at', 'desc')->get())
            ->filter(function ($producto) {
                return stripos($producto->nombre, $this->buscado) !== false;
            });
    }
    public function seleccProducto($id){
        $this->id = $id;
        $this->productoSelec = ModelsProductos::find($id);
        $this->precio = $this->productoSelec->precio;

    }
    public function updatedCantidad(){
        // Ensure that cantidad is a valid number (cast to float or integer)
        $this->cantidad = (float) $this->cantidad;  // Cast to float to handle decimal values
        $this->precio = (float) $this->precio;      // Ensure precio is also a valid number (in case it's a string)
    
        // Ensure both values are numeric before multiplying
        if (is_numeric($this->cantidad) && is_numeric($this->precio)) {
            $this->total = $this->cantidad * $this->precio;  // Perform multiplication
        } else {
            $this->total = 0;  // Set total to 0 if either value is invalid
        }
    }
    
    public function filtro($categoria){
        $this->productos = ModelsProductos::where('categoria', $categoria)->orderBy('created_at', 'desc')->get();
    }
    public function render()
    {
        if (!empty($this->buscado)) {
            $this->buscador();
        }
        return view('livewire.productos');
    }
}
