<header class="sticky top-0 z-40 bg-white border-b border-gray-200">
  <div class="max-w-7xl mx-auto px-20 lg:px-32">
    <div class="flex items-center justify-between h-64">

      <!-- Logo -->
      <a href="{{ route('page.landing') }}" class="text-gray-900 hover:text-gray-700">
        <x-icons.logo class="h-32 w-auto" />
      </a>

      <!-- Navigation -->
      <nav class="hidden sm:flex items-center gap-32">
        <a
          href="{{ route('page.products') }}"
          @class([
            'text-sm font-medium transition-colors',
            'text-gray-900' => request()->routeIs('page.products'),
            'text-gray-600 hover:text-gray-900' => !request()->routeIs('page.products'),
          ])>
          Produkte
        </a>
        <a
          href="{{ route('page.landing') }}"
          @class([
            'text-sm font-medium transition-colors',
            'text-gray-900' => request()->routeIs('page.landing'),
            'text-gray-600 hover:text-gray-900' => !request()->routeIs('page.landing'),
          ])>
          Kategorien
        </a>
      </nav>

      <!-- Cart Icon + Mobile Menu -->
      <div class="flex items-center gap-16">

        <!-- Mobile Menu Button -->
        <button
          x-data
          @click="$dispatch('toggle-mobile-menu')"
          class="sm:hidden p-8 text-gray-600 hover:text-gray-900"
          aria-label="Menu">
          <i class="ph ph-list text-2xl"></i>
        </button>

        <livewire:cart.icon />
      </div>

    </div>
  </div>

  <!-- Mobile Navigation -->
  <div
    x-data="{ open: false }"
    @toggle-mobile-menu.window="open = !open"
    x-show="open"
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0 -translate-y-2"
    x-transition:enter-end="opacity-100 translate-y-0"
    x-transition:leave="transition ease-in duration-150"
    x-transition:leave-start="opacity-100 translate-y-0"
    x-transition:leave-end="opacity-0 -translate-y-2"
    class="sm:hidden border-t border-gray-200 bg-white"
    style="display: none;">
    <nav class="px-20 py-16 space-y-8">
      <a
        href="{{ route('page.products') }}"
        @class([
          'block px-12 py-8 rounded-lg text-base font-medium',
          'bg-gray-100 text-gray-900' => request()->routeIs('page.products'),
          'text-gray-600 hover:bg-gray-50 hover:text-gray-900' => !request()->routeIs('page.products'),
        ])>
        Produkte
      </a>
      <a
        href="{{ route('page.landing') }}"
        @class([
          'block px-12 py-8 rounded-lg text-base font-medium',
          'bg-gray-100 text-gray-900' => request()->routeIs('page.landing'),
          'text-gray-600 hover:bg-gray-50 hover:text-gray-900' => !request()->routeIs('page.landing'),
        ])>
        Kategorien
      </a>
    </nav>
  </div>

</header>

<livewire:cart.mini-cart />
