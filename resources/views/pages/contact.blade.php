@extends('layouts.app')

@section('title', 'Contact Us — EstateHub Marketplace')

@section('content')
<div class="bg-slate-900 text-white py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center max-w-2xl">
        <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight">Get in Touch</h1>
        <p class="text-slate-300 text-sm sm:text-base mt-2">
            Have questions about listing your property or finding matched requirements? Our advisory team is here to assist.
        </p>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
        
        <!-- Contact Information -->
        <div class="space-y-6">
            <div class="bg-white rounded-3xl border border-slate-200/80 p-6 shadow-sm">
                <div class="w-10 h-10 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center text-lg mb-3">
                    <i class="fa-solid fa-headset"></i>
                </div>
                <h3 class="font-bold text-slate-900 text-base">Customer Helpline</h3>
                <p class="text-xs text-slate-500 mt-1">Available Mon–Sat, 9:00 AM – 7:00 PM IST</p>
                <div class="mt-3 text-sm font-bold text-brand-600">
                    <a href="tel:+919876543210">+91 98765 43210</a>
                </div>
            </div>

            <div class="bg-white rounded-3xl border border-slate-200/80 p-6 shadow-sm">
                <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-lg mb-3">
                    <i class="fa-solid fa-envelope"></i>
                </div>
                <h3 class="font-bold text-slate-900 text-base">Direct Email</h3>
                <p class="text-xs text-slate-500 mt-1">For partnerships, listings verification, and press</p>
                <div class="mt-3 text-sm font-bold text-brand-600">
                    <a href="mailto:support@estatehub.in">support@estatehub.in</a>
                </div>
            </div>

            <div class="bg-white rounded-3xl border border-slate-200/80 p-6 shadow-sm">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg mb-3">
                    <i class="fa-solid fa-location-dot"></i>
                </div>
                <h3 class="font-bold text-slate-900 text-base">Regional Hubs</h3>
                <p class="text-xs text-slate-500 mt-1">
                    Offices in Guwahati (GS Road), Mumbai (BKC), and Bengaluru (Indiranagar).
                </p>
            </div>
        </div>

        <!-- Contact Form -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-3xl border border-slate-200/80 p-8 sm:p-10 shadow-sm">
                <h2 class="text-xl font-extrabold text-slate-900 mb-6">Send Us a Direct Message</h2>

                <form action="{{ route('contact.submit') }}" method="POST" class="space-y-5">
                    @csrf

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Your Name</label>
                            <input type="text" name="name" required placeholder="Full Name" 
                                   class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-brand-500">
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Email Address</label>
                            <input type="email" name="email" required placeholder="name@domain.com" 
                                   class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-brand-500">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Phone Number (Optional)</label>
                        <input type="tel" name="phone" placeholder="9876543210" 
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-brand-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Message / Inquiry Details</label>
                        <textarea name="message" rows="5" required placeholder="How can we assist you?" 
                                  class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-brand-500"></textarea>
                    </div>

                    <button type="submit" class="px-6 py-3 rounded-xl bg-brand-600 hover:bg-brand-700 text-white font-bold text-sm shadow-md transition">
                        Submit Message
                    </button>
                </form>
            </div>
        </div>

    </div>
</div>
@endsection
