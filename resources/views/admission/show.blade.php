@php
    $navLinks = [
        ['label' => 'হোম', 'url' => url('/')],
        ['label' => 'ভর্তি', 'url' => route('admission')],
        ['label' => 'আমাদের সম্পর্কে', 'url' => 'https://attawheedic.com/about/'],
        ['label' => 'যোগাযোগ', 'url' => 'https://attawheedic.com/contact/'],
        ['label' => 'অনুদান', 'url' => route('donations.guest.entry')],
    ];
@endphp

<x-public-shell
    title="ভর্তি নির্দেশনা"
    description="ভর্তি-সংক্রান্ত নির্দেশনা এখানে দেখুন, তারপর অনুমোদিত বাহ্যিক application handoff-এ নিরাপদভাবে এগিয়ে যান।"
    :nav-links="$navLinks"
>
    <section class="space-y-6">
        <x-ui.page-header
            eyebrow="ভর্তি সহায়তা"
            title="প্রথমে নির্দেশনা, তারপর নিরাপদ বাহ্যিক handoff"
            description="এই public surface-এ ভর্তি-সংক্রান্ত প্রয়োজনীয় ধারণা, প্রস্তুতি ও সীমারেখা দেখানো হয়। প্রকৃত application submission এখনও বাহ্যিক ও অনুমোদিত admission destination-এই থাকে।"
        >
            <x-slot:actions>
                <x-ui.badge variant="info">Public guidance</x-ui.badge>
            </x-slot:actions>
        </x-ui.page-header>

        <div class="grid gap-6 xl:grid-cols-[1.05fr,0.95fr]">
            <x-ui.card
                title="এগোনোর আগে যা জানা প্রয়োজন"
                description="ভর্তি site-এ যাওয়ার আগে প্রয়োজনীয় প্রস্তুতি ও সীমারেখাগুলো সংক্ষেপে দেখে নিন।"
            >
                <div class="space-y-3">
                    @foreach ($admissionChecklist as $item)
                        <div class="ui-auth-note ui-auth-note--soft">
                            {{ $item }}
                        </div>
                    @endforeach
                </div>
            </x-ui.card>

            <x-ui.card
                title="External application handoff"
                description="আবেদন, আপলোড, draft saving এবং submission tracking এই Laravel application-এর বাইরে থাকে।"
            >
                <x-admission.external-handoff
                    :url="$admissionUrl"
                    safety-message="এই handoff guardian, donor, payment এবং management boundary-কে আলাদা রেখেই admission process-এ এগোনোর সুযোগ দেয়।"
                />

                <div class="ui-auth-note mt-5">
                    এই পেজে account তৈরি, application draft সংরক্ষণ, guardian data উন্মুক্ত করা বা payment-related protected data দেখানোর কোনো ব্যবস্থা নেই।
                </div>
            </x-ui.card>
        </div>

        <div class="grid gap-5 lg:grid-cols-3">
            @foreach ($admissionHighlights as $highlight)
                <x-ui.card :title="$highlight['title']" soft>
                    <p class="text-sm leading-7 text-slate-600">{{ $highlight['description'] }}</p>
                </x-ui.card>
            @endforeach
        </div>

        <x-ui.alert variant="info" title="Boundary preserved">
            Admission information কেবল অনুমোদিত public এবং guardian-informational surface-এ থাকে। Protected guardian, donor, payment এবং management route পৃথকই রাখা হয়েছে।
        </x-ui.alert>
    </section>
</x-public-shell>
