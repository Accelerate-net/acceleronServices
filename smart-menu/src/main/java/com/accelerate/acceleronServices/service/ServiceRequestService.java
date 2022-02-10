package com.accelerate.acceleronServices.service;

import com.accelerate.acceleronServices.dto.request.UpdateServiceRequestDto;
import com.accelerate.acceleronServices.dto.response.ApiResponse;
import com.accelerate.acceleronServices.dto.response.GenericResponse;

public interface ServiceRequestService {
    public ApiResponse<GenericResponse> updateServiceRequest(UpdateServiceRequestDto request);
}
