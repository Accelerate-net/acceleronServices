package com.accelerate.acceleronServices.api;

import com.accelerate.acceleronServices.smartMenu.dto.request.UpdateServiceRequestDto;
import com.accelerate.acceleronServices.smartMenu.dto.response.ApiResponse;
import com.accelerate.acceleronServices.smartMenu.dto.response.GenericResponse;
import com.accelerate.acceleronServices.smartMenu.service.ServiceRequestService;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.PostMapping;
import org.springframework.web.bind.annotation.RequestBody;
import org.springframework.web.bind.annotation.RequestMapping;
import org.springframework.web.bind.annotation.RestController;

@RestController
@RequestMapping("/smart-menu")
public class SmartMenuApi {

    @Autowired
    private ServiceRequestService smartMenuService;

    @PostMapping("/admin/update-service-request")
    public ResponseEntity<?> updateServiceRequest(@RequestBody UpdateServiceRequestDto request) {
        ApiResponse<GenericResponse> response = smartMenuService.updateServiceRequest(request);
        return ResponseEntity.ok().body(response);
    }
}
