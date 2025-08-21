<div class="sidenav-header">
    <i class="fas fa-times p-3 cursor-pointer text-secondary opacity-5 position-absolute end-0 top-0 d-none d-xl-none" aria-hidden="true" id="iconSidenav"></i>
    <a class="navbar-brand d-flex align-items-center m-0" href="{{ route('admin.dashboard') }}">
        <img src="{{ asset('build/assets/VN Logo Secondary Web.png') }}" alt="Logo" class="navbar-brand-img">
    </a>
</div>

<ul class="navbar-nav">
    <!-- Progress Analytics -->
    @if(auth()->user()->hasPermission('view_progress_analytics'))
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('admin.ProgressAnalytics') ? 'active' : '' }}" href="{{ route('admin.ProgressAnalytics') }}">
            <div class="icon icon-shape icon-sm px-0 text-center d-flex align-items-center justify-content-center">
                <i class="fas fa-chart-line text-purple-600"></i>
            </div>
            <span class="nav-link-text ms-1">Progress Analytics</span>
        </a>
    </li>
    @endif

    @if(auth()->user()->hasPermission('manage_regions'))
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('admin.regions.index') ? 'active' : '' }}" href="{{ route('admin.regions.index') }}">
            <div class="icon icon-shape icon-sm px-0 text-center d-flex align-items-center justify-content-center">
                <i class="fas fa-globe text-purple-600"></i>
            </div>
            <span class="nav-link-text ms-1">Regions</span>
        </a>
    </li>
    @endif

    @if(auth()->user()->hasPermission('manage_resources'))
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('admin.resources.index') ? 'active' : '' }}" href="{{ route('admin.resources.index') }}">
            <div class="icon icon-shape icon-sm px-0 text-center d-flex align-items-center justify-content-center">
                <i class="fas fa-book text-purple-600"></i>
            </div>
            <span class="nav-link-text ms-1">Resources</span>
        </a>
    </li>
    @endif

    <!-- User Management Dropdown -->
    @if(auth()->user()->hasPermission('manage_users') || auth()->user()->hasPermission('manage_roles'))
    <li class="nav-item" x-data="{ open: false }">
        <a href="#" @click="open = !open" class="nav-link d-flex align-items-center">
            <div class="icon icon-shape icon-sm px-0 text-center d-flex align-items-center justify-content-center">
                <i class="fas fa-users-cog text-gray-700"></i>
            </div>
            <span class="nav-link-text ms-1">User Management</span>
            <i :class="open ? 'fas fa-chevron-down' : 'fas fa-chevron-right'" class="text-xs ms-auto transition-all duration-200"></i>
        </a>
        <ul x-show="open" x-transition.duration.200ms class="nav flex-column ms-4 ps-3" style="list-style: none;">
            @if(auth()->user()->hasPermission('manage_users'))
            <li class="nav-item">
                <a class="dropdown-item border-radius-md {{ request()->routeIs('admin.users.index') && !request()->get('type') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-users me-2"></i>
                        <span>All Users</span>
                    </div>
                </a>
            </li>
            <li class="nav-item">
                <a class="dropdown-item border-radius-md {{ request()->routeIs('admin.users.index') && request()->get('type') == 'regular' ? 'active' : '' }}" href="{{ route('admin.users.index', ['type' => 'regular']) }}">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-user me-2"></i>
                        <span>Regular Users</span>
                    </div>
                </a>
            </li>
            <li class="nav-item">
                <a class="dropdown-item border-radius-md {{ request()->routeIs('admin.users.index') && request()->get('type') == 'role' ? 'active' : '' }}" href="{{ route('admin.users.index', ['type' => 'role']) }}">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-user-shield me-2"></i>
                        <span>Users with Roles</span>
                    </div>
                </a>
            </li>
            <li><hr class="horizontal dark my-2"></li>
            @endif

            @if(auth()->user()->hasPermission('manage_roles'))
            <li class="nav-item">
                <a class="dropdown-item border-radius-md {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}" href="{{ route('admin.roles.index') }}">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-tasks me-2"></i>
                        <span>Roles & Permissions</span>
                    </div>
                </a>
            </li>
            @endif
        </ul>
    </li>
    @endif

    @if(auth()->user()->hasPermission('manage_videos'))
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('admin.videos.index') ? 'active' : '' }}" href="{{ route('admin.videos.index') }}">
            <div class="icon icon-shape icon-sm px-0 text-center d-flex align-items-center justify-content-center">
                <i class="fas fa-video text-gray-600"></i>
            </div>
            <span class="nav-link-text ms-1">Video Management</span>
        </a>
    </li>
    @endif

    <!-- Pages Section -->
    @if(auth()->user()->hasAnyPermission([
        'manage_landing_pages',
        'manage_business_ideas',
        'manage_testing_ideas',
        'manage_market_research',
        'manage_simple_solutions',
        'manage_marketing',
        'manage_sales_strategies',
        'manage_business_setups',
        'manage_financial_planners',
        'manage_websites',
        'manage_stories'
    ]))
    <li class="nav-item mt-3" x-data="{ openPages: false }">
        <a @click="openPages = !openPages" class="nav-link cursor-pointer d-flex align-items-center">
            <div class="icon icon-shape icon-sm px-0 text-center d-flex align-items-center justify-content-center">
                <i class="fas fa-copy text-gray-600"></i>
            </div>
            <span class="nav-link-text ms-1">Pages Management</span>
            <i :class="openPages ? 'fas fa-chevron-down' : 'fas fa-chevron-right'" class="text-xs ms-auto transition-all duration-200"></i>
        </a>
        <ul x-show="openPages" x-transition.duration.200ms class="nav flex-column ms-4 ps-3" style="list-style: none;">
            @if(auth()->user()->hasPermission('manage_landing_pages'))
            <li class="nav-item">
                <a href="{{ route('admin.landing-page.index') }}" class="nav-link position-relative ps-2 py-1 {{ request()->routeIs('admin.landing-page.index') ? 'active' : '' }}">
                    <i class="fas fa-globe text-indigo-500 me-2"></i>
                    <span class="nav-link-text">Landing Page</span>
                </a>
            </li>
            @endif

            @if(auth()->user()->hasPermission('manage_business_ideas'))
            <li class="nav-item">
                <a href="{{ route('admin.business-ideas.index') }}" class="nav-link position-relative ps-2 py-1 {{ request()->routeIs('admin.business-ideas.index') ? 'active' : '' }}">
                    <i class="fas fa-lightbulb text-yellow-400 me-2"></i>
                    <span class="nav-link-text">Business Ideas</span>
                </a>
            </li>
            @endif

            @if(auth()->user()->hasPermission('manage_testing_ideas'))
            <li class="nav-item">
                <a href="{{ route('admin.testing-your-idea.index') }}" class="nav-link position-relative ps-2 py-1 {{ request()->routeIs('admin.testing-your-idea.index') ? 'active' : '' }}">
                    <i class="fas fa-vial text-pink-500 me-2"></i>
                    <span class="nav-link-text">Testing Your Idea</span>
                </a>
            </li>
            @endif

            @if(auth()->user()->hasPermission('manage_market_research'))
            <li class="nav-item">
                <a href="{{ route('admin.market-research.index') }}" class="nav-link position-relative ps-2 py-1 {{ request()->routeIs('admin.market-research.index') ? 'active' : '' }}">
                    <i class="fas fa-search text-blue-400 me-2"></i>
                    <span class="nav-link-text">Market Research</span>
                </a>
            </li>
            @endif

            @if(auth()->user()->hasPermission('manage_simple_solutions'))
            <li class="nav-item">
                <a href="{{ route('admin.start-simple.index') }}" class="nav-link position-relative ps-2 py-1 {{ request()->routeIs('admin.start-simple.index') ? 'active' : '' }}">
                    <i class="fas fa-play text-gray-500 me-2"></i>
                    <span class="nav-link-text">Start Simple</span>
                </a>
            </li>
            @endif

            @if(auth()->user()->hasPermission('manage_marketing'))
            <li class="nav-item">
                <a href="{{ route('admin.marketing-new.index') }}" class="nav-link position-relative ps-2 py-1 {{ request()->routeIs('admin.marketing-new.index') ? 'active' : '' }}">
                    <i class="fas fa-bullhorn text-green-500 me-2"></i>
                    <span class="nav-link-text">Marketing</span>
                </a>
            </li>
            @endif

            @if(auth()->user()->hasPermission('manage_sales_strategies'))
            <li class="nav-item">
                <a href="{{ route('admin.sales-strategies.index') }}" class="nav-link position-relative ps-2 py-1 {{ request()->routeIs('admin.sales-strategies.index') ? 'active' : '' }}">
                    <i class="fas fa-bullseye text-red-500 me-2"></i>
                    <span class="nav-link-text">Sales Strategies</span>
                </a>
            </li>
            @endif

            @if(auth()->user()->hasPermission('manage_business_setups'))
            <li class="nav-item">
                <a href="{{ route('admin.business-setups.index') }}" class="nav-link position-relative ps-2 py-1 {{ request()->routeIs('admin.business-setups.index') ? 'active' : '' }}">
                    <i class="fas fa-building text-amber-500 me-2"></i>
                    <span class="nav-link-text">Business Setups</span>
                </a>
            </li>
            @endif

            @if(auth()->user()->hasPermission('manage_financial_planners'))
            <li class="nav-item">
                <a href="{{ route('admin.financial_planners.index') }}" class="nav-link position-relative ps-2 py-1 {{ request()->routeIs('admin.financial_planners.index') ? 'active' : '' }}">
                    <i class="fas fa-chart-pie text-teal-500 me-2"></i>
                    <span class="nav-link-text">Financial Planners</span>
                </a>
            </li>
            @endif

            @if(auth()->user()->hasPermission('manage_websites'))
            <li class="nav-item">
                <a href="{{ route('admin.websites.index') }}" class="nav-link position-relative ps-2 py-1 {{ request()->routeIs('admin.websites.index') ? 'active' : '' }}">
                    <i class="fas fa-globe text-cyan-500 me-2"></i>
                    <span class="nav-link-text">Websites</span>
                </a>
            </li>
            @endif

            @if(auth()->user()->hasPermission('manage_stories'))
            <li class="nav-item">
                <a href="{{ route('admin.stories.index') }}" class="nav-link position-relative ps-2 py-1 {{ request()->routeIs('admin.stories.index') ? 'active' : '' }}">
                    <i class="fas fa-book-open text-purple-500 me-2"></i>
                    <span class="nav-link-text">Success Stories</span>
                </a>
            </li>
            @endif
        </ul>
    </li>
    @endif
</ul>
