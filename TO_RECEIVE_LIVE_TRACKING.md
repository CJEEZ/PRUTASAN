# "To Receive" Status - Live Order Tracking

## Overview

When an order status is **"To Receive"**, customers can see **live, real-time tracking** with their driver's location on an interactive map. The location updates automatically every 5 seconds.

## Features When Status is "To Receive"

### 1. **Live Tracking Badge**
- Prominent "LIVE TRACKING" badge appears with a pulsing red indicator
- Shows that updates are happening in real-time

### 2. **Live Location Map**
- Interactive Leaflet.js map displayed prominently at the top
- Shows both:
  - **Customer delivery address** (blue marker) - your home address
  - **Driver current location** (red marker) - where the driver is now
- Map auto-fits to show both markers
- Updates every 5 seconds (instead of 30 seconds)

### 3. **Real-Time Updates**
- Driver location refreshes automatically
- Map centers on both driver and delivery location
- Connection status visible
- Smooth marker position updates

### 4. **Visual Indicators**
- Status banner with red "To Receive" label
- "Arriving Soon" message in timeline
- "LIVE TRACKING" pulse animation
- Driver status message: "Driver is on the way!"

## How It Works

### For Customers

1. **View Order Tracking**
   - Go to Profile → My Orders
   - Click "Track" button on order with "To Receive" status
   - See live tracking page with interactive map

2. **Monitor Driver Location**
   - Map shows driver position (red marker) moving toward you
   - Your delivery address shown as blue marker
   - Distance and direction clearly visible

3. **Get Updates**
   - Location updates every 5 seconds automatically
   - No need to refresh the page
   - Real-time delivery status shown

### For Sellers/Drivers

1. **Update Driver Location**
   - Send location updates via API endpoint
   - Location is sent to customer in real-time

2. **Update Order Status**
   - Mark order as "to_receive" when driver is heading to customer
   - System automatically logs all location updates

## API Integration

### Update Driver Location (For Sellers/Drivers)

```bash
curl -X POST /seller/orders/{order_id}/update-tracking-location \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer {token}" \
  -d '{
    "latitude": 14.5995,
    "longitude": 120.9842,
    "location": "Cainta, Rizal",
    "status": "to_receive"
  }'
```

### Update Order Status to "To Receive"

```php
// In controller or service
use App\Services\TrackingService;

$trackingService = app(TrackingService::class);

$trackingService->updateOrderStatus(
    $order,
    'to_receive',
    'Driver heading to delivery address',
    'Driver is on the way to you',
    14.5995,  // latitude
    120.9842  // longitude
);
```

## Database Fields Used

```sql
-- Location tracking fields in orders table
latitude              -- Delivery address latitude
longitude             -- Delivery address longitude
driver_latitude       -- Current driver latitude
driver_longitude      -- Current driver longitude
current_location      -- Human-readable location name
status               -- Order status (e.g., "to_receive")
tracking_number      -- Shipment tracking number
```

## Tracking Timeline Display

When order is "To Receive", the timeline shows:

```
✓ Order Placed        (Completed)
✓ Order Confirmed     (Completed)
✓ Packed              (Completed)
✓ Shipped             (Completed)
✓ In Transit          (Completed)
→ To Receive - Arriving Soon! (CURRENT)
  └─ Driver is on the way to you
     (Live map location updates here)
```

## Real-Time Update Intervals

| Status | Update Interval | Purpose |
|--------|-----------------|---------|
| pending | 30 seconds | Waiting for confirmation |
| confirmed | 30 seconds | Order being prepared |
| processing | 30 seconds | Packing items |
| packed | 30 seconds | Ready to ship |
| shipped | 30 seconds | In courier system |
| in_transit | 30 seconds | En route |
| **to_receive** | **5 seconds** | **Driver arriving - LIVE** |
| delivered | 30 seconds | Order completed |

## Example Scenario

### Timeline of a "To Receive" Order

