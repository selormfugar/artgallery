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
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">15% off</span>
                        </td>
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