<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AboutPageSetting;
use App\Models\ActivityLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AdminAboutController extends Controller
{
    /**
     * Show the edit form for the public About page.
     */
    public function edit(): View
    {
        $setting = AboutPageSetting::getSettings();

        return view('admin.about.edit', compact('setting'));
    }

    /**
     * Update the About page settings in the database.
     */
    public function update(Request $request): RedirectResponse
    {
        $setting = AboutPageSetting::getSettings();

        $validated = $request->validate([
            // Hero
            'hero_badge' => 'nullable|string|max:120',
            'hero_title' => 'required|string|max:255',
            'hero_subtitle' => 'nullable|string',
            'hero_stats' => 'nullable|array',
            'hero_stats.*.value' => 'nullable|string|max:50',
            'hero_stats.*.label' => 'nullable|string|max:100',
            'hero_stats.*.color' => 'nullable|string|max:50',

            // Story / Challenge
            'challenge_tagline' => 'nullable|string|max:150',
            'challenge_title' => 'nullable|string|max:255',
            'challenge_content_1' => 'nullable|string',
            'challenge_content_2' => 'nullable|string',
            'challenge_content_3' => 'nullable|string',
            'traditional_title' => 'nullable|string|max:150',
            'traditional_points' => 'nullable|array',
            'traditional_points.*' => 'nullable|string|max:255',
            'solution_title' => 'nullable|string|max:150',
            'solution_points' => 'nullable|array',
            'solution_points.*' => 'nullable|string|max:255',

            // 100-Point Match Algorithm Engine
            'algorithm_tagline' => 'nullable|string|max:150',
            'algorithm_title' => 'nullable|string|max:255',
            'algorithm_subtitle' => 'nullable|string',
            'algorithm_vectors' => 'nullable|array',
            'algorithm_vectors.*.icon' => 'nullable|string|max:60',
            'algorithm_vectors.*.color' => 'nullable|string|max:40',
            'algorithm_vectors.*.points' => 'nullable|string|max:40',
            'algorithm_vectors.*.title' => 'nullable|string|max:100',
            'algorithm_vectors.*.desc' => 'nullable|string|max:500',
            'algorithm_vectors.*.badge' => 'nullable|string|max:100',

            // Verified Standards
            'standards_cards' => 'nullable|array',
            'standards_cards.*.icon' => 'nullable|string|max:60',
            'standards_cards.*.color' => 'nullable|string|max:40',
            'standards_cards.*.title' => 'nullable|string|max:120',
            'standards_cards.*.desc' => 'nullable|string|max:500',

            // Buyer & Seller Advantages
            'buyer_title' => 'nullable|string|max:150',
            'buyer_points' => 'nullable|array',
            'buyer_points.*.title' => 'nullable|string|max:150',
            'buyer_points.*.desc' => 'nullable|string|max:500',
            'seller_title' => 'nullable|string|max:150',
            'seller_points' => 'nullable|array',
            'seller_points.*.title' => 'nullable|string|max:150',
            'seller_points.*.desc' => 'nullable|string|max:500',

            // 6-Step Verification Protocol
            'verification_tagline' => 'nullable|string|max:150',
            'verification_title' => 'nullable|string|max:255',
            'verification_subtitle' => 'nullable|string',
            'verification_steps' => 'nullable|array',
            'verification_steps.*.step' => 'nullable|numeric',
            'verification_steps.*.title' => 'nullable|string|max:150',
            'verification_steps.*.desc' => 'nullable|string|max:500',

            // FAQs
            'faq_tagline' => 'nullable|string|max:150',
            'faq_title' => 'nullable|string|max:255',
            'faq_subtitle' => 'nullable|string',
            'faqs' => 'nullable|array',
            'faqs.*.question' => 'nullable|string|max:255',
            'faqs.*.answer' => 'nullable|string',

            // CTA Banner
            'cta_title' => 'nullable|string|max:255',
            'cta_subtitle' => 'nullable|string',
            'cta_btn1_text' => 'nullable|string|max:100',
            'cta_btn1_url' => 'nullable|string|max:255',
            'cta_btn2_text' => 'nullable|string|max:100',
            'cta_btn2_url' => 'nullable|string|max:255',
        ]);

        // Clean arrays: filter out completely empty entries
        if (isset($validated['traditional_points'])) {
            $validated['traditional_points'] = array_values(array_filter($validated['traditional_points'], fn($v) => filled(trim((string)$v))));
        }
        if (isset($validated['solution_points'])) {
            $validated['solution_points'] = array_values(array_filter($validated['solution_points'], fn($v) => filled(trim((string)$v))));
        }
        if (isset($validated['faqs'])) {
            $validated['faqs'] = array_values(array_filter($validated['faqs'], fn($item) => filled(trim($item['question'] ?? '')) || filled(trim($item['answer'] ?? ''))));
        }
        if (isset($validated['verification_steps'])) {
            $validated['verification_steps'] = array_values(array_filter($validated['verification_steps'], fn($item) => filled(trim($item['title'] ?? ''))));
        }

        $setting->update($validated);

        if (class_exists(ActivityLog::class)) {
            ActivityLog::record(
                Auth::id(),
                'ABOUT_PAGE_UPDATED',
                'AboutPageSetting',
                $setting->id,
                ['ip' => $request->ip()]
            );
        }

        return redirect()->route('admin.about.edit')
            ->with('success', 'About Page content has been successfully updated and is live!');
    }
}
