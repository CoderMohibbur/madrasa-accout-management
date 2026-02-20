<nav x-data="{
    open: false,
    mStudents: false,
    mDonor: false,
    mLender: false,
    mAccount: false,
    mExpense: false,
    mIncome: false,
    mSettings: false,
}"
    class="sticky top-0 z-50 border-b border-slate-200 bg-white/90 backdrop-blur dark:bg-gray-800/90 dark:border-gray-700">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 items-center justify-between gap-3">
            <!-- Left -->
            <div class="flex items-center gap-3">
                <!-- Logo -->
                <a href="{{ route('dashboard') }}" class="shrink-0 flex items-center">
                    <x-application-logo class="block h-9 w-auto fill-current text-gray-800 dark:text-gray-100" />
                </a>

                <!-- Desktop Links -->
                <div class="hidden lg:flex items-center gap-1">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}

                    </x-nav-link>

                    @php
                        $btnBase =
                            'inline-flex items-center gap-1 rounded-xl px-3 py-2 text-sm font-medium text-slate-700 dark:text-gray-200 hover:bg-slate-50 dark:hover:bg-gray-700/60 focus:outline-none focus:ring-2 focus:ring-slate-200 dark:focus:ring-gray-600 transition';
                    @endphp

                    <!-- Students -->
                    <x-dropdown align="left" width="56">
                        <x-slot name="trigger">
                            <button class="{{ $btnBase }}">
                                <span>Students</span>
                                <svg class="h-4 w-4 opacity-70" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </button>
                        </x-slot>
                        <x-slot name="content">
                            <x-dropdown-link :href="route('students.index')" :active="request()->routeIs('students.index')">
                                {{ __('List Student') }}
                            </x-dropdown-link>
                            <x-dropdown-link href="{{ route('boarding.students.index') }}">
                                Boarding Students
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('students.create')" :active="request()->routeIs('students.create')">
                                {{ __('Add Student') }}
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('add_student_fees.index')" :active="request()->routeIs('add_student_fees.index')">
                                {{ __('Add Fees') }}
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('student_fees.show')" :active="request()->routeIs('student_fees.show')">
                                {{ __('Student Fees') }}
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('add_student_fees.list')" :active="request()->routeIs('add_student_fees.list')">
                                {{ __('List Fees') }}
                            </x-dropdown-link>
                        </x-slot>
                    </x-dropdown>

                    <!-- Doner -->
                    <x-dropdown align="left" width="56">
                        <x-slot name="trigger">
                            <button class="{{ $btnBase }}">
                                <span>Doner</span>
                                <svg class="h-4 w-4 opacity-70" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </button>
                        </x-slot>
                        <x-slot name="content">
                            <x-dropdown-link :href="route('donors.index')" :active="request()->routeIs('donors.index')">
                                {{ __('List Doner') }}
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('donors.create')" :active="request()->routeIs('donors.create')">
                                {{ __('Add Doner') }}
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('add_donar')" :active="request()->routeIs('add_donar')">
                                {{ __('Add Donation') }}
                            </x-dropdown-link>
                        </x-slot>
                    </x-dropdown>

                    <!-- Lender -->
                    <x-dropdown align="left" width="56">
                        <x-slot name="trigger">
                            <button class="{{ $btnBase }}">
                                <span>Lender</span>
                                <svg class="h-4 w-4 opacity-70" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </button>
                        </x-slot>
                        <x-slot name="content">
                            <x-dropdown-link :href="route('lender.index')" :active="request()->routeIs('lender.index')">
                                {{ __('List Lender') }}
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('lender.create')" :active="request()->routeIs('lender.create')">
                                {{ __('Add Lender') }}
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('add_loan')" :active="request()->routeIs('add_loan')">
                                {{ __('Add Loan') }}
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('loan_repayment')" :active="request()->routeIs('loan_repayment')">
                                {{ __('Loan Repayment') }}
                            </x-dropdown-link>
                        </x-slot>
                    </x-dropdown>

                    <!-- Account -->
                    <x-dropdown align="left" width="56">
                        <x-slot name="trigger">
                            <button class="{{ $btnBase }}">
                                <span>Account</span>
                                <svg class="h-4 w-4 opacity-70" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </button>
                        </x-slot>
                        <x-slot name="content">
                            <x-dropdown-link :href="route('account.index')" :active="request()->routeIs('account.index')">
                                {{ __('List Account') }}
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('account.create')" :active="request()->routeIs('account.create')">
                                {{ __('Add Account') }}
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('account.chart')" :active="request()->routeIs('account.chart')">
                                {{ __('Chart Of Accounts') }}
                            </x-dropdown-link>
                        </x-slot>
                    </x-dropdown>

                    <!-- Expense -->
                    <x-dropdown align="left" width="56">
                        <x-slot name="trigger">
                            <button class="{{ $btnBase }}">
                                <span>Expense</span>
                                <svg class="h-4 w-4 opacity-70" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </button>
                        </x-slot>
                        <x-slot name="content">
                            <x-dropdown-link :href="route('expens.index')" :active="request()->routeIs('expens.index')">
                                {{ __('List Expense') }}
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('expens.create')" :active="request()->routeIs('expens.create')">
                                {{ __('Add Expense') }}
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('add_catagory.index')" :active="request()->routeIs('add_catagory.index')">
                                {{ __('Add Category') }}
                            </x-dropdown-link>
                        </x-slot>
                    </x-dropdown>

                    <!-- Income -->
                    <x-dropdown align="left" width="56">
                        <x-slot name="trigger">
                            <button class="{{ $btnBase }}">
                                <span>Income</span>
                                <svg class="h-4 w-4 opacity-70" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </button>
                        </x-slot>
                        <x-slot name="content">
                            <x-dropdown-link :href="route('income.index')" :active="request()->routeIs('income.index')">
                                {{ __('List Income') }}
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('income.create')" :active="request()->routeIs('income.create')">
                                {{ __('Add Income') }}
                            </x-dropdown-link>
                        </x-slot>
                    </x-dropdown>

                    <!-- Settings -->
                    <x-dropdown align="left" width="64">
                        <x-slot name="trigger">
                            <button class="{{ $btnBase }}">
                                <span>Settings</span>
                                <svg class="h-4 w-4 opacity-70" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            {{-- Recommended: one hub page (if you created settings.index) --}}
                            @if (\Illuminate\Support\Facades\Route::has('settings.index'))
                                <x-dropdown-link :href="route('settings.index')" :active="request()->routeIs('settings.index')">
                                    {{ __('Manage Settings') }}
                                </x-dropdown-link>
                                <div class="my-1 border-t border-slate-100 dark:border-gray-700"></div>
                            @endif

                            {{-- Legacy links (keep if you still use them) --}}
                            <x-dropdown-link :href="route('add_class.index')" :active="request()->routeIs('add_class.index')">
                                {{ __('Add Class') }}
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('add_month.index')" :active="request()->routeIs('add_month.index')">
                                {{ __('Add Month') }}
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('add_academy.index')" :active="request()->routeIs('add_academy.index')">
                                {{ __('Add Academy Year') }}
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('add_fees_type.index')" :active="request()->routeIs('add_fees_type.index')">
                                {{ __('Add Fees Type') }}
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('add_transaction_type.index')" :active="request()->routeIs('add_transaction_type.index')">
                                {{ __('Add Transactions Type') }}
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('Section.index')" :active="request()->routeIs('Section.index')">
                                {{ __('Add Section') }}
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('add_registration.index')" :active="request()->routeIs('add_registration.index')">
                                {{ __('Add Registration Fees') }}
                            </x-dropdown-link>
                        </x-slot>
                    </x-dropdown>
                </div>
            </div>

            <!-- Right -->
            <div class="flex items-center gap-2">
                <!-- Profile Dropdown (desktop) -->
                <div class="hidden lg:flex lg:items-center">
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button
                                class="inline-flex items-center gap-2 rounded-xl px-3 py-2 text-sm font-medium text-slate-700 dark:text-gray-200 hover:bg-slate-50 dark:hover:bg-gray-700/60 focus:outline-none focus:ring-2 focus:ring-slate-200 dark:focus:ring-gray-600 transition">
                                <span class="max-w-[160px] truncate">{{ Auth::user()->name }}</span>
                                <svg class="h-4 w-4 opacity-70" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="route('profile.edit')">
                                {{ __('Profile') }}
                            </x-dropdown-link>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault(); this.closest('form').submit();">
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                </div>

                <!-- Hamburger (mobile/tablet) -->
                <div class="lg:hidden">
                    <button @click="open = !open"
                        class="inline-flex items-center justify-center rounded-xl p-2 text-slate-500 hover:bg-slate-100 hover:text-slate-700 dark:text-gray-300 dark:hover:bg-gray-700/60 focus:outline-none focus:ring-2 focus:ring-slate-200 dark:focus:ring-gray-600 transition"
                        aria-label="Open menu">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path :class="{ 'hidden': open, 'inline-flex': !open }" class="inline-flex"
                                stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                            <path :class="{ 'hidden': !open, 'inline-flex': open }" class="hidden"
                                stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Mobile / Tablet Menu -->
    <div x-show="open" x-transition
        class="lg:hidden border-t border-slate-200 dark:border-gray-700 bg-white dark:bg-gray-800" x-cloak>
        <div class="px-4 py-3 space-y-2">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>

            <!-- Students -->
            <div class="rounded-2xl border border-slate-200 dark:border-gray-700 overflow-hidden">
                <button @click="mStudents=!mStudents"
                    class="w-full flex items-center justify-between px-4 py-3 text-sm font-semibold text-slate-700 dark:text-gray-200">
                    <span>Students</span>
                    <svg class="h-4 w-4" :class="{ 'rotate-180': mStudents }" xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                            clip-rule="evenodd" />
                    </svg>
                </button>
                <div x-show="mStudents" x-transition class="px-3 pb-3 space-y-1">
                    <x-responsive-nav-link :href="route('students.index')"
                        :active="request()->routeIs('students.index')">{{ __('List Student') }}</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('students.create')"
                        :active="request()->routeIs('students.create')">{{ __('Add Student') }}</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('add_student_fees.index')"
                        :active="request()->routeIs('add_student_fees.index')">{{ __('Add Fees') }}</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('student_fees.show')"
                        :active="request()->routeIs('student_fees.show')">{{ __('Student Fees') }}</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('add_student_fees.list')"
                        :active="request()->routeIs('add_student_fees.list')">{{ __('List Fees') }}</x-responsive-nav-link>
                </div>
            </div>

            <!-- Doner -->
            <div class="rounded-2xl border border-slate-200 dark:border-gray-700 overflow-hidden">
                <button @click="mDonor=!mDonor"
                    class="w-full flex items-center justify-between px-4 py-3 text-sm font-semibold text-slate-700 dark:text-gray-200">
                    <span>Doner</span>
                    <svg class="h-4 w-4" :class="{ 'rotate-180': mDonor }" xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                            clip-rule="evenodd" />
                    </svg>
                </button>
                <div x-show="mDonor" x-transition class="px-3 pb-3 space-y-1">
                    <x-responsive-nav-link :href="route('donors.index')"
                        :active="request()->routeIs('donors.index')">{{ __('List Doner') }}</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('donors.create')"
                        :active="request()->routeIs('donors.create')">{{ __('Add Doner') }}</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('add_donar')"
                        :active="request()->routeIs('add_donar')">{{ __('Add Donation') }}</x-responsive-nav-link>
                </div>
            </div>

            <!-- Lender -->
            <div class="rounded-2xl border border-slate-200 dark:border-gray-700 overflow-hidden">
                <button @click="mLender=!mLender"
                    class="w-full flex items-center justify-between px-4 py-3 text-sm font-semibold text-slate-700 dark:text-gray-200">
                    <span>Lender</span>
                    <svg class="h-4 w-4" :class="{ 'rotate-180': mLender }" xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                            clip-rule="evenodd" />
                    </svg>
                </button>
                <div x-show="mLender" x-transition class="px-3 pb-3 space-y-1">
                    <x-responsive-nav-link :href="route('lender.index')"
                        :active="request()->routeIs('lender.index')">{{ __('List Lender') }}</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('lender.create')"
                        :active="request()->routeIs('lender.create')">{{ __('Add Lender') }}</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('add_loan')"
                        :active="request()->routeIs('add_loan')">{{ __('Add Loan') }}</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('loan_repayment')"
                        :active="request()->routeIs('loan_repayment')">{{ __('Loan Repayment') }}</x-responsive-nav-link>
                </div>
            </div>

            <!-- Account -->
            <div class="rounded-2xl border border-slate-200 dark:border-gray-700 overflow-hidden">
                <button @click="mAccount=!mAccount"
                    class="w-full flex items-center justify-between px-4 py-3 text-sm font-semibold text-slate-700 dark:text-gray-200">
                    <span>Account</span>
                    <svg class="h-4 w-4" :class="{ 'rotate-180': mAccount }" xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                            clip-rule="evenodd" />
                    </svg>
                </button>
                <div x-show="mAccount" x-transition class="px-3 pb-3 space-y-1">
                    <x-responsive-nav-link :href="route('account.index')"
                        :active="request()->routeIs('account.index')">{{ __('List Account') }}</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('account.create')"
                        :active="request()->routeIs('account.create')">{{ __('Add Account') }}</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('account.chart')"
                        :active="request()->routeIs('account.chart')">{{ __('Chart Of Accounts') }}</x-responsive-nav-link>
                </div>
            </div>

            <!-- Expense -->
            <div class="rounded-2xl border border-slate-200 dark:border-gray-700 overflow-hidden">
                <button @click="mExpense=!mExpense"
                    class="w-full flex items-center justify-between px-4 py-3 text-sm font-semibold text-slate-700 dark:text-gray-200">
                    <span>Expense</span>
                    <svg class="h-4 w-4" :class="{ 'rotate-180': mExpense }" xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                            clip-rule="evenodd" />
                    </svg>
                </button>
                <div x-show="mExpense" x-transition class="px-3 pb-3 space-y-1">
                    <x-responsive-nav-link :href="route('expens.index')"
                        :active="request()->routeIs('expens.index')">{{ __('List Expense') }}</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('expens.create')"
                        :active="request()->routeIs('expens.create')">{{ __('Add Expense') }}</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('add_catagory.index')"
                        :active="request()->routeIs('add_catagory.index')">{{ __('Add Category') }}</x-responsive-nav-link>
                </div>
            </div>

            <!-- Income -->
            <div class="rounded-2xl border border-slate-200 dark:border-gray-700 overflow-hidden">
                <button @click="mIncome=!mIncome"
                    class="w-full flex items-center justify-between px-4 py-3 text-sm font-semibold text-slate-700 dark:text-gray-200">
                    <span>Income</span>
                    <svg class="h-4 w-4" :class="{ 'rotate-180': mIncome }" xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                            clip-rule="evenodd" />
                    </svg>
                </button>
                <div x-show="mIncome" x-transition class="px-3 pb-3 space-y-1">
                    <x-responsive-nav-link :href="route('income.index')"
                        :active="request()->routeIs('income.index')">{{ __('List Income') }}</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('income.create')"
                        :active="request()->routeIs('income.create')">{{ __('Add Income') }}</x-responsive-nav-link>
                </div>
            </div>

            <!-- Settings -->
            <div class="rounded-2xl border border-slate-200 dark:border-gray-700 overflow-hidden">
                <button @click="mSettings=!mSettings"
                    class="w-full flex items-center justify-between px-4 py-3 text-sm font-semibold text-slate-700 dark:text-gray-200">
                    <span>Settings</span>
                    <svg class="h-4 w-4" :class="{ 'rotate-180': mSettings }" xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                            clip-rule="evenodd" />
                    </svg>
                </button>
                <div x-show="mSettings" x-transition class="px-3 pb-3 space-y-1">
                    @if (\Illuminate\Support\Facades\Route::has('settings.index'))
                        <x-responsive-nav-link :href="route('settings.index')" :active="request()->routeIs('settings.index')">
                            {{ __('Manage Settings') }}
                        </x-responsive-nav-link>
                        <div class="my-1 border-t border-slate-200 dark:border-gray-700"></div>
                    @endif

                    <x-responsive-nav-link :href="route('add_class.index')"
                        :active="request()->routeIs('add_class.index')">{{ __('Add Class') }}</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('add_month.index')"
                        :active="request()->routeIs('add_month.index')">{{ __('Add Month') }}</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('add_academy.index')"
                        :active="request()->routeIs('add_academy.index')">{{ __('Add Academy Year') }}</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('add_fees_type.index')"
                        :active="request()->routeIs('add_fees_type.index')">{{ __('Add Fees Type') }}</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('add_transaction_type.index')"
                        :active="request()->routeIs('add_transaction_type.index')">{{ __('Add Transactions Type') }}</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('Section.index')"
                        :active="request()->routeIs('Section.index')">{{ __('Add Section') }}</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('add_registration.index')"
                        :active="request()->routeIs('add_registration.index')">{{ __('Add Registration Fees') }}</x-responsive-nav-link>
                </div>
            </div>
        </div>

        <!-- Mobile Profile Area -->
        <div class="border-t border-slate-200 dark:border-gray-700 px-4 py-4">
            <div class="font-medium text-base text-slate-800 dark:text-gray-200">{{ Auth::user()->name }}</div>
            <div class="font-medium text-sm text-slate-500 dark:text-gray-400">{{ Auth::user()->email }}</div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                        onclick="event.preventDefault(); this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
