package com.example.webte2.PDFController;

import org.springframework.stereotype.Service;
import org.springframework.web.client.RestTemplate;

import java.util.Map;

@Service
public class LocationService {
    private final RestTemplate restTemplate = new RestTemplate();
    public Location getLocationFromIp(String ip) {
        try {
            String url = "http://ip-api.com/json/" + ip;
            Map<?, ?> response = restTemplate.getForObject(url, Map.class);
            if ("success".equals(response.get("status"))) {
                Location location = new Location();
                location.setCity((String) response.get("city"));
                location.setState((String) response.get("country")); // or "state"
                return location;
            }
        } catch (Exception e) {
            e.printStackTrace();
        }
        Location fallback = new Location();
        fallback.setCity("Unknown");
        fallback.setState("Unknown");
        return fallback;
    }

}
