# EstateHub — Full Published Property Inventory & Detailed Knowledge Base

> Complete real-time catalog dump of active, verified property listings for AI models, LLM agents, and semantic retrieval systems.

- **Base URL**: {{ $baseUrl }}
- **Generated**: {{ now()->toRfc2822String() }}
- **Listing Count**: {{ $properties->count() }}

---

## Active Property Listings

@foreach($properties as $index => $prop)
### {{ $index + 1 }}. {{ $prop->title }}
- **Listing ID**: #PROP-{{ $prop->id }}
- **Public URL**: {{ $prop->url }}
- **City**: {{ $prop->city }}
- **Locality / Area**: {{ $prop->area }}
- **Price**: {{ $prop->formatted_price }} (Numeric: ₹{{ number_format($prop->price, 0) }})
- **Category**: {{ $prop->property_type }}
@if($prop->bhk)
- **Configuration**: {{ $prop->bhk }} BHK
@endif
@if($prop->area_sqft)
- **Built-up Area**: {{ number_format($prop->area_sqft) }} sq.ft.
@endif
- **Possession Status**: {{ $prop->possession_status }}
- **Description**: {{ strip_tags($prop->description) }}
- **Listed By**: {{ $prop->user->name ?? 'Verified Seller' }}
- **Primary Image**: {{ $prop->primary_image_url }}

---
@endforeach

## API Integration Note
For programmatic search by budget range, BHK, or city, query:
`GET {{ $baseUrl }}/properties?city={city}&locality={locality}&property_type={type}&min_price={min}&max_price={max}`
