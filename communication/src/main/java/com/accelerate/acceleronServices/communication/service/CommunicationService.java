package com.accelerate.acceleronServices.communication.service;

import com.accelerate.acceleronServices.communication.dto.request.CommunicationDto;
import com.accelerate.acceleronServices.communication.dto.response.ApiResponse;
import com.accelerate.acceleronServices.communication.model.CommunicationEntity;

import java.util.List;

public interface CommunicationService {
    ApiResponse addCommunication(CommunicationDto request);

    List<CommunicationEntity> getAllCommunication();

    CommunicationEntity getCommunicationById(int id);


    ApiResponse deleteCommunicationById(int id);
}
