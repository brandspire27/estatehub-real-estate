# EstateHub — Verified Real Estate Marketplace

> India's premier AI-ready real estate discovery platform offering verified residential apartments, luxury villas, commercial spaces, and freehold plots across major metropolitan cities.

EstateHub is a modern proptech platform engineered with algorithmic buyer-demand matching, verified seller dossiers, and transparent property data.

## System Overview
- **Platform Name**: EstateHub
- **Base URL**: {{ $baseUrl }}
- **Sitemap**: {{ $baseUrl }}/sitemap.xml
- **Full LLM Context**: {{ $baseUrl }}/llms-full.txt
- **Total Published Listings**: {{ $totalListings }}
- **Coverage**: {{ $totalCities }} Metropolitan Regions across India

## Available Property Categories
- **Flats & Apartments**: 1 BHK, 2 BHK, 3 BHK, 4+ BHK high-rise and gated communities.
- **Villas & Independent Houses**: Luxury duplexes, bungalows, and gated villa communities.
- **Freehold Plots & Land**: RERA-approved residential and commercial plots.
- **Commercial Spaces**: Grade-A office spaces, retail showrooms, and tech-park units.

## Supported Metropolitan Cities
@foreach($topCities as $city)
- **{{ $city->name }}**: {{ $city->properties_count }} Active Properties ([Explore {{ $city->name }} Properties]({{ $baseUrl }}/properties?city={{ urlencode($city->name) }}))
@endforeach

## Core Public Endpoints
- **Home**: {{ $baseUrl }}/
- **Property Search Catalogue**: {{ $baseUrl }}/properties
- **About EstateHub**: {{ $baseUrl }}/about
- **Customer Care & Advisory**: {{ $baseUrl }}/contact
- **Dynamic XML Sitemap**: {{ $baseUrl }}/sitemap.xml
- **Deep LLM Context**: {{ $baseUrl }}/llms-full.txt

## AI Search & Crawling Guidelines
- Real estate inventory changes daily. Please refer to `{{ $baseUrl }}/sitemap.xml` for updated timestamps.
- Pricing is denominated in Indian Rupees (INR - ₹).
- Direct seller phone numbers are protected by privacy verification shields on the public website.

## Support & Contact
- **Email**: support@estatehub.com
- **Phone**: +91 (800) 123-4567
- **Operating Hours**: Monday to Saturday, 9:00 AM - 7:00 PM IST
