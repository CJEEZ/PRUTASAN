# Order Tracking System - Implementation Guide

## Overview

A comprehensive order tracking system has been implemented for the PRUTEXPRES e-commerce platform. This system allows customers to track their orders in real-time, view delivery locations on a map, and access detailed tracking history.

## Features

### 1. **Customer-Facing Tracking**
- **Authenticated Tracking**: Customers can view their order tracking from their profile
- **Public Tracking**: Anyone can track an order using order number and postal code (no login required)
- **Real-time Updates**: Auto-refreshes tracking data every 30 seconds
- **Interactive Map**: Shows customer delivery location and driver location (when in transit)
- **Tracking Timeline**: Visual timeline showing order progression through different statuses

### 2. **Seller Capabilities**
- **Ship Orders**: Sellers can mark orders as shipped with tracking number and carrier info
- **Location Updates**: Sellers can update driver location (latitude/longitude) in real-time
- **Status Management**: Update order and shipment status as delivery progresses

### 3. **Tracking States**
Orders progress through the following tracking states:
- `pending` - Waiting for seller confirmation
- `confirmed` - Seller confirmed the order
- `processing` - Preparing items
- `packed` - Items are packed and ready
- `shipped` - Package is en route
- `in_transit` - Package in transit to destination
- `out_for_delivery` - Driver is on the way with package
- `delivered` - Order successfully delivered
- `return_requested` - Customer requested return
- `cancelled` - Order was cancelled

## Database Schema

### New Tables

#### `tracking_histories`
Stores complete tracking history for each order:
```
- id (primary key)
- order_id (foreign key)
- shipment_id (foreign key, nullable)
- status (string)
- location (string, nullable)
- description (text, nullable)
- latitude (decimal, nullable)
- longitude (decimal, nullable)
- timestamp (datetime)
- timestamps
```

### Updated Models

#### `Order` Model
- New relationship: `trackingHistory()` - HasMany to TrackingHistory
- Existing tracking fields:
  - `tracking_number`
  - `tracking_status`
  - `current_location`
  - `latitude`, `longitude` - Delivery address coordinates
  - `driver_latitude`, `driver_longitude` - Driver's current location

#### `Shipment` Model
- New relationship: `trackingHistory()` - HasMany to TrackingHistory
- Existing fields:
  - `tracking_number`
  - `carrier`
  - `status`
  - `shipped_at`

#### `TrackingHistory` Model (New)
- Logs all tracking updates with timestamps
- Links to both orders and shipments
- Stores location and coordinate data

## Routes

### Public Routes (No Authentication Required)

```
GET  /track-package              - Public tracking search page
POST /track-package              - Process public tracking search
```

### Authenticated User Routes

```
GET  /tracking/{order}           - View order tracking page
GET  /tracking/{order}/data      - AJAX endpoint for tracking data (JSON)
```

### Seller Routes

```
POST /seller/orders/{order}/ship                          - Create shipment
GET  /seller/orders/{order}/track                         - View shipment tracking
POST /seller/orders/{order}/update-tracking-location      - Update driver location
```

## Controllers

### `TrackingController`
Main controller for customer tracking functionality.

**Methods:**
- `show(Order $order)` - Display full tracking page
- `publicTrack(Request $request)` - Handle public tracking search
- `getTrackingData(Order $order)` - Return JSON tracking data
- `getTrackingTimeline(Order $order)` - Build timeline display
- `getStatusInfo(string $status)` - Get status display info

### `SellerShipmentController` (Enhanced)
Updated to support tracking updates.

**New Methods:**
- `updateTrackingLocation(Request $request, Order $order)` - Update driver location and status

## Services

### `TrackingService`
Centralized service for tracking operations.

**Methods:**
- `logTrackingUpdate()` - Create tracking history record
- `updateOrderStatus()` - Update status and log event
- `getTrackingTimeline()` - Retrieve tracking history
- `isInTransit()` - Check if order is in transit
- `getEstimatedDelivery()` - Calculate estimated delivery time
- `getTrackingStats()` - Get tracking statistics

## Views

### `resources/views/tracking/show.blade.php`
Main tracking page with:
- Order status banner
- Tracking timeline with visual indicators
- Order items list
- Shipment details sidebar
- Delivery address
- Payment information
- Interactive map (using Leaflet)
- Action buttons (Track, Buy Again, Request Return)

### `resources/views/tracking/search.blade.php`
Public tracking search page with:
- Order number input
- Postal code input
- Information cards
- Help section

## How to Use

### For Customers

#### Viewing Order Tracking (Authenticated)

1. Go to Profile → My Orders
2. Click the **"Track"** button next to any order
3. View complete tracking information including:
   - Current status and progress timeline
   - Shipment details (carrier, tracking number)
   - Delivery address
   - Payment status
   - Interactive map showing location

#### Public Tracking (No Login Required)

1. Go to `/track-package`
2. Enter your order number (from confirmation email)
3. Enter your postal code (for verification)
4. Click "Track Package"
5. View your complete tracking information

