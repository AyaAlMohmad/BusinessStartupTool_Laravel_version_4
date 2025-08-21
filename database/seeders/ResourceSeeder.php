<?php

namespace Database\Seeders;

use App\Models\Resource;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ResourceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'title' => 'NSW Business Grants',
                'description' => 'Government grants for small businesses',
                'link' => 'https://nsw.gov.au/business-grants',
                'region_id' => 1
            ],
            [
                'title' => 'Victoria Startups Guide',
                'description' => 'Resources for tech startups',
                'link' => 'https://vicstartups.com/guide',
                'region_id' => 2
            ],
            [
                'title' => 'Queensland Tourism Info',
                'description' => 'Explore Queensland attractions',
                'link' => 'https://qldtourism.com/explore',
                'region_id' => 3
            ],
            [
                'title' => 'Western Australia Jobs Portal',
                'description' => 'Job search tool for WA residents',
                'link' => 'https://wa.gov.au/jobs',
                'region_id' => 4
            ],
            [
                'title' => 'South Australia Innovation Hub',
                'description' => 'Hub for innovators and creators',
                'link' => 'https://sa.gov.au/innovation-hub',
                'region_id' => 5
            ],
            [
                'title' => 'Tasmania Nature Trails',
                'description' => 'Hiking and nature guides',
                'link' => 'https://tasmania.com/nature-trails',
                'region_id' => 6
            ],
            [
                'title' => 'ACT Government Services',
                'description' => 'All services from the ACT government',
                'link' => 'https://act.gov.au/services',
                'region_id' => 7
            ],
            [
                'title' => 'Northern Territory Education Grants',
                'description' => 'Scholarships and grants for students',
                'link' => 'https://nt.gov.au/education/grants',
                'region_id' => 8
            ],
        ];

        foreach ($data as $item) {
            Resource::create($item);
        }
    }
}
