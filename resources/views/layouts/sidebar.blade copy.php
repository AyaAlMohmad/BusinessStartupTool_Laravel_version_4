
    <!-- Navigation Links -->
    <div class="space-y-4">
        <!-- Financial Insights -->
        <div class="mt-4 space-y-1">
            <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')" class="flex items-center gap-3 w-full">
                <i class="fas fa-coins text-gray-600 w-5"></i>
                {{ __('Financial Insights') }}
            </x-nav-link>
        </div>

        <!-- Progress Analytics -->
        <div class="mt-2 space-y-1">
            <x-nav-link :href="route('admin.ProgressAnalytics')" :active="request()->routeIs('admin.ProgressAnalytics')" class="flex items-center gap-3 w-full">
                <i class="fas fa-chart-line text-purple-600 w-5"></i>
                {{ __('Progress Analytics') }}
            </x-nav-link>
        </div>
      




        <!-- User Management -->
        <div class="mt-4 space-y-1">
            <x-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.index')" class="flex items-center gap-3 w-full">
                <i class="fas fa-users text-gray-700 w-5"></i>
                {{ __('User Management') }}
            </x-nav-link>
        </div>

        <!-- Video Management -->
        <div class="mt-2 space-y-1">
            <x-nav-link :href="route('admin.videos.index')" :active="request()->routeIs('admin.videos.index')" class="flex items-center gap-3 w-full">
                <i class="fas fa-video text-gray-600 w-5"></i>
                {{ __('Video Management') }}
            </x-nav-link>
        </div>
        <div class="mt-2 space-y-1">
            <div x-data="{ openPages: false }">
                <button @click="openPages = !openPages"
                    class="flex items-center justify-between w-full text-sm font-medium text-gray-600 hover:text-gray-900 focus:outline-none">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-copy text-gray-600 w-5"></i>
                        <span>{{ __('Pages') }}</span>
                    </div>
                    <!-- السهم يتغير بناءً على الحالة -->
                    <i :class="openPages ? 'fas fa-chevron-down' : 'fas fa-chevron-right'"
                        class="text-gray-500 text-xs transition-all duration-200"></i>
                </button>

                <ul x-show="openPages" x-transition.duration.200ms class="ml-4 mt-2 space-y-2"
                    @click.outside="openPages = false">
                    <li>
                        <a href="{{ route('admin.landing-page.index') }}"
                            class="flex items-center gap-3 px-2 py-1 text-sm text-gray-700 hover:bg-gray-100"
                            :class="{ 'bg-gray-100': request() - > routeIs('admin.landing-page.index') }">
                            <i class="fas fa-globe text-indigo-500 w-5"></i> {{ __('Landing Page') }}
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.business-ideas.index') }}"
                            class="flex items-center gap-3 px-2 py-1 text-sm text-gray-700 hover:bg-gray-100"
                            :class="{ 'bg-gray-100': request() - > routeIs('admin.business-ideas.index') }">
                            <i class="fas fa-lightbulb text-yellow-400 w-5"></i> {{ __('Business Ideas') }}
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.testing-your-idea.index') }}"
                            class="flex items-center gap-3 px-2 py-1 text-sm text-gray-700 hover:bg-gray-100"
                            :class="{ 'bg-gray-100': request() - > routeIs('admin.testing-your-idea.index') }">
                            <i class="fas fa-vial text-pink-500 w-5"></i> {{ __('Testing Your Idea') }}
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('admin.market-research.index') }}"
                            class="flex items-center gap-3 px-2 py-1 text-sm text-gray-700 hover:bg-gray-100"
                            :class="{ 'bg-gray-100': request() - > routeIs('admin.market-research.index') }">
                            <i class="fas fa-search text-blue-400 w-5"></i> {{ __('Marketing Research') }}
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.start-simple.index') }}"
                            class="flex items-center gap-3 px-2 py-1 text-sm text-gray-700 hover:bg-gray-100"
                            :class="{ 'bg-gray-100': request() - > routeIs('admin.start-simple.index') }">
                            <i class="fas fa-play text-gray-500 w-5"></i> {{ __('Start Simple') }}
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.marketing-new.index') }}"
                            class="flex items-center gap-3 px-2 py-1 text-sm text-gray-700 hover:bg-gray-100"
                            :class="{ 'bg-gray-100': request() - > routeIs('admin.marketing-new.index') }">
                            <i class="fas fa-bullhorn text-green-500 w-5"></i> {{ __('Marketing') }}
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.sales-strategies.index') }}"
                            class="flex items-center gap-3 px-2 py-1 text-sm text-gray-700 hover:bg-gray-100"
                            :class="{ 'bg-gray-100': request() - > routeIs('admin.sales-strategies.index') }">
                            <i class="fas fa-bullseye text-red-500 w-5"></i> {{ __('Sales Strategies') }}
                        </a>
                    </li>
          
            
                
                 
                    <li>
                        <a href="{{ route('admin.business-setups.index') }}"
                            class="flex items-center gap-3 px-2 py-1 text-sm text-gray-700 hover:bg-gray-100"
                            :class="{ 'bg-gray-100': request() - > routeIs('admin.business-setups.index') }">
                            <i class="fas fa-vial text-pink-500 w-5"></i> {{ __('Business Setups') }}
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.financial_planners.index') }}"
                            class="flex items-center gap-3 px-2 py-1 text-sm text-gray-700 hover:bg-gray-100"
                            :class="{ 'bg-gray-100': request() - > routeIs('admin.financial_planners.index') }">
                            <i class="fas fa-vial text-pink-500 w-5"></i> {{ __('Financial Planners') }}
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.websites.index') }}"
                            class="flex items-center gap-3 px-2 py-1 text-sm text-gray-700 hover:bg-gray-100"
                            :class="{ 'bg-gray-100': request() - > routeIs('admin.websites.index') }">
                            <i class="fas fa-vial text-pink-500 w-5"></i> {{ __('Websites') }}
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.stories.index') }}"
                            class="flex items-center gap-3 px-2 py-1 text-sm text-gray-700 hover:bg-gray-100"
                            :class="{ 'bg-gray-100': request() - > routeIs('admin.stories.index') }">
                            <i class="fas fa-vial text-pink-500 w-5"></i> {{ __('stories') }}
                        </a>
                    </li>
                </ul>
            </div>
        </div>
</div>


