<x-app-layout>
    <div class="w-full bg-[#F4F2ED] dark:bg-gray-800 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 md:py-12">
            <div class="flex flex-col md:hidden text-center relative">
                <div class="p-2 font-poppins">
                    <h1 class="text-3xl font-bold text-custom-brown leading-tight dark:text-white">
                        Kid's
                    </h1>
                    <p class="font-poppins text-xl text-custom-brown dark:text-white mb-4">
                        Clothing
                    </p>

                    <div class=" p-4  mb-4">
                        <h2 class="text-lg font-semibold flex relative text-right text-custom-brown dark:text-white mb-2">
                            Sustainable Kid's Fashion
                        </h2>
                        <p class="text-sm text-gray-700 dark:text-gray-300 text-left">
                            Discover our curated collection of kid's clothing that combines durability and fun style with sustainability. 
                            Each piece is chosen to reduce fashion's environmental impact while keeping your children 
                            comfortable and stylish.
                        </p>
                    </div>

                    <div class="  p-4 ">
                        <h3 class="text-md font-medium flex relative text-right text-custom-brown dark:text-white mb-2">
                            Why Choose Our Kid's Collection?
                        </h3>
                        <ul class="text-sm text-gray-700 dark:text-gray-300 text-left space-y-1">
                            <li class="flex items-start">
                                <span class="text-green-600 mr-2">✓</span>
                                <span>Eco-friendly materials and production</span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-green-600 mr-2">✓</span>
                                <span>Durable and comfortable clothing for active kids</span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-green-600 mr-2">✓</span>
                                <span>Fair trade and ethical manufacturing</span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-green-600 mr-2">✓</span>
                                <span>Quality pieces at accessible prices</span>
                            </li>
                        </ul>
                    </div>

                    <span class="absolute bottom-1 right-1 bg-white px-2 py-0.5 rounded-full text-xs text-[#7C6A46] font-medium shadow-sm">
                        Sustainable Fashion
                    </span>
                </div>
            </div>
            
            <div class="hidden md:flex md:flex-row md:items-center">
                <div class="p-3 md:w-1/2 font-poppins relative">
                    <div class="absolute top-[-200px] left-[-150px] z-0 w-[145px] h-[510px]">
                        <img src="{{ asset('images/Rectangle123.png') }}" 
                            alt="Background" 
                            class="w-full h-full">
                    </div> 
                    <div class="relative z-6">
                        <h1 class="text-6xl lg:text-7xl flex relative left-[50px] font-bold text-custom-brown leading-tight dark:text-white">
                            Kid's 
                        </h1>
                        <p class="flex relative left-[170px] font-poppins text-6xl lg:text-3xl">
                            <span class="block h-[10px]" aria-hidden="true"></span>
                            Clothing
                        </p>
                    </div>
                </div>
                
                <div class="md:w-[1900px] h-[400px] flex flex-row gap-[-30] relative p-6 left-[130px] top-[55px] overflow-hidden">
                    <div class=" flex relative right-[70px]">
                        <img src="{{ asset('images/image 159.png') }}" alt="Thrift-IT Sustainable Fashion" class="w-full flex relative left-[180px] max-h-[200px] object-contain">
                        <img src="{{ asset('images/image 161.png') }}" alt="Thrift-IT Sustainable Fashion" class="w-full  flex relative left-[90px] top-[50px] max-h-[240px] object-contain">
                        <img src="{{ asset('images/image 162.png') }}" alt="Thrift-IT Sustainable Fashion" class="w-full flex relative top-[70px] max-h-[260px] object-contain">
                        <img src="{{ asset('images/image 158.png') }}" alt="Thrift-IT Sustainable Fashion" class="w-full flex relative top-[60px] right-[30px] max-h-[200px] object-contain">
                        <img src="{{ asset('images/image 160.png') }}" alt="Thrift-IT Sustainable Fashion" class="w-full flex relative right-[80px] bottom-[0px] max-h-[200px] object-contain">
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="py-6 bg-white dark:bg-gray-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-6 relative">
                <h2 class="text-xl font-bold text-gray-800 dark:text-gray-200">{{ $segment->name }} Items</h2>
                
                <x-segment-filters 
                    :segment="$segment" 
                    :categories="$categories" 
                    :barangays="$barangays" 
                    :selected-category-id="$selectedCategoryId ?? null"
                    :selected-barangay-id="$selectedBarangayId ?? null"
                />
                </div>

            <div class="rounded-xl shadow-sm overflow-hidden">
                <div id="loadingIndicator" class="hidden flex items-center justify-center py-4">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-[#634600]"></div>
                    <span class="ml-2 text-gray-600 dark:text-gray-300">Loading items...</span>
                </div>
                <div id="productsGrid" class="p-4 sm:p-6">
                    @include('segments.partials.products-grid', ['products' => $products])
                </div>
            </div>
            
            <script>
            document.addEventListener('DOMContentLoaded', function() {
              const container = document.getElementById('productsGrid');
              const loadingIndicator = document.getElementById('loadingIndicator');
              
              // Helper function to show loading
              function showLoading() {
                if (loadingIndicator) {
                  loadingIndicator.classList.remove('hidden');
                }
                if (container) {
                  container.style.opacity = '0.5';
                }
              }

              // Helper function to hide loading
              function hideLoading() {
                if (loadingIndicator) {
                  loadingIndicator.classList.add('hidden');
                }
                if (container) {
                  container.style.opacity = '1';
                }
              }
              
              // Handle category links
              document.querySelectorAll('[data-category-link]').forEach(link => {
                link.addEventListener('click', async (e) => {
                  e.preventDefault();
                  const currentUrl = new URL(window.location);
                  const linkUrl = new URL(e.currentTarget.href, window.location.origin);
                  const categoryButtonText = document.getElementById('categoryButtonText');
                  
                  // Update button text
                  const categoryName = e.currentTarget.getAttribute('data-category-name') || 'Category';
                  if (categoryButtonText) {
                    categoryButtonText.textContent = categoryName;
                  }
                  
                  // Build query params for API call preserving location
                  const params = new URLSearchParams();
                  if (linkUrl.searchParams.get('category')) {
                    params.set('category', linkUrl.searchParams.get('category'));
                  }
                  if (currentUrl.searchParams.get('barangay')) {
                    params.set('barangay', currentUrl.searchParams.get('barangay'));
                  }
                  
                  showLoading();
                  
                  try {
                    const apiUrl = `{{ url('segments/'.$segment->id.'/products') }}` + (params.toString() ? '?' + params.toString() : '');
                    const res = await fetch(apiUrl, { headers: { 'X-Requested-With': 'XMLHttpRequest' }});
                    const json = await res.json();
                    container.innerHTML = json.html;
                  } catch (error) {
                    console.error('Error filtering items:', error);
                  } finally {
                    hideLoading();
                  }
                  
                  // update query string without reloading - keep barangay if present
                  const newUrl = new URL(window.location);
                  if (linkUrl.searchParams.get('category')) {
                    newUrl.searchParams.set('category', linkUrl.searchParams.get('category'));
                  } else {
                    newUrl.searchParams.delete('category');
                  }
                  // Keep barangay param if it exists
                  if (currentUrl.searchParams.get('barangay')) {
                    newUrl.searchParams.set('barangay', currentUrl.searchParams.get('barangay'));
                  }
                  window.history.replaceState({}, '', newUrl);
                });
              });
              
              // Handle location links
              document.querySelectorAll('[data-location-link]').forEach(link => {
                link.addEventListener('click', async (e) => {
                  e.preventDefault();
                  const currentUrl = new URL(window.location);
                  const linkUrl = new URL(e.currentTarget.href, window.location.origin);
                  const locationButtonText = document.getElementById('locationButtonText');
                  
                  // Update button text
                  const locationName = e.currentTarget.getAttribute('data-location-name') || 'Location';
                  if (locationButtonText) {
                    locationButtonText.textContent = locationName;
                  }
                  
                  // Build query params for API call preserving category
                  const params = new URLSearchParams();
                  if (currentUrl.searchParams.get('category')) {
                    params.set('category', currentUrl.searchParams.get('category'));
                  }
                  if (linkUrl.searchParams.get('barangay')) {
                    params.set('barangay', linkUrl.searchParams.get('barangay'));
                  }
                  
                  showLoading();
                  
                  try {
                    const apiUrl = `{{ url('segments/'.$segment->id.'/products') }}` + (params.toString() ? '?' + params.toString() : '');
                    const res = await fetch(apiUrl, { headers: { 'X-Requested-With': 'XMLHttpRequest' }});
                    const json = await res.json();
                    container.innerHTML = json.html;
                  } catch (error) {
                    console.error('Error filtering items:', error);
                  } finally {
                    hideLoading();
                  }
                  
                  // update query string without reloading - keep category if present
                  const newUrl = new URL(window.location);
                  if (linkUrl.searchParams.get('barangay')) {
                    newUrl.searchParams.set('barangay', linkUrl.searchParams.get('barangay'));
                  } else {
                    newUrl.searchParams.delete('barangay');
                  }
                  // Keep category param if it exists
                  if (currentUrl.searchParams.get('category')) {
                    newUrl.searchParams.set('category', currentUrl.searchParams.get('category'));
                  }
                  window.history.replaceState({}, '', newUrl);
                });
              });
            });
            </script>
        </div>
    </div>
</x-app-layout>