package com.accelerate.acceleronServices.api;

import org.springframework.web.bind.annotation.*;

import java.util.Date;
import java.util.HashMap;
import java.util.Map;

@RestController
public class HealthCheckApi {
    private static final Date startedSince = new Date();

    public HealthCheckApi() {
    }

    @GetMapping({"/health"})
    public Map<String, Object> healthApi() {
        Map<String, Object> map = new HashMap();
        map.put("status", "ok");
        map.put("time", (new Date()).toString());
        map.put("since", startedSince.toString());
        return map;
    }
}
