package com.accelerate.acceleronServices.service;

import com.accelerate.acceleronServices.dto.request.UpdateServiceRequestDto;
import com.accelerate.acceleronServices.dto.response.ApiResponse;
import com.accelerate.acceleronServices.dto.response.GenericResponse;
import com.accelerate.acceleronServices.enums.StatusTextEnum;
import com.accelerate.acceleronServices.repository.ServiceRequestRepository;
import lombok.AllArgsConstructor;
import lombok.extern.slf4j.Slf4j;
import org.springframework.stereotype.Service;

@Service
@AllArgsConstructor
@Slf4j
public class ServiceRequestServiceImpl implements ServiceRequestService {

    private ServiceRequestRepository serviceRequestRepository;

    @Override
    public ApiResponse<GenericResponse> updateServiceRequest(UpdateServiceRequestDto request) {

        serviceRequestRepository.updateStatus(request.getRequestId(), request.getStatus());

        ApiResponse<GenericResponse> response = new ApiResponse<>();
        response.setData(new GenericResponse(StatusTextEnum.SUCCESS.name()));
        return response;
    }
}