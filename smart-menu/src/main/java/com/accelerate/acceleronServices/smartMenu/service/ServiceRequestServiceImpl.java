package com.accelerate.acceleronServices.smartMenu.service;

import com.accelerate.acceleronServices.smartMenu.dto.request.UpdateServiceRequestDto;
import com.accelerate.acceleronServices.smartMenu.dto.response.ApiResponse;
import com.accelerate.acceleronServices.smartMenu.dto.response.GenericResponse;
import com.accelerate.acceleronServices.smartMenu.enums.StatusTextEnum;
import com.accelerate.acceleronServices.smartMenu.repository.ServiceRequestRepository;
import lombok.AllArgsConstructor;
import lombok.extern.slf4j.Slf4j;
import org.springframework.http.HttpStatus;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;

@Service
@AllArgsConstructor
@Slf4j
public class ServiceRequestServiceImpl implements ServiceRequestService {

    private ServiceRequestRepository serviceRequestRepository;

    @Override
    @Transactional
    public ApiResponse<GenericResponse> updateServiceRequest(UpdateServiceRequestDto request) {

        serviceRequestRepository.updateStatus(request.getRequestId(), request.getStatus());

        ApiResponse<GenericResponse> response = new ApiResponse<>();
        response.setStatus(true);
        response.setMessage(StatusTextEnum.SUCCESS.value());
        response.setStatusCode(HttpStatus.OK.value());
        response.setData(new GenericResponse(StatusTextEnum.SUCCESS.value()));
        return response;
    }
}
