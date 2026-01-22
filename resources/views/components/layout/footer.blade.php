<footer class="bg-white border-t border-gray-200 mt-auto">
  <div class="max-w-7xl mx-auto px-20 lg:px-32 py-32">

    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-24">

      <!-- Newsletter -->
      <div class="lg:max-w-md">
        <h3 class="text-sm font-medium text-gray-900 mb-12">Newsletter</h3>
        <livewire:newsletter-signup />
      </div>

      <!-- Copyright -->
      <p class="text-sm text-gray-500">
        &copy; {{ date('Y') }} {{ config('app.name', 'Shop') }}
      </p>

    </div>

  </div>
</footer>

@livewireScripts
@vite('resources/js/app.js')

</body>
</html>
