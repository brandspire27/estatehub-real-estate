<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AboutPageSetting extends Model
{
    use HasFactory;

    protected $table = 'about_page_settings';

    protected $fillable = [
        'hero_badge',
        'hero_title',
        'hero_subtitle',
        'hero_stats',
        'challenge_tagline',
        'challenge_title',
        'challenge_content_1',
        'challenge_content_2',
        'challenge_content_3',
        'traditional_title',
        'traditional_points',
        'solution_title',
        'solution_points',
        'algorithm_tagline',
        'algorithm_title',
        'algorithm_subtitle',
        'algorithm_vectors',
        'standards_cards',
        'buyer_title',
        'buyer_points',
        'seller_title',
        'seller_points',
        'verification_tagline',
        'verification_title',
        'verification_subtitle',
        'verification_steps',
        'faq_tagline',
        'faq_title',
        'faq_subtitle',
        'faqs',
        'cta_title',
        'cta_subtitle',
        'cta_btn1_text',
        'cta_btn1_url',
        'cta_btn2_text',
        'cta_btn2_url',
    ];

    protected $casts = [
        'hero_stats' => 'array',
        'traditional_points' => 'array',
        'solution_points' => 'array',
        'algorithm_vectors' => 'array',
        'standards_cards' => 'array',
        'buyer_points' => 'array',
        'seller_points' => 'array',
        'verification_steps' => 'array',
        'faqs' => 'array',
    ];

    /**
     * Get the active About page configuration, creating default seed if none exists.
     */
    public static function getSettings(): self
    {
        $setting = static::first();

        if (! $setting) {
            $setting = static::create(static::defaultSettings());
        }

        return $setting;
    }

    /**
     * Default values reflecting the full rich About page content.
     */
    public static function defaultSettings(): array
    {
        return [
            'hero_badge' => 'Redefining Indian Real Estate',
            'hero_title' => 'Precision Property Discovery with Zero Spam',
            'hero_subtitle' => 'EstateHub eliminates fake listings, duplicate broker spam, and endless cold calls through an intelligent 100-point algorithmic engine connecting qualified buyers directly with verified sellers.',
            'hero_stats' => [
                ['value' => '10,000+', 'label' => 'Verified Properties', 'color' => 'text-brand-400'],
                ['value' => '98.4%', 'label' => 'Match Precision', 'color' => 'text-emerald-400'],
                ['value' => '45,000+', 'label' => 'Direct Buyer Calls', 'color' => 'text-indigo-400'],
                ['value' => '₹0', 'label' => 'Hidden Broker Fees', 'color' => 'text-amber-400'],
            ],
            'challenge_tagline' => 'The Modern Realty Challenge',
            'challenge_title' => 'Why Traditional Property Portals Fail Indian Buyers',
            'challenge_content_1' => 'For over two decades, online real estate search in India has suffered from the same core frustrations: ghost listings with attractive prices that were sold months ago, duplicate listings posted dozens of times by competing brokers, and privacy violations where a buyer\'s phone number is distributed to hundreds of telemarketers.',
            'challenge_content_2' => 'Sellers face equal frustration—spending large sums on listing packages only to receive spam calls from tire-kickers who don\'t have the budget or are searching in entirely different localities.',
            'challenge_content_3' => 'EstateHub was built from the ground up to solve this with mathematics: an automated, bi-directional 100-point matching protocol that pairs buyers and property owners strictly based on confirmed criteria.',
            'traditional_title' => 'Traditional Classifieds Model',
            'traditional_points' => [
                'Over 65% listings are unverified, sold out, or duplicates',
                'Buyer phone numbers leaked to aggressive telemarketing desks',
                'Generic keyword search returns thousands of irrelevant results',
                'Sellers waste time with unqualified inquiries',
            ],
            'solution_title' => 'The EstateHub Precision Engine',
            'solution_points' => [
                '100% Admin & RERA verified listings with actual property photos & video tours',
                'Masked phone privacy shield: verified buyer submission required before number reveal',
                'Mathematical 100-point matching eliminates noise across 5 critical parameters',
                'Direct seller connection with one-click WhatsApp Web and direct calling',
            ],
            'algorithm_tagline' => 'The Technology Architecture',
            'algorithm_title' => 'How Our 100-Point Match Engine Works',
            'algorithm_subtitle' => 'When a buyer posts their requirements or a seller lists a property, our background engine computes pairwise compatibility across 5 weighted vectors, producing a match score between 0 and 100:',
            'algorithm_vectors' => [
                [
                    'icon' => 'fa-solid fa-location-dot',
                    'color' => 'brand',
                    'points' => '25 Pts',
                    'title' => 'Geographic Match',
                    'desc' => 'Strict city boundary validation combined with micro-locality alignment (e.g. Sixmile, Beltola, Powai, Whitefield).',
                    'badge' => 'Mandatory Boundary Rule',
                ],
                [
                    'icon' => 'fa-solid fa-indian-rupee-sign',
                    'color' => 'emerald',
                    'points' => '25 Pts',
                    'title' => 'Budget Fit',
                    'desc' => 'Evaluates whether asking price falls strictly within buyer\'s minimum and maximum budget bracket.',
                    'badge' => 'High Purchase Intent',
                ],
                [
                    'icon' => 'fa-solid fa-building',
                    'color' => 'indigo',
                    'points' => '20 Pts',
                    'title' => 'Property Type',
                    'desc' => 'Matching asset classes: Apartments, Luxury Villas, Freehold Plots, Commercial Spaces, and Buildings.',
                    'badge' => 'Exact Asset Class',
                ],
                [
                    'icon' => 'fa-solid fa-bed',
                    'color' => 'purple',
                    'points' => '15 Pts',
                    'title' => 'Configuration',
                    'desc' => 'Bedroom & layout compatibility (1 BHK, 2 BHK, 3 BHK, 4+ BHK, or open commercial floor).',
                    'badge' => 'Layout Alignment',
                ],
                [
                    'icon' => 'fa-solid fa-key',
                    'color' => 'amber',
                    'points' => '15 Pts',
                    'title' => 'Possession Fit',
                    'desc' => 'Distinguishes Ready to Move in homes versus Under Construction projects based on buyer move-in urgency.',
                    'badge' => 'Timing Synchronization',
                ],
            ],
            'standards_cards' => [
                [
                    'icon' => 'fa-solid fa-shield-halved',
                    'color' => 'brand',
                    'title' => 'Verified Sellers Only',
                    'desc' => 'Every property listing undergoes mandatory administrative review. Sellers must provide verifiable contact records, clear property titles, and authentic photographs before listings become public.',
                ],
                [
                    'icon' => 'fa-solid fa-award',
                    'color' => 'emerald',
                    'title' => 'RERA Guidelines Alignment',
                    'desc' => 'We strictly promote compliance with the Real Estate (Regulation and Development) Act, 2016. Promoters and builders are required to supply their State RERA registration numbers and verified carpet area dimensions.',
                ],
                [
                    'icon' => 'fa-solid fa-user-shield',
                    'color' => 'indigo',
                    'title' => 'Anti-Spam Phone Masking',
                    'desc' => 'Seller phone numbers are protected with two-way masking (e.g. 98******12). Buyers submit their verified details before unlocking direct calling, preventing scraper bots and cold-call agencies from scraping contact data.',
                ],
            ],
            'buyer_title' => 'Buyer Advantages',
            'buyer_points' => [
                ['title' => 'Direct Contact Without Intermediaries', 'desc' => 'Talk or message directly with the property owner or official developer sales team on WhatsApp.'],
                ['title' => 'Automated Compatibility Scoring', 'desc' => 'Every match highlights exactly why it fits your budget, preferred locality, and move-in timeline.'],
                ['title' => 'HD Video Tours & Floor Plan Details', 'desc' => 'Walk through the home virtually with embedded YouTube property tours before scheduling an in-person visit.'],
                ['title' => 'Free Buyer Requirement Posting', 'desc' => 'Post what you want once. Let unlisted opportunities and new inventory come to you automatically.'],
            ],
            'seller_title' => 'Seller & Builder Advantages',
            'seller_points' => [
                ['title' => 'Pre-Qualified, Ready-to-Buy Leads', 'desc' => 'Stop fielding cold calls. Receive inquiries only from buyers actively looking in your exact locality.'],
                ['title' => 'Inquiry CRM & Buyer Lead Notifications', 'desc' => 'Manage every prospective buyer\'s name, verified phone number, and requested move-in details in your seller dashboard.'],
                ['title' => 'Multi-Media Showcase', 'desc' => 'Upload high-resolution photography, YouTube property walkthroughs, and detailed square footage specifications.'],
                ['title' => '70% Faster Deal Closing Time', 'desc' => 'By eliminating unnecessary broker chains, negotiations move swiftly between decision makers.'],
            ],
            'verification_tagline' => 'Integrity Framework',
            'verification_title' => 'Our 6-Step Verification Protocol',
            'verification_subtitle' => 'Every listing on EstateHub must pass our stringent editorial and administrative checklist before publishing.',
            'verification_steps' => [
                ['step' => 1, 'title' => 'Owner Identity Validation', 'desc' => 'Cross-verification of seller phone, email, and identity to prevent anonymous broker posting.'],
                ['step' => 2, 'title' => 'Locality Geocoding', 'desc' => 'Pinpoint assignment to official micro-market sectors (e.g. Sixmile, Beltola, Powai, Whitefield).'],
                ['step' => 3, 'title' => 'RERA Number Crosscheck', 'desc' => 'Verification of project registration numbers with relevant State RERA statutory portals.'],
                ['step' => 4, 'title' => 'Media Authenticity Check', 'desc' => 'Rejection of low-resolution watermarked stock images in favor of actual site walkthroughs & YouTube tours.'],
                ['step' => 5, 'title' => 'Price Benchmark Audit', 'desc' => 'Comparison against prevailing circle rates and square foot averages to prevent misleading clickbait pricing.'],
                ['step' => 6, 'title' => 'Spam Shield Activation', 'desc' => 'Activation of masked phone protection to ensure both seller and buyer interact under mutual consent.'],
            ],
            'faq_tagline' => 'Got Questions?',
            'faq_title' => 'Frequently Asked Questions',
            'faq_subtitle' => 'Everything you need to know about the EstateHub marketplace and matching engine.',
            'faqs' => [
                [
                    'question' => 'How does the 100-Point Match Algorithm work?',
                    'answer' => 'The 100-point algorithm scores property compatibility across 5 distinct dimensions: Geographic alignment within the same city and locality (25 pts), Budget Fit strictly bounded by the buyer\'s minimum and maximum capacity (25 pts), Property Type alignment like Flats, Villas, Plots (20 pts), BHK configuration (15 pts), and Possession status (15 pts). Matches scoring 80+ points are flagged as high-affinity opportunities.',
                ],
                [
                    'question' => 'Why is the seller\'s phone masked as 98******12 initially?',
                    'answer' => 'To prevent telemarketing scraping bots and commercial spam from harvesting seller phone numbers, we mask the middle digits. Genuine buyers can click the phone number, provide their contact details in a quick popup, and the direct phone number is instantly revealed, along with direct WhatsApp chat options.',
                ],
                [
                    'question' => 'Can I watch YouTube video tours in the property slider?',
                    'answer' => 'Yes! EstateHub seamlessly integrates YouTube video walkthroughs directly alongside high-definition photo galleries. You can click between the video tour and photos using either the slider navigation arrows or the thumbnail preview strip located directly underneath the slider.',
                ],
                [
                    'question' => 'Is it free for buyers to post requirements?',
                    'answer' => 'Posting buyer requirements on EstateHub is 100% free. Once you register and submit your target city, preferred micro-localities, BHK preference, and budget bracket, our match engine immediately calculates compatible properties and notifies you in real-time.',
                ],
                [
                    'question' => 'How does EstateHub support Freehold Plots & Commercial Spaces?',
                    'answer' => 'Unlike standard residential portals that force plot buyers to choose irrelevant \'BHK\' options, EstateHub customizes the specification view. When viewing Freehold Plots or Commercial Spaces, irrelevant bedroom fields are replaced with exact land dimensions, square footage, boundary status, and commercial usage indicators.',
                ],
                [
                    'question' => 'Are listings verified with State RERA authorities?',
                    'answer' => 'Yes. We require commercial developers and new residential project promoters to submit their State Real Estate Regulatory Authority (RERA) registration details. All users are advised to cross-reference these registrations on respective state portals prior to any transaction.',
                ],
            ],
            'cta_title' => 'Ready to Experience Transparent Real Estate?',
            'cta_subtitle' => 'Join thousands of smart property buyers and verified owners across India who save weeks of wasted time using our precision matching engine.',
            'cta_btn1_text' => 'Explore Verified Properties',
            'cta_btn1_url' => '/properties',
            'cta_btn2_text' => 'Post Buyer Requirement (Free)',
            'cta_btn2_url' => '/requirements/create',
        ];
    }
}
