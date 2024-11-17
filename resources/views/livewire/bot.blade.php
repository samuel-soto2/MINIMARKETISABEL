<div class="w-full h-full flex flex-col">
    <!-- Área de mensajes -->
    <div class="flex-grow p-6 mb-6 overflow-y-auto bg-gradient-to-r from-blue-50 via-indigo-100 to-purple-100 rounded-xl shadow-lg md:h-80 flex flex-col-reverse space-y-4">
        @foreach (array_reverse($botResponse) as $index => $response)
            <div class="flex {{ $response['from'] === 'user' ? 'justify-end' : 'justify-start' }} mb-5">
                <div class="max-w-xs 
                    @if (strpos($response['message'], 'stock bajo') !== false) 
                        bg-gradient-to-r from-red-500 to-red-600 text-white
                    @else
                        {{ $response['from'] === 'user' ? 'bg-gradient-to-r from-blue-400 to-blue-500 text-white' : 'bg-gradient-to-r from-pink-300 via-pink-400 to-pink-500 text-white' }}
                    @endif
                    p-4 rounded-xl shadow-xl transform transition-all hover:scale-105 hover:shadow-2xl duration-300 hover:bg-opacity-80">
                    
                    <!-- Texto del mensaje con fuente más grande -->
                    <p class="text-sm md:text-base font-semibold">{{ $response['message'] }}</p>
                    
                    <!-- Mostrar imagen si está presente -->
                    @if (isset($response['image_url']))
                        <img src="{{ $response['image_url'] }}" alt="Imagen del producto" class="mt-3 max-w-full rounded-lg shadow-md transition-all transform hover:scale-105 hover:rotate-3 duration-300">
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    <!-- Entrada de mensaje del usuario -->
    <div class="flex items-center bg-white p-2 rounded-lg shadow-xl mt-4">
        <input
            type="text"
            wire:model="userMessage"
            wire:keydown.enter="sendMessage"
            placeholder="Escribe tu mensaje..."
            class="flex-grow p-4 text-sm md:text-base border-2 border-gray-300 rounded-l-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
        />
        <button
            wire:click="sendMessage"
            class="p-4 ml-2 text-white bg-blue-500 rounded-r-lg hover:bg-blue-600 transition-all duration-200">
            Enviar
        </button>
    </div>
</div>
