<x-layout.app :title="$product->title">

  <div class="max-w-7xl mx-auto px-20 lg:px-32 py-32">

    <!-- Breadcrumb -->
    <nav class="mb-24">
      @if($category)
        <a href="{{ route('page.category', ['category' => $category]) }}" class="text-gray-500 hover:text-gray-700">
          <i class="ph ph-arrow-left mr-4"></i> {{ $category->name }}
        </a>
      @else
        <a href="{{ route('page.products') }}" class="text-gray-500 hover:text-gray-700">
          <i class="ph ph-arrow-left mr-4"></i> Produkte
        </a>
      @endif
    </nav>

    <!-- Product Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-32 lg:gap-48">

      <!-- Product Images -->
      <div class="space-y-16">
        @if($product->images && $product->images->count() > 0)
          @foreach($product->images as $image)
            <div class="aspect-square bg-gray-100 rounded-lg overflow-hidden">
              <x-media.image
                :src="$image->file_path"
                :alt="$product->title"
                :width="800"
                :height="800"
                fit="crop"
                class="w-full h-full object-cover"
              />
            </div>
          @endforeach
        @else
          <div class="aspect-square bg-gray-100 rounded-lg flex items-center justify-center">
            <i class="ph ph-image text-6xl text-gray-300"></i>
          </div>
        @endif
      </div>

      <!-- Product Info -->
      <div class="lg:py-16">

        <h1 class="text-2xl font-semibold text-gray-900 mb-16">{{ $product->title }}</h1>

        @if($product->short_description)
          <p class="text-gray-600 mb-24">{{ $product->short_description }}</p>
        @endif

        @if($product->children->isNotEmpty())
          {{-- Product with variations --}}
          @php
            $childrenData = $product->children->map(fn($c) => [
              'uuid' => $c->uuid,
              'label' => $c->label,
              'price' => (float) $c->price,
            ])->values();
            $firstChild = $product->children->first();
          @endphp

          <div
            x-data="{
              children: {{ Js::from($childrenData) }},
              selectedUuid: '{{ $firstChild->uuid }}',
              get selected() {
                return this.children.find(c => c.uuid === this.selectedUuid) || this.children[0];
              },
              formatPrice(price) {
                return price.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, String.fromCharCode(39));
              }
            }"
          >
            <!-- Variation Selector -->
            <div class="mb-24">
              <label class="block text-sm font-medium text-gray-700 mb-8">Variante</label>
              <select
                x-model="selectedUuid"
                class="w-full border border-gray-300 rounded-lg px-16 py-12 bg-white focus:ring-2 focus:ring-gray-900 focus:border-transparent">
                @foreach($product->children as $child)
                  <option value="{{ $child->uuid }}">{{ $child->label }}</option>
                @endforeach
              </select>
            </div>

            <!-- Price -->
            <p class="text-2xl font-semibold text-gray-900 mb-24">
              CHF <span x-text="formatPrice(selected.price)"></span>
            </p>

            @if($product->delivery_time)
              <p class="text-sm text-gray-500 mb-24">
                <i class="ph ph-truck mr-4"></i> Lieferzeit: {{ $product->delivery_time }}
              </p>
            @endif

            <!-- Add to Cart (one button per variation, show only selected) -->
            @foreach($product->children as $child)
              <div x-show="selectedUuid === '{{ $child->uuid }}'">
                <livewire:cart.button
                  :productUuid="$child->uuid"
                  :key="'cart-btn-' . $child->uuid" />
              </div>
            @endforeach
          </div>

        @else
          {{-- Simple product --}}

          <!-- Price -->
          <p class="text-2xl font-semibold text-gray-900 mb-24">
            CHF {{ number_format($product->price, 2, '.', "'") }}
          </p>

          @if($product->delivery_time)
            <p class="text-sm text-gray-500 mb-24">
              <i class="ph ph-truck mr-4"></i> Lieferzeit: {{ $product->delivery_time }}
            </p>
          @endif

          <!-- Add to Cart -->
          <livewire:cart.button :productUuid="$product->uuid" />

        @endif

        <!-- Description -->
        @if($product->description)
          <div class="mt-32 pt-32 border-t border-gray-200">
            <h2 class="text-lg font-medium text-gray-900 mb-16">Beschreibung</h2>
            <div class="prose prose-gray max-w-none">
              {!! nl2br(e($product->description)) !!}
            </div>
          </div>
        @endif

      </div>

    </div>

  </div>

</x-layout.app>
