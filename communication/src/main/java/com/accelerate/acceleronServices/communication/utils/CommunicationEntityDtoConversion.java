package com.accelerate.acceleronServices.communication.utils;

import com.accelerate.acceleronServices.communication.dto.request.CommunicationDto;
import com.accelerate.acceleronServices.communication.model.CommunicationEntity;
import org.modelmapper.ModelMapper;
import org.modelmapper.TypeToken;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Component;

import java.lang.reflect.Type;

public class CommunicationEntityDtoConversion {

    @Autowired
    private ModelMapper modelMapper;

    public <T1, T2> T1 convert(T2 dto){
        Type typeOfT1 = new TypeToken<T1>(){}.getType();
        return modelMapper.map(dto, typeOfT1);
    }

    public <T> CommunicationEntity convertToEntity(T entity){
        return modelMapper.map(entity, CommunicationEntity.class);
    }

    public <T> CommunicationDto convertToDto(T entity){ return modelMapper.map(entity, CommunicationDto.class);}
}
