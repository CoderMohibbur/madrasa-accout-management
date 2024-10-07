<nav x-data="{ open: false }" class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800 dark:text-gray-900" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>
                     <!-- Students -->
                     <div class="hidden sm:flex sm:items-center sm:ms-6">
                        <x-dropdown>
                            <x-slot name="trigger">
                                <button
                                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-black-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-green-500 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150">
                                    <div>
                                        <h1>Students</h1>
                                    </div>

                                    <div class="ms-1">
                                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </button>
                            </x-slot>

                            <x-slot name="content">
                                <x-dropdown-link :href="route('students.index')" :active="request()->routeIs('students.index')">
                                    {{ __('List Student') }}
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('students.create')" :active="request()->routeIs('students.index')">
                                    {{ __('Add Student') }}
                                </x-dropdown-link>

                            </x-slot>
                        </x-dropdown>
                    </div>
                     <!-- Doner -->
                     <div class="hidden sm:flex sm:items-center sm:ms-6">
                        <x-dropdown>
                            <x-slot name="trigger">
                                <button
                                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-black-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-red-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150">
                                    <div>
                                        <h1>Doner</h1>
                                    </div>

                                    <div class="ms-1">
                                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </button>
                            </x-slot>

                            <x-slot name="content">
                                <x-dropdown-link :href="route('donors.index')" :active="request()->routeIs('donors.index')">
                                    {{ __('List Doner') }}
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('donors.create')" :active="request()->routeIs('donors.index')">
                                    {{ __('Add Doner') }}
                                </x-dropdown-link>

                            </x-slot>
                        </x-dropdown>
                    </div>

                     <!-- Account -->
                     <div class="hidden sm:flex sm:items-center sm:ms-6">
                        <x-dropdown>
                            <x-slot name="trigger">
                                <button
                                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-black-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-purple-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150">
                                    <div>
                                        <h1>Account</h1>
                                    </div>

                                    <div class="ms-1">
                                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </button>
                            </x-slot>

                            <x-slot name="content">
                                <x-dropdown-link :href="route('account.index')" :active="request()->routeIs('account.index')">
                                    {{ __('List Account') }}
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('account.create')" :active="request()->routeIs('account.index')">
                                    {{ __('Add Account') }}
                                </x-dropdown-link>



                            </x-slot>
                        </x-dropdown>
                    </div>

                     <!-- Expen -->
                     <div class="hidden sm:flex sm:items-center sm:ms-6">
                        <x-dropdown>
                            <x-slot name="trigger">
                                <button
                                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-black-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-yellow-400 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150">
                                    <div>
                                        <h1>Expense</h1>
                                    </div>

                                    <div class="ms-1">
                                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </button>
                            </x-slot>

                            <x-slot name="content">
                                <x-dropdown-link :href="route('expens.index')" :active="request()->routeIs('expens.index')">
                                    {{ __('List Expense') }}
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('expens.create')" :active="request()->routeIs('expens.index')">
                                    {{ __('Add Expense') }}
                                </x-dropdown-link>

                                <x-dropdown-link :href="route('add_catagory.index')" :active="request()->routeIs('add-catagory')">
                                    {{ __('Add catagory') }}
                                </x-dropdown-link>



                            </x-slot>
                        </x-dropdown>
                    </div>
                     <!-- income -->
                     <div class="hidden sm:flex sm:items-center sm:ms-6">
                        <x-dropdown>
                            <x-slot name="trigger">
                                <button
                                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-black-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-orange-600 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150">
                                    <div>
                                        <h1>Income</h1>
                                    </div>

                                    <div class="ms-1">
                                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </button>
                            </x-slot>

                            <x-slot name="content">
                                <x-dropdown-link :href="route('income.index')" :active="request()->routeIs('income.index')">
                                    {{ __('List Income') }}
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('income.create')" :active="request()->routeIs('income.index')">
                                    {{ __('Add Income') }}
                                </x-dropdown-link>
                            </x-slot>
                        </x-dropdown>
                    </div>
                      <!-- Transaction -->
                      <div class="hidden sm:flex sm:items-center sm:ms-6">
                        <x-dropdown>
                            <x-slot name="trigger">
                                <button
                                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-black-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-blue-600 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150">
                                    <div>
                                        <h1>Transaction</h1>
                                    </div>

                                    <div class="ms-1">
                                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </button>
                            </x-slot>

                            <x-slot name="content">
                                <x-dropdown-link :href="route('income.index')" :active="request()->routeIs('income.index')">
                                    {{ __('List Income') }}
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('income.create')" :active="request()->routeIs('income.index')">
                                    {{ __('Add Income') }}
                                </x-dropdown-link>
                            </x-slot>
                        </x-dropdown>
                    </div>
                    <!-- Settings -->
                    <div class="hidden sm:flex sm:items-center sm:ms-6">
                        <x-dropdown>
                            <x-slot name="trigger">
                                <button
                                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-black-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-lime-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150">
                                    <div>
                                        <h1>Settings</h1>
                                    </div>

                                    <div class="ms-1">
                                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </button>
                            </x-slot>

                            <x-slot name="content">
                                <x-dropdown-link :href="route('add_class.index')" :active="request()->routeIs('add_class')">
                                    {{ __('Add Class') }}
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('add_month.index')" :active="request()->routeIs('add_month')">
                                    {{ __('Add Month') }}
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('add_academy.index')" :active="request()->routeIs('add-academy')">
                                    {{ __('Add Academy Year') }}
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('accounting.index')" :active="request()->routeIs('accounting')">
                                    {{ __('Add Accounting') }}
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('add_fees_type.index')" :active="request()->routeIs('add_fees_type.index')">
                                    {{ __('Add Fees Type') }}
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('Section.index')" :active="request()->routeIs('Section.index')">
                                    {{ __('Add Section') }}
                                </x-dropdown-link>
                            </x-slot>
                        </x-dropdown>
                    </div>

                </div>
            </div>



            <!-- Profile Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button
                            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open"
                    class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-900 focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{ 'hidden': open, 'inline-flex': !open }" class="inline-flex"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{ 'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round"
                            stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{ 'block': open, 'hidden': !open }" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('add_class.index')" :active="request()->routeIs('add_class.index')">
                {{ __('Add Class') }}
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800 dark:text-gray-200">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                        onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
