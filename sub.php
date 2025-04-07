<!-- Artist subscription plans display -->
<div class="subscription-plans-container p-6 bg-white rounded-lg shadow-lg">
    <h2 class="text-2xl font-bold mb-4">Support this Artist</h2>
    <p class="text-gray-600 mb-6">Subscribe to get exclusive benefits and discounts on all artwork by this artist.</p>
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Plan 1: Monthly -->
        <div class="subscription-plan border rounded-lg p-6 hover:shadow-md transition duration-300">
            <h3 class="text-xl font-semibold mb-2">Monthly Supporter</h3>
            <p class="text-gray-600 mb-4">Support your favorite artist on a monthly basis.</p>
            <div class="flex items-baseline mb-4">
                <span class="text-3xl font-bold">$5</span>
                <span class="text-gray-500 ml-1">/month</span>
            </div>
            <ul class="mb-6 space-y-2">
                <li class="flex items-center">
                    <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    10% off all purchases
                </li>
                <li class="flex items-center">
                    <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Early access to new works
                </li>
            </ul>
            <button 
                class="subscribe-button w-full px-6 py-3 bg-amber-600 hover:bg-amber-700 text-white rounded-lg transition duration-300 flex items-center justify-center" 
                data-artist-id="123" 
                data-plan-id="1"
            >
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Subscribe
            </button>
        </div>
        
        <!-- Plan 2: Annual -->
        <div class="subscription-plan border rounded-lg p-6 hover:shadow-md transition duration-300 border-amber-300 bg-amber-50">
            <div class="absolute top-0 right-0 bg-amber-500 text-white py-1 px-3 rounded-bl-lg rounded-tr-lg text-xs font-bold">BEST VALUE</div>
            <h3 class="text-xl font-semibold mb-2">Annual Patron</h3>
            <p class="text-gray-600 mb-4">Save 16% compared to monthly subscription.</p>
            <div class="flex items-baseline mb-4">
                <span class="text-3xl font-bold">$50</span>
                <span class="text-gray-500 ml-1">/year</span>
            </div>
            <ul class="mb-6 space-y-2">
                <li class="flex items-center">
                    <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    15% off all purchases
                </li>
                <li class="flex items-center">
                    <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Early access to new works
                </li>
                <li class="flex items-center">
                    <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Quarterly high-res digital art
                </li>
            </ul>
            <button 
                class="subscribe-button w-full px-6 py-3 bg-amber-600 hover:bg-amber-700 text-white rounded-lg transition duration-300 flex items-center justify-center" 
                data-artist-id="123" 
                data-plan-id="2"
            >
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Subscribe
            </button>
        </div>
        
        <!-- Plan 3: Lifetime -->
        <div class="subscription-plan border rounded-lg p-6 hover:shadow-md transition duration-300">
            <h3 class="text-xl font-semibold mb-2">Lifetime Benefactor</h3>
            <p class="text-gray-600 mb-4">Support this artist for life with a one-time payment.</p>
            <div class="flex items-baseline mb-4">
                <span class="text-3xl font-bold">$200</span>
                <span class="text-gray-500 ml-1">/lifetime</span>
            </div>
            <ul class="mb-6 space-y-2">
                <li class="flex items-center">
                    <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    20% off all purchases forever
                </li>
                <li class="flex items-center">
                    <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Early access to new works
                </li>
                <li class="flex items-center">
                    <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Exclusive digital art collection
                </li>
                <li class="flex items-center">
                    <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Special thank-you in artist bio
                </li>
            </ul>
            <button 
                class="subscribe-button w-full px-6 py-3 bg-amber-600 hover:bg-amber-700 text-white rounded-lg transition duration-300 flex items-center justify-center" 
                data-artist-id="123" 
                data-plan-id="3"
            >
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Subscribe
            </button>
        </div>
    </div>
</div>