```
14:00 - Order Placed
        Customer sees "Order Placed" status
        
14:15 - Order Confirmed
        Seller confirms the order
        Customer gets notification
        
14:30 - Items Packed
        Seller packs items
        
14:45 - Shipped
        Courier picks up package
        Seller provides tracking number
        
15:00 - In Transit
        Package is with courier
        Standard tracking every 30 sec
        
15:30 - To Receive (STATUS CHANGES)
        Driver assigned to delivery
        ✅ "LIVE TRACKING" badge appears
        ✅ Map shows driver location
        ✅ Updates every 5 seconds
        ✅ Customer sees driver moving on map
        
15:45 - Delivered
        Driver arrives at address
        Package delivered
        Status changes to "Delivered"
```

## Features & Implementation

### Real-Time Map Features

✅ **Automatic Updates**
- Map location updates every 5 seconds
- No manual refresh needed
- Smooth marker movements

✅ **Map Controls**
- Zoom in/out
- Pan around map
- Marker info popups
- OpenStreetMap attribution

✅ **Mobile Responsive**
- Works on phones and tablets
- Touch-friendly map controls
- Full-screen capable

✅ **Location Accuracy**
- Uses GPS coordinates
- Shows exact positions
- Distance between driver and customer

### Safety & Privacy

✅ **Customer Privacy**
- Location only shown during delivery
- Automatically hidden when "Delivered"
- Access limited to order owner

✅ **Driver Privacy**
- No personal info displayed
- Only position shown
- Tracked until delivery complete

## Configuration

### Update Interval Settings

To change the live update interval (currently 5 seconds for "to_receive"):

**In `resources/views/tracking/show.blade.php`:**
```javascript
// Change this value
const updateInterval = isToReceive ? 5000 : 30000; // milliseconds

// To change, for example:
const updateInterval = isToReceive ? 10000 : 30000; // 10 seconds for "to_receive"
```

### Status Recognition

Status is recognized as "To Receive" in the controller:

```php
// In TrackingController
$statusInfo['color'] => 'red'           // Red status banner
$statusInfo['icon'] => 'truck'          // Truck icon
$statusInfo['label'] => 'To Receive'    // Display label
```

## Troubleshooting

### Issue: Map not showing driver location
**Solution**: 
- Ensure driver_latitude and driver_longitude are updated
- Check if order status is "to_receive"
- Verify API endpoint is being called

### Issue: Location not updating
**Solution**:
- Check browser console for errors
- Verify internet connection
- Refresh the page
- Check if update interval is set correctly

### Issue: "LIVE TRACKING" badge not showing
**Solution**:
- Verify order status is exactly "to_receive"
- Check if status is saved in database
- Clear browser cache and reload

## Best Practices

1. **Update Status to "To Receive" When:**
   - Driver is assigned for delivery
   - Driver is heading to customer address
   - Order is in final delivery phase

2. **Send Location Updates:**
   - Every few minutes as driver travels
   - When driver is close to destination
   - More frequently in traffic areas

3. **Customer Communication:**
   - Send SMS/email when changing to "to_receive"
   - Notify about estimated arrival time
   - Ask customer to be ready

## Data Flow

```
Driver Mobile App/Vehicle GPS
           ↓
    Update Location API
           ↓
    Order.driver_latitude
    Order.driver_longitude
           ↓
    TrackingController.getTrackingData()
           ↓
    Customer Browser (AJAX)
           ↓
    Update Leaflet Map
           ↓
    Live Display to Customer
```

## Security Notes

- All location data encrypted in transit
- Customer can only see location for their own orders
- Seller can only update their own shipments
- Admin override available
- Rate limiting on API updates

---

## Quick Reference

| Component | Details |
|-----------|---------|
| Status | "to_receive" |
| Badge | "LIVE TRACKING" (red pulsing) |
| Update Interval | 5 seconds |
| Map | Interactive Leaflet.js |
| Features | Real-time driver location |
| Privacy | Shown only during delivery |
| Mobile | Fully responsive |

---

**Last Updated**: June 27, 2026
**Version**: 1.0
**Status**: ✅ Production Ready
