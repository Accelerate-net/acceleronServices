package com.accelerate.acceleronServices.communication.service;

import com.accelerate.acceleronServices.communication.dto.request.CommunicationDto;
import com.accelerate.acceleronServices.communication.dto.response.ApiResponse;
import com.accelerate.acceleronServices.communication.model.CommunicationEntity;
import com.accelerate.acceleronServices.communication.repository.CommunicationRepository;
import com.accelerate.acceleronServices.communication.utils.CommunicationEntityDtoConversion;
import com.accelerate.acceleronServices.communication.utils.ResponseMessage;
import lombok.AllArgsConstructor;
import lombok.extern.slf4j.Slf4j;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.http.HttpStatus;
import org.springframework.stereotype.Service;

import javax.persistence.EntityNotFoundException;
import java.util.List;

@Service
@AllArgsConstructor
@Slf4j
public class CommunicationServiceImpl implements CommunicationService {

    @Autowired
    private CommunicationRepository communicationRepository;

    @Autowired
    private CommunicationEntityDtoConversion communicationEntityDtoConversion;

    @Override
    public ApiResponse addCommunication(CommunicationDto request) {
        CommunicationEntity communicationEntity = communicationEntityDtoConversion.convertToEntity(request);
        communicationRepository.save(communicationEntity);

        ApiResponse response = new ApiResponse(true, HttpStatus.CREATED.value(), ResponseMessage.success);
        return response;

    }

    @Override
    public List<CommunicationEntity> getAllCommunication() {
        return communicationRepository.findAll();
    }

    @Override
    public CommunicationEntity getCommunicationById(int id) {
        CommunicationEntity communicationEntity = communicationRepository.getById(id);
        if (communicationEntity != null) {
            return communicationEntity;
        } else {
            throw new EntityNotFoundException(ResponseMessage.notFound);
        }
    }

    @Override
    public ApiResponse deleteCommunicationById(int id) {
        if(communicationRepository.DeleteById(id) == 1){
            ApiResponse response = new ApiResponse(true, HttpStatus.OK.value(), ResponseMessage.success);
            return response;
        }
        else{
            throw new EntityNotFoundException(ResponseMessage.notFound);
        }
    }
}