<!-- Active subscriptions display (for user dashboard) -->
<div class="active-subscriptions p-6 bg-white rounded-lg shadow-lg">
    <h2 class="text-2xl font-bold mb-4">Your Active Subscriptions</h2>
    
    <div class="overflow-x-auto">
        <table class="min-w-full bg-white">
            <thead>
                <tr>
                    <th class="py-3 px-4 border-b border-gray-200 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Artist</th>
                    <th class="py-3 px-4 border-b border-gray-200 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Plan</th>
                    <th class="py-3 px-4 border-b border-gray-200 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Discount</th>
                    <th class="py-3 px-4 border-b border-gray-200 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Expires</th>
                    <th class="py-3 px-4 border-b border-gray-200 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Auto-Renew</th>
                    <th class="py-3 px-4 border-b border-gray-200 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody>
                <!-- Example subscription row -->
                <tr>
                    <td class="py-4 px-4 border-b border-gray-200">
                        <div class="flex items-center">
                            <img class="h-10 w-10 rounded-full mr-2" src="/api/placeholder/40/40" alt="Artist avatar">
                            <div>
                                <div class="font-medium">Jane Artist</div>
                                <div class="text-sm text-gray-500">Abstract painter</div>
                            </div>
                        </div>
                    </td>
                    <td class="py-4 px-4 border-b border-gray-200">
                        <div class="font-medium">Annual Patron</div>
                        <div class="text-sm text-gray-500">$50/year</div>
                    </td>
                    <td class="py-4 px-4 border-b border-gray-200">
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">15% off</span></td>
                    <td class="py-4 px-4 border-b border-gray-200">
                        <div>Mar 15, 2026</div>
                        <div class="text-sm text-gray-500">In 11 months</div>
                    </td>
                    <td class="py-4 px-4 border-b border-gray-200">
                        <span class="flex items-center">
                            <span class="h-4 w-4 bg-green-500 rounded-full mr-2"></span>
                            Enabled
                        </span>
                    </td>
                    <td class="py-4 px-4 border-b border-gray-200">
                        <button 
                            class="unsubscribe-button px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded transition duration-300 text-sm" 
                            data-subscription-id="12345"
                            data-artist-id="123"
                            data-plan-id="2"
                        >
                            Cancel
                        </button>
                    </td>
                </tr>

                <!-- Example subscription row 2 -->
                <tr>
                    <td class="py-4 px-4 border-b border-gray-200">
                        <div class="flex items-center">
                            <img class="h-10 w-10 rounded-full mr-2" src="/api/placeholder/40/40" alt="Artist avatar">
                            <div>
                                <div class="font-medium">Michael Creator</div>
                                <div class="text-sm text-gray-500">Digital artist</div>
                            </div>
                        </div>
                    </td>
                    <td class="py-4 px-4 border-b border-gray-200">
                        <div class="font-medium">Lifetime Benefactor</div>
                        <div class="text-sm text-gray-500">$200 one-time</div>
                    </td>
                    <td class="py-4 px-4 border-b border-gray-200">
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">20% off</span>
                    </td>
                    <td class="py-4 px-4 border-b border-gray-200">
                        <div>Lifetime</div>
                        <div class="text-sm text-gray-500">Never expires</div>
                    </td>
                    <td class="py-4 px-4 border-b border-gray-200">
                        <span class="flex items-center">
                            <span class="h-4 w-4 bg-gray-300 rounded-full mr-2"></span>
                            N/A
                        </span>
                    </td>
                    <td class="py-4 px-4 border-b border-gray-200">
                        <button 
                            class="unsubscribe-button px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded transition duration-300 text-sm" 
                            data-subscription-id="12346"
                            data-artist-id="456"
                            data-plan-id="3"
                        >
                            Cancel
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    
    <div class="mt-6 text-center text-gray-600">
        <p>Subscribe to more artists to get exclusive benefits and discounts.</p>
        <a href="/explore/artists" class="text-amber-600 hover:text-amber-700 font-medium">Explore Artists</a>
    </div>
</div>

<!-- Artwork display with subscription-aware pricing -->
<div class="artwork-grid grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mt-8">
    <!-- Artwork Item with regular pricing -->
    <div class="artwork-item rounded-lg overflow-hidden shadow-lg bg-white" data-artist-id="123" data-artwork-id="789">
        <img src="/api/placeholder/400/300" alt="Artwork" class="w-full h-64 object-cover">
        <div class="p-4">
            <h3 class="text-xl font-semibold mb-2">Summer Breeze</h3>
            <p class="text-gray-600 mb-4">Oil on canvas, 24" x 36"</p>
            <div class="flex justify-between items-center">
                <div class="artwork-price" data-artwork-id="789">
                    <span class="font-bold">$350.00</span>
                </div>
                <button class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded transition duration-300">
                    Add to Cart
                </button>
            </div>
        </div>
    </div>
    
    <!-- Artwork Item with discounted pricing -->
    <div class="artwork-item rounded-lg overflow-hidden shadow-lg bg-white" data-artist-id="123" data-artwork-id="790">
        <img src="/api/placeholder/400/300" alt="Artwork" class="w-full h-64 object-cover">
        <div class="p-4">
            <h3 class="text-xl font-semibold mb-2">Ocean Waves</h3>
            <p class="text-gray-600 mb-4">Acrylic on canvas, 18" x 24"</p>
            <div class="flex justify-between items-center">
                <div class="artwork-price" data-artwork-id="790">
                    <span class="line-through text-gray-500">$250.00</span>
                    <span class="font-bold text-green-600">$212.50</span>
                </div>
                <button class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded transition duration-300">
                    Add to Cart
                </button>
            </div>
            <div class="mt-2">
                <span class="text-xs font-semibold text-green-600">15% subscriber discount applied</span>
            </div>
        </div>
    </div>
</div>