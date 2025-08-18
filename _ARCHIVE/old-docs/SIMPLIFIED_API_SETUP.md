# 🚀 Simplified API Setup for Culture Radar

## You Only Need 3 API Keys!

### Required APIs (Must Have):

1. **OpenAgenda** - For event data (FREE)
2. **OpenWeatherMap** - For weather (FREE) 
3. **Google Maps** - For maps AND directions (FREE $200/month credit)

### That's it! ✨

**You DON'T need:**
- ❌ Mapbox (Google Maps does everything)
- ❌ Citymapper (discontinued)
- ❌ HERE Maps (only if Google Maps fails)
- ❌ Any transport API (Google Maps includes directions)

## Why Google Maps is Enough:

**One Google Maps API key gives you:**
- ✅ Interactive maps
- ✅ Directions (walking, driving, transit, cycling)
- ✅ Geocoding (address to coordinates)
- ✅ Places (venue information)
- ✅ Distance calculations
- ✅ Route planning

## Your Minimal .env File:

```env
# Just these 3 APIs!
OPENAGENDA_API_KEY=your_openagenda_key
OPENWEATHERMAP_API_KEY=your_weather_key  
GOOGLE_MAPS_API_KEY=your_google_maps_key

# That's all you need!
```

## Google Maps Setup:

1. Go to: https://console.cloud.google.com/
2. Create new project
3. Enable these APIs (all use same key):
   - Maps JavaScript API
   - Directions API
   - Geocoding API
   - Places API
4. Create credentials → API Key
5. Copy the key

**Free Tier:** $200/month credit = ~28,000 map loads or 40,000 directions requests

## Transport/Directions:

**Google Maps Directions API handles:**
- Public transit routes with real-time data
- Walking directions
- Cycling paths
- Driving directions with traffic
- Multiple route options
- Step-by-step instructions

**Example usage in the app:**
```javascript
// This single API call gets complete transit directions
const directionsService = new google.maps.DirectionsService();
directionsService.route({
    origin: userLocation,
    destination: eventVenue,
    travelMode: 'TRANSIT', // or WALKING, BICYCLING, DRIVING
}, callback);
```

## Alternative Transport APIs (Optional):

Only consider these if you need specific features:

### For Paris Only:
**IDF Mobilités** (FREE, no key needed)
- Real-time Paris metro/bus/RER data
- URL: https://prim.iledefrance-mobilites.fr/
- Only useful if focused on Paris

### For Europe:
**HERE Maps** (Freemium)
- 250k transactions/month free
- Good for European cities
- URL: https://developer.here.com/

### What We Removed:
- **Citymapper** - API discontinued in 2023
- **Mapbox** - Redundant with Google Maps
- **TomTom** - Expensive, no free tier
- **Azure Maps** - Complex setup

## Cost Comparison:

| Service | Free Tier | Your Expected Usage | Monthly Cost |
|---------|-----------|-------------------|--------------|
| Google Maps | $200 credit | ~$50 usage | **$0** |
| OpenAgenda | Unlimited | Unlimited | **$0** |
| OpenWeatherMap | 1000 calls/day | 200 calls/day | **$0** |
| **TOTAL** | | | **$0/month** |

## Quick Test:

After adding your 3 API keys, run:
```bash
php setup-apis.php
```

This will verify:
- ✅ Events load from OpenAgenda
- ✅ Weather shows real temperature
- ✅ Maps display correctly
- ✅ Directions work for all transport modes

## Summary:

**Before:** 6+ APIs, complex setup, potential costs
**Now:** Just 3 APIs, simple setup, completely free

The Google Maps API is a complete solution for mapping and transport. No need for multiple transport APIs!

---

*Remember: Simpler is better. These 3 APIs give you everything Culture Radar needs.*