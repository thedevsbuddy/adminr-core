{{--@if(request()->segment(2) != 'manage') c-sidebar-minimized @endif--}}
<div class="c-sidebar c-sidebar-dark c-sidebar-fixed c-sidebar-lg-show @if(activeRoute('adminr.builder')) c-sidebar-minimized @endif" id="sidebar">
    <div class="c-sidebar-brand d-lg-down-none">
        <span class="c-sidebar-brand-full d-flex align-items-center">
            @if(!is_null(getSetting('site_logo')))
                <img src="{{ asset(getSetting('site_logo')) }}" class="mr-2 rounded" style="max-height: 35px; width: auto" alt="">
            @endif
            <h4 class="m-0">{{ getSetting('site_name') }}</h4>
        </span>
        <span class="c-sidebar-brand-minimized">
            @if(!is_null(getSetting('site_logo')))
                <img src="{{ asset(getSetting('site_logo')) }}" class="rounded" style="max-height: 30px; width: auto" alt="">
            @else
                {{ config('app.shortname') }}
            @endif
            </span>
    </div>
    <ul class="c-sidebar-nav ps ps--active-y">
        <li class="c-sidebar-nav-item">
            <a class="c-sidebar-nav-link" href="{{ route('adminr.index') }}">
                <svg class="c-sidebar-nav-icon">
                    <use xlink:href="{{ coreUiIcon('cil-speedometer') }}"></use>
                </svg>
                {{ __('Dashboard') }}
            </a>
        </li>
        @can('create_crud')
            <li class="c-sidebar-nav-item">
                <a class="c-sidebar-nav-link" href="{{ route('adminr.builder') }}">
                    <svg class="c-sidebar-nav-icon">
                        <use xlink:href="{{ coreUiIcon('cil-diamond') }}"></use>
                    </svg>
                    {{ __('Builder') }}
                </a>
            </li>
            <li class="c-sidebar-nav-item">
                <a class="c-sidebar-nav-link" href="{{ route('adminr.cruds.index') }}">
                    <svg class="c-sidebar-nav-icon">
                        <use xlink:href="{{ coreUiIcon('cil-apps') }}"></use>
                    </svg>
                    {{ __('Generated Resources') }}
                </a>
            </li>
        @endcan
        @can('manage_settings')
            <li class="c-sidebar-nav-item">
                <a class="c-sidebar-nav-link" href="{{ route('adminr.settings.index') }}">
                    <svg class="c-sidebar-nav-icon">
                        <use xlink:href="{{ coreUiIcon('cil-settings') }}"></use>
                    </svg>
                    {{ __('Settings') }}
                </a>
            </li>
        @endcan
        <li class="c-sidebar-nav-title">Permissible</li>
        @can('manage_permissions')
            <li class="c-sidebar-nav-item">
                <a class="c-sidebar-nav-link" href="javascript:void(0)" onclick="alert('Coming soon!')">
                    <svg class="c-sidebar-nav-icon">
                        <use xlink:href="{{ coreUiIcon('cil-star') }}"></use>
                    </svg>
                    {{ __('Roles & Permissions') }}
                </a>
            </li>
        @endcan
        @can('manage_users')
            <li class="c-sidebar-nav-item">
                <a class="c-sidebar-nav-link {{ returnIfRoutes(['adminr.users.index', 'adminr.users.create', 'adminr.users.edit'], 'c-active') }}" href="{{ route('adminr.users.index') }}">
                    <svg class="c-sidebar-nav-icon">
                        <use xlink:href="{{ coreUiIcon('cil-user') }}"></use>
                    </svg>
                    {{ __('Users') }}
                </a>
            </li>
        @endcan

        <li class="c-sidebar-nav-title">Resources</li>
        @forelse($liquid_cruds as $crud)
            @can(strtolower($crud->name) . '_show_list')
                <li class="c-sidebar-nav-item">
                    <a class="c-sidebar-nav-link {{ returnIfRoutes(['adminr.'.strtolower($crud->name).'.index', 'adminr.'.strtolower($crud->name).'.create', 'adminr.'.strtolower($crud->name).'.edit'], 'c-active') }}" href="{{ route($crud->menu->route) }}">
                        <svg class="c-sidebar-nav-icon">
                            <use xlink:href="{{ coreUiIcon('cil-folder') }}"></use>
                        </svg>
                        {{ ucfirst($crud->menu->label) }}
                    </a>
                </li>
            @endcan
        @empty

        @endforelse

{{--        <li class="c-sidebar-nav-item c-sidebar-nav-dropdown">--}}
{{--            <a class="c-sidebar-nav-link c-sidebar-nav-dropdown-toggle" href="javascript:void(0)">--}}
{{--                <svg class="c-sidebar-nav-icon">--}}
{{--                    <use xlink:href="{{ coreUiIcon('cil-puzzle') }}"></use>--}}
{{--                </svg>--}}
{{--                Base--}}
{{--            </a>--}}
{{--            <ul class="c-sidebar-nav-dropdown-items">--}}
{{--                <li class="c-sidebar-nav-item">--}}
{{--                    <a class="c-sidebar-nav-link" href="base/breadcrumb.html">--}}
{{--                        <svg class="c-sidebar-nav-icon">--}}
{{--                            <use xlink:href="{{ coreUiIcon('cil-puzzle') }}"></use>--}}
{{--                        </svg>--}}
{{--                        Breadcrumb--}}
{{--                    </a>--}}
{{--                </li>--}}
{{--            </ul>--}}
{{--        </li>--}}

    </ul>
    <button class="c-sidebar-minimizer c-class-toggler" type="button" data-target="_parent"
            data-class="c-sidebar-minimized"></button>
</div>