<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Property;
use App\Models\PropertyImage;
use App\Models\Requirement;
use App\Models\User;
use App\Services\MatchingService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Users
        $admin = User::updateOrCreate(
            ['email' => 'admin@estatehub.com'],
            [
                'name' => 'EstateHub Administrator',
                'phone' => '9876543210',
                'password' => Hash::make('password'),
                'role' => 'ADMIN',
                'is_active' => true,
            ]
        );

        User::updateOrCreate(
            ['email' => 'admin@realestate.com'],
            [
                'name' => 'Platform Administrator',
                'phone' => '9876543219',
                'password' => Hash::make('password'),
                'role' => 'ADMIN',
                'is_active' => true,
            ]
        );

        $staff = User::updateOrCreate(
            ['email' => 'staff@realestate.com'],
            [
                'name' => 'Vikram Singhania',
                'phone' => '9876543211',
                'password' => Hash::make('password'),
                'role' => 'STAFF',
                'created_by_user_id' => $admin->id,
                'is_active' => true,
            ]
        );

        $seller1 = User::updateOrCreate(
            ['email' => 'seller@realestate.com'],
            [
                'name' => 'Rajesh Agarwal',
                'phone' => '9876543212',
                'password' => Hash::make('password'),
                'role' => 'SELLER',
                'is_active' => true,
            ]
        );

        $seller2 = User::updateOrCreate(
            ['email' => 'seller2@realestate.com'],
            [
                'name' => 'Priya Sharma',
                'phone' => '9876543214',
                'password' => Hash::make('password'),
                'role' => 'SELLER',
                'is_active' => true,
            ]
        );

        $seller3 = User::updateOrCreate(
            ['email' => 'seller3@realestate.com'],
            [
                'name' => 'Amitav Das',
                'phone' => '9876543215',
                'password' => Hash::make('password'),
                'role' => 'SELLER',
                'created_by_user_id' => $staff->id,
                'created_via_staff' => true,
                'is_active' => true,
            ]
        );

        $buyer1 = User::updateOrCreate(
            ['email' => 'buyer@realestate.com'],
            [
                'name' => 'Siddharth Sengupta',
                'phone' => '9876543213',
                'password' => Hash::make('password'),
                'role' => 'BUYER',
                'is_active' => true,
            ]
        );

        $buyer2 = User::updateOrCreate(
            ['email' => 'buyer2@realestate.com'],
            [
                'name' => 'Neha Kapoor',
                'phone' => '9876543216',
                'password' => Hash::make('password'),
                'role' => 'BUYER',
                'is_active' => true,
            ]
        );

        $buyer3 = User::updateOrCreate(
            ['email' => 'buyer3@realestate.com'],
            [
                'name' => 'Bikram Barua',
                'phone' => '9876543217',
                'password' => Hash::make('password'),
                'role' => 'BUYER',
                'created_by_user_id' => $staff->id,
                'created_via_staff' => true,
                'is_active' => true,
            ]
        );

        // 2. Cities
        $citiesData = [
            [
                'name' => 'Guwahati',
                'state' => 'Assam',
                'image_url' => 'https://images.unsplash.com/photo-1570168007204-dfb528c6958f?auto=format&fit=crop&w=800&q=80',
            ],
            [
                'name' => 'Mumbai',
                'state' => 'Maharashtra',
                'image_url' => 'https://images.unsplash.com/photo-1570168007204-dfb528c6958f?auto=format&fit=crop&w=800&q=80',
            ],
            [
                'name' => 'Delhi NCR',
                'state' => 'Delhi',
                'image_url' => 'https://images.unsplash.com/photo-1587474260584-136574528ed5?auto=format&fit=crop&w=800&q=80',
            ],
            [
                'name' => 'Bangalore',
                'state' => 'Karnataka',
                'image_url' => 'https://images.unsplash.com/photo-1596176530529-78163a4f7af2?auto=format&fit=crop&w=800&q=80',
            ],
            [
                'name' => 'Hyderabad',
                'state' => 'Telangana',
                'image_url' => 'https://images.unsplash.com/photo-1605649487212-47bdab064df7?auto=format&fit=crop&w=800&q=80',
            ],
            [
                'name' => 'Kolkata',
                'state' => 'West Bengal',
                'image_url' => 'https://images.unsplash.com/photo-1558431382-27e303142255?auto=format&fit=crop&w=800&q=80',
            ],
            [
                'name' => 'Pune',
                'state' => 'Maharashtra',
                'image_url' => 'https://images.unsplash.com/photo-1605146769289-440113cc3d00?auto=format&fit=crop&w=800&q=80',
            ],
        ];

        foreach ($citiesData as $c) {
            City::updateOrCreate(['name' => $c['name']], $c);
        }

        // 3. Properties
        $propertiesData = [
            [
                'user_id' => $seller1->id,
                'title' => 'Luxury 3 BHK Hillview Apartment with Balcony',
                'description' => "Sun-drenched, corner unit 3 BHK apartment offering panoramic views of the hills. Includes premium Italian marble flooring, modular kitchen with chimney, 2 covered reserved parking bays, and 100% DG power backup. Located 5 minutes from city center and top hospitals.",
                'price' => 6800000,
                'property_type' => 'Apartment',
                'bhk' => 3,
                'area_sqft' => 1750,
                'city' => 'Guwahati',
                'area' => 'GS Road, Christian Basti',
                'possession_status' => 'Ready to Move',
                'status' => 'PUBLISHED',
                'views' => 142,
                'images' => [
                    'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=1200&q=80',
                    'https://images.unsplash.com/photo-1600566753190-17f0baa2a6c3?auto=format&fit=crop&w=1200&q=80',
                    'https://images.unsplash.com/photo-1600210492486-724fe5c67fb0?auto=format&fit=crop&w=1200&q=80',
                ]
            ],
            [
                'user_id' => $seller1->id,
                'title' => 'Modern 2 BHK Gated Community Flat',
                'description' => "Spacious 2 BHK home located in a secured luxury high-rise enclave. Features club house, Olympic length swimming pool, indoor squash court, and EV charging points. Ideal for families and young executives.",
                'price' => 4500000,
                'property_type' => 'Flat',
                'bhk' => 2,
                'area_sqft' => 1150,
                'city' => 'Guwahati',
                'area' => 'Zoo Road, Tiniali',
                'possession_status' => 'Ready to Move',
                'status' => 'PUBLISHED',
                'views' => 98,
                'images' => [
                    'https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?auto=format&fit=crop&w=1200&q=80',
                    'https://images.unsplash.com/photo-1512917774080-9991f1c4c750?auto=format&fit=crop&w=1200&q=80',
                ]
            ],
            [
                'user_id' => $seller2->id,
                'title' => 'Ultra-Luxury 4 BHK Sea View Penthouse',
                'description' => "Breathtaking sea-facing luxury penthouse with double-height ceiling, private infinity splash pool, dedicated servant quarters, and 3-car parking. World-class concierge facilities and high security.",
                'price' => 45000000,
                'property_type' => 'Apartment',
                'bhk' => 4,
                'area_sqft' => 3800,
                'city' => 'Mumbai',
                'area' => 'Worli Sea Face',
                'possession_status' => 'Ready to Move',
                'status' => 'PUBLISHED',
                'views' => 420,
                'images' => [
                    'https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?auto=format&fit=crop&w=1200&q=80',
                    'https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?auto=format&fit=crop&w=1200&q=80',
                ]
            ],
            [
                'user_id' => $seller2->id,
                'title' => 'Contemporary 2 BHK in Prime Bandra West',
                'description' => "Centrally located close to Carter Road and trendy cafes. Completely renovated modern apartment with imported bathroom fittings, soundproof German double-glazed windows, and automated ambient lighting.",
                'price' => 28500000,
                'property_type' => 'Apartment',
                'bhk' => 2,
                'area_sqft' => 950,
                'city' => 'Mumbai',
                'area' => 'Bandra West, Pali Hill',
                'possession_status' => 'Ready to Move',
                'status' => 'PUBLISHED',
                'views' => 210,
                'images' => [
                    'https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?auto=format&fit=crop&w=1200&q=80',
                    'https://images.unsplash.com/photo-1560185127-6ed189bf02f4?auto=format&fit=crop&w=1200&q=80',
                ]
            ],
            [
                'user_id' => $seller3->id,
                'title' => 'Exclusive 4 BHK Spanish Courtyard Villa',
                'description' => "Architect-designed independent villa on a 4,000 sq.ft private plot with landscaped Spanish courtyard, solar rooftop power, private home theater room, and heated Jacuzzi. Located inside prestige township.",
                'price' => 35000000,
                'property_type' => 'Villa',
                'bhk' => 4,
                'area_sqft' => 4200,
                'city' => 'Bangalore',
                'area' => 'Whitefield, Palm Meadows',
                'possession_status' => 'Ready to Move',
                'status' => 'PUBLISHED',
                'views' => 312,
                'images' => [
                    'https://images.unsplash.com/photo-1613490493576-7fde63acd811?auto=format&fit=crop&w=1200&q=80',
                    'https://images.unsplash.com/photo-1613977257363-707ba9348227?auto=format&fit=crop&w=1200&q=80',
                ]
            ],
            [
                'user_id' => $seller3->id,
                'title' => 'Premium 3 BHK Tech-Park Adjacent Condo',
                'description' => "Strategically positioned directly opposite Outer Ring Road tech corridor. Walk-to-work comfort, zero traffic hassle, semi-furnished with premium German wardrobes and modern kitchen hob.",
                'price' => 14500000,
                'property_type' => 'Apartment',
                'bhk' => 3,
                'area_sqft' => 1850,
                'city' => 'Bangalore',
                'area' => 'Bellandur / Outer Ring Road',
                'possession_status' => 'Under Construction',
                'status' => 'PUBLISHED',
                'views' => 185,
                'images' => [
                    'https://images.unsplash.com/photo-1574362848149-11496d93a7c7?auto=format&fit=crop&w=1200&q=80',
                ]
            ],
            [
                'user_id' => $seller1->id,
                'title' => 'Golf View 4 BHK Luxury Residence',
                'description' => "Overlooking the lush 18-hole championship golf greens. Master suite with walk-in closet, private lift lobby, central VRV air conditioning, and clubhouse membership included.",
                'price' => 38000000,
                'property_type' => 'Apartment',
                'bhk' => 4,
                'area_sqft' => 3200,
                'city' => 'Delhi NCR',
                'area' => 'Golf Course Road, Gurgaon',
                'possession_status' => 'Ready to Move',
                'status' => 'PUBLISHED',
                'views' => 290,
                'images' => [
                    'https://images.unsplash.com/photo-1512915922686-57c11dde9b6b?auto=format&fit=crop&w=1200&q=80',
                ]
            ],
            [
                'user_id' => $seller2->id,
                'title' => 'Prime Corner Commercial Office Space',
                'description' => "High footfall grade-A IT & commercial tower office with glass facade, double height entrance lobby, 3 high-speed Otis passenger elevators, and cafeteria terrace. Excellent rental yield of 8.5%.",
                'price' => 22000000,
                'property_type' => 'Commercial Space',
                'bhk' => null,
                'area_sqft' => 2500,
                'city' => 'Hyderabad',
                'area' => 'Hitec City, Madhapur',
                'possession_status' => 'Ready to Move',
                'status' => 'PUBLISHED',
                'views' => 174,
                'images' => [
                    'https://images.unsplash.com/photo-1497366216548-37526070297c?auto=format&fit=crop&w=1200&q=80',
                ]
            ],
            [
                'user_id' => $seller1->id,
                'title' => 'Heritage Style 3 BHK South Kolkata Residence',
                'description' => "Classic aesthetic blended with ultra-modern smart living. Massive airy balconies, south-open layout with continuous natural breeze, high ceilings, and gated security.",
                'price' => 9500000,
                'property_type' => 'Apartment',
                'bhk' => 3,
                'area_sqft' => 1650,
                'city' => 'Kolkata',
                'area' => 'Ballygunge Circular Road',
                'possession_status' => 'Ready to Move',
                'status' => 'PUBLISHED',
                'views' => 112,
                'images' => [
                    'https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?auto=format&fit=crop&w=1200&q=80',
                ]
            ],
            [
                'user_id' => $seller3->id,
                'title' => 'Serene 2 BHK Riverside Flat in Kalyani Nagar',
                'description' => "Peaceful riverside facing residential development. Includes jogging track along water body, meditation zone, covered badminton court, and supermarket on premise.",
                'price' => 8200000,
                'property_type' => 'Flat',
                'bhk' => 2,
                'area_sqft' => 1100,
                'city' => 'Pune',
                'area' => 'Kalyani Nagar',
                'possession_status' => 'Ready to Move',
                'status' => 'PUBLISHED',
                'views' => 164,
                'images' => [
                    'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?auto=format&fit=crop&w=1200&q=80',
                ]
            ],
            [
                'user_id' => $seller1->id,
                'title' => 'Residential Freehold Plot inside Green Enclave',
                'description' => "Ready for immediate construction with clear title, municipal water line, underground electricity cabling, and 40-foot wide tarred road frontage. North-east facing auspicious Vastu plot.",
                'price' => 5200000,
                'property_type' => 'Plot',
                'bhk' => null,
                'area_sqft' => 2880,
                'city' => 'Guwahati',
                'area' => 'VIP Road, Borbari',
                'possession_status' => 'Ready to Move',
                'status' => 'PUBLISHED',
                'views' => 88,
                'images' => [
                    'https://images.unsplash.com/photo-1500382017468-9049fed747ef?auto=format&fit=crop&w=1200&q=80',
                ]
            ],
            [
                'user_id' => $seller2->id,
                'title' => 'Affordable 2 BHK Newly Completed Apartment',
                'description' => "Newly constructed complex with bank approvals from SBI and HDFC for 90% loan. Low maintenance society with children play area, rainwater harvesting, and lift backup.",
                'price' => 3800000,
                'property_type' => 'Flat',
                'bhk' => 2,
                'area_sqft' => 980,
                'city' => 'Guwahati',
                'area' => 'Lokhra / NH-37',
                'possession_status' => 'Ready to Move',
                'status' => 'PENDING_VERIFICATION',
                'views' => 15,
                'images' => [
                    'https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?auto=format&fit=crop&w=1200&q=80',
                ]
            ],
            [
                'user_id' => $seller3->id,
                'title' => 'Upcoming 3 BHK Riverfront Luxury Tower',
                'description' => "Under construction project by top reputed developer. Delivery in Q4 2027. Attractive 20:80 subvention payment scheme available with zero pre-EMI interest.",
                'price' => 6200000,
                'property_type' => 'Apartment',
                'bhk' => 3,
                'area_sqft' => 1500,
                'city' => 'Guwahati',
                'area' => 'Uzan Bazar, Riverside',
                'possession_status' => 'Under Construction',
                'status' => 'PENDING_VERIFICATION',
                'views' => 23,
                'images' => [
                    'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?auto=format&fit=crop&w=1200&q=80',
                ]
            ],
        ];

        foreach ($propertiesData as $pData) {
            $images = $pData['images'];
            unset($pData['images']);

            $property = Property::create($pData);

            $sort = 0;
            foreach ($images as $imgUrl) {
                PropertyImage::create([
                    'property_id' => $property->id,
                    'image_url' => $imgUrl,
                    'sort_order' => $sort++,
                ]);
            }
        }

        // 4. Requirements
        $requirementsData = [
            [
                'user_id' => $buyer1->id,
                'property_type' => 'Apartment',
                'bhk' => 3,
                'budget_min' => 5000000,
                'budget_max' => 7500000,
                'possession_status' => 'Ready to Move',
                'city' => 'Guwahati',
                'description' => 'Urgent requirement for family relocation. Prefer Christian Basti, GS Road, or Zoo Road with covered car parking and lift.',
                'status' => 'ACTIVE',
            ],
            [
                'user_id' => $buyer1->id,
                'property_type' => 'Flat',
                'bhk' => 2,
                'budget_min' => 3500000,
                'budget_max' => 5000000,
                'possession_status' => 'Ready to Move',
                'city' => 'Guwahati',
                'description' => 'Looking for a verified 2 BHK in gated community with security and backup power.',
                'status' => 'ACTIVE',
            ],
            [
                'user_id' => $buyer2->id,
                'property_type' => 'Villa',
                'bhk' => 4,
                'budget_min' => 30000000,
                'budget_max' => 40000000,
                'possession_status' => 'Ready to Move',
                'city' => 'Bangalore',
                'description' => 'Looking for independent luxury villa in Whitefield or Sarjapur Road inside a gated enclave.',
                'status' => 'ACTIVE',
            ],
            [
                'user_id' => $buyer2->id,
                'property_type' => 'Apartment',
                'bhk' => 2,
                'budget_min' => 25000000,
                'budget_max' => 32000000,
                'possession_status' => 'Ready to Move',
                'city' => 'Mumbai',
                'description' => 'Seeking modern apartment in Bandra or Khar with balcony.',
                'status' => 'ACTIVE',
            ],
            [
                'user_id' => $buyer3->id,
                'property_type' => 'Plot',
                'bhk' => null,
                'budget_min' => 4000000,
                'budget_max' => 6000000,
                'possession_status' => 'Ready to Move',
                'city' => 'Guwahati',
                'description' => 'Freehold residential land wanted on VIP Road or Six Mile for home construction.',
                'status' => 'ACTIVE',
            ],
            [
                'user_id' => $buyer3->id,
                'property_type' => 'Commercial Space',
                'bhk' => null,
                'budget_min' => 18000000,
                'budget_max' => 25000000,
                'possession_status' => 'Ready to Move',
                'city' => 'Hyderabad',
                'description' => 'Commercial space for software engineering office in Hitec City or Gachibowli.',
                'status' => 'ACTIVE',
            ],
            [
                'user_id' => $buyer1->id,
                'property_type' => 'Apartment',
                'bhk' => 3,
                'budget_min' => 8000000,
                'budget_max' => 11000000,
                'possession_status' => 'Ready to Move',
                'city' => 'Kolkata',
                'description' => 'South Kolkata residential flat required with good road connectivity.',
                'status' => 'ACTIVE',
            ],
        ];

        foreach ($requirementsData as $rData) {
            Requirement::create($rData);
        }

        // 5. Run matching service to calculate all matches and save into `matches` table
        $matchingService = app(MatchingService::class);
        $matchingService->recalculateAll();
    }
}
