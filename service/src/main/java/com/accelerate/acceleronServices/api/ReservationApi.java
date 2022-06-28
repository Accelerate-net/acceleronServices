package com.accelerate.acceleronServices.api;


import com.accelerate.acceleronServices.dto.request.UpdateServiceRequestDto;
import com.accelerate.acceleronServices.dto.response.ApiResponse;
import com.accelerate.acceleronServices.dto.response.GenericResponse;
import com.accelerate.acceleronServices.service.ServiceRequestService;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.PostMapping;
import org.springframework.web.bind.annotation.RequestBody;
import org.springframework.web.bind.annotation.RequestMapping;
import org.springframework.web.bind.annotation.RestController;

@RestController
@RequestMapping("/reservation")
public class ReservationApi {

    @Autowired
    private ServiceRequestService smartMenuService;

    @PostMapping("/")
    public ResponseEntity<?> getReservation(@RequestBody UpdateServiceRequestDto request) {
        ApiResponse<GenericResponse> response = smartMenuService.updateServiceRequest(request);
        return ResponseEntity.ok().body(response);
    }
}
