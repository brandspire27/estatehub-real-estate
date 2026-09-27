<?php

namespace Database\Seeders;

use App\Models\SeoMeta;
use Illuminate\Database\Seeder;

class SeoMetaSeeder extends Seeder
{
    public function run(): void
    {
        $pages = [
            [
                'page_key' => 'home',
                'page_name' => 'Home Page',
                'meta_title' => 'EstateHub — Modern Real Estate Marketplace & Verified Properties',
                'meta_description' => 'Browse 1,000+ verified residential flats, luxury villas, commercial spaces, and freehold plots across Guwahati, Bangalore, Mumbai, and all major Indian cities with direct seller connectivity.',
                'meta_keywords' => 'real estate india, buy flat, luxury villa, commercial space, freehold plot, guwahati properties, bangalore flats, mumbai apartments',
                'canonical_url' => url('/'),
                'is_indexed' => true,
                'is_followed' => true,
                'og_title' => 'EstateHub — Discover Verified Real Estate Across India',
                'og_description' => 'Instant buyer-property matching, direct WhatsApp contact with verified sellers, and transparent zero-spam listings.',
                'og_image' => 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=1200&q=80',
                'twitter_card' => 'summary_large_image',
                'json_ld' => json_encode([
                    '@context' => 'https://schema.org',
                    '@type' => 'RealEstateAgent',
                    'name' => 'EstateHub',
                    'url' => url('/'),
                    'logo' => url('/favicon.ico'),
                    'description' => 'Modern real estate marketplace connecting buyers, sellers, and verified properties across major Indian cities.',
                    'potentialAction' => [
                        '@type' => 'SearchAction',
                        'target' => url('/properties?keyword={search_term_string}'),
                        'query-input' => 'required name=search_term_string',
                    ],
                ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
            ],
            [
                'page_key' => 'properties',
                'page_name' => 'Properties Search Catalogue',
                'meta_title' => 'Verified Properties for Sale in India — Apartments, Villas & Plots | EstateHub',
                'meta_description' => 'Explore verified properties for sale across top cities and prime localities. Filter by BHK, budget range, plot size, commercial spaces, and ready-to-move status.',
                'meta_keywords' => 'properties for sale, flats for sale, plots in guwahati, apartments in bangalore, buy house india, 2 bhk flats, 3 bhk luxury',
                'canonical_url' => url('/properties'),
                'is_indexed' => true,
                'is_followed' => true,
                'og_title' => 'Explore Verified Real Estate Listings | EstateHub',
                'og_description' => 'Filter properties by city, locality, budget, and configuration with instant buyer demand matching.',
                'og_image' => 'https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?auto=format&fit=crop&w=1200&q=80',
                'twitter_card' => 'summary_large_image',
                'json_ld' => json_encode([
                    '@context' => 'https://schema.org',
                    '@type' => 'CollectionPage',
                    'name' => 'Verified Properties for Sale',
                    'url' => url('/properties'),
                    'description' => 'Comprehensive catalogue of verified properties across residential, plot, and commercial categories.',
                ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
            ],
            [
                'page_key' => 'about',
                'page_name' => 'About Us Page',
                'meta_title' => 'About EstateHub — India\'s Premier Technology-Driven Real Estate Marketplace',
                'meta_description' => 'Learn how EstateHub is revolutionizing property buying and selling with algorithmic buyer-property matching, verified seller dossiers, and transparent real estate advisory.',
                'meta_keywords' => 'about estatehub, real estate platform india, proptech marketplace, verified real estate, real estate mission',
                'canonical_url' => url('/about'),
                'is_indexed' => true,
                'is_followed' => true,
                'og_title' => 'About EstateHub — Transforming Real Estate Across India',
                'og_description' => 'Building transparency, reliability, and algorithmic efficiency into property discovery and ownership.',
                'og_image' => 'https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?auto=format&fit=crop&w=1200&q=80',
                'twitter_card' => 'summary_large_image',
                'json_ld' => json_encode([
                    '@context' => 'https://schema.org',
                    '@type' => 'AboutPage',
                    'name' => 'About EstateHub',
                    'url' => url('/about'),
                ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
            ],
            [
                'page_key' => 'contact',
                'page_name' => 'Contact Us Page',
                'meta_title' => 'Contact EstateHub Advisory — Real Estate Consultation & Support',
                'meta_description' => 'Get in touch with EstateHub property advisors, customer care, and seller support. We are available Monday to Saturday for consultations and listing assistance.',
                'meta_keywords' => 'contact estatehub, real estate phone support, property inquiry, estatehub customer care',
                'canonical_url' => url('/contact'),
                'is_indexed' => true,
                'is_followed' => true,
                'og_title' => 'Connect with EstateHub Real Estate Advisors',
                'og_description' => 'Expert guidance on property purchases, seller verification, and marketplace packages.',
                'og_image' => 'https://images.unsplash.com/photo-1560518883-ce09059eeffa?auto=format&fit=crop&w=1200&q=80',
                'twitter_card' => 'summary_large_image',
                'json_ld' => json_encode([
                    '@context' => 'https://schema.org',
                    '@type' => 'ContactPage',
                    'name' => 'Contact EstateHub',
                    'url' => url('/contact'),
                ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
            ],
        ];

        foreach ($pages as $page) {
            SeoMeta::updateOrCreate(
                ['page_key' => $page['page_key']],
                $page
            );
        }
    }
}
