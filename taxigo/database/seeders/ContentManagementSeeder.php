<?php

namespace Database\Seeders;

use App\Models\CrmPages;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ContentManagementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pages = [
            [
                'type'=>'about-us',
                'title'=>'About Us',
            ],
            [
                'type'=>'inclusion-exclusions',
                'title'=>'Inclusions & Exclusions',
            ],
            [
                'type'=>'read-before-you-book',
                'title'=>'Read Before You Book',
            ],
            [
                'type'=>'privacy-policy',
                'title'=>'Privacy Policy',
            ],
            [
                'type'=>'user-agreement',
                'title'=>'User Agreement',
            ],
            [
                'type'=>'terms-of-service',
                'title'=>'Terms of Service',
            ],
            [
                'type'=>'driver-agreement',
                'title'=>'Driver Agreement',
            ],
            [
                'type'=>'advertiser-agreement',
                'title'=>'Advertiser Agreement',
            ],
            [
                'type'=>'cancel-refund',
                'title'=>'Cancel & Refund Policy',
            ],
        ];

        CrmPages::insert($pages);
    }
}
