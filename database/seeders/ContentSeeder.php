<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Service;
use Illuminate\Database\Seeder;

/**
 * Categories and boutique services. Safe to run more than once (skips what already exists).
 * Photos and videos come from InstagramSeeder.
 */
class ContentSeeder extends Seeder
{
    public const CATEGORIES = [
        'fragrances' => ['hy' => 'Բույրեր', 'ru' => 'Ароматы', 'en' => 'Fragrances'],
        'by-calone' => ['hy' => 'BY CALONE', 'ru' => 'BY CALONE', 'en' => 'BY CALONE'],
        'video' => ['hy' => 'Տեսանյութեր', 'ru' => 'Видео', 'en' => 'Videos'],
        'boutique' => ['hy' => 'Բուտիկ', 'ru' => 'Бутик', 'en' => 'Boutique'],
        'skincare' => ['hy' => 'Մաշկի խնամք', 'ru' => 'Уход за кожей', 'en' => 'Skin care'],
        'gifts' => ['hy' => 'Նվերներ', 'ru' => 'Подарки', 'en' => 'Gifts'],
    ];

    public function run(): void
    {
        foreach (array_keys(self::CATEGORIES) as $position => $slug) {
            Category::firstOrCreate(['slug' => $slug], ['name' => self::CATEGORIES[$slug], 'position' => $position]);
        }

        if (Service::exists()) {
            return;
        }

        $services = [
            ['star', ['hy' => 'Նիշային բրենդներ', 'ru' => 'Нишевые бренды', 'en' => 'Niche houses'],
                ['hy' => 'Նիշային օծանելիքների պաշտոնական ներկայացուցիչ Երևանում։ Միայն օրիգինալ բույրեր։', 'ru' => 'Официальный представитель нишевой парфюмерии в Ереване. Только оригиналы.', 'en' => 'Official representative of niche perfumery in Yerevan. Originals only.']],
            ['sparkles', ['hy' => 'BY CALONE', 'ru' => 'BY CALONE', 'en' => 'BY CALONE'],
                ['hy' => 'Մեր սեփական բույրերի գիծը։ Find Your Star։', 'ru' => 'Наша собственная линия ароматов. Find Your Star.', 'en' => 'Our own line of fragrances. Find Your Star.']],
            ['heart', ['hy' => 'Անհատական ընտրություն', 'ru' => 'Персональный подбор', 'en' => 'Personal consultation'],
                ['hy' => 'Կօգնենք գտնել ձեր ստորագրության բույրը բուտիկում կամ առցանց։', 'ru' => 'Поможем найти ваш аромат-подпись в бутике или онлайн.', 'en' => 'We help you find your signature scent in the boutique or online.']],
            ['gift', ['hy' => 'Նվերներ և նվեր քարտեր', 'ru' => 'Подарки и сертификаты', 'en' => 'Gifts & gift cards'],
                ['hy' => 'Նրբաճաշակ փաթեթավորում և նվեր քարտեր ցանկացած առիթի համար։', 'ru' => 'Изящная упаковка и подарочные карты на любой повод.', 'en' => 'Elegant wrapping and gift cards for any occasion.']],
            ['truck', ['hy' => 'Անվճար առաքում Երևանում', 'ru' => 'Бесплатная доставка по Еревану', 'en' => 'Free delivery in Yerevan'],
                ['hy' => 'Պատվիրեք հեռախոսով կամ Instagram-ով, և մենք կառաքենք։', 'ru' => 'Закажите по телефону или в Instagram, мы привезём.', 'en' => 'Order by phone or on Instagram and we deliver.']],
            ['beaker', ['hy' => 'Մաշկի խնամք', 'ru' => 'Уход за кожей', 'en' => 'Skin care'],
                ['hy' => 'Շքեղ խնամքի միջոցներ, որոնք շարունակում են բույրը։', 'ru' => 'Люксовый уход, который продолжает аромат.', 'en' => 'Luxury care that extends your scent.']],
        ];
        foreach ($services as $i => [$icon, $title, $description]) {
            Service::create(compact('icon', 'title', 'description') + ['position' => $i]);
        }
    }
}
