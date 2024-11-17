<?php

namespace App\Livewire;

use App\Models\Productos;
use Livewire\Component;

class Bot extends Component
{
    public $userMessage;
    public $botResponse = [];
    public $lowStockThreshold = 12; // Definimos el umbral para stock bajo (3 unidades en este caso)

    public function mount()
    {
        // Llamamos a la función que verifica los productos con stock bajo al cargar la página
        $this->checkLowStock();
    }

    // Función que revisa los productos con stock bajo
    public function checkLowStock()
    {
        // Obtenemos todos los productos cuyo stock es menor al umbral definido
        $productosConStockBajo = Productos::where('stock', '<', $this->lowStockThreshold)->get();

        // Si hay productos con stock bajo, agregamos una notificación del bot
        foreach ($productosConStockBajo as $producto) {
            $this->botResponse[] = [
                'from' => 'bot',
                'message' => "¡Alerta! El producto '{$producto->nombre}' de la marca '{$producto->marca}' tiene un stock bajo: solo {$producto->stock} unidades disponibles.",
                'image_url' => $producto->imagen ? asset('ServidorProductos/' . $producto->imagen) : asset('images/placeholder.png'),
            ];
        }
    }

    // Función que maneja los mensajes enviados por el usuario
    public function sendMessage()
    {
        if (empty($this->userMessage)) {
            return;
        }

        $message = strtolower($this->userMessage);

        // Log the user's message
        $this->botResponse[] = ['from' => 'user', 'message' => $this->userMessage];

        // Regex para capturar nombre y marca del producto
        if (preg_match('/^([\w\s]+?)\s+\s*[:]*\s*(.+)$/i', $message, $matches)) {
            $nombreProducto = trim($matches[1]);
            $marcaProducto = trim($matches[2]);

            // Intentamos encontrar el producto en la base de datos
            $producto = Productos::where('nombre', 'like', '%' . $nombreProducto . '%')
                ->where('marca', 'like', '%' . $marcaProducto . '%')
                ->first();

            if ($producto) {
                // Generamos la URL de la imagen o un placeholder si no existe imagen
                $imageUrl = $producto->imagen ? asset('ServidorProductos/' . $producto->imagen) : asset('images/placeholder.png');
                $responseMessage = "Producto: {$producto->nombre}\nMarca: {$producto->marca}\nPrecio: S/. {$producto->precio}\nStock: {$producto->stock}";

                $this->botResponse[] = [
                    'from' => 'bot',
                    'message' => $responseMessage,
                    'image_url' => $imageUrl,
                ];
            } else {
                $responseMessage = 'Lo siento, no encontré ese producto con la marca especificada. Por favor, verifica los detalles o intenta con otro producto.';
                $this->botResponse[] = ['from' => 'bot', 'message' => $responseMessage];
            }
        } else {
            $responseMessage = 'Por favor, Solo ingresa el nombre del producto seguido de la marca. Ejemplo: "pelota Walon"';
            $this->botResponse[] = ['from' => 'bot', 'message' => $responseMessage];
        }

        // Limpiamos el mensaje del usuario
        $this->userMessage = '';
    }

    public function render()
    {
        return view('livewire.bot');
    }
}

