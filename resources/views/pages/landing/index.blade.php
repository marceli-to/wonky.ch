<x-layout.app title="Shop">

  <div class="max-w-7xl mx-auto px-20 lg:px-32 py-32">

    <!-- Page Title -->
    <h1 class="text-2xl font-semibold text-gray-900 mb-32">Kategorien</h1>

    <!-- Categories Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-24">

      @foreach($categories as $category)
        <a
          href="{{ route('page.category', $category) }}"
          class="group block bg-white rounded-lg border border-gray-200 overflow-hidden hover:shadow-lg transition-shadow">

          <!-- Category Image -->
          <div class="aspect-[4/3] overflow-hidden bg-gray-100">
            @if($category->image)
              <x-media.image
                :src="$category->image->file_path"
                :alt="$category->name"
                :width="500"
                :height="375"
                fit="crop"
                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
              />
            @else
              <div class="w-full h-full flex items-center justify-center">
                <i class="ph ph-image text-4xl text-gray-300"></i>
              </div>
            @endif
          </div>

          <!-- Category Name -->
          <div class="p-16">
            <h2 class="text-lg font-medium text-gray-900 group-hover:text-gray-700">
              {{ $category->name }}
            </h2>
          </div>

        </a>
      @endforeach

    </div>

    @if($categories->isEmpty())
      <p class="text-gray-500 my-48">Keine Kategorien vorhanden.</p>
    @endif

  </div>

</x-layout.app>
