<nav x-data="{
    open: false,
    mQuick: false,
    mReports: false,
    mStudents: false,
    mDonor: false,
    mLender: false,
    mAccount: false,
    mExpense: false,
    mIncome: false,
    mSettings: false,
}"
    class="sticky top-0 z-50 border-b border-slate-200 bg-white/90 backdrop-blur dark:bg-slate-900/80 dark:border-white/10">
    @php
        // ✅ Active flags (web.php অনুযায়ী)
        $studentsActive = request()->routeIs(
            'students.*',
            'student_fees.*',
            'add_student_fees.*',
            'boarding.students.*',
        );
        $donorActive = request()->routeIs('donors.*', 'add_donar', 'edit_donor.*', 'update_donor.*', 'destroy_donor.*');
        $lenderActive = request()->routeIs(
            'lender.*',
            'add_loan*',
            'loan_repayment*',
            'loan_repayment_store.*',
            'edit_loan_repaymen.*',
            'update_loan_repayment.*',
            'destroy_loan_repayment.*',
        );
        $accountActive = request()->routeIs('account.*', 'account.chart');
        $expenseActive = request()->routeIs('expens.*', 'add_catagory.*');
        $incomeActive = request()->routeIs('income.*');
        $reportsActive = request()->routeIs('reports.*');
        $settingsActive = request()->routeIs(
            'settings.*',
            'add_class.*',
            'add_month.*',
            'add_academy.*',
            'add_fees_type.*',
            'add_transaction_type.*',
            'Section.*',
            'add_registration.*',
        );

        // ✅ Button base (Emerald focus)
        $btnBase = 'inline-flex items-center gap-1 rounded-xl px-3 py-2 text-sm font-medium
                    text-slate-700 dark:text-slate-200
                    hover:bg-slate-50 dark:hover:bg-white/5
                    focus:outline-none focus:ring-2 focus:ring-emerald-500 transition';

        // ✅ Trigger active style
        $btnActive =
            'bg-emerald-50 text-emerald-900 border border-emerald-200 dark:bg-emerald-500/10 dark:border-emerald-400/30';
        $btnIdle = 'border border-transparent';
    @endphp

    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 items-center justify-between gap-3">

            <!-- Left -->
            <div class="flex items-center gap-3">
                <!-- Logo -->
                <a href="{{ route('dashboard') }}" class="shrink-0 flex items-center">
                    <img src="{{ asset('img/image.png') }}" alt="Logo" class="ml-2 h-8 w-auto">
                </a>

                <!-- Desktop Links -->
                <div class="hidden lg:flex items-center gap-1">

                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        Dashboard
                    </x-nav-link>

                    <x-nav-link :href="route('transactions.center')" :active="request()->routeIs('transactions.center')">
                        Transaction Center
                    </x-nav-link>

                    <!-- Reports -->
                    <x-dropdown align="left" width="64">
                        <x-slot name="trigger">
                            <button class="{{ $btnBase }} {{ $reportsActive ? $btnActive : $btnIdle }}">
                                <span>Reports</span>
                                <svg class="h-4 w-4 opacity-70" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="route('reports.transactions')" :active="request()->routeIs('reports.transactions')">
                                Transactions Report
                            </x-dropdown-link>

                            <x-dropdown-link :href="route('reports.monthly-statement')" :active="request()->routeIs('reports.monthly-statement')">
                                Monthly Statement
                            </x-dropdown-link>

                            <x-dropdown-link :href="route('reports.yearly-summary')" :active="request()->routeIs('reports.yearly-summary')">
                                Yearly Summary
                            </x-dropdown-link>

                            <x-dropdown-link :href="route('reports.expense-report')" :active="request()->routeIs('reports.expense-report')">
                                Expense Report
                            </x-dropdown-link>
                        </x-slot>
                    </x-dropdown>

                    <!-- Students -->
                    <x-dropdown align="left" width="64">
                        <x-slot name="trigger">
                            <button class="{{ $btnBase }} {{ $studentsActive ? $btnActive : $btnIdle }}">
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
                                List Students
                            </x-dropdown-link>

                            <x-dropdown-link :href="route('students.create')" :active="request()->routeIs('students.create')">
                                Add Student
                            </x-dropdown-link>

                            <div class="my-1 border-t border-slate-100 dark:border-white/10"></div>

                            <x-dropdown-link :href="route('boarding.students.index')" :active="request()->routeIs('boarding.students.index')">
                                Boarding Students
                            </x-dropdown-link>

                            <div class="my-1 border-t border-slate-100 dark:border-white/10"></div>

                        </x-slot>
                    </x-dropdown>

                    <!-- Donor -->
                    <x-dropdown align="left" width="56">
                        <x-slot name="trigger">
                            <button class="{{ $btnBase }} {{ $donorActive ? $btnActive : $btnIdle }}">
                                <span>Donor</span>
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
                                List Donors
                            </x-dropdown-link>

                            <x-dropdown-link :href="route('donors.create')" :active="request()->routeIs('donors.create')">
                                Add Donor
                            </x-dropdown-link>

                            <div class="my-1 border-t border-slate-100 dark:border-white/10"></div>
                        </x-slot>
                    </x-dropdown>

                    <!-- Lender -->
                    <x-dropdown align="left" width="56">
                        <x-slot name="trigger">
                            <button class="{{ $btnBase }} {{ $lenderActive ? $btnActive : $btnIdle }}">
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
                                List Lenders
                            </x-dropdown-link>

                            <x-dropdown-link :href="route('lender.create')" :active="request()->routeIs('lender.create')">
                                Add Lender
                            </x-dropdown-link>

                            <div class="my-1 border-t border-slate-100 dark:border-white/10"></div>

                            <x-dropdown-link :href="route('add_loan')" :active="request()->routeIs('add_loan')">
                                Add Loan
                            </x-dropdown-link>

                            <x-dropdown-link :href="route('loan_repayment')" :active="request()->routeIs('loan_repayment')">
                                Loan Repayment
                            </x-dropdown-link>
                        </x-slot>
                    </x-dropdown>

                    <!-- Account -->
                    <x-dropdown align="left" width="64">
                        <x-slot name="trigger">
                            <button class="{{ $btnBase }} {{ $accountActive ? $btnActive : $btnIdle }}">
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
                                List Accounts
                            </x-dropdown-link>

                            <x-dropdown-link :href="route('account.create')" :active="request()->routeIs('account.create')">
                                Add Account
                            </x-dropdown-link>

                            <x-dropdown-link :href="route('account.chart')" :active="request()->routeIs('account.chart')">
                                Chart Of Accounts
                            </x-dropdown-link>
                        </x-slot>
                    </x-dropdown>

                    <!-- Expense -->
                    <x-dropdown align="left" width="64">
                        <x-slot name="trigger">
                            <button class="{{ $btnBase }} {{ $expenseActive ? $btnActive : $btnIdle }}">
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
                                List Expense Heads
                            </x-dropdown-link>

                            <div class="my-1 border-t border-slate-100 dark:border-white/10"></div>

                            <x-dropdown-link :href="route('add_catagory.index')" :active="request()->routeIs('add_catagory.index')">
                                Expense Categories
                            </x-dropdown-link>
                        </x-slot>
                    </x-dropdown>

                    <!-- Income -->
                    <x-dropdown align="left" width="56">
                        <x-slot name="trigger">
                            <button class="{{ $btnBase }} {{ $incomeActive ? $btnActive : $btnIdle }}">
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
                                List Income Heads
                            </x-dropdown-link>

                            <x-dropdown-link :href="route('income.create')" :active="request()->routeIs('income.create')">
                                Add Income Head
                            </x-dropdown-link>
                        </x-slot>
                    </x-dropdown>

                    <!-- Settings -->
                    <x-dropdown align="left" width="72">
                        <x-slot name="trigger">
                            <button class="{{ $btnBase }} {{ $settingsActive ? $btnActive : $btnIdle }}">
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
                            <x-dropdown-link :href="route('settings.index')" :active="request()->routeIs('settings.index')">
                                Calender
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
                                class="inline-flex items-center gap-2 rounded-xl px-3 py-2 text-sm font-medium
                                           text-slate-700 dark:text-slate-200
                                           hover:bg-slate-50 dark:hover:bg-white/5
                                           focus:outline-none focus:ring-2 focus:ring-emerald-500 transition">
                                <span class="max-w-[160px] truncate">{{ Auth::user()->name }}</span>
                                <svg class="h-4 w-4 opacity-70" xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            {{-- <x-dropdown-link :href="route('profile.edit')">
                                Profile
                            </x-dropdown-link> --}}

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault(); this.closest('form').submit();">
                                    Log Out
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                </div>

                <!-- Hamburger (mobile/tablet) -->
                <div class="lg:hidden">
                    <button @click="open = !open"
                        class="inline-flex items-center justify-center rounded-xl p-2
                               text-slate-500 hover:bg-slate-100 hover:text-slate-700
                               dark:text-slate-300 dark:hover:bg-white/5
                               focus:outline-none focus:ring-2 focus:ring-emerald-500 transition"
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
        class="lg:hidden border-t border-slate-200 dark:border-white/10 bg-white dark:bg-slate-900" x-cloak>
        <div class="px-4 py-3 space-y-2">

            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                Dashboard
            </x-responsive-nav-link>

            <!-- Quick Links -->
            <div class="rounded-2xl border border-slate-200 dark:border-white/10 overflow-hidden">
                <button @click="mQuick=!mQuick"
                    class="w-full flex items-center justify-between px-4 py-3 text-sm font-semibold text-slate-700 dark:text-slate-200">
                    <span>Quick</span>
                    <svg class="h-4 w-4 transition" :class="{ 'rotate-180': mQuick }"
                        xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                            clip-rule="evenodd" />
                    </svg>
                </button>
                <div x-show="mQuick" x-transition class="px-3 pb-3 space-y-1">
                    <x-responsive-nav-link :href="route('transactions.center')" :active="request()->routeIs('transactions.center')">
                        Transaction Center
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('reports.transactions')" :active="request()->routeIs('reports.transactions')">
                        Transactions Report
                    </x-responsive-nav-link>
                </div>
            </div>

            <!-- Reports -->
            <div class="rounded-2xl border border-slate-200 dark:border-white/10 overflow-hidden">
                <button @click="mReports=!mReports"
                    class="w-full flex items-center justify-between px-4 py-3 text-sm font-semibold text-slate-700 dark:text-slate-200">
                    <span>Reports</span>
                    <svg class="h-4 w-4 transition" :class="{ 'rotate-180': mReports }"
                        xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                            clip-rule="evenodd" />
                    </svg>
                </button>
                <div x-show="mReports" x-transition class="px-3 pb-3 space-y-1">
                    <x-responsive-nav-link :href="route('reports.transactions')" :active="request()->routeIs('reports.transactions')">
                        Transactions Report
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('reports.monthly-statement')" :active="request()->routeIs('reports.monthly-statement')">
                        Monthly Statement
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('reports.yearly-summary')" :active="request()->routeIs('reports.yearly-summary')">
                        Yearly Summary
                    </x-responsive-nav-link>

                    <x-responsive-nav-link :href="route('reports.expense-report')" :active="request()->routeIs('reports.yearly-summary')">
                        Expense Report
                    </x-responsive-nav-link>

                </div>
            </div>

            <!-- Students -->
            <div class="rounded-2xl border border-slate-200 dark:border-white/10 overflow-hidden">
                <button @click="mStudents=!mStudents"
                    class="w-full flex items-center justify-between px-4 py-3 text-sm font-semibold text-slate-700 dark:text-slate-200">
                    <span>Students</span>
                    <svg class="h-4 w-4 transition" :class="{ 'rotate-180': mStudents }"
                        xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                            clip-rule="evenodd" />
                    </svg>
                </button>
                <div x-show="mStudents" x-transition class="px-3 pb-3 space-y-1">
                    <x-responsive-nav-link :href="route('students.index')" :active="request()->routeIs('students.index')">
                        List Students
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('students.create')" :active="request()->routeIs('students.create')">
                        Add Student
                    </x-responsive-nav-link>

                    <div class="my-1 border-t border-slate-200 dark:border-white/10"></div>

                    <x-responsive-nav-link :href="route('boarding.students.index')" :active="request()->routeIs('boarding.students.index')">
                        Boarding Students
                    </x-responsive-nav-link>

                    <div class="my-1 border-t border-slate-200 dark:border-white/10"></div>

                </div>
            </div>

            <!-- Donor -->
            <div class="rounded-2xl border border-slate-200 dark:border-white/10 overflow-hidden">
                <button @click="mDonor=!mDonor"
                    class="w-full flex items-center justify-between px-4 py-3 text-sm font-semibold text-slate-700 dark:text-slate-200">
                    <span>Donor</span>
                    <svg class="h-4 w-4 transition" :class="{ 'rotate-180': mDonor }"
                        xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                            clip-rule="evenodd" />
                    </svg>
                </button>
                <div x-show="mDonor" x-transition class="px-3 pb-3 space-y-1">
                    <x-responsive-nav-link :href="route('donors.index')" :active="request()->routeIs('donors.index')">
                        List Donors
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('donors.create')" :active="request()->routeIs('donors.create')">
                        Add Donor
                    </x-responsive-nav-link>
                </div>
            </div>

            <!-- Lender -->
            <div class="rounded-2xl border border-slate-200 dark:border-white/10 overflow-hidden">
                <button @click="mLender=!mLender"
                    class="w-full flex items-center justify-between px-4 py-3 text-sm font-semibold text-slate-700 dark:text-slate-200">
                    <span>Lender</span>
                    <svg class="h-4 w-4 transition" :class="{ 'rotate-180': mLender }"
                        xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                            clip-rule="evenodd" />
                    </svg>
                </button>
                <div x-show="mLender" x-transition class="px-3 pb-3 space-y-1">
                    <x-responsive-nav-link :href="route('lender.index')" :active="request()->routeIs('lender.index')">
                        List Lenders
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('lender.create')" :active="request()->routeIs('lender.create')">
                        Add Lender
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('add_loan')" :active="request()->routeIs('add_loan')">
                        Add Loan
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('loan_repayment')" :active="request()->routeIs('loan_repayment')">
                        Loan Repayment
                    </x-responsive-nav-link>
                </div>
            </div>

            <!-- Account -->
            <div class="rounded-2xl border border-slate-200 dark:border-white/10 overflow-hidden">
                <button @click="mAccount=!mAccount"
                    class="w-full flex items-center justify-between px-4 py-3 text-sm font-semibold text-slate-700 dark:text-slate-200">
                    <span>Account</span>
                    <svg class="h-4 w-4 transition" :class="{ 'rotate-180': mAccount }"
                        xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                            clip-rule="evenodd" />
                    </svg>
                </button>
                <div x-show="mAccount" x-transition class="px-3 pb-3 space-y-1">
                    <x-responsive-nav-link :href="route('account.index')" :active="request()->routeIs('account.index')">
                        List Accounts
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('account.create')" :active="request()->routeIs('account.create')">
                        Add Account
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('account.chart')" :active="request()->routeIs('account.chart')">
                        Chart Of Accounts
                    </x-responsive-nav-link>
                </div>
            </div>

            <!-- Expense -->
            <div class="rounded-2xl border border-slate-200 dark:border-white/10 overflow-hidden">
                <button @click="mExpense=!mExpense"
                    class="w-full flex items-center justify-between px-4 py-3 text-sm font-semibold text-slate-700 dark:text-slate-200">
                    <span>Expense</span>
                    <svg class="h-4 w-4 transition" :class="{ 'rotate-180': mExpense }"
                        xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                            clip-rule="evenodd" />
                    </svg>
                </button>
                <div x-show="mExpense" x-transition class="px-3 pb-3 space-y-1">
                    <x-responsive-nav-link :href="route('expens.index')" :active="request()->routeIs('expens.index')">
                        List Expense Heads
                    </x-responsive-nav-link>

                    <x-responsive-nav-link :href="route('add_catagory.index')" :active="request()->routeIs('add_catagory.index')">
                        Expense Categories
                    </x-responsive-nav-link>
                </div>
            </div>

            <!-- Income -->
            <div class="rounded-2xl border border-slate-200 dark:border-white/10 overflow-hidden">
                <button @click="mIncome=!mIncome"
                    class="w-full flex items-center justify-between px-4 py-3 text-sm font-semibold text-slate-700 dark:text-slate-200">
                    <span>Income</span>
                    <svg class="h-4 w-4 transition" :class="{ 'rotate-180': mIncome }"
                        xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                            clip-rule="evenodd" />
                    </svg>
                </button>
                <div x-show="mIncome" x-transition class="px-3 pb-3 space-y-1">
                    <x-responsive-nav-link :href="route('income.index')" :active="request()->routeIs('income.index')">
                        List Income Heads
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('income.create')" :active="request()->routeIs('income.create')">
                        Add Income Head
                    </x-responsive-nav-link>
                </div>
            </div>

            <!-- Settings -->
            <div class="rounded-2xl border border-slate-200 dark:border-white/10 overflow-hidden">
                <button @click="mSettings=!mSettings"
                    class="w-full flex items-center justify-between px-4 py-3 text-sm font-semibold text-slate-700 dark:text-slate-200">
                    <span>Settings</span>
                    <svg class="h-4 w-4 transition" :class="{ 'rotate-180': mSettings }"
                        xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                            clip-rule="evenodd" />
                    </svg>
                </button>
                <div x-show="mSettings" x-transition class="px-3 pb-3 space-y-1">
                    <x-responsive-nav-link :href="route('settings.index')" :active="request()->routeIs('settings.index')">
                        Calender
                    </x-responsive-nav-link>
                </div>
            </div>

        </div>

        <!-- Mobile Profile Area -->
        <div class="border-t border-slate-200 dark:border-white/10 px-4 py-4">
            <div class="font-medium text-base text-slate-800 dark:text-slate-200">{{ Auth::user()->name }}</div>
            <div class="font-medium text-sm text-slate-500 dark:text-slate-400">{{ Auth::user()->email }}</div>

            <div class="mt-3 space-y-1">
                {{-- <x-responsive-nav-link :href="route('profile.edit')">
                    Profile
                </x-responsive-nav-link> --}}

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                        onclick="event.preventDefault(); this.closest('form').submit();">
                        Log Out
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
