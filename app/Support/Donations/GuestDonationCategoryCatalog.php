<?php

namespace App\Support\Donations;

final class GuestDonationCategoryCatalog
{
    public static function all(): array
    {
        return [
            [
                'key' => 'madrasa_complex',
                'label' => 'মাদ্রাসা কমপ্লেক্স',
                'description' => 'শিক্ষা, হিফজ, তালিম, তারবিয়াহ এবং প্রাতিষ্ঠানিক পরিচালনায় সহায়তা।',
                'badge' => 'প্রধান খাত',
                'featured' => true,
            ],
            [
                'key' => 'mosque_complex',
                'label' => 'মসজিদ কমপ্লেক্স',
                'description' => 'ইবাদত, দাওয়াহ, রক্ষণাবেক্ষণ এবং জনসম্পৃক্ত সেবায় সহায়তা।',
                'badge' => 'প্রধান খাত',
                'featured' => true,
            ],
            [
                'key' => 'general_education_fund',
                'label' => 'সাধারণ শিক্ষা তহবিল',
                'description' => 'প্রাতিষ্ঠানিক শিক্ষার ধারাবাহিকতা ও শিক্ষাবান্ধব পরিবেশ রক্ষার সহায়তা।',
                'badge' => 'চলমান খাত',
                'featured' => false,
            ],
            [
                'key' => 'student_support_and_guardian_care',
                'label' => 'শিক্ষার্থী সহায়তা ও অভিভাবকীয় সহমর্মিতা',
                'description' => 'অসচ্ছল শিক্ষার্থী, মৌলিক সামগ্রী এবং প্রয়োজনভিত্তিক সহায়ক উদ্যোগে অংশগ্রহণ।',
                'badge' => 'চলমান খাত',
                'featured' => false,
            ],
            [
                'key' => 'dawah_and_welfare_programs',
                'label' => 'দাওয়াহ, তালিম ও জনকল্যাণমূলক কর্মসূচি',
                'description' => 'ইলম, নসিহত, সামাজিক উপকার এবং জনসম্পৃক্ত কার্যক্রমের সহায়তা।',
                'badge' => 'চলমান খাত',
                'featured' => false,
            ],
            [
                'key' => 'publication_library_and_infrastructure',
                'label' => 'প্রকাশনা, লাইব্রেরি ও অবকাঠামোগত উন্নয়ন',
                'description' => 'জ্ঞানচর্চা, পাঠাগার, মিডিয়া এবং গ্রহণযোগ্য প্রাতিষ্ঠানিক সক্ষমতা উন্নয়ন।',
                'badge' => 'চলমান খাত',
                'featured' => false,
            ],
        ];
    }

    public static function keys(): array
    {
        return array_column(self::all(), 'key');
    }

    public static function find(?string $key): ?array
    {
        if (! is_string($key) || $key === '') {
            return null;
        }

        foreach (self::all() as $category) {
            if ($category['key'] === $key) {
                return $category;
            }
        }

        return null;
    }
}