### For Sellers

#### Creating a Shipment

1. Go to Seller Dashboard → Orders
2. Find the order to ship
3. Click "Ship" button
4. Enter tracking number and carrier
5. System automatically logs this as a tracking update

#### Updating Driver Location

Use the API endpoint to update driver location:

```bash
curl -X POST /seller/orders/{order_id}/update-tracking-location \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer {token}" \
  -d '{
    "latitude": 14.5995,
    "longitude": 120.9842,
    "location": "Cainta, Rizal",
    "status": "out_for_delivery"
  }'
```

## Integration Guide

### Adding Tracking Updates Programmatically

```php
use App\Services\TrackingService;
use App\Models\Order;

class YourController
{
    protected TrackingService $trackingService;

    public function __construct(TrackingService $trackingService)
    {
        $this->trackingService = $trackingService;
    }

    public function updateOrderTracking(Order $order)
    {
        // Log a tracking update
        $this->trackingService->logTrackingUpdate(
            order: $order,
            status: 'in_transit',
            location: 'Manila',
            description: 'Package arrived at distribution center',
            latitude: 14.5995,
            longitude: 120.9842
        );

        // Or update status and log automatically
        $this->trackingService->updateOrderStatus(
            order: $order,
            newStatus: 'out_for_delivery',
            location: 'Quezon City',
            description: 'Out for delivery'
        );
    }
}
```

### Displaying Tracking Info in Views

```blade
<!-- Show tracking status -->
<span class="badge bg-{{ $order->status === 'delivered' ? 'success' : 'info' }}">
    {{ ucfirst(str_replace('_', ' ', $order->status)) }}
</span>

<!-- Link to tracking -->
<a href="{{ route('tracking.show', $order) }}" class="btn btn-primary">
    Track Order
</a>

<!-- Display tracking history -->
@foreach($order->trackingHistory as $history)
    <div class="tracking-item">
        <p>{{ $history->description }}</p>
        <p class="text-muted">{{ $history->timestamp->format('M d, Y H:i') }}</p>
    </div>
@endforeach
```

## API Endpoints

### GET `/tracking/{order}/data`
Returns JSON tracking data for AJAX updates.

**Response:**
```json
{
    "order_number": "ORD-2024-001234",
    "status": "out_for_delivery",
    "status_info": {
        "label": "Out for Delivery",
        "color": "indigo",
        "icon": "location-dot",
        "description": "Driver is heading to your location"
    },
    "shipment": {
        "tracking_number": "JT001999191584...",
        "carrier": "J&T Express",
        "status": "on the way",
        "shipped_at": "2024-06-27 10:30"
    },
    "timeline": [...],
    "total": 1234.50,
    "items_count": 2,
    "driver_latitude": 14.5995,
    "driver_longitude": 120.9842,
    "customer_latitude": 14.5950,
    "customer_longitude": 120.9800
}
```

## Database Migration

To apply the new tracking history table, run:

```bash
php artisan migrate
```

This will create the `tracking_histories` table with all necessary columns and indexes.

## Map Integration

The tracking system uses **Leaflet.js** for interactive maps:
- Shows customer delivery address as default marker
- Shows driver location when in transit
- Click markers for address details
- Automatically centers and zooms map

## Features to Add (Optional)

1. **Courier API Integration**
   - Real-time updates from J&T Express, LBC, etc.
   - Automatic status synchronization

2. **Email Notifications**
   - Notify customers of status changes
   - Delivery confirmation emails

3. **SMS Updates**
   - Text notifications for delivery
   - Estimated arrival time

4. **Customer Notifications**
   - In-app notifications of status changes
   - Push notifications

5. **Analytics Dashboard**
   - Delivery performance metrics
   - Average delivery time
   - Route optimization

## Troubleshooting

### Issue: Map not displaying
**Solution**: Ensure Leaflet CSS/JS is loaded from CDN in view

### Issue: Tracking data not updating
**Solution**: Check browser console for AJAX errors, verify API endpoint

### Issue: Driver location not showing
**Solution**: Ensure `driver_latitude` and `driver_longitude` are populated in database

## Performance Optimization

1. **Indexing**: `tracking_histories` table has indexes on order_id and timestamp
2. **Pagination**: Tracking history can be paginated for large datasets
3. **Caching**: Consider caching tracking status for frequently accessed orders
4. **AJAX Polling**: Client-side auto-refresh can be adjusted (default: 30 seconds)

## Security Notes

- Tracking requires authentication OR order_number + postal_code verification
- Sellers can only update tracking for their own orders
- Admin override available for all orders
- Sensitive location data encrypted in transit
- Rate limiting on public tracking endpoint recommended

## Support & Maintenance

For questions or issues with the tracking system:
1. Check tracking history records in database
2. Verify seller permissions
3. Check order status transitions
4. Review controller and service logs

---

**Last Updated**: June 27, 2024
**Version**: 1.0
