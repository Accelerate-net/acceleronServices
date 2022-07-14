package com.accelerate.acceleronServices.smartMenu.service;

import com.accelerate.acceleronServices.smartMenu.dto.request.UpdateServiceRequestDto;
import com.accelerate.acceleronServices.smartMenu.dto.response.ApiResponse;
import com.accelerate.acceleronServices.smartMenu.dto.response.GenericResponse;

public interface ServiceRequestService {
    ApiResponse<GenericResponse> updateServiceRequest(UpdateServiceRequestDto request);
}
