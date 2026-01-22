<x-layout.app title="Produkte">

  <div class="max-w-7xl mx-auto px-20 lg:px-32 py-32">

    <!-- Page Title -->
    <h1 class="text-2xl font-semibold text-gray-900 mb-32">Produkte</h1>

    <!-- Products Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-24">

      @foreach($products as $product)
        @php
          $category = $product->categories->first();
          $productUrl = $category
            ? route('page.product', ['category' => $category, 'product' => $product])
            : route('page.product.direct', ['product' => $product]);
        @endphp

        <a
          href="{{ $productUrl }}"
          class="group block bg-white rounded-lg border border-gray-200 overflow-hidden hover:shadow-lg transition-shadow">

          <!-- Product Image -->
          <div class="aspect-square overflow-hidden bg-gray-100">
            @if($product->previewImage)
              <x-media.image
                :src="$product->previewImage->file_path"
                :alt="$product->title"
                :width="400"
                :height="400"
                fit="crop"
                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
              />
            @else
              <div class="w-full h-full flex items-center justify-center">
                <i class="ph ph-image text-4xl text-gray-300"></i>
              </div>
            @endif
          </div>

          <!-- Product Info -->
          <div class="p-16">
            @if($category)
              <p class="text-xs text-gray-500 mb-4">{{ $category->name }}</p>
            @endif
            <h2 class="text-base font-medium text-gray-900 group-hover:text-gray-700 mb-8">
              {{ $product->title }}
            </h2>
            <p class="text-lg font-semibold text-gray-900">
              @if($product->children->isNotEmpty())
                ab CHF {{ number_format($product->children->min('price'), 2, '.', "'") }}
              @else
                CHF {{ number_format($product->price, 2, '.', "'") }}
              @endif
            </p>
          </div>

        </a>
      @endforeach

    </div>

    @if($products->isEmpty())
      <p class="text-gray-500 my-48">Keine Produkte vorhanden.</p>
    @endif

  </div>

</x-layout.app>
